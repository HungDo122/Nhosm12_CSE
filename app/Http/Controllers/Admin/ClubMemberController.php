<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubMember;
use Illuminate\Http\Request;

class ClubMemberController extends Controller
{
    // Thêm thành viên vào CLB
    public function store(Request $request, Club $club)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:leader,member',
        ]);

        $exists = ClubMember::where('club_id', $club->id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Thành viên này đã có trong câu lạc bộ!');
        }

        ClubMember::create([
            'club_id' => $club->id,
            'user_id' => $request->user_id,
            'role' => $request->role,
            'is_manager' => $request->role === 'leader',
        ]);

        return redirect()->back()->with('success', 'Đã thêm thành viên vào câu lạc bộ!');
    }

    // Cập nhật vai trò thành viên trong CLB
    public function update(Request $request, Club $club, ClubMember $member)
    {
        $request->validate([
            'role' => 'required|in:leader,member',
        ]);

        $member->update([
            'role' => $request->role,
            'is_manager' => $request->role === 'leader',
        ]);

        return redirect()->back()->with('success', 'Đã cập nhật vai trò thành viên!');
    }

    // Rời / Xóa thành viên khỏi CLB
    public function destroy(Club $club, ClubMember $member)
    {
        $member->delete();
        return redirect()->back()->with('success', 'Đã xóa thành viên khỏi câu lạc bộ!');
    }
}
