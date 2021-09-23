@extends ('layouts.client.main')

@section('page-css')
@endsection


@section('main-content')
<!-- Home Banner -->
<section class="section section-search">
	<div class="container-fluid">
		<div class="banner-wrapper">
			<div class="banner-header text-center">
				<h1>{{__('Choose a mission , make an Appointment') }}</h1>
				<p>{{__('Discover the best consultant, secretary, sections.')}}</p>
				
				<a href="{{url('/bookings')}}" class="btn book-btn1 btn-rounded px-5 py-3 mt-3" tabindex="0">{{__('Book Now')}}</a>
			</div>                        										
		</div>
	</div>
</section>
<!-- /Home Banner -->

<section class="section home-tile-section">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-9 m-auto">
				<div class="section-header text-center">
					<h2>{{__('What are you looking for?')}}</h2>
				</div>
				<div class="row">
					@foreach ($missions as $one)
					<div class="col-lg-4 mb-3">
						<div class="card text-center doctor-book-card">
							<img src="{{ asset($one->cover_image) }}"
								alt="{{$one->name}}" class="img-fluid">
							<div class="doctor-book-card-content tile-card-content-1">
								<div>
									@if (app()->getLocale()=='en')
									<h3 class="card-title mb-0">{{$one->name}}</h3>
									@else
									<h3 class="card-title mb-0">{{$one->name_ar}}</h3>
									@endif
									<a href="{{url('booking/'.base64_encode($one->id))}}"
									class="btn book-btn1 btn-rounded px-3 py-2 mt-3" tabindex="0">{{__('Book Now')}}</a>
								</div>
							</div>
						</div>
					</div>
					@endforeach								
				</div>
			</div>
		</div>
	</div>
</section>							
@endsection


@section('page-js')
@endsection


@section('bottom-js')
@endsection
