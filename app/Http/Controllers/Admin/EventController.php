<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Club;
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
        } else {
            $clubs = $user->managedClubs;
        }

        // Thống kê nhanh
        $stats = [
            'total'    => $user->isAdmin() ? Event::count() : Event::whereIn('club_id', $user->managedClubs->pluck('id')->toArray())->count(),
            'pending'  => $user->isAdmin() ? Event::where('status', 'pending')->count() : Event::whereIn('club_id', $user->managedClubs->pluck('id')->toArray())->where('status', 'pending')->count(),
            'approved' => $user->isAdmin() ? Event::where('status', 'approved')->count() : Event::whereIn('club_id', $user->managedClubs->pluck('id')->toArray())->where('status', 'approved')->count(),
            'rejected' => $user->isAdmin() ? Event::where('status', 'rejected')->count() : Event::whereIn('club_id', $user->managedClubs->pluck('id')->toArray())->where('status', 'rejected')->count(),
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

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $rules = [
            'club_id' => 'required|exists:clubs,id',
            'category_id' => 'required|exists:event_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ];

        if ($user->isAdmin()) {
            $rules['status'] = 'required|in:pending,approved,rejected';
        }

        $request->validate($rules);

        // Security check for manager
        if (!$user->isAdmin()) {
            $clubIds = $user->managedClubs->pluck('id')->toArray();
            if (!in_array($request->club_id, $clubIds)) {
                abort(403, 'Bạn không có quyền tạo sự kiện cho CLB này.');
            }
        }

        $data = $request->validated();
        if (!$user->isAdmin()) {
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
        $user = Auth::user();
        if (!$user->isAdmin()) {
            $clubIds = $user->managedClubs->pluck('id')->toArray();
            if (!in_array($event->club_id, $clubIds)) {
                abort(403, 'Bạn không có quyền xem sự kiện này.');
            }
        }

        $event->load(['club', 'category', 'registrations.user', 'registrations.checkinLog']);
        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            $clubIds = $user->managedClubs->pluck('id')->toArray();
            if (!in_array($event->club_id, $clubIds)) {
                abort(403, 'Bạn không có quyền sửa sự kiện này.');
            }
        }

        if ($user->isAdmin()) {
            $clubs = Club::where('status', 'active')->get();
        } else {
            $clubs = $user->managedClubs;
        }
        
        $categories = EventCategory::all();

        return view('admin.events.edit', compact('event', 'clubs', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            $clubIds = $user->managedClubs->pluck('id')->toArray();
            if (!in_array($event->club_id, $clubIds)) {
                abort(403, 'Bạn không có quyền sửa sự kiện này.');
            }
        }

        $rules = [
            'club_id' => 'required|exists:clubs,id',
            'category_id' => 'required|exists:event_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ];

        if ($user->isAdmin()) {
            $rules['status'] = 'required|in:pending,approved,rejected';
        }

        $request->validate($rules);

        $data = $request->validated();
        
        if (!$user->isAdmin()) {
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
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Bạn không có quyền duyệt sự kiện.');
        }

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
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Bạn không có quyền từ chối sự kiện.');
        }

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
        $user = Auth::user();

        if (!$user->isAdmin()) {
            $clubIds = $user->managedClubs->pluck('id')->toArray();
            if (!in_array($event->club_id, $clubIds)) {
                abort(403, 'Bạn không có quyền xóa sự kiện này.');
            }
        }

        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Đã xóa sự kiện!');
    }
}
