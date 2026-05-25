<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WasteCategory;
use Illuminate\Http\Request;

class WasteCategoryController extends Controller
{
    public function index()
    {
        $categories = WasteCategory::latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string',
            'color'       => 'required|string',
            'description' => 'nullable|string',
        ]);

        WasteCategory::create([
            ...$validated,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(WasteCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, WasteCategory $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string',
            'color'       => 'required|string',
            'description' => 'nullable|string',
        ]);

        $category->update([
            ...$validated,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(WasteCategory $category)
    {
        if ($category->pickupRequests()->count() > 0) {
            return back()->with('error', 'Cannot delete category because it has pickup requests associated with it.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
