@extends ('layouts.client.main')

@section('page-css')
	<link href="{{ asset('client/assets/stepwizard/css/smart_wizard_all.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('client/assets/front-calendar/style.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('client/assets/front-calendar/theme.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('client/assets/sweet/sweetalert2.css') }}" rel="stylesheet" type="text/css" />

	<style>
	 #section-text-content {
		 max-height: 500px;
		 overflow-y: auto;
	 }
	 #form {      
      margin: 25px auto 0;
	 }

      #form input {
        margin: 0 5px;
        text-align: center;
        line-height: 80px;
        font-size: 40px;
        border: solid 1px #ccc;
        box-shadow: 0 0 5px #ccc inset;
        outline: none;
        width: 100%;
        transition: all 0.2s ease-in-out;
        border-radius: 3px;
	  }

        #form input:focus {
          border-color: purple;
          box-shadow: 0 0 5px purple inset;
        }

        #form input::selection {
          background: transparent;
        }

      #form button {
        margin: 30px 0 50px;
        width: 100%;
        padding: 6px;
        background-color: #b85fc6;
        border: none;
        text-transform: uppercase;
      }

	  .next, .prev, .next-mission-arrow, .next-section-arrow, .next-date-arrow ,.submit-book-btn,.personal-next ,.verify-next, .upload-next{
		padding: 15px 25px;
		font-size: 18px;
		font-weight: bold;
	  }
	</style>
@endsection


@section('main-content')

<section class="container mt-3 mb-3">
	<!--     <p>
      <label>Form Style:</label>
      <select id="theme_selector" class="form-control">	  
        <option value="default">Default</option>
        <option value="arrows" selected>Arrows</option>
        <option value="dots">Dots</option>
        <option value="progress">Progress</option>
      </select>
	</p>   -->
