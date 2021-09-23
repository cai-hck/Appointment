
@extends ('layouts.secret.main')

@section('page-css')
<link rel="stylesheet" href="{{ asset('client/assets/summernote/summernote-bs4.min.css') }}" />
@endsection


@section('main-content')

<!-- Basic Information -->
<div class="card">
    <div class="card-body">
        <form action="{{url('secret/sections/update')}}" method="POST">
        @include('alert')
        @csrf
        <input type="hidden" name="s_id" value="{{$section->id}}" />
        <h4 class="card-title">{{ __('Update Section') }}</h4>
        <div class="row form-row">			
            <div class="col-md-8">								
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{__('Name of Section')}} ({{__('English')}})<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" required name="en_name" value="{{$section->en_name}}">
                    </div>
                </div>									
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{__('Name of Section')}} ({{__('Arabic')}})<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" required name="ar_name" value="{{$section->ar_name}}">
                    </div>
                </div>									            
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{__('General Info')}} ({{__('English')}})<span class="text-danger">*</span></label>
                        <textarea id="en_about"  type="text" class="form-control" rows="10" required name="en_about">{{$section->en_about}}</textarea>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{__('General Info')}}  ({{__('Arabic')}})<span class="text-danger">*</span></label>
                        <textarea id="ar_about" type="text" class="form-control" rows="10" required name="ar_about">{{$section->ar_about}}</textarea>
                    </div>
                </div>            
            </div>
            <div class="col-md-4">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{__('Has Meeting')}}<span class="text-danger">*</span></label>
                        <select class="form-control" name="has_meeting_type" required>
                            <option value="">Choose Meeting Type</option>
                            <option value="both" {{$section->info&&$section->info->meetings=='both'?'selected':''}}>{{__('Both Online & Onsite Meetings') }}</option>
                            <option value="onsite" {{$section->info&&$section->info->meetings=='onsite'?'selected':''}}>{{__('Only Onsite Meetings') }}</option>
                            <option value="online" {{$section->info&&$section->info->meetings=='online'?'selected':''}}>{{__('Only Online Meetings') }}</option>
                        </select>
                    </div>
                    <div class="form-group">                    
                        <label>{{__('Required Document')}}<span class="text-danger">*</span></label>                    
                        <div class="form-group" id="add-chk-group">       
                            <input type="text" class="form-control en-label mt-1" placeholder="English Label"/>
                            <input type="text" class="form-control ar-label mt-1" placeholder="التسمية العربية"/>                            
                            <a class="btn btn-info btn-block add-require-doc  mt-1" href="javascript:void(0)"> <i class="fa fa-plus"></i></a>
                        </div>
                        <div class="mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody id="chk-body">
                                        @if ($section->info && $section->info->doc_list!='')
                                        @foreach (json_decode($section->info->doc_list) as $one)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="chks[]" value="on" checked class="mt-2 check-td" style="height:20px;width:20px;font-size:14px;"/>
                                                <input type="hidden" name="en_chks[]"  value="{{$one->en}}" />
                                                <input type="hidden"  name="ar_chks[]" value="{{$one->ar}}"/>                                                                                            
                                            </td>
                                            <td>
                                                <label >{{$one->en}}</label>
                                                <br>
                                                <label >{{$one->ar}}</label>
                                            </td>
                                            <td class="text-center">
                                                <a class="btn btn-sm btn-danger chk-del mt-2" href="javascript:void(0);">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>	
            </div>    
        </div>
        <div class="submit-section submit-btn-bottom">
            <button type="submit" class="btn btn-primary submit-btn">Save</button>
        </div>
        </form>        
       
    </div>
</div>
<!-- /Basic Information -->	

@endsection


@section('page-js')
<script src="{{ asset('client/assets/summernote/summernote-bs4.min.js') }}"></script>
        
<script>
$(document).ready(function() {
    $('#en_about').summernote({height:300});
    $('#ar_about').summernote({height:300});

    $('.add-require-doc').click(function(){

        var label_en = $('#add-chk-group').find('input.en-label').val();
        var label_ar = $('#add-chk-group').find('input.ar-label').val();

        if (label_en == '' || label_ar == '') {
            alert('Input fields correctly');
            return;
        }

        var child ='';

        child += '<tr>';
        child += '<td class="text-center"><input type="checkbox" name="chks[]" value="off" class="mt-2 check-td" style="height:20px;width:20px;font-size:14px;"/>';
        child += '<input type="hidden" name="en_chks[]" disabled value="'+label_en+'" /><input type="hidden" disabled name="ar_chks[]" value="'+label_ar+'"/>';
        child +=' </td>';
        child += '<td><label >'+ label_en +'</label><br><label >'+ label_ar +'</label></td>';        child += '<td class="text-center"><a class="btn btn-sm btn-danger chk-del mt-2" href="javascript:void(0);"><i class="fa fa-trash"></i></a></td>';
        child += '</tr>';

        $('#chk-body').append(child);
        $('#add-chk-group').find('input').val('');
    })

    $(document).on('click','.chk-del',function() {
        $(this).closest('tr').remove();
    });

    $(document).on('change','.check-td',function() {
        if ($(this).prop('checked'))  {
            $(this).val('on');
            $(this).closest('tr').find('input[type=hidden]').attr('disabled', false);
        }
        else {             
            $(this).val('off');
            $(this).closest('tr').find('input[type=hidden]').attr('disabled', true);
        }
    })
});
</script>
@endsection


@section('bottom-js')



@endsection
