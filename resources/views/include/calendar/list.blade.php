<?php 
if ($user->role == 'consul') {  
    $layout = 'layouts.consul.main';
} else {
    $layout = 'layouts.secret.main';
}
?>
@extends ($layout)



@section('page-css')
    <!-- Full Calander CSS -->
    <link rel="stylesheet" href="{{ asset('client/assets/simple-calendar/dist/simple-calendar.css') }}">
    <style>
        .calendar .day.has-event {
            border: 2px solid #6691CC;
        }        
        .calendar-container {
            margin-top:50px;
        }
        .calendar header{
            padding-top:30px;
            padding-bottom:30px;
        }
        .calendar table {
            min-height:500px;
        }
        .note-day {
            width:2.5em;
            height:2.5em;
            border-radius:50%;
        }
    </style>
@endsection
@section('main-content')


<div class="card">
    <div class="card-body">
        <div class="row">   
            <div class="col-md-10 offset-md-1 inline-group">
                <div class="row">
                    <div class="col-md-4 text-center"><div class="note-day bg-primary m-auto"></div> {{__('Onsite Meeting') }}</div>
                    <div class="col-md-4 text-center"><div class="note-day bg-info m-auto"></div>{{__('Online Meeting') }}</div>
                    <div class="col-md-4 text-center"><div class="note-day bg-warning m-auto"></div>{{__('Holiday') }}</div>
                </div>
            </div>

            <div class="col-md-10 offset-md-1">
                <div id="container" class="calendar-container"></div>
            </div>
        </div>
    </div>
</div>  


@endsection


@section('page-js')
    <!-- Datatables JS -->
    <script src="{{ asset('client/assets/'.app()->getLocale().'/js/moment.min.js') }}"></script>
    <!-- Full Calendar JS -->
    <script src="{{ asset('client/assets/simple-calendar/dist/jquery.simple-calendar.js') }}"></script>
@endsection
@section('bottom-js')
    <script>    
$(document).ready(function () {
    $("#container").simpleCalendar({
        months: ['january','february','march','april','may','june','july','august','september','october','november','december'],
        days: ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'],
        displayYear: true,              // Display year in header
        fixedStartDay: true,            // Week begin always by monday or by day set by number 0 = sunday, 7 = saturday, false = month always begin by first day of the month
        displayEvent: true,             // Display existing event
        disableEventDetails: false, // disable showing event details
        disableEmptyDetails: false, // disable showing empty date details
        events: <?php echo $json_schedule?>,
        onInit: function (calendar) {}, // Callback after first initialization
        onMonthChange: function (month, year) {}, // Callback on month change
        onDateSelect: function (date, events) {

        }, // Callback on date selection
        onEventSelect: function() {}, // Callback on event selection - use $(this).data('event') to access the event
        onEventCreate: function( $el ) {},          // Callback fired when an HTML event is created - see $(this).data('event')
        onDayCreate:   function( $el, d, m, y ) {}  // Callback fired when an HTML day is created   - see $(this).data('today'), .data('todayEvents')

    });
  });
    </script>
@endsection