<div class="card">

	<div class="card-header text-right">
		<button class="btn btn-danger submit-book-btn text-right " id="reset-btn" type="button">{{__('Reset')}}</button>
	</div>
	<div class="card-body" style="min-height:600px">
	<!-- SmartWizard html -->
	<form id="total-book-form" action="{{url('/finalsubmit')}}" method="post"  autocomplete="off" enctype="multipart/form-data">
	@csrf
	<div id="smartwizard" style="min-height:300px">
		<input type="hidden" name="mission_id" id="mission_id" value="{{$mission->mission_id}}"/>
		<input type="hidden" name="section_id" id="section_id" value="0"/>
		<input type="hidden" name="sch_date" id="sch_date" value=""/>
		<input type="hidden" name="meeting_value" id="meeting_value" value=""/>
		<ul class="nav">
			<li class="nav-item">
			<a class="nav-link" href="#step-2">
				<strong>{{__('Step 1')}}</strong> <br>{{__('Choose a Section')}}
			</a>
			</li>
			<li class="nav-item">
			<a class="nav-link" href="#step-3" data-repo="date">
				<strong>{{__('Step 2')}}</strong> <br>{{__('Choose a Date')}}
			</a>
			</li>
			<li class="nav-item">
			<a class="nav-link " href="#step-4">
				<strong>{{__('Step 3')}}</strong> <br>{{__('Confirm Document')}}
			</a>
			</li>
			<li class="nav-item">
			<a class="nav-link " href="#step-5">
				<strong>{{__('Step 4')}}</strong> <br>{{__('Enter Information')}}
			</a>
			</li>			
			<li class="nav-item">
			<a class="nav-link " href="#step-6">
				<strong>{{__('Step 5')}}</strong> <br>{{__('Confirm SMS')}}
			</a>
			</li>			
		</ul>

		<div class="tab-content">			
			<div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">

	  			<div class="form-group">
	  				<h3>
					  @if(app()->getLocale() == 'en')
					  {{ucwords($main_mission->name)}}
					  @else
					  {{$main_mission->name_ar}}
					  @endif
					</h3>
				</div>
	  			<label>{{__('Select Section Caption')}}</label>
                <select class="form-control" name="section_sel" id="sel-section">
	    			<option value="">{{__('Choose a Section')}}</option>
						@foreach($sections as $one)
							@if (app()->getLocale()=='en')
							<option value="{{$one->id}}">{{$one->en_name}}</option>
							@else
							<option value="{{$one->id}}">{{$one->ar_name}}</option>
							@endif
						@endforeach
				</select>

                <div class="col-md-12 mt-3 mb-3">
                    <div class="form-group text-right" id="action-part" hidden>					
                        <label class="ml-3 mr-3"><input type="checkbox" id="agree_term"/><strong>&nbsp;&nbsp;{{ __('I agree, Understand') }}</strong> </label>
                            <a href="javascript:void(0)" class="btn btn-sm btn-success next-section-arrow"><strong>{{__('Next') }}<i class="fa fa-chevron-right" ></i></strong></a>
                    </div>
                    <div class="form-group" id="section-text-content" >
                    </div>
                </div>	
				<!-- Choose Sections -->	
				<div class="form-group">	
					<div class="col-md-12 col-sm-12 d-flex" style="justify-content:space-between">
						<a href="{{url('/')}}" class="btn btn-sm btn-info  prev"> <i class="fa fa-chevron-left "></i> {{__('Prev')}}  </a>						
					</div>
				</div>		

					<div class="blog grid-blog">								
						<div class="blog-content">	
							<h3 class="blog-title">{{__('News')}}<!--  <a href="#"><i class="fa fa-sync"></i> </a> --></h3>
							@foreach (\App\MissionNews::where('mission_id',$main_mission->id)->latest()->take(5)->get() as $new)							
							<div class="form-group border-bottom d-flex justify-content-between">
								<div>
									@if (app()->getLocale() == 'en')											  
									<h5>{{$new->title_en}}</h5>
									@else
									<h5>{{$new->title_ar}}</h5>
									@endif								
									<p><a href="{{$new->link}}" target="_blank">{{$new->link}}</a></p>	
								</div>
								<div class="mb-1">
									@if ($new->file!='')
									<img src="{{asset(json_decode($new->file)->s)}}"/>
									@elseif ($new->f_link != '')
									<img src="{{$new->f_link}}"/>
									@else

									@endif
							</div>
							</div>
							@endforeach
						</div>
					</div>

			</div>
			<div id="step-3" class="tab-pane" role="tabpanel" aria-labelledby="step-3">				
				<div class="row">
					<div class="col-md-6 col-sm-12 mb-4">
						<label>{{__('Choose Slot Caption')}}</label>
						<div class="calendar-wrapper"></div>
					</div>
					<div class="col-md-6 col-sm-12 mb-4" id="scheudle-info">
						<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
                                <tr>
                                    <th>{{__('Start Time') }}</th>
									<th>{{__('End Time') }}</th>
                                    <th>{{__('Current Slots') }}</th>
                                    <th>{{__('Type') }}</th>
                                    <th></th>
                                </tr>                                
                            </thead>
							<tbody id="sch-table-body">
								
							</tbody>
						</table>
						<a class="btn btn-sm btn-success btn-block next-date-arrow mt-2 mb-2" href="javascript:void(0)">{{__('Next')}} <i class="fa fa-chevron-right"></i></a>
						</div>
					</div>	
					<div class="form-group">	
					<div class="col-md-12 col-sm-12 d-flex" style="justify-content:space-between">
						<a href="javascript:void(0);" class="btn btn-sm btn-info  prev"> <i class="fa fa-chevron-left "></i> {{__('Prev')}}  </a>
					</div>
				</div>									
				</div>
			</div>
			<div id="step-4" class="tab-pane" role="tabpanel" aria-labelledby="step-4">

				<label>{{__('Upload File Caption')}}</label>				

				<div class="form-group" id="file-tag-board">	
					<div class="col-md-12 col-sm-12">
						<div class="form-group card-label">
							<label>{{__('File')}} ({{__('Optional') }})</label>
							<input class="form-control" type="file" name="file" style="padding-top:15px;">
							<small class="form-text text-muted">{{__('Allowed JPG, GIF or PNG. Max size of 2MB')}}</small>
						</div>
					</div>					
				</div>

				<div class="form-group">	
					<div class="col-md-12 col-sm-12 d-flex" style="justify-content:space-between">
						<a href="javascript:void(0);" class="btn btn-sm btn-info  prev"> <i class="fa fa-chevron-left "></i> {{__('Prev')}}  </a>
						<a href="javascript:void(0);" class="btn btn-sm btn-success  upload-next">{{__('Next')}} <i class="fa fa-chevron-right"></i> </a>
					</div>
				</div>

			</div>
			<div id="step-5" class="tab-pane" role="tabpanel" aria-labelledby="step-5">
				<div class="info-widget">
					<h4 class="card-title">{{__('Personal Information')}}</h4>
					<div class="row">
						<div class="col-md-12">
						<div class="alert alert-warning">
							<p>By entering your phone number, you agree to receive booking confirmation messages at the number
								provided. Consent is not a condition of purchase. Message and data rates may apply.
								Message frequency varies. If you wish to cancel please reply HELP for help or STOP to cancel.</p>
						</div>
						<label>{{__('Personal Info Caption')}}</label>	
						</div>
												
					</div>					
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="form-group card-label">
								<label>{{__('First Name') }} <span class="text-danger">*</span></label>
								<input class="form-control" type="text"  name="fname">
							</div>
						</div>
						<div class="col-md-6 col-sm-12">
							<div class="form-group card-label">
								<label>{{__('Last Name') }} <span class="text-danger">*</span></label>
								<input class="form-control" type="text"  name="lname">
							</div>
						</div>																																						
						<div class="col-md-4 col-sm-12">
							<div class="form-group card-label">
								<label>{{__('Email') }} <span class="text-danger">*</span></label>
								<input class="form-control" type="email"  name="email">
							</div>
						</div>
						<div class="col-md-4 col-sm-12">
							<div class="form-group card-label">
								<label>{{__('Phone') }} <span class="text-danger">*</span>(+Country Code)</label>
								<input class="form-control phone_number" type="text"  name="phone">
							</div>
						</div>
						<div class="col-md-4 col-sm-12">
							<div class="form-group card-label">
								<label>{{__('Whatsapp') }} <span class="text-muted">({{__('optional')}})</span></label>
								<input class="form-control phone_number" type="text" name="whatsapp">
							</div>
						</div>		
						<div class="col-md-12 col-sm-12">
							<div class="form-group card-label">
								<label>{{__('Address') }} <span class="text-danger">*</span></label>
								<input class="form-control" type="text"  name="address">
							</div>
						</div>	
						<div class="col-md-12 col-sm-12" id="reason-not-onsite">
							<div class="form-group card-label">
								<label>{{__('Reason not choosing onsite meeting') }} <span class="text-danger">*</span></label>
								<input class="form-control" type="text"  name="reason">
							</div>
						</div>																	
					</div>
				</div>
				

				<div class="form-group">	
					<div class="col-md-12 col-sm-12 d-flex" style="justify-content:space-between">
						<a href="javascript:void(0);" class="btn btn-sm btn-info  prev"> <i class="fa fa-chevron-left "></i> {{__('Prev')}}  </a>
						<a href="javascript:void(0);" class="btn btn-sm btn-success  personal-next">{{__('Next')}} <i class="fa fa-chevron-right"></i> </a>
					</div>
				</div>
			</div>
			<div id="step-6" class="tab-pane" role="tabpanel" aria-labelledby="step-6">
				@if ($message = Session::get('error'))
				<div class="alert alert-danger alert-block mt-3 mb-3 ">
					<button type="button" class="close" data-dismiss="alert">×</button>	
						<strong>{{ $message }}</strong>		
						<br>

						<p class="text-center"><a class="btn btn-sm btn-success submit-book-btn" href="javascript:void(0)" id="book_again">{{__('Book Again')}}</a></p>					
				</div>
				@else
				<div class="form-group m-auto mt-5 text-center">
					<div class="container">
						<h3>{{__('Please enter the 6-digit verification code we sent via SMS')}}:  
							<a href="javascript:void(0)" id="resend_code" class="text-info" style="font-size:14px"><i>{{__('Resend Code') }}</i></a></h3>
					</div>
				</div>
				<div class="form-group m-auto mt-5 mb-5 text-center">
					<div class="col-md-10 m-auto">
						<div id="form" class="d-flex" style="justify-content:space-between">
							<input type="text" class="sms"  name="dig"/>

						</div>						
					</div>
				</div>
				@endif

				<div class="form-group mt-5">	
					<div class="col-md-12 col-sm-12 d-flex" style="justify-content:space-between">
						<a href="javascript:void(0);" class="btn btn-sm btn-info  prev"> <i class="fa fa-chevron-left "></i> {{__('Prev')}}  </a>
						@if (!Session::get('error'))
						<a href="javascript:void(0);" class="btn btn-sm btn-success  verify-next" ><i class="fa fa-save"></i> {{__('Submit Book Appointment') }}</a>
						@endif
						<button class="btn btn-lg btn-success submit-book-btn final-btn" hidden><i class="fa fa-save"></i> {{__('Submit Book Appointment') }}</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	</form>

	</div>
