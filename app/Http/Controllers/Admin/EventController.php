<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Club;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $events = Event::with(['club', 'category'])->orderBy('created_at', 'desc')->get();
        } else {
            $clubIds = $user->managedClubs->pluck('id')->toArray();
            $events = Event::with(['club', 'category'])
                ->whereIn('club_id', $clubIds)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('admin.events.index', compact('events'));
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
        
        // Cập nhật sự kiện, nếu manager cập nhật thì nó có thể bị chuyển về pending lại (tùy logic, ở đây giữ nguyên hoặc chuyển về pending).
        // Để đơn giản: Manager không đổi được status.
        if (!$user->isAdmin()) {
            unset($data['status']);
        }

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'Cập nhật sự kiện thành công!');
    }

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
        return redirect()->route('admin.events.index')->with('success', 'Xóa sự kiện thành công!');
    }
}
