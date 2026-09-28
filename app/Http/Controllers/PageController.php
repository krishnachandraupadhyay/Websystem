<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the pages ordered by display order.
     */
    public function index()
    {
        $pages = Page::orderBy('order', 'asc')->latest()->get();
        return view('superadmin.pages.addpages', compact('pages'));
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'page_name'   => 'required|string|max:255|unique:pages,page_name',
            'title'       => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:pages,slug',
            'status'      => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ], [
            'page_name.unique' => 'Page is already exist.',
            'slug.unique'      => 'Page is already exist.',
        ]);

        // Auto-assign next order sequence
        $maxOrder = Page::max('order') ?? 0;
        $validated['order'] = $maxOrder + 1;

        Page::create($validated);

        return redirect()->route('superadmin.pages.addpages')->with('success', 'Page "' . $validated['page_name'] . '" created successfully!');
    }

    /**
     * Update the specified page in storage.
     */
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'page_name'   => 'required|string|max:255|unique:pages,page_name,' . $page->id,
            'title'       => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'status'      => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ], [
            'page_name.unique' => 'Page is already exist.',
            'slug.unique'      => 'Page is already exist.',
        ]);

        $page->update($validated);

        return redirect()->route('superadmin.pages.addpages')->with('success', 'Page "' . $page->page_name . '" updated successfully!');
    }

    /**
     * Update the display order of the specified page.
     */
    public function updateOrder(Request $request, Page $page)
    {
        $validated = $request->validate([
            'order' => 'required|integer|min:0',
        ]);

        $page->update(['order' => $validated['order']]);

        return redirect()->route('superadmin.pages.addpages')->with('success', 'Order for page "' . $page->page_name . '" updated to #' . $validated['order'] . '!');
    }

    /**
     * Remove the specified page from storage.
     */
    public function destroy(Page $page)
    {
        $pageName = $page->page_name;
        $page->delete();

        return redirect()->route('superadmin.pages.addpages')->with('success', 'Page "' . $pageName . '" deleted successfully!');
    }

    /**
     * Toggle the status of the specified page.
     */
    public function toggleStatus(Page $page)
    {
        $newStatus = ($page->status === 'active') ? 'inactive' : 'active';
        $page->update(['status' => $newStatus]);

        $label = ucfirst($newStatus);
        return back()->with('success', 'Page "' . $page->page_name . '" status changed to ' . $label . ' successfully!');
    }
}
