<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Club;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Danh sách tất cả sự kiện với filter trạng thái
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Event::with(['club', 'category'])
            ->withCount('registrations');

        if (!$user->isAdmin()) {
            $clubIds = $user->managedClubs->pluck('id')->toArray();
            $query->whereIn('club_id', $clubIds);
        }

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
        
        if ($user->isAdmin()) {
            $clubs = Club::orderBy('name')->get();
            $clubIdsForStats = [];
        } else {
            $clubs = $user->managedClubs;
            $clubIdsForStats = $user->managedClubs->pluck('id')->toArray();
        }

        // Thống kê nhanh: Tối ưu hoá gom thành 1 truy vấn
        $statsQuery = Event::query();
        if (!$user->isAdmin()) {
            $statsQuery->whereIn('club_id', $clubIdsForStats);
        }
        
        $rawStats = $statsQuery->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $stats = [
            'total'    => $rawStats->sum(),
            'pending'  => $rawStats->get('pending', 0),
            'approved' => $rawStats->get('approved', 0),
            'rejected' => $rawStats->get('rejected', 0),
        ];

        return view('admin.events.index', compact('events', 'clubs', 'stats'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $clubs = Club::where('status', 'active')->get();
        } else {
            $clubs = $user->managedClubs;
        }
        
        $categories = EventCategory::all();

        return view('admin.events.create', compact('clubs', 'categories'));
    }

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();

        if (!Auth::user()->isAdmin()) {
            $data['status'] = 'pending';
        }

        Event::create($data);

        return redirect()->route('admin.events.index')->with('success', 'Tạo sự kiện thành công!');
    }

    /**
     * Chi tiết sự kiện
     */
    public function show(Event $event)
    {
        $this->authorize('view', $event);

        $event->load(['club', 'category', 'registrations.user', 'registrations.checkinLog']);
        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        $user = Auth::user();

        if ($user->isAdmin()) {
            $clubs = Club::where('status', 'active')->get();
        } else {
            $clubs = $user->managedClubs;
        }
        
        $categories = EventCategory::all();

        return view('admin.events.edit', compact('event', 'clubs', 'categories'));
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $data = $request->validated();
        
        if (!Auth::user()->isAdmin()) {
            unset($data['status']);
        }

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'Cập nhật sự kiện thành công!');
    }

    /**
     * Duyệt sự kiện (pending → approved)
     */
    public function approve(Event $event)
    {
        $this->authorize('approve', $event);

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
        $this->authorize('reject', $event);

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
        $this->authorize('delete', $event);

        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Đã xóa sự kiện!');
    }
}
