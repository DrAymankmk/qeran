<section class="b-services area-bg area-bg_dark area-bg_op_90 parallax">
	<div class="area-bg__inner">
		<div class="container">
			@php
			$servicesSection = $homePage->activeSections->where('name', 'services')->first();
			@endphp
			<div class="row" style="display:flex">
				<div class="col-md-5">
					<div class="ui-decor-1"><img
							src="{{ asset('frontend/assets/media/general/ui-decor-1.png') }}"
							alt="decor" /></div>
					<h2 class="ui-title-block">{{ $servicesSection->title }}</h2>
					<div class="ui-subtitle-block">{{ $servicesSection->subtitle }}</div>
					{!! formatCmsContent($servicesSection->description) !!}
				</div>
				<div class="col-md-7">
					<div class="bxslider">
						<section class="b-advantages-2 b-advantages-2_light">
							<i
								class="b-advantages-2__icon flaticon-people"></i>
							<div class="b-advantages-2__inner">
								<h3
									class="b-advantages-2__title ui-title-inner bg-primary_b">
									Wedding
									Events
								</h3>
								<div class="b-advantages-2__info">
									Sit amet
									consectetur
									elit sed
									lusm
									tempor
									incidant
									temdore ut
									labore
									dolore
									lorem
									ipsum
									dolor sit
									amet
									consectetur
									adipisicing
									elit sed
									do eiusmod
									tempor
									incididunt
									ut labore
									et dolore.
								</div>
							</div>
						</section>
						<!-- end .b-advantages-->
						<section class="b-advantages-2 b-advantages-2_light">
							<i class="b-advantages-2__icon flaticon-food"></i>
							<div class="b-advantages-2__inner">
								<h3
									class="b-advantages-2__title ui-title-inner bg-primary_b">
									Birthday
									Parties
								</h3>
								<div class="b-advantages-2__info">
									Sit amet
									consectetur
									elit sed
									lusm
									tempor
									incidant
									temdore ut
									labore
									dolore
									lorem
									ipsum
									dolor sit
									amet
									consectetur
									adipisicing
									elit sed
									do eiusmod
									tempor
									incididunt
									ut labore
									et dolore.
								</div>
							</div>
						</section>
						<!-- end .b-advantages-->
						<section class="b-advantages-2 b-advantages-2_light">
							<i
								class="b-advantages-2__icon flaticon-karaoke"></i>
							<div class="b-advantages-2__inner">
								<h3
									class="b-advantages-2__title ui-title-inner bg-primary_b">
									Corporate
									Seminars
								</h3>
								<div class="b-advantages-2__info">
									Sit amet
									consectetur
									elit sed
									lusm
									tempor
									incidant
									temdore ut
									labore
									dolore
									lorem
									ipsum
									dolor sit
									amet
									consectetur
									adipisicing
									elit sed
									do eiusmod
									tempor
									incididunt
									ut labore
									et dolore.
								</div>
							</div>
						</section>
						<!-- end .b-advantages-->
						<section class="b-advantages-2 b-advantages-2_light">
							<i
								class="b-advantages-2__icon flaticon-people"></i>
							<div class="b-advantages-2__inner">
								<h3
									class="b-advantages-2__title ui-title-inner bg-primary_b">
									Wedding
									Events
								</h3>
								<div class="b-advantages-2__info">
									Sit amet
									consectetur
									elit sed
									lusm
									tempor
									incidant
									temdore ut
									labore
									dolore
									lorem
									ipsum
									dolor sit
									amet
									consectetur
									adipisicing
									elit sed
									do eiusmod
									tempor
									incididunt
									ut labore
									et dolore.
								</div>
							</div>
						</section>
						<!-- end .b-advantages-->
						<section class="b-advantages-2 b-advantages-2_light">
							<i class="b-advantages-2__icon flaticon-food"></i>
							<div class="b-advantages-2__inner">
								<h3
									class="b-advantages-2__title ui-title-inner bg-primary_b">
									Birthday
									Parties
								</h3>
								<div class="b-advantages-2__info">
									Sit amet
									consectetur
									elit sed
									lusm
									tempor
									incidant
									temdore ut
									labore
									dolore
									lorem
									ipsum
									dolor sit
									amet
									consectetur
									adipisicing
									elit sed
									do eiusmod
									tempor
									incididunt
									ut labore
									et dolore.
								</div>
							</div>
						</section>
						<!-- end .b-advantages-->
						<section class="b-advantages-2 b-advantages-2_light">
							<i
								class="b-advantages-2__icon flaticon-karaoke"></i>
							<div class="b-advantages-2__inner">
								<h3
									class="b-advantages-2__title ui-title-inner bg-primary_b">
									Corporate
									Seminars
								</h3>
								<div class="b-advantages-2__info">
									Sit amet
									consectetur
									elit sed
									lusm
									tempor
									incidant
									temdore ut
									labore
									dolore
									lorem
									ipsum
									dolor sit
									amet
									consectetur
									adipisicing
									elit sed
									do eiusmod
									tempor
									incididunt
									ut labore
									et dolore.
								</div>
							</div>
						</section>
						<!-- end .b-advantages-->
					</div>
				</div>
			</div>
		</div>
	</div>
</section>