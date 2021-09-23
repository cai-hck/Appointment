@extends ('layouts.consul.main')

@section('page-css')

@endsection


@section('main-content')

<div class="appointments">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('Credit Card Payment') }}</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 ">
                       
                    <!-- Booking Doctor Info -->
                    <div class="booking-doc-info mb-3">
                        <a href="doctor-profile.html" class="booking-doc-img">
                            <img src="{{asset($user->consultant->mission->cover_image)}}" alt="User Image">
                        </a>
                        <div class="booking-info">
                            <h4><a href="doctor-profile.html">{{$user->consultant->mission->name}}</a></h4>                            
                        </div>
                    </div>
                    <!-- Booking Doctor Info -->
                    
                    <div class="booking-summary">
                        <div class="booking-item-wrap">
                            <form action="{{url('consul/pay')}}" method="POST" id="pay_form" >                           
                            @csrf
                            <input type="hidden" name="stripeToken" id="stripeToken" required />
                            <input type="hidden" id="per_user" value="{{$user->consultant->mission->cost_per_user}}"  required/>
                            <ul class="booking-fee mb-3">
                                <style>
                                    .booking-fee li {
                                        display:flex;
                                        justify-content: space-between;
                                    }
                                    .booking-fee li span {
                                        float:unset;
                                    }
                                    .booking-fee li span input, .booking-fee li span select{
                                        max-width:150px;
                                    }
                                </style>
                                <li class="mb-2">Cost per Account<span>${{$user->consultant->mission->cost_per_user}}</span></li>
                                <li class="mb-2">No. of Accounts<span>
                                    <input  class="form-control w-100 float-right " type="number" id="amount_user" name="amount_user" required value="1"
                                     style="min-height:30px;text-align:right"/></span>
                                </li>
                                <li class="mb-2">Type<span>
                                    <select class="form-control w-100" required name="type" id="pay-type-select">
                                        <option>Choose Type</option>
                                        <option value="activate">Activate</option>
                                        <option value="extend">Extend</option>
                                        <option value="add">Add</option>
                                    </select>
                                    </span>
                                </li>                                
                                <li>About</li>
                                <li><textarea class="form-control" name="about" required></textarea></li>
                            </ul>
                            <div class="booking-total mt-4">
                                <ul class="booking-total-list">
                                    <li>
                                        <span>Total</span>
                                        <span class="total-cost">$<span  class="total-cost" id="total_amount" >70</span></span>
                                    </li>
                                </ul>
                            </div>
                            </form>
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div id="card-element"><!-- A Stripe Element will be inserted here. --></div>
                        <!-- Used to display form errors. -->
                        <div id="card-errors" role="alert"></div>
                    </div>
                    <p style="font-size:12px;">The payment includes postage and gurantee delivery charges of your passport</p>
                    <a class="btn btn-block btn-success text-white" href="#pay_modal" data-toggle="modal" id="pay_express_service">Pay </a>

                    <div class="form-group">
                        <table class="table table-bordered mt-3">                            
                            <tr><td wdith="50%" class="w-50 tex-center text-info"><i class="fa fa-users"></i> All Users</td><td>{{$user_anal['total']}}</td></tr>
                            <tr><td wdith="50%" class="w-50 tex-center text-success"><i class="fa fa-users"></i> Active Users</td><td>{{$user_anal['active']}}</td></tr>
                            <tr><td wdith="50%" class="w-50 tex-center text-danger"><i class="fa fa-users"></i> Expired Users</td><td>{{$user_anal['expire']}}</td></tr>
                            <tr><td wdith="50%" class="w-50 tex-center text-warning"><i class="fa fa-users"></i> Pending Users</td><td>{{$user_anal['pending']}}</td></tr>                        
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pay_modal" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document" >
            <div class="modal-content text-center">						
                <div class="modal-body">
                    <div class="form-content p-2">
                            <input type="hidden" name="u_id" id="u_id" />
                            <h4 class="modal-title">Pay confirmation</h4>
                            <p class="mb-4">Are you sure to pay?</p>											
                            <button type="submit" class="btn btn-success" id="pay_credit_btn">{{ __('Yes, Sure') }} </button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('No, Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('All Payments') }} 
                <span class="float-right">{{ __('Total Spent') }} - <span class="text-danger"><i class="fa fa-dollar-sign"></i>  {{$total_spent}}</span></span>
            </h4>
        </div>
        <div class="card-body">    
            <div class="table-responsive">
                <table class="datatable table table-stripped">
                    <thead>
                        <tr>
                            <th>{{ __('Issued Date') }}</th>
                            <th>{{ __('Purpose') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Approved Date') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $one)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($one->date)->toDateString()}}</td>
                            <td>                                                            
                                {{$one->about}}
                            </td>
                            <td class="text-danger"> -<i class="fa fa-dollar-sign"></i> {{$one->amount}}</td>       
                            <td>
                                @if ($one->status)
                                {{ \Carbon\Carbon::parse($one->accept_date)->toDateString()}}
                                @endif
                            </td>
                            <td>
                                @if ($one->status)
                                <a href="javascript:void(0);" class="badge badge-pill bg-success inv-badge text-white">
                                    {{__('Paid')}}
                                </a> 
                                @else
                                <a href="javascript:void(0);" class="badge badge-pill bg-pending inv-badge text-white">
                                    {{__('Pending')}}
                                </a> 
                                @endif
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
<script src="https://js.stripe.com/v3/"></script>

@endsection


@section('bottom-js')
<script>
    $(document).ready(function(){

        $(window).keydown(function(event){
            if(event.keyCode == 13) {
            event.preventDefault();
            return false;
            }
        });
		// Create a Stripe client.
        <?php 
            if (config('app.stripe')['STRIPE_TESTLIVE'])
		        echo 'var stripe = Stripe("' .config('app.stripe')['STRIPE_LIVE_PK'].'");';
            else
                echo 'var stripe = Stripe("' .config('app.stripe')['STRIPE_TEST_PK'].'");';
        ?>
		// Create an instance of Elements.
		var elements = stripe.elements();

		// Custom styling can be passed to options when creating an Element.
		// (Note that this demo uses a wider set of styles than the guide below.)
		var style = {
			base: {
				color: '#32325d',
				fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
				fontSmoothing: 'antialiased',
				fontSize: '16px',
				'::placeholder': {
				color: '#aab7c4'
				}
			},
			invalid: {
				color: '#fa755a',
				iconColor: '#fa755a'
			}
		};

		// Create an instance of the card Element.
		var card = elements.create('card', {style: style, hidePostalCode: true});

		// Add an instance of the card Element into the `card-element` <div>.
		card.mount('#card-element');

		// Handle real-time validation errors from the card Element.
		card.on('change', function(event) {
		var displayError = document.getElementById('card-errors');
		if (event.error) {
			displayError.textContent = event.error.message;
		} else {
			displayError.textContent = '';
		}
		});

		$('#pay_credit_btn').click(function(){
			stripe.createToken(card).then(function(result) {
				if (result.error) {
					// Inform the user if there was an error.
					var errorElement = document.getElementById('card-errors');
					errorElement.textContent = result.error.message;
				} else {
					// Send the token to your server.			
					stripeTokenHandler(result.token);
				}
			});			
		});
		// Submit the form with the token ID.
		function stripeTokenHandler(token) {

			$('#pay_form').find('#stripeToken').val(token.id); 
            $('#pay_form').submit();
        }        
        

        $('#amount_user').change(function(){
            $('#total_amount').text($(this).val() * $('#per_user').val());
        });

        $('#pay-type-select').change(function(){
            if ($(this).val()=='activate') {
                $('#amount_user').val();
            }
            if ($(this).val()=='extend') {

            }
            if ($(this).val()=='add') {

            }           
        })
    })

</script>
@endsection

