<!doctype html>
<html lang='{{ app()->getLocale() }}'>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <title>PayPal Dashboard</title>
        <link rel="shortcut icon" type="image/png" href="/images/paypal_icon.png">
        <!-- Fonts -->
        <link href='https://fonts.googleapis.com/css?family=Raleway:100,600' rel='stylesheet' type='text/css'>        
        <link rel="stylesheet" href="/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/jquery-ui.css">
        <link rel="stylesheet" href="/css/all.css">
        <link rel="stylesheet" href="/css/semantic.min.css">
        <link rel="stylesheet" href="/css/dropdown.min.css">
        <link rel="stylesheet" href="/css/toastr.css">
        <style>
            .content {
                text-align: center;
                margin-top: 25px;
            }
            .table>thead>tr>th {
                text-align: center;
            }
            .input-group {
                display: flex;
                float: right;
            }
            .btn.btn-success {
                padding: 5px 47px;
                letter-spacing: 1px;
                border-bottom-left-radius: 0px;
                border-top-left-radius: 0px;          
            }
            table {
                border-bottom: 1px solid;
            }
            .table {
                padding-top: 30px;
            }
            .table th {
                background: #ebedef;
            }

            #send_money {
                padding: 5px 20px!important;
            }

            .modal {
                display:    none;
                position:   fixed;
                z-index:    1000;
                top:        0;
                left:       0;
                height:     100%;
                width:      100%;
                background: rgba( 255, 255, 255, .8 ) 
                            url('/images/loading.gif')
                            50% 50% 
                            no-repeat;
            }

            /* When the body has the loading class, we turn
            the scrollbar off with overflow:hidden */
            body.loading .modal {
                overflow: hidden; 
            }

            /* Anytime the body has the loading class, our
            modal element will be visible */
            body.loading .modal {
                display: block;
            }

            .form-control {
                padding: 18px 12px;
            }
        </style>
    </head>
    <body>
        <div id="app">
            <nav class="navbar navbar-default navbar-static-top">
                <div class="container">
                    <div class="navbar-header">

                        <!-- Collapsed Hamburger -->
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                            <span class="sr-only">Toggle Navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>

                        <!-- Branding Image -->
                        <a class="navbar-brand" href="{{ url('/') }}">
                            Paypal Payout Dashboard
                        </a>
                    </div>

                    <div class="collapse navbar-collapse" id="app-navbar-collapse">
                        <!-- Left Side Of Navbar -->
                        <ul class="nav navbar-nav">
                            &nbsp;
                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="nav navbar-nav navbar-right">
                            <li>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                    Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="content">     
                <div class="modal">
                    <div class="spinner loading"></div>
                </div>           
                <div class='container'>
                <h1>Total Amount: ${{$total}} </h1>            
                    <div class="content">
                        <form id='loadForm' method='post'>
                            {{ csrf_field() }}
                            <div class="ui selection dropdown sheet_select" tabindex="0" style="min-height: unset; width:300px; float: left">
                                <input type="hidden" name="sheet" id="sheet_select" value="{{$sheets[0]['id']}}">
                                <i class="dropdown icon"></i>
                                <div class="text">default</div>
                                <div class="menu transition hidden" tabindex="-1">
                                    @foreach($sheets as $sheet)
                                        <div class="item" data-value="{{$sheet['id']}}">{{$sheet['title']}}</div>
                                    @endforeach                       
                                </div>
                            </div>
                            <div class='input-group' style='float:right'>
                                <input type='text' class='form-control title' name='title' placeholder='GoogleSheet Title' value='' />
                                <input type='text' class='form-control email' name='email_column' placeholder='Email Column' value=''/>
                                <input type='text' class='form-control amount' name='amount_column' placeholder='Amount Column' value=''/>
                                <button class='btn btn-success'>Load</button>
                            </div>
                        </form>
                    </div>     
                    <div class='input-group' style="width:100%; margin-top: 20px">
                            <input type='text' class='form-control message' name='message' placeholder='Payment Message' value='' />
                            <button class='btn btn-success' id='send_money'>Send Money</button>
                        </div>       
                    
                    <div class='table-responsive table table-hover'>
                        <table id='dataTable' class='table table-hover'>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Email</th>
                                    <th>Amount</th>
                                    <th class='actions'>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach($entries as $key => $entry) { ?>
                                        <tr>
                                            <td>{{$key + 1}}</td>
                                            <td>{{$entry['email']}}</td>
                                            <td>{{$entry['amount']}}</td>
                                            <td><a href='/delete/{{$entry["id"]}}'>Delete</a></td>
                                        </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>        
            </div>
        </div>    
    </body>
    <script src="/js/jquery.js"></script>
    <script src="/js/semantic.min.js"></script>
    <script src="/js/dropdown.js"></script>
    <script src="/js/jquery-ui.js"></script>
    <script src="/js/toastr.js"></script>
    <script>        
        $(document).ready(function() {
            @if(isset($google_error) && $google_error != '')
                toastr.error('Please check Google Sheet Title again')
            @endif
            var dropdown = $('.dropdown').dropdown()
            $('form#loadForm').submit(function(e) {                
                $title = $('.title').val()
                $email = $('.email').val()
                $amount = $('.amount').val()

                if($title == '' || $email == '' || $amount == '')  {
                    e.preventDefault()
                    toastr.error('Something is missing!<br/>Please check title, email & amount columns')
                }                
            })

            $('button#send_money').on('click', function(e) {                
                var message = $('.message').val()                
                if(message == '') {
                    toastr.error('Please input message')
                    return
                }

                var isSendMoney = confirm('All is fine? We will send money?')
                if(isSendMoney) {
                    $('.modal').show()

                    $.ajax({
                        method: 'GET',
                        url: '/sendmoney/' + message,
                        dataType: 'json',
                        async: true,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success:function(data) {
                            $('.modal').hide()
                            if(data.result == 'success') {
                                toastr.success('Successfully Paid!')
                                setTimeout(() => {
                                    window.location.reload()
                                }, 3000)                            
                            } else if(data.result == 'failed') {
                                toastr.error('Error Occurred! <br/>There is issue to get Access Token')
                            } else if(data.result == 'error') {
                                toastr.error('Something wrong in Sending Money!!!')
                            } else if(data.result == 'no_record') {
                                toastr.error('No any email exist!')
                            }
                        }
                    })
                }
            })
        })
    </script>
</html>
