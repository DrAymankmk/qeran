<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = CmsPage::with('translations')
            ->orderBy('order')
            ->paginate(15);
        
        return view('admin.cms.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.cms.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|unique:cms_pages,slug|regex:/^[a-z0-9-]+$/',
            'name' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'en.title' => 'required|string|max:255',
            'ar.title' => 'required|string|max:255',
            'en.meta_description' => 'nullable|string|max:500',
            'ar.meta_description' => 'nullable|string|max:500',
        ]);

        $page = CmsPage::create([
            'slug' => $validated['slug'],
            'name' => $validated['name'],
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $page->translateOrNew('en')->title = $validated['en']['title'];
        $page->translateOrNew('en')->meta_description = $validated['en']['meta_description'] ?? null;
        $page->translateOrNew('ar')->title = $validated['ar']['title'];
        $page->translateOrNew('ar')->meta_description = $validated['ar']['meta_description'] ?? null;
        $page->save();

        return redirect()->route('cms.pages.index')
            ->with('success', 'Page created successfully');
    }

    public function edit(CmsPage $page)
    {
        $page->load('translations');
        return view('admin.cms.pages.edit', compact('page'));
    }

    public function update(Request $request, CmsPage $page)
    {
        $validated = $request->validate([
            'slug' => 'required|unique:cms_pages,slug,' . $page->id . '|regex:/^[a-z0-9-]+$/',
            'name' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'en.title' => 'required|string|max:255',
            'ar.title' => 'required|string|max:255',
            'en.meta_description' => 'nullable|string|max:500',
            'ar.meta_description' => 'nullable|string|max:500',
        ]);

        $page->update([
            'slug' => $validated['slug'],
            'name' => $validated['name'],
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $page->translateOrNew('en')->title = $validated['en']['title'];
        $page->translateOrNew('en')->meta_description = $validated['en']['meta_description'] ?? null;
        $page->translateOrNew('ar')->title = $validated['ar']['title'];
        $page->translateOrNew('ar')->meta_description = $validated['ar']['meta_description'] ?? null;
        $page->save();

        return redirect()->route('cms.pages.index')
            ->with('success', 'Page updated successfully');
    }

    public function destroy(CmsPage $page)
    {
        $page->delete();
        return redirect()->route('cms.pages.index')
            ->with('success', 'Page deleted successfully');
    }

    public function reorder(Request $request)
    {
        foreach ($request->order as $index => $id) {
            CmsPage::where('id', $id)->update(['order' => $index]);
        }
        return response()->json(['success' => true]);
    }
}

