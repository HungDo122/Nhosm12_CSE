<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Club;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Danh sách tất cả sự kiện với filter trạng thái
     */
    public function index(Request $request)
    {
        $query = Event::with(['club', 'category'])
            ->withCount('registrations');

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Lọc theo CLB
        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        $events = $query->latest()->paginate(15)->withQueryString();
        $clubs = Club::orderBy('name')->get();

        // Thống kê nhanh
        $stats = [
            'total'    => Event::count(),
            'pending'  => Event::where('status', 'pending')->count(),
            'approved' => Event::where('status', 'approved')->count(),
            'rejected' => Event::where('status', 'rejected')->count(),
        ];

        return view('admin.events.index', compact('events', 'clubs', 'stats'));
    }

    /**
     * Chi tiết sự kiện
     */
    public function show(Event $event)
    {
        $event->load(['club', 'category', 'registrations.user', 'registrations.checkinLog']);
        return view('admin.events.show', compact('event'));
    }

    /**
     * Duyệt sự kiện (pending → approved)
     */
    public function approve(Event $event)
    {
        if ($event->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể duyệt sự kiện đang chờ duyệt!');
        }

        $event->update(['status' => 'approved']);

        return redirect()->back()->with('success', "Đã duyệt sự kiện: {$event->name}");
    }

    /**
     * Từ chối sự kiện (pending → rejected)
     */
    public function reject(Event $event)
    {
        if ($event->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể từ chối sự kiện đang chờ duyệt!');
        }

        $event->update(['status' => 'rejected']);

        return redirect()->back()->with('success', "Đã từ chối sự kiện: {$event->name}");
    }

    /**
     * Xóa sự kiện (soft delete)
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Đã xóa sự kiện!');
    }
}
