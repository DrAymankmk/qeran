<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $testimonials = Testimonial::with('translations')
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('pages.testimonials.index', compact('testimonials'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.testimonials.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TestimonialRequest $request)
    {
        $validated = $request->validated();
        
        // Extract translation data
        $enData = [
            'name' => $validated['en']['name'],
            'job' => $validated['en']['job'] ?? null,
            'message' => $validated['en']['message'],
        ];
        $arData = [
            'name' => $validated['ar']['name'],
            'job' => $validated['ar']['job'] ?? null,
            'message' => $validated['ar']['message'],
        ];
        
        // Remove translation data from main data
        unset($validated['en'], $validated['ar'], $validated['image']);
        
        $testimonial = Testimonial::create($validated);
        
        // Save translations
        $testimonial->translateOrNew('en')->name = $enData['name'];
        $testimonial->translateOrNew('en')->job = $enData['job'];
        $testimonial->translateOrNew('en')->message = $enData['message'];
        $testimonial->translateOrNew('ar')->name = $arData['name'];
        $testimonial->translateOrNew('ar')->job = $arData['job'];
        $testimonial->translateOrNew('ar')->message = $arData['message'];
        $testimonial->save();
        
        // Handle image upload
        if ($request->image) {
            storeImage([
                'value' => $request->image,
                'folderName' => Constant::TESTIMONIAL_IMAGE_FOLDER_NAME,
                'model' => $testimonial,
                'saveInDatabase' => true
            ]);
        }
        
        return redirect()->route('testimonials.index')->with('success', 'Testimonial created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\Http\Response
     */
    public function edit(Testimonial $testimonial)
    {
        $testimonial->load('translations');
        return view('pages.testimonials.edit', compact('testimonial'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\Http\Response
     */
    public function update(TestimonialRequest $request, Testimonial $testimonial)
    {
        $validated = $request->validated();
        
        // Extract translation data
        $enData = [
            'name' => $validated['en']['name'],
            'job' => $validated['en']['job'] ?? null,
            'message' => $validated['en']['message'],
        ];
        $arData = [
            'name' => $validated['ar']['name'],
            'job' => $validated['ar']['job'] ?? null,
            'message' => $validated['ar']['message'],
        ];
        
        // Remove translation data from main data
        unset($validated['en'], $validated['ar'], $validated['image']);
        
        $testimonial->update($validated);
        
        // Save translations
        $testimonial->translateOrNew('en')->name = $enData['name'];
        $testimonial->translateOrNew('en')->job = $enData['job'];
        $testimonial->translateOrNew('en')->message = $enData['message'];
        $testimonial->translateOrNew('ar')->name = $arData['name'];
        $testimonial->translateOrNew('ar')->job = $arData['job'];
        $testimonial->translateOrNew('ar')->message = $arData['message'];
        $testimonial->save();
        
        // Handle image upload
        if ($request->image) {
            if ($testimonial->hubFiles()->exists()) {
                deleteImage($testimonial->image(), $testimonial->hubFiles());
            }
            
            storeImage([
                'value' => $request->image,
                'folderName' => Constant::TESTIMONIAL_IMAGE_FOLDER_NAME,
                'model' => $testimonial,
                'saveInDatabase' => true
            ]);
        }
        
        return redirect()->route('testimonials.index')->with('success', 'Testimonial updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\Http\Response
     */
    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->hubFiles()->exists()) {
            deleteImage($testimonial->image(), $testimonial->hubFiles());
        }
        
        $testimonial->delete();
        
        return redirect()->route('testimonials.index')->with('success', 'Testimonial deleted successfully');
    }
}







