@extends ('layouts.consul.main')

@section('page-css')
    <!-- Full Calander CSS -->
    <link rel="stylesheet" href="{{ asset('client/assets/calendar/main.css') }}">
    <style>
        .fc-event-time {
            display:none;
        }
    </style>
@endsection
@section('main-content')


<div class="card">
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>  


@endsection


@section('page-js')
    <!-- Datatables JS -->
    <script src="{{ asset('client/assets/'.app()->getLocale().'/js/moment.min.js') }}"></script>
    <!-- Full Calendar JS -->
    <script src="{{ asset('client/assets/calendar/main.js') }}"></script>
@endsection
@section('bottom-js')
    <script>    
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');            
            var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {},
            initialDate: "{{date('Y-m-d')}}",
            editable: false,
            eventDrop: function(info) {
                info.revert();
            },  
            navLinks: true, // can click day/week names to navigate views
            dayMaxEvents: true, // allow "more" link when too many events
            events:  { 
                events: <?php echo $json_schedule?>,
            }           
            
            });

            calendar.render();
        });
    </script>
@endsection