
@extends ('layouts.secret.main')

@section('page-css')
@endsection


@section('main-content')

<div class="appointments">
    
    <div class="card">
        <div class="card-header">
            
            <div class="col-auto text-right float-right ml-auto">
                <a href="{{url('secret/sections/add')}}" class="btn btn-primary "><i class="fa fa-plus mr-1"></i>{{__('Add Section') }}</a>
            </div>
            <h4 class="card-title">{{__('All Sections') }}</h4>
        </div>
        <div class="card-body">

            @include ('alert')
            <div class="table-responsive">
                <table class="datatable table table-stripped">
                    <thead>
                        <tr>
                            <th width="50%">{{__('Name') }}</th>
                            <th>{{__('Created By') }}</th>
                            <th>{{__('Creator') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sections as $one)
                        <tr>
                            <td>     
                                @if (app()->getLocale() == 'en')                                                       
                                    {{$one->en_name}}
                                @else
                                    {{$one->ar_name}}
                                @endif
                            </td>
                            <td>
                                @if ($one->role_by == 'consul')
                                <a href="javascript:void(0);" class="btn btn-sm bg-success-light">
                                    {{__('Consultant')}}
                                </a> 
                                @else
                                <a href="javascript:void(0);" class="btn btn-sm bg-warning-light">
                                    {{__('Secretary')}}
                                </a> 
                                @endif
                            </td>                                                         
                            <td>
                                <?php 
                                    $creator = DB::table('user_infos')->where('user_id', $one->creator)->get()->first();
                                ?>
                                <h2 class="table-avatar">
                                    <a href="" class="avatar avatar-sm mr-2">
                                        <img class="avatar-img rounded-circle" 
                                        src="{{asset(json_decode($creator->photo)->s)}}" alt="">
                                    </a>
                                    <a href="">{{$creator->fname. ' ' .$creator->lname}}<span></span></a>
                                </h2>
                            </td>   
                            <td class="text-right">
                                <div class="table-action">
                                    <a href="{{url('secret/sedit/'. $one->id)}}" class="btn btn-sm bg-info-light">
                                        <i class="far fa-eye"></i> View
                                    </a>																																							
                                    <a href="javascript:void(0);" class="btn btn-sm bg-danger-light del-section" data-toggle="modal" data-target="#delete_modal" data-sid="{{$one->id}}">
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
            $('#del-form').attr('action',"{{url('secret/deletesection')}}");
            $('#del-form').find('#u_id').val($(this).attr('data-sid'));
        })
    })
</script>
@endsection


@section('bottom-js')
@endsection
