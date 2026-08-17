<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\CheckinLog;
use App\Models\StudentPoint;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckinController extends Controller
{
    public function index()
    {
        // Danh sách sự kiện đã duyệt và (có thể) đang diễn ra
        $events = Event::where('status', 'approved')
            ->where('end_time', '>=', Carbon::now())
            ->orderBy('start_time', 'desc')
            ->get();
        return view('manager.checkin.index', compact('events'));
    }

    public function process(Request $request)
    {
        $qrString = $request->input('qr_code_string');
        $eventId = $request->input('event_id');

        if (!$qrString || !$eventId) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
        }

        try {
            DB::beginTransaction();

            $registration = EventRegistration::with('user')
                ->where('qr_code_string', $qrString)
                ->where('event_id', $eventId)
                ->first();

            if (!$registration) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Mã QR không hợp lệ cho sự kiện này!']);
            }

            // Kiểm tra xem đã checkin chưa
            $alreadyCheckedIn = CheckinLog::where('registration_id', $registration->id)->exists();
            if ($alreadyCheckedIn) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Sinh viên ' . $registration->user->name . ' đã check-in trước đó!']);
            }

            // Ghi nhận checkin
            CheckinLog::create([
                'registration_id' => $registration->id,
                'checkin_time' => Carbon::now()
            ]);

            // Cộng điểm rèn luyện (VD: 5 điểm/sự kiện)
            StudentPoint::create([
                'user_id' => $registration->user_id,
                'event_id' => $eventId,
                'points' => 5
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Check-in thành công: ' . $registration->user->name . ' (' . $registration->user->student_code . ')'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log lỗi để dễ debug
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
}
