<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventCategoryController extends Controller
{
    public function index()
    {
        $categories = EventCategory::withCount('events')->latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:event_categories,name',
            'description' => 'nullable|string',
        ]);

        EventCategory::create($request->only(['name', 'description']));

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục sự kiện thành công!');
    }

    public function edit(EventCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, EventCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:event_categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($request->only(['name', 'description']));

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(EventCategory $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Đã xóa danh mục sự kiện!');
    }
}