</div>

</section>
@endsection


@section('page-js')
<script type="text/javascript" src="{{ asset('client/assets/stepwizard/js/jquery.smartWizard.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('client/assets/front-calendar/calendar.js') }}"></script>
<script type="text/javascript" src="{{ asset('client/assets/sweet/sweetalert2.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
@endsection


@section('bottom-js')

<script>
function selectDate(date) {
	$('.calendar-wrapper').updateCalendarOptions({
		date: date
	});

	$('#sch_date').val(new Date(date));
	//call ajax 
	var ajaxURL = "{{url('/getschs/')}}";
	$.ajax({
		method  : "GET",
		url: ajaxURL,
		data: { _token:"{{csrf_token()}}", mid: $('#mission_id').val() ,sid:$('#section_id').val(),curdate: new Date(date) },
		//data: { _token:"{{csrf_token()}}", mid: 1 ,sid:1, curdate: new Date(date).toLocaleString()},
		beforeSend: function( xhr ) {
			// Show the loader				
			$('#smartwizard').smartWizard("loader", "show");
		}
	}).done(function(res){				
		$('#sch-table-body').empty();
		$('.tab-content').css('height','auto');
		var html='';

		if (res.empty == true) {
			if (res.holiday!='') {
				html+='<tr>';
				<?php if (app()->getLocale() == 'en') { ?>
				html+='<td colspan="5">'+ res.holiday.about_en +'</td>';                                                                   
				<?php } else  { ?>
				html+='<td colspan="5">'+ res.holiday.about_ar +'</td>';                                                                   
				<?php } ?>
				html+='</tr>';
			} else {
				html+='<tr>';
				html+='<td colspan="5">Nothing avialable slots</td>';                                                                   
				html+='</tr>';
			}
		} else {
			for (var i=0;i<res.onsite.length;i++) {
			html+='<tr>';
			html+='<td>'+ res.onsite[i]['start'] +'</td>';                                                                   
			html+='<td>'+ res.onsite[i]['end'] +'</td>';                                                                   
			html+='<td>'+ res.onsite[i]['slots'] +'</td>';                                                                   				
			html+='<td><span class="badge badge-pill bg-info text-white">{{__("Onsite Meeting")}}</span></td>';
			html+='<td  class="text-center">';
			if (res.onsite[i]['slots'] != 0) html+='<input type="checkbox" data-ctype="onsite" data-index="'+ res.onsite[i]['index'] +'"  class="checkbox-meeting"/>';							
			html+='</td>';
			html+='</tr>';
			}
			for (var i=0;i<res.online.length;i++) {
				html+='<tr>';
				html+='<td>'+ res.online[i]['start'] +'</td>';                                                                   
				html+='<td>'+ res.online[i]['end'] +'</td>';                                                                   
				html+='<td>'+ res.online[i]['slots'] +'</td>';                                                                   				
				html+='<td><span class="badge badge-pill bg-success text-white">{{__("Online Meeting")}}</span></td>';
				html+='<td  class="text-center">';
				if (res.online[i]['slots'] != 0)  html+='<input type="checkbox" data-ctype="online" data-index="'+ res.online[i]['index'] +'"  class="checkbox-meeting"/>';
				html+='</td>';
				html+='</tr>';
			}
		}
		$('#smartwizard').smartWizard("loader", "hide");
		$('#sch-table-body').append(html);

		if (html != '' && res.empty == false && res.holiday=='') 		
			$('.next-date-arrow').show();
		else 
			$('.next-date-arrow').hide();

	}).fail(function(err) {
		$('#smartwizard').smartWizard("loader", "hide");
		$('.next-date-arrow').hide();
	});

}

