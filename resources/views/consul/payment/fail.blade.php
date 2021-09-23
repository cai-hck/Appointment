@extends ('layouts.consul.main')

@section('page-css')

@endsection


@section('main-content')

<!-- Success Card -->
<div class="card success-card">
    <div class="card-body">
        <div class="success-cont">
            <i class="fa fa-info bg-white text-danger border-danger"></i>
            <h3>{{__('Transactions failed!') }}</h3>
            <p>
                @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>	
                        <strong>{{ $message }}</strong>
                </div>
                @endif
            </p>
            <a href="{{url('consul/payments')}}" class="btn btn-danger view-inv-btn">{{ __('Go to Payment page') }}</a>
        </div>
    </div>
</div>
<!-- /Success Card -->

@endsection


@section('page-js')

@endsection


@section('bottom-js')

@endsection

