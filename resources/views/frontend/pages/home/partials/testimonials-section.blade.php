@if($testimonials && $testimonials->count() > 0)
<div class="section-default">
	<div class="container">
		<div class="row">
			<div class="col-sm-11">
				<div data-pagination="true" data-navigation="false" data-single-item="true"
					data-auto-play="7000" data-transition-style="fade"
					data-main-text-animation="true" data-after-init-delay="3000"
					data-after-move-delay="1000" data-stop-on-hover="true"
					class="owl-carousel owl-theme owl-theme_mod-a enable-owl-carousel">
					@foreach($testimonials as $testimonial)
					<blockquote class="b-blockquote b-blockquote-3">
						<p>{{ $testimonial->message }}</p>
						<footer class="b-blockquote__footer">
							@if($testimonial->image())
							<div class="b-blockquote__face">
								<img src="{{ $testimonial->image() }}"
									alt="{{ $testimonial->name }}"
									class="img-responsive" />
							</div>
							@else
							<div class="b-blockquote__face">
								<img src="{{ asset('frontend/assets/media/components/b-blockquote/face-1.jpg') }}"
									alt="{{ $testimonial->name }}"
									class="img-responsive" />
							</div>
							@endif
							<cite title="{{ $testimonial->name }}"
								class="b-blockquote__cite">
								<span
									class="b-blockquote__author">{{ $testimonial->name }}</span>
								@if($testimonial->job)
								<span
									class="b-blockquote__category">{{ $testimonial->job }}</span>
								@endif
								@if($testimonial->rating)
								<div class="testimonial-rating"
									style="margin-top: 5px;">
									@for($i = 1; $i <= 5; $i++) <i
										class="fas fa-star {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}"
										style="font-size: 12px;">
										</i>
										@endfor
								</div>
								@endif
							</cite>
						</footer>
					</blockquote>
					<!-- end .b-blockquote-->
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>
@endif

