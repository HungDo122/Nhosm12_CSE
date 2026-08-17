<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\StudentPoint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class EventController extends Controller
{
    public function index()
    {
        // withCount giải quyết N+1 query: chỉ 1 query thêm thay vì N query trong vòng lặp
        $events = Event::with('category')
            ->withCount('registrations')
            ->where('status', 'approved')
            ->orderBy('start_time', 'asc')
            ->get();
            
        $registeredEventIds = Auth::check() ? Auth::user()->eventRegistrations()->pluck('event_id')->toArray() : [];
            
        return view('student.events.index', compact('events', 'registeredEventIds'));
    }

    public function myTickets()
    {
        $registrations = EventRegistration::with(['event', 'checkinLog'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('student.events.my_tickets', compact('registrations'));
    }

    public function register(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Lock for update để chống quá tải (Race condition)
            $event = Event::lockForUpdate()->findOrFail($id);

            // Kiểm tra số lượng
            $currentRegistrations = EventRegistration::where('event_id', $event->id)->count();
            if ($currentRegistrations >= $event->capacity) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Sự kiện đã hết chỗ!');
            }

            // Kiểm tra trùng lặp
            $exists = EventRegistration::where('event_id', $event->id)
                ->where('user_id', Auth::id())
                ->exists();
                
            if ($exists) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Bạn đã đăng ký sự kiện này rồi!');
            }

            // Sinh mã QR duy nhất
            $qrString = uniqid('EVENT_') . '_' . Auth::id() . '_' . $event->id;

            EventRegistration::create([
                'event_id' => $event->id,
                'user_id' => Auth::id(),
                'qr_code_string' => $qrString
            ]);

            DB::commit();
            return redirect()->route('student.my_tickets')->with('success', 'Đăng ký thành công!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            // Không lộ thông tin lỗi nội bộ ra ngoài
            return redirect()->back()->with('error', 'Đăng ký thất bại, vui lòng thử lại sau.');
        }
    }

    public function downloadCertificate($id)
    {
        $registration = EventRegistration::with([
                'event' => fn($q) => $q->withTrashed(),
                'user',
                'checkinLog'
            ])
            ->where('user_id', Auth::id())
            ->find($id);

        if (!$registration) {
            return redirect()->back()->with('error', 'Không tìm thấy chứng nhận này.');
        }

        if (!$registration->checkinLog) {
            return redirect()->back()->with('error', 'Bạn chưa tham gia sự kiện này nên không thể tải chứng nhận!');
        }

        if (!$registration->event) {
            return redirect()->back()->with('error', 'Sự kiện không còn tồn tại!');
        }

        $pdf = Pdf::loadView('student.events.certificate', compact('registration'));
        // Dùng tên sự kiện thay vì ID để tên file có ý nghĩa hơn
        $filename = 'chung-nhan-' . \Illuminate\Support\Str::slug($registration->event->name) . '.pdf';
        return $pdf->download($filename);
    }
}
