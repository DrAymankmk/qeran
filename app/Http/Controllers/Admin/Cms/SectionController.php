<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\CmsItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SectionController extends Controller
{
    public function index(CmsPage $page)
    {
        $sections = $page->sections()->with('translations')->orderBy('order')->get();
        return view('admin.cms.sections.index', compact('page', 'sections'));
    }

    public function create(CmsPage $page)
    {
        return view('admin.cms.sections.create', compact('page'));
    }

    public function store(Request $request, CmsPage $page)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'template' => 'nullable|string|max:255',
            'image_files' => 'nullable|array',
            'image_files.*' => 'nullable|image|max:5120', // 5MB
            'video_file' => 'nullable|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska|max:51200', // 50MB
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'en.title' => 'nullable|string|max:255',
            'ar.title' => 'nullable|string|max:255',
            'en.subtitle' => 'nullable|string|max:500',
            'ar.subtitle' => 'nullable|string|max:500',
            'en.description' => 'nullable|string',
            'ar.description' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.type' => 'nullable|string|max:255',
            'items.*.order' => 'nullable|integer',
            'items.*.is_active' => 'nullable|boolean',
            'items.*.en.title' => 'nullable|string|max:255',
            'items.*.ar.title' => 'nullable|string|max:255',
            'items.*.en.sub_title' => 'nullable|string|max:500',
            'items.*.ar.sub_title' => 'nullable|string|max:500',
            'items.*.en.content' => 'nullable|string',
            'items.*.ar.content' => 'nullable|string',
            'items.*.en.icon' => 'nullable|string|max:255',
            'items.*.ar.icon' => 'nullable|string|max:255',
            'items.*.images' => 'nullable|array',
            'items.*.images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'items.*.existing_images' => 'nullable|array',
            'items.*.existing_images.*' => 'nullable|exists:media,id',
        ]);

        $imageUrls = [];

        // Handle uploaded image files
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                if ($file->isValid()) {
                    $imagePath = $file->store('cms/sections/images', 'public');
                    $imageUrls[] = Storage::url($imagePath);
                }
            }
        }

        $videoUrl = null;

        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('cms/sections/videos', 'public');
            $videoUrl = Storage::url($videoPath);
        }

        $section = $page->sections()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'template' => $validated['template'] ?? null,
            'settings' => [
                'images' => $imageUrls,
                'video' => $videoUrl,
            ],
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $section->translateOrNew('en')->title = $validated['en']['title'] ?? null;
        $section->translateOrNew('en')->subtitle = $validated['en']['subtitle'] ?? null;
        $section->translateOrNew('en')->description = $validated['en']['description'] ?? null;
        $section->translateOrNew('ar')->title = $validated['ar']['title'] ?? null;
        $section->translateOrNew('ar')->subtitle = $validated['ar']['subtitle'] ?? null;
        $section->translateOrNew('ar')->description = $validated['ar']['description'] ?? null;
        $section->save();

        // Handle items creation
        if ($request->has('items')) {
            foreach ($request->items as $index => $itemData) {
                if (!empty($itemData['en']['title']) || !empty($itemData['ar']['title'])) {
                    $item = $section->items()->create([
                        'type' => $itemData['type'] ?? 'default',
                        'order' => $itemData['order'] ?? $index,
                        'is_active' => $itemData['is_active'] ?? true,
                    ]);

                    $item->translateOrNew('en')->title = $itemData['en']['title'] ?? '';
                    $item->translateOrNew('en')->sub_title = $itemData['en']['sub_title'] ?? null;
                    $item->translateOrNew('en')->content = $itemData['en']['content'] ?? null;
                    $item->translateOrNew('en')->icon = $itemData['en']['icon'] ?? null;
                    $item->translateOrNew('ar')->title = $itemData['ar']['title'] ?? '';
                    $item->translateOrNew('ar')->sub_title = $itemData['ar']['sub_title'] ?? null;
                    $item->translateOrNew('ar')->content = $itemData['ar']['content'] ?? null;
                    $item->translateOrNew('ar')->icon = $itemData['ar']['icon'] ?? null;
                    $item->save();

                    // Handle item images using Spatie Media Library
                    if (isset($itemData['images']) && is_array($itemData['images'])) {
                        foreach ($itemData['images'] as $imageIndex => $image) {
                            if ($request->hasFile("items.{$index}.images.{$imageIndex}")) {
                                $item->addMediaFromRequest("items.{$index}.images.{$imageIndex}")
                                    ->withCustomProperties([
                                        'order' => $imageIndex
                                    ])
                                    ->toMediaCollection('images');
                            }
                        }
                    }
                }
            }
        }

        return redirect()->route('cms.sections.index', $page)
            ->with('success', 'Section created successfully');
    }

    public function edit(CmsPage $page, CmsSection $section)
    {
        $section->load(['translations', 'items.translations', 'items.media']);
        return view('admin.cms.sections.edit', compact('page', 'section'));
    }

    public function update(Request $request, CmsPage $page, CmsSection $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'template' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'nullable|string|max:500', // existing images from hidden inputs
            'image_files' => 'nullable|array',
            'image_files.*' => 'nullable|image|max:5120', // 5MB
            'video_file' => 'nullable|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska|max:51200', // 50MB
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'en.title' => 'nullable|string|max:255',
            'ar.title' => 'nullable|string|max:255',
            'en.subtitle' => 'nullable|string|max:500',
            'ar.subtitle' => 'nullable|string|max:500',
            'en.description' => 'nullable|string',
            'ar.description' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|exists:cms_items,id',
            'items.*.type' => 'nullable|string|max:255',
            'items.*.order' => 'nullable|integer',
            'items.*.is_active' => 'nullable|boolean',
            'items.*.en.title' => 'nullable|string|max:255',
            'items.*.ar.title' => 'nullable|string|max:255',
            'items.*.en.sub_title' => 'nullable|string|max:500',
            'items.*.ar.sub_title' => 'nullable|string|max:500',
            'items.*.en.content' => 'nullable|string',
            'items.*.ar.content' => 'nullable|string',
            'items.*.en.icon' => 'nullable|string|max:255',
            'items.*.ar.icon' => 'nullable|string|max:255',
            'items.*.images' => 'nullable|array',
            'items.*.images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'items.*.existing_images' => 'nullable|array',
            'items.*.existing_images.*' => 'nullable|exists:media,id',
            'existing_items' => 'nullable|array',
            'existing_items.*' => 'exists:cms_items,id',
        ]);

        $settings = $section->settings ?? [];

        // Get existing images from hidden inputs (preserved when not removed)
        $imageUrls = $validated['images'] ?? ($settings['images'] ?? []);

        // Handle new uploaded image files
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                if ($file->isValid()) {
                    $imagePath = $file->store('cms/sections/images', 'public');
                    $imageUrls[] = Storage::url($imagePath);
                }
            }
        }

        $settings['images'] = $imageUrls;

        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('cms/sections/videos', 'public');
            $settings['video'] = Storage::url($videoPath);
        } else {
            // Preserve existing video if no new upload
            $settings['video'] = $settings['video'] ?? null;
        }

        $section->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'template' => $validated['template'] ?? null,
            'settings' => $settings,
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $section->translateOrNew('en')->title = $validated['en']['title'] ?? null;
        $section->translateOrNew('en')->subtitle = $validated['en']['subtitle'] ?? null;
        $section->translateOrNew('en')->description = $validated['en']['description'] ?? null;
        $section->translateOrNew('ar')->title = $validated['ar']['title'] ?? null;
        $section->translateOrNew('ar')->subtitle = $validated['ar']['subtitle'] ?? null;
        $section->translateOrNew('ar')->description = $validated['ar']['description'] ?? null;
        $section->save();

        // Handle items update/create
        $existingItemIds = $request->input('existing_items', []);

        // Delete removed items
        $section->items()->whereNotIn('id', $existingItemIds)->delete();

        // Update or create items
        if ($request->has('items')) {
            foreach ($request->items as $index => $itemData) {
                if (isset($itemData['id']) && in_array($itemData['id'], $existingItemIds)) {
                    // Update existing item
                    $item = CmsItem::find($itemData['id']);
                    if ($item && $item->section_id == $section->id) {
                        $item->update([
                            'type' => $itemData['type'] ?? 'default',
                            'order' => $itemData['order'] ?? $index,
                            'is_active' => $itemData['is_active'] ?? true,
                        ]);

                        $item->translateOrNew('en')->title = $itemData['en']['title'] ?? '';
                        $item->translateOrNew('en')->sub_title = $itemData['en']['sub_title'] ?? null;
                        $item->translateOrNew('en')->content = $itemData['en']['content'] ?? null;
                        $item->translateOrNew('en')->icon = $itemData['en']['icon'] ?? null;
                        $item->translateOrNew('ar')->title = $itemData['ar']['title'] ?? '';
                        $item->translateOrNew('ar')->sub_title = $itemData['ar']['sub_title'] ?? null;
                        $item->translateOrNew('ar')->content = $itemData['ar']['content'] ?? null;
                        $item->translateOrNew('ar')->icon = $itemData['ar']['icon'] ?? null;
                        $item->save();

                        // Handle item images - delete removed images
                        $existingImageIds = $itemData['existing_images'] ?? [];
                        $item->getMedia('images')
                            ->whereNotIn('id', $existingImageIds)
                            ->each(function ($media) {
                                $media->delete();
                            });

                        // Handle new item images
                        if (isset($itemData['images']) && is_array($itemData['images'])) {
                            $existingMediaCount = $item->getMedia('images')->count();
                            foreach ($itemData['images'] as $imageIndex => $image) {
                                if ($request->hasFile("items.{$index}.images.{$imageIndex}")) {
                                    $item->addMediaFromRequest("items.{$index}.images.{$imageIndex}")
                                        ->withCustomProperties([
                                            'order' => $existingMediaCount + $imageIndex
                                        ])
                                        ->toMediaCollection('images');
                                }
                            }
                        }
                    }
                } else {
                    // Create new item
                    if (!empty($itemData['en']['title']) || !empty($itemData['ar']['title'])) {
                        $item = $section->items()->create([
                            'type' => $itemData['type'] ?? 'default',
                            'order' => $itemData['order'] ?? $index,
                            'is_active' => $itemData['is_active'] ?? true,
                        ]);

                        $item->translateOrNew('en')->title = $itemData['en']['title'] ?? '';
                        $item->translateOrNew('en')->sub_title = $itemData['en']['sub_title'] ?? null;
                        $item->translateOrNew('en')->content = $itemData['en']['content'] ?? null;
                        $item->translateOrNew('en')->icon = $itemData['en']['icon'] ?? null;
                        $item->translateOrNew('ar')->title = $itemData['ar']['title'] ?? '';
                        $item->translateOrNew('ar')->sub_title = $itemData['ar']['sub_title'] ?? null;
                        $item->translateOrNew('ar')->content = $itemData['ar']['content'] ?? null;
                        $item->translateOrNew('ar')->icon = $itemData['ar']['icon'] ?? null;
                        $item->save();

                        // Handle new item images
                        if (isset($itemData['images']) && is_array($itemData['images'])) {
                            foreach ($itemData['images'] as $imageIndex => $image) {
                                if ($request->hasFile("items.{$index}.images.{$imageIndex}")) {
                                    $item->addMediaFromRequest("items.{$index}.images.{$imageIndex}")
                                        ->withCustomProperties([
                                            'order' => $imageIndex
                                        ])
                                        ->toMediaCollection('images');
                                }
                            }
                        }
                    }
                }
            }
        }

        return redirect()->route('cms.sections.index', $page)
            ->with('success', 'Section updated successfully');
    }

    public function destroy(CmsPage $page, CmsSection $section)
    {
        $section->delete();
        return redirect()->route('cms.sections.index', $page)
            ->with('success', 'Section deleted successfully');
    }
}

