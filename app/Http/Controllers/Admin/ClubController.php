<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    // Hiển thị danh sách CLB
    public function index()
    {
        $clubs = Club::with('leaders')->withCount('members')->latest()->get();
        return view('admin.clubs.index', compact('clubs'));
    }

    // Form tạo mới CLB
    public function create()
    {
        return view('admin.clubs.create');
    }

    // Lưu CLB vào DB
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:clubs,name',
            'code' => 'nullable|string|max:50|unique:clubs,code',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Club::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.clubs.index')->with('success', 'Thêm câu lạc bộ thành công!');
    }

    // Chi tiết CLB & Quản lý thành viên
    public function show(Club $club)
    {
        $club->load(['members.user', 'leaders']);
        $users = User::orderBy('name')->get();
        return view('admin.clubs.show', compact('club', 'users'));
    }

    // Form chỉnh sửa CLB
    public function edit(Club $club)
    {
        return view('admin.clubs.edit', compact('club'));
    }

    // Cập nhật CLB
    public function update(Request $request, Club $club)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:clubs,name,' . $club->id,
            'code' => 'nullable|string|max:50|unique:clubs,code,' . $club->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $club->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.clubs.index')->with('success', 'Cập nhật câu lạc bộ thành công!');
    }

    // Xóa CLB
    public function destroy(Club $club)
    {
        if ($club->events()->count() > 0 || $club->members()->count() > 0) {
            return redirect()->route('admin.clubs.index')->with('error', 'Không thể xóa CLB đang có thành viên hoặc sự kiện!');
        }

        $club->delete();
        return redirect()->route('admin.clubs.index')->with('success', 'Đã xóa câu lạc bộ!');
    }
}