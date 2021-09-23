@extends ('layouts.client.main')

@section('page-css')
<style>
    .blog-content.full {
        height:100% !important;
    }
</style>
@endsection


@section('main-content')

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{__('Booking')}} - {{__('Choose a Section')}} </h2>
            </div>
        </div>
    </div>
</div>
<!-- /Breadcrumb -->

<!-- Page Content -->
<div class="content">
    <div class="container">
    
        <div class="row">
        
            <div class="col-lg-8 col-md-12">
            @foreach ($sections as $one)
                <!-- Blog Post -->
                <div class="blog">
                    <h3 class="blog-title"><a href="javascript:void(0)">
                    @if (app()->getLocale() == 'en')
                        {{$one->en_name}}
                    @else
                        {{$one->ar_name}}
                    @endif
                    </a></h3>
                    <div class="blog-info clearfix" data-sid="{{$one->id}}">                       
                        <div class="blog-content" data-sid="{{$one->id}}" style="height:120px;overflow-y:hidden">
                            <?php echo app()->getLocale() == 'en'? $one->en_about:$one->ar_about ?>
                        </div>
                        <div class="divider mt-3" style="border:1px solid grey"></div>
                        <div class="post-left mt-3">
                            <ul>                                
                                <!--
                                <li>
                                    <div class="post-author">
                                        <a href="javscript:void(0)">
                                            <?php 
                                            $info = DB::table('user_infos')->where('user_id', $one->creator)->get()->first();
                                            ?>
                                            <img src="{{asset(json_decode($info->photo)->s)}}" alt="Creator">
                                            <span>{{$info->fname. ' ' .$info->lname}}</span>
                                        </a>
                                    </div>
                                </li>
                                <li><i class="far fa-clock"></i>{{ \Carbon\Carbon::parse($one->created_at)->toDateString() }}</li>
                                -->
                                <li><i class="fa fa-eye"></i><a class="open-content" data-sid="{{$one->id}}" href="javascript:void(0)">{{__('Read More') }}</a></li> 
                            </ul>
                            </div>
                        </div>                        
                        <form class="blog-action" style="display:none" data-sid="{{$one->id}}" action="{{url('booking/'.base64_encode($mission->id).'/appointment/'.base64_encode($one->id) ) }}">
                            <div class="form-group mt-3" style="display: flex;justify-content: space-between;">
                                <div class="">
                                    <label><input type="checkbox"  required/> {{__('Agree')}}, {{__('I understand') }}</label>
                                </div>                        
                                <button class="btn btn-primary submit-btn btn-sm text-right">{{__('Next Step')}}</button>
                            </div>
                        </form>
                    </div>
                <!-- /Blog Post -->
            @endforeach    
            
                    <!-- Blog Pagination -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="blog-pagination">
                                <nav>
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item">
                                            <a class="page-link" href="{{ url('booking/'. $base.'?'. $prevlink)}}"><i class="fas fa-angle-double-left"></i></a>
                                        </li>
                                        <?php for($i=1;$i<=$pages;$i++) { ?>
                                        <li class="page-item {{$i==$current?'active':''}}">
                                            <a class="page-link" href="{{url('/booking/'.$base.'?p='.$i)}}">{{$i}}</a>
                                        </li>
                                        <?php } ?>
                                        <li class="page-item">
                                            <a class="page-link" href="{{ url('booking/'. $base.'?'. $nextlink)}}"><i class="fas fa-angle-double-right"></i></a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <!-- /Blog Pagination -->            
            </div>
            
            <!-- Blog Sidebar -->
            <div class="col-lg-4 col-md-12 sidebar-right">

                <!-- Search -->
                <div class="card search-widget">
                    <div class="card-body">
                        <form class="search-form"  method="GET">
                            @csrf
                            <div class="input-group">
                                <input type="text" placeholder="Search..." class="form-control" name="key" value="{{$key}}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Search -->

                <!-- Latest Posts -->
                <div class="card post-widget">
                    <div class="card-header">
                        <h4 class="card-title">{{__('Latest Sections')}}</h4>
                    </div>
                    <div class="card-body">
                        <ul class="latest-posts">
                        @foreach ($sections as $one)
                            <li>
                                <div class="post-thumb">
                                    <a href="javascript:void(0)">
                                        <img class="img-fluid" src="{{asset($config['icon'])}}" alt="">
                                    </a>
                                </div>
                                <div class="post-info">
                                    <h4>
                                        <a href="javscript:void(0)">{{app()->getLocale() == 'en'? $one->en_name:$one->ar_name}}</a>
                                    </h4>
                                </div>
                            </li>      
                        @endforeach                    
                        </ul>
                    </div>
                </div>
                <!-- /Latest Posts -->
                
            </div>
            <!-- /Blog Sidebar -->
            
        </div>
    </div>
</div>		
<!-- /Page Content -->

@endsection


@section('page-js')
<script>
    $(document).ready(function(){
        $('.open-content').click(function(){
            var sid = $(this).attr('data-sid');
            var blog_content =  $('div.blog-content[data-sid='+ sid+ ']'); 
            var blog_action = $('form.blog-action[data-sid='+ sid+ ']');

            if ( blog_content.hasClass( "full" )  ==  false) {
                blog_content.slideDown( "slow" );
                blog_content.addClass( "full" );
            } else {
                //blog_content.hide();
                blog_content.removeClass( "full" );
            }
            if ( blog_action.is( ":hidden" ) ) {
                blog_action.slideDown( "slow" );
            } else {
                blog_action.hide();
            }
        });
    })
</script>
@endsection


@section('bottom-js')
<script>

</script>
@endsection

