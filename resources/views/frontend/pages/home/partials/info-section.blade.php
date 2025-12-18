<section class="b-taglines area-bg area-bg_dark parallax">
	<div class="area-bg__inner">
		<div class="container">
			@php
			$infoSection = $homePage->activeSections->where('name', 'info')->first();
			@endphp
			<div class="row">
				<div class="col-sm-10 col-sm-offset-1">
					<div class="b-taglines__inner">
						<h2 class="b-taglines__title">{{ $infoSection->title }}</h2>
						<!-- <div class="b-taglines__text">We make
							your events smart & impactful
							by personalised event
							management services.</div> -->
					</div>
				</div>
			</div>
		</div>
	</div>
</section>