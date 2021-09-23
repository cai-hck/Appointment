
@extends ('layouts.consul.main')

@section('page-css')
@endsection

@section('main-content')

<div class="appointments">
    
    
    <div class="card">
        <form method="post" action="{{url('consul/news/save')}}" enctype="multipart/form-data">
        @csrf
        <div class="card-header">            
            <div class="col-auto text-right float-right ml-auto">
                <button  class="btn btn-primary "><i class="fa fa-plus mr-1"></i>{{__('Add News') }}</button>
            </div>
            <h4 class="card-title">{{__('Add News') }}</h4>
        </div>
        <div class="card-body">
            @include('alert')
            <div class="form-row row">
                <div class="col-md-6">
                <div class="form-group">
                    <label>{{__('Title')}} (English) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title_en" value="" required>
                </div>
                </div>
                <div class="col-md-6">
                <div class="form-group">
                    <label>{{__('Title')}} (Arabic) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title_ar" value="" required>
                </div>
                </div>                
                <div class="col-md-12">
                <div class="form-group">
                    <label>{{__('Link')}} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="link" value="" required>
                </div>
                </div>
                <div class="col-md-12">
                    <label>{{__('Upload Image or Image Link')}} <span class="text-danger">*</span></label>
                    <input type="file" name="file" class="form-control"/>
                    <input type="text" name="f_link" class="form-control mt-3" />
                </div>                
            </div>
        </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">            
            <h4 class="card-title">{{__('All News') }}</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="datatable table table-stripped">
                    <thead>
                        <tr>
                            <th width="20%">{{__('Date') }}</th>
                            <th>{{__('Title') }}</th>
                            <th>{{__('Image') }}</th>
                            <th>{{__('Link') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($news as $new)
                            <tr>
                                <td>{{date('Y-m-d H:i:s', strtotime($new->created_at))}}</td>
                                <td>{{$new->title_en}}<br>{{$new->title_ar}}</td>
                                <td>
                                @if ($new->file!=''||$new->f_link!='')
                                    @if ($new->file!='')
                                    <img src="{{asset(json_decode($new->file)->s)}}"/>
                                    @else
                                    <img src="{{$new->f_link}}"/>
                                    @endif
                                @endif
                                </td>
                                <td>{{$new->link}}</td>
                                <td class="text-right">
                                <div class="table-action">                                
                                    <a href="javascript:void(0);" class="btn btn-sm bg-danger-light del-section" data-toggle="modal" data-target="#delete_modal" data-sid="{{$new->id}}">
                                        <i class="fas fa-times"></i> Remove
                                    </a>
                                </div>
                                </td>
                            </tr>
                        @endforeach                                            
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection


@section('page-js')
<script>
    $(document).ready(function(){
        $('.del-section').click(function(){
            $('#del-form').attr('action',"{{url('consul/deletenews')}}");
            $('#del-form').find('#u_id').val($(this).attr('data-sid'));
        })
    });
</script>
@endsection


@section('bottom-js')
@endsection
