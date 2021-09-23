@extends ('layouts.admin.main')

@section('page-css')
<link rel="stylesheet" href="{{ asset('client/assets/summernote/summernote-bs4.min.css') }}" />
@endsection


@section('main-content')


<div class="page-wrapper">
			
    <div class="content container-fluid">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item active">Privacy Policy</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->        
        <div class="row">
            <div class="col-md-12">
                @include ('alert')
            </div> 
            <div class="col-md-6">
            <form action="{{url('admin/extrapolicy/en')}}" method="post">
                @csrf
              <div class="col-md-12">
                <div class="submit-section submit-btn-bottom text-right">
                    <button type="submit" class="btn btn-primary">Save Content</button>
                </div>                 
                </div>            
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{__('Content')}} ({{__('English')}})<span class="text-danger">*</span></label>
                        <textarea id="en_about"  type="text" class="form-control" rows="15" required name="en_about">{{$setting['en_policy']!=false?$setting['en_policy']:''}}</textarea>
                    </div>
                </div>
            </form>
            </div>
            <div class="col-md-6">
            <form action="{{url('admin/extrapolicy/ar')}}" method="post">
                @csrf
              <div class="col-md-12">
                <div class="submit-section submit-btn-bottom text-right">
                    <button type="submit" class="btn btn-primary">Save Content</button>
                </div>                 
                </div>            
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{__('Content')}} ({{__('Arabic')}})<span class="text-danger">*</span></label>
                        <textarea id="ar_about"  type="text" class="form-control" rows="15" required name="ar_about">{{$setting['ar_policy']!=false?$setting['ar_policy']:''}}</textarea>
                    </div>
                </div>
            </form>
            </div>  

        </div>
    </div>
</div>        

@endsection


@section('page-js')
<script src="{{ asset('client/assets/summernote/summernote-bs4.min.js') }}"></script>
<script>
    $('#en_about').summernote({height:300});
    $('#ar_about').summernote({height:300});
</script>
@endsection


@section('bottom-js')
@endsection
