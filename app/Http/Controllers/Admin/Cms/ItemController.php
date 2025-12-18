<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\CmsItem;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ItemController extends Controller
{
    public function index(CmsPage $page, CmsSection $section)
    {
        $items = $section->items()
            ->with(['translations', 'media'])
            ->orderBy('order')
            ->get();

        return view('admin.cms.items.index', compact('page', 'section', 'items'));
    }

    public function create(CmsPage $page, CmsSection $section)
    {
        return view('admin.cms.items.create', compact('page', 'section'));
    }

    public function store(Request $request, CmsPage $page, CmsSection $section)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'en.title' => 'required|string|max:255',
            'ar.title' => 'required|string|max:255',
            'en.sub_title' => 'nullable|string|max:500',
            'ar.sub_title' => 'nullable|string|max:500',
            'en.content' => 'nullable|string',
            'ar.content' => 'nullable|string',
            'en.icon' => 'nullable|string|max:255',
            'ar.icon' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_alt.*' => 'nullable|string|max:255',
            'image_name.*' => 'nullable|string|max:255',
        ]);

        $item = $section->items()->create([
            'type' => $validated['type'],
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Save translations
        $item->translateOrNew('en')->title = $validated['en']['title'];
        $item->translateOrNew('en')->sub_title = $validated['en']['sub_title'] ?? null;
        $item->translateOrNew('en')->content = $validated['en']['content'] ?? null;
        $item->translateOrNew('en')->icon = $validated['en']['icon'] ?? null;
        $item->translateOrNew('ar')->title = $validated['ar']['title'];
        $item->translateOrNew('ar')->sub_title = $validated['ar']['sub_title'] ?? null;
        $item->translateOrNew('ar')->content = $validated['ar']['content'] ?? null;
        $item->translateOrNew('ar')->icon = $validated['ar']['icon'] ?? null;
        $item->save();

        // Handle images using Spatie Media Library
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $media = $item->addMediaFromRequest("images.{$index}")
                    ->usingName($request->input("image_name_{$index}", $image->getClientOriginalName()))
                    ->withCustomProperties([
                        'alt_text' => $request->input("image_alt_{$index}"),
                        'order' => $index
                    ])
                    ->toMediaCollection('images');
            }
        }

        return redirect()->route('cms.items.index', [$page, $section])
            ->with('success', 'Item created successfully');
    }

    public function edit(CmsPage $page, CmsSection $section, CmsItem $item)
    {
        $item->load(['translations', 'media']);
        return view('admin.cms.items.edit', compact('page', 'section', 'item'));
    }

    public function update(Request $request, CmsPage $page, CmsSection $section, CmsItem $item)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'en.title' => 'required|string|max:255',
            'ar.title' => 'required|string|max:255',
            'en.sub_title' => 'nullable|string|max:500',
            'ar.sub_title' => 'nullable|string|max:500',
            'en.content' => 'nullable|string',
            'ar.content' => 'nullable|string',
            'en.icon' => 'nullable|string|max:255',
            'ar.icon' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_alt.*' => 'nullable|string|max:255',
            'image_name.*' => 'nullable|string|max:255',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'exists:media,id',
            'existing_image_alt.*' => 'nullable|string|max:255',
        ]);

        $item->update([
            'type' => $validated['type'],
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Update translations
        $item->translateOrNew('en')->title = $validated['en']['title'];
        $item->translateOrNew('en')->sub_title = $validated['en']['sub_title'] ?? null;
        $item->translateOrNew('en')->content = $validated['en']['content'] ?? null;
        $item->translateOrNew('en')->icon = $validated['en']['icon'] ?? null;
        $item->translateOrNew('ar')->title = $validated['ar']['title'];
        $item->translateOrNew('ar')->sub_title = $validated['ar']['sub_title'] ?? null;
        $item->translateOrNew('ar')->content = $validated['ar']['content'] ?? null;
        $item->translateOrNew('ar')->icon = $validated['ar']['icon'] ?? null;
        $item->save();

        // Handle new images using Spatie Media Library
        if ($request->hasFile('images')) {
            $existingMediaCount = $item->getMedia('images')->count();
            foreach ($request->file('images') as $index => $image) {
                $media = $item->addMediaFromRequest("images.{$index}")
                    ->usingName($request->input("image_name_{$index}", $image->getClientOriginalName()))
                    ->withCustomProperties([
                        'alt_text' => $request->input("image_alt_{$index}"),
                        'order' => $existingMediaCount + $index
                    ])
                    ->toMediaCollection('images');
            }
        }

        // Update existing images order and alt text
        if ($request->has('existing_images')) {
            foreach ($request->input('existing_images') as $index => $mediaId) {
                $media = Media::find($mediaId);
                if ($media && $media->model_id == $item->id) {
                    $media->setCustomProperty('order', $index);
                    $media->setCustomProperty('alt_text', $request->input("existing_image_alt_{$mediaId}"));
                    $media->save();
                }
            }
        }

        // Delete removed images
        $existingImageIds = $request->input('existing_images', []);
        $item->getMedia('images')
            ->whereNotIn('id', $existingImageIds)
            ->each(function ($media) {
                $media->delete();
            });

        return redirect()->route('cms.items.index', [$page, $section])
            ->with('success', 'Item updated successfully');
    }

    public function destroy(CmsPage $page, CmsSection $section, CmsItem $item)
    {
        // Delete all media (images are automatically deleted by Spatie)
        $item->clearMediaCollection('images');

        $item->delete();
        return redirect()->route('cms.items.index', [$page, $section])
            ->with('success', 'Item deleted successfully');
    }

    public function deleteImage(Media $media)
    {
        // Verify the media belongs to a CMS item
        if ($media->model_type !== CmsItem::class) {
            return response()->json(['success' => false, 'message' => 'Invalid media'], 403);
        }

        $media->delete();
        return response()->json(['success' => true]);
    }
}
