@extends ('layouts.consul.main')

@section('page-css')

@endsection


@section('main-content')

<!-- Success Card -->
<div class="card success-card">
    <div class="card-body">
        <div class="success-cont">
            <i class="fas fa-check"></i>
            <h3>{{ __('Payment Done Successfully!') }}</h3>
            <p>
                @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>	
                        <strong>{{ $message }}</strong>
                </div>
                @endif
            </p>
            <a href="{{url('consul/payments')}}" class="btn btn-primary view-inv-btn">{{__('View Transactions') }}</a>
        </div>
    </div>
</div>
<!-- /Success Card -->

@endsection


@section('page-js')

@endsection


@section('bottom-js')

@endsection

