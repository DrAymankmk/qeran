@extends('frontend.layouts.app')

@section('content')
<div class="cms-page">
    <div class="container">
        <h1>{{ $page->title }}</h1>
        
        {{-- Display page-level links --}}
        @if($page->links->count() > 0)
            <div class="page-links mb-4">
                @foreach($page->links as $link)
                    <a href="{{ $link->url }}" 
                       target="{{ $link->target }}"
                       class="page-link btn btn-outline-primary me-2 mb-2"
                       rel="{{ $link->target === '_blank' ? 'noopener noreferrer' : '' }}">
                        @if($link->icon)
                            {!! $link->icon_html !!}
                        @endif
                        <span>{{ $link->name }}</span>
                    </a>
                @endforeach
            </div>
        @endif
        
        @foreach($page->activeSections as $section)
            <section class="cms-section cms-section-{{ $section->type }} mb-5">
                @if($section->title)
                    <h2>{{ $section->title }}</h2>
                @endif
                
                @if($section->subtitle)
                    <p class="subtitle text-muted">{{ $section->subtitle }}</p>
                @endif
                
                @if($section->description)
                    <div class="description mb-4">
                        {!! formatCmsContent($section->description) !!}
                    </div>
                @endif
                
                {{-- Display section-level links --}}
                @if($section->links->count() > 0)
                    <div class="section-links mb-4">
                        @foreach($section->links as $link)
                            <a href="{{ $link->url }}" 
                               target="{{ $link->target }}"
                               class="section-link btn btn-sm btn-outline-secondary me-2 mb-2"
                               rel="{{ $link->target === '_blank' ? 'noopener noreferrer' : '' }}">
                                @if($link->icon)
                                    {!! $link->icon_html !!}
                                @endif
                                <span>{{ $link->name }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
                
                <div class="cms-items row">
                    @foreach($section->activeItems as $item)
                        <div class="cms-item cms-item-{{ $item->type }} col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h3 class="card-title">
                                        @if($item->icon)
                                            <i class="{{ $item->icon }} me-2"></i>
                                        @endif
                                        {{ $item->title }}
                                    </h3>
                                    
                                    @if($item->sub_title)
                                        <h4 class="card-subtitle mb-2 text-muted">{{ $item->sub_title }}</h4>
                                    @endif
                                    
                                    @if($item->content)
                                        <div class="item-content card-text">
                                            {!! formatCmsContent($item->content) !!}
                                        </div>
                                    @endif
                                    
                                    @if($item->getMedia('images')->count() > 0)
                                        <div class="item-images mt-3">
                                            @foreach($item->getMedia('images') as $media)
                                                <img src="{{ $media->getUrl() }}" 
                                                     alt="{{ $media->getCustomProperty('alt_text', '') }}"
                                                     class="img-fluid mb-2"
                                                     loading="lazy">
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    @if($item->links->count() > 0)
                                        <div class="item-links mt-3">
                                            @foreach($item->links as $link)
                                                <a href="{{ $link->url }}" 
                                                   target="{{ $link->target }}"
                                                   class="item-link btn btn-sm btn-link"
                                                   rel="{{ $link->target === '_blank' ? 'noopener noreferrer' : '' }}">
                                                    @if($link->icon)
                                                        {!! $link->icon_html !!}
                                                    @endif
                                                    <span>{{ $link->name }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</div>
@endsection