var defaultConfig = {
  weekDayLength: 1,
  date: new Date(),
  onClickDate: selectDate,
  showYearDropdown: true,
  disable:function (date) {
	var dateObj = new Date(); 
	dateObj.setDate(dateObj.getDate() - 1);  
    return date < dateObj;
  },
  prevButton:"Prev",
  nextButton:"Next",
  showThreeMonthsInARow:true,
  showTodayButton:false,

};
$('.calendar-wrapper').calendar(defaultConfig);
</script>

<script>
 $(document).ready(function(){

	function option_file_board(doc_list, type) {
		$('#file-tag-board').empty();		
		if (doc_list.length>0) {
			for (var i=0;i<doc_list.length;i++) {
				var html='';
				html+='<div class="col-md-12 col-sm-12">';
				html+='<div class="form-group card-label">';
				<?php if (app()->getLocale() == 'en') { ?>
					html+='<label>'+ doc_list[i]['en'] + ' {{__("File")}} '+'</label>';
				<?php } else { ?>
					html+='<label>' + doc_list[i]['ar'] + ' {{__("File")}} ({{__("Optional") }})'+'</label>';
				<?php } ?>
				html+='<input class="form-control upload-file-tags" required type="file" name="file[]" style="padding-top:15px;">';
				html+='<small class="form-text text-muted">{{__("Allowed JPG, GIF or PNG. Max size of 2MB")}}</small>';
				html+='</div>';
				html+='</div>';	
				$('#file-tag-board').append(html);
			}
		}
	}


	$('.phone_number').inputmask('+99999999999999');
    $('.sms').inputmask('999999');
	$('#sch_date').val(new Date());
	// Toolbar extra buttons	
	var btnFinish = $('<button></button>').text('Finish')
									.addClass('btn btn-info')
									.on('click', function(){ alert('Finish Clicked'); });
	var btnCancel = $('<button></button>').text('Cancel')
									.addClass('btn btn-danger')
									.on('click', function(){ $('#smartwizard').smartWizard("reset"); });

	// Step show event
	$("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection, stepPosition) {
		$("#prev-btn").removeClass('disabled');
		$("#next-btn").removeClass('disabled');
		if(stepPosition === 'first') {
			$("#prev-btn").addClass('disabled');
		} else if(stepPosition === 'last') {
			$("#next-btn").addClass('disabled');
		} else {
			$("#prev-btn").removeClass('disabled');
			$("#next-btn").removeClass('disabled');
		}
		$('#smartwizard').smartWizard("loader", "hide");
	});
	$("#smartwizard").on("stepContent", function(e, anchorObject, stepIndex, stepDirection) {
		var repo    = anchorObject.data('repo');
		$('#smartwizard').smartWizard("loader", "show");
		if (repo!= undefined) {
			var ajaxURL = "{{url('/getdata/')}}";
			if (repo == 'date') {				
				//get date values
				$('.next-date-arrow').hide();
				return new Promise((resolve, reject) => {
					$.ajax({
						method  : "GET",
						url: ajaxURL,
						data: { _token:"{{csrf_token()}}", mid: $('#mission_id').val() ,sid:$('#section_id').val(), curdate: $('#sch_date').val() },
						//data: { _token:"{{csrf_token()}}", mid: 1 ,sid:1,curdate: new Date($('#sch_date').val()).toLocaleString() },
						beforeSend: function( xhr ) {
							// Show the loader
							$('#smartwizard').smartWizard("loader", "show");
						}
					}).done(function(res){
						$('#sch-table-body').empty();
						$('.tab-content').css('height','auto');
						var html='';
						if (res.empty == true) {
							if (res.holiday!='') {
								html+='<tr>';
								<?php if (app()->getLocale() == 'en') { ?>
								html+='<td colspan="5">'+ res.holiday.about_en +'</td>';                                                                   
								<?php } else  { ?>
								html+='<td colspan="5">'+ res.holiday.about_ar +'</td>';                                                                   
								<?php } ?>
								html+='</tr>';
							} else {
								html+='<tr>';
								html+='<td colspan="5">Nothing avialable slots</td>';                                                                   
								html+='</tr>';
							}
						} else {
							for (var i=0;i<res.onsite.length;i++) {
							html+='<tr>';
							html+='<td>'+ res.onsite[i]['start'] +'</td>';                                                                   
							html+='<td>'+ res.onsite[i]['end'] +'</td>';                                                                   
							html+='<td>'+ res.onsite[i]['slots'] +'</td>';                                                                   				
							html+='<td><span class="badge badge-pill bg-info text-white">{{__("Onsite Meeting")}}</span></td>';
							html+='<td  class="text-center">';
							if (res.onsite[i]['slots'] != 0) html+='<input type="checkbox" data-ctype="onsite" data-index="'+ res.onsite[i]['index'] +'"  class="checkbox-meeting"/>';							
							html+='</td>';
							html+='</tr>';
							}
							for (var i=0;i<res.online.length;i++) {
								html+='<tr>';
								html+='<td>'+ res.online[i]['start'] +'</td>';                                                                   
								html+='<td>'+ res.online[i]['end'] +'</td>';                                                                   
								html+='<td>'+ res.online[i]['slots'] +'</td>';                                                                   				
								html+='<td><span class="badge badge-pill bg-success text-white">{{__("Online Meeting")}}</span></td>';
								html+='<td  class="text-center">';
								if (res.online[i]['slots'] != 0)  html+='<input type="checkbox" data-ctype="online" data-index="'+ res.online[i]['index'] +'"  class="checkbox-meeting"/>';
								html+='</td>';
								html+='</tr>';
							}
						}
						$('#smartwizard').smartWizard("loader", "hide");
						$('#sch-table-body').append(html);
						if (html != '' && res.empty == false && res.holiday=='') 		
							$('.next-date-arrow').show();
						else 
							$('.next-date-arrow').hide();

						// Make file upload tags
						option_file_board(res.dlist, res.type);

						resolve();						
					}).fail(function(err) {
						reject(err);
						reject( "An error loading the resource" );
						$('#smartwizard').smartWizard("loader", "hide");
					});
				});
			} 
		}
	})

	// Smart Wizard
	$('#smartwizard').smartWizard({
		selected: 0,
		justified: true,
		theme: 'progress', // default, arrows, dots, progress
		// darkMode: true,
		transition: {
			animation: 'slide-horizontal', // Effect on navigation, none/fade/slide-horizontal/slide-vertical/slide-swing
		},
		toolbarSettings: {
			toolbarPosition: 'none', // both bottom
			toolbarExtraButtons: [btnFinish, btnCancel]
		},
		keyboardSettings: {
			keyNavigation: false, // Enable/Disable keyboard navigation(left and right keys are used if enabled)
		},
	});

	// External Button Events
	$("#reset-btn").on("click", function() {
		// Reset wizard
		$('#smartwizard').smartWizard("reset");
		return true;
	});

	$('#book_again').on('click',function(){
		// Reset wizard
		$('#smartwizard').smartWizard("reset");
		return true;
	})

	$("#prev-btn").on("click", function() {
		// Navigate previous
		$('#smartwizard').smartWizard("prev");
		return true;
	});

	$("#next-btn").on("click", function() {
		// Navigate next
		$('#smartwizard').smartWizard("next");
		return true;
	});

	// Demo Button Events
	$("#got_to_step").on("change", function() {
		// Go to step
		var step_index = $(this).val() - 1;
		$('#smartwizard').smartWizard("goToStep", step_index);
		return true;
	});


	$("#theme_selector").on("change", function() {
		// Change theme
		var options = {
		theme: $(this).val()
		};
		$('#smartwizard').smartWizard("setOptions", options);
		return true;
	});


	$('.next').click(function() {
		// Navigate next
		$('#smartwizard').smartWizard("next");
		return true;
	})

	$('.personal-next').click(function(){
		//$('#smartwizard').smartWizard("next");
		if( $('input[name=phone]').val()=='' ||  $('input[name=fname]').val()=='' ||  $('input[name=lname]').val()=='' ||  $('input[name=email]').val()=='' ||  $('input[name=address]').val()==''  ) {
			Swal.fire({
				icon: 'error',
				title: '',
				text: '{{__("Please enter all required fields")}}',
			});
		} else {
			//send sms code & and then next
			var ajaxURL = "{{url('/chkphone/')}}";
			$.ajax({
			method  : "GET",
			url: ajaxURL,
			data: { _token:"{{csrf_token()}}", phone: $('input[name=phone]').val() },
			beforeSend: function( xhr ) {
				// Show the loader
				$('#smartwizard').smartWizard("loader", "show");
			}
			}).done(function(res){
				if (res == 'fail') {
					Swal.fire({
						icon: 'error',
						title: 'Oops',
						text: '{{__("Phone No is invalid. Error in sending SMS")}}',
					});
					$('#smartwizard').smartWizard("loader", "hide");
				} else {

					$('#smartwizard').smartWizard("loader", "hide");
					$('#smartwizard').smartWizard("next");	
				}
			}).fail(function(err) {
				$('#smartwizard').smartWizard("loader", "hide");
			});
		}
	});

	$('.upload-next').click(function(){
		var upload_flag = true;
		$(document).find('.upload-file-tags').each(function(){
			if ($(this).val() == '') {
				upload_flag = false;
			}
		});
		if (!upload_flag) {
			Swal.fire({
					icon: 'error',
					title: '',
					text: '{{__("Upload File Caption")}}',
			});
		} else {
			//$('#smartwizard').smartWizard("loader", "show");
			$('#smartwizard').smartWizard("next");
		}
	});


	$('.verify-next').click(function(){
		//$('#smartwizard').smartWizard("next");	

		if( $('input[name=dig]').val()=='' ) {
			Swal.fire({
				icon: 'error',
				title: '',
				text: '{{__("Please enter verification code")}}',
			});
		} else {
			//send sms code & and then next
			var ajaxURL = "{{url('/chkverify')}}";
			var vcode = '';

			vcode = $('#form').find('input.sms').val();

			$.ajax({
			method  : "GET",
			url: ajaxURL,
			data: { _token:"{{csrf_token()}}", phone: $('input[name=phone]').val() , vcode: vcode},
			beforeSend: function( xhr ) {
				// Show the loader
				$('#smartwizard').smartWizard("loader", "show");
			}
			}).done(function(res){
				if (res == 'fail') {
					Swal.fire({
						icon: 'error',
						title: 'Oops',
						text: '{{__("Verificaton code is incorrect.")}}',
					});
					$('#smartwizard').smartWizard("loader", "hide");
				} else {
					$('#smartwizard').smartWizard("loader", "hide");
					//$('#smartwizard').smartWizard("next");			
					$('.submit-book-btn.final-btn').click();	
				}
			}).fail(function(err) {
				$('#smartwizard').smartWizard("loader", "hide");
			});
		}
	});

	$('.prev').click(function() {
		// Navigate previous
		$('#smartwizard').smartWizard("prev");
		return true;
	})	


	$('.next-mission-arrow').click(function() {
		$('#mission_id').val($(this).attr('data-mid'));
		$('#smartwizard').smartWizard("next");
	})


	$(document).on('click','.next-section-arrow',function() {
		//$('#mission_id').val($(this).attr('data-mid'));
		if ($('#agree_term').prop('checked')) {
			$('#smartwizard').smartWizard("next");
		} else {
			Swal.fire({
			icon: 'error',
			title: '',
			text: '{{__("Please agree with the content of section")}}',
			})
		}

	})

	$(document).on('click','.next-date-arrow',function() {		
		if ($('#meeting_value').val() == '') {
			Swal.fire({
			icon: 'error',
			title: '',
			text: '{{__("Please choose one of schedule time")}}',
			})
		} else {

			//Check available slot
			var ajaxURL = "{{url('/chkslot/')}}";
			$.ajax({
				method  : "GET",
				url: ajaxURL,
				data: { _token:"{{csrf_token()}}", mid: $('#mission_id').val() ,sid:$('#section_id').val(), curdate: $('#sch_date').val(), meeting: $('#meeting_value').val() },
				beforeSend: function( xhr ) {
					// Show the loader
					$('#smartwizard').smartWizard("loader", "show");
				}
			}).done(function(res){
				if (res == true) {
					$('#smartwizard').smartWizard("next");
				} else {
					Swal.fire({
					icon: 'error',
					title: '',
					text: '{{__("No available slot at selected Time")}}',
					})
				}

				$('#smartwizard').smartWizard("loader", "hide");
			}).fail(function(err) {
				$('#smartwizard').smartWizard("loader", "hide");
			});
		}
	})

	$(document).on('change','#sel-section', function(){
		
		$('#section_id').val($(this).val());

		if ($(this).val() == '') {
			$('#action-part').attr('hidden','hidden');
			$('#section-text-content').empty();
		} else {
			var ajaxURL = "{{url('/getdata/')}}";
			$.ajax({
				method  : "GET",
				url: ajaxURL,
				data: { _token:"{{csrf_token()}}", mid: $('#mission_id').val() ,sid:$('#section_id').val(), curdate:'' },
				beforeSend: function( xhr ) {
					// Show the loader
					$('#smartwizard').smartWizard("loader", "show");
				}
			}).done(function(res){
				$('.tab-content').css('height','auto');
				$('#action-part').removeAttr('hidden');
				$('#section-text-content').empty();
				@if (app()->getLocale()=='en')
				$('#section-text-content').append(res.en_about);
				@else
				$('#section-text-content').append(res.ar_about);
				@endif
				$('#smartwizard').smartWizard("loader", "hide");
			}).fail(function(err) {
				$('#smartwizard').smartWizard("loader", "hide");
			});
		}
	

	});

	$(document).on('change','.checkbox-meeting', function () {
		if ($(this).is(':checked')) {
			$('.checkbox-meeting').not(this).prop('checked',false);
			$('#meeting_value').val($(this).attr('data-index'));
		} else {
			$('#meeting_value').val('');
		}

		if ($(this).attr('data-ctype') == 'onsite') {
			$('#reason-not-onsite').hide();
		} else {
			$('#reason-not-onsite').show();
		}
	});

	$('#resend_code').click(function(){
		$('input.sms').val('');

		if ($('input[name=phone]').val()=='') {
			Swal.fire({
				icon: 'error',
				title: '',
				text: '{{__("Phone number is invalid")}}',
			})
		} else {
			var ajaxURL = "{{url('/rechkphone/')}}";
			$.ajax({
				method  : "GET",
				url: ajaxURL,
				data: { _token:"{{csrf_token()}}", phone:  $('input[name=phone]').val()},
				beforeSend: function( xhr ) {
					// Show the loader
					$('#smartwizard').smartWizard("loader", "show");
				}
			}).done(function(res){
				$('.tab-content').css('height','auto');
			
					Swal.fire({
						icon: 'success',
						title: '',
						text: '{{__("Sent SMS successfully")}}',
					})

				$('#smartwizard').smartWizard("loader", "hide");
			}).fail(function(err) {
				reject( "An error loading the resource" );
				$('#smartwizard').smartWizard("loader", "hide");
			});

		}	

		//Send Verify code ajax

	})
});
</script>
<script>
$(function() {
  'use strict';
  var body = $('body');

  function goToNextInput(e) {
    var key = e.which,
      t = $(e.target),
      sib = t.next('input.sms');

    if (key != 9 && (key < 48 || key > 57)) {
      e.preventDefault();
      return false;
    }

    if (key === 9) {
      return true;
    }

    if (!sib || !sib.length) {
      sib = body.find('input.sms').eq(0);
    }
    sib.select().focus();
  }

  function onKeyDown(e) {
    var key = e.which;

    if (key === 9 || (key >= 48 && key <= 57)) {
      return true;
    }

    e.preventDefault();
    return false;
  }
  
  function onFocus(e) {
    $(e.target).select();
  }

  body.on('keyup', 'input.sms', goToNextInput);
  body.on('keydown', 'input.sms', onKeyDown);
  body.on('click', 'input.sms', onFocus);

});
</script>
@endsection
