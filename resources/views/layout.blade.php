<!DOCTYPE html>
<html>
    <head>
        <title>{{ View::hasSection('title') ? View::getSection('title') . ' - ' . config('app.name') : config('app.name') }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="apple-touch-icon" sizes="57x57" href="/storage/soft_files/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/storage/soft_files/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/storage/soft_files/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/storage/soft_files/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/storage/soft_files/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/storage/soft_files/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/storage/soft_files/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/storage/soft_files/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/storage/soft_files/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="/storage/soft_files/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/storage/soft_files/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/storage/soft_files/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/storage/soft_files/favicon-16x16.png">
        <link rel="manifest" href="/storage/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="/storage/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="stylesheet" href="/storage/twemoji-flags.css" />
        <script src="/storage/all.min.js"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:400,400i,600,600i" />
        <link rel="stylesheet" href="/css/app.css" />
        <link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css" />
        <script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css">
        <script
                src="https://code.jquery.com/jquery-3.4.1.min.js"
                integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
                crossorigin="anonymous"></script>
        <style>
            .ct-series-a .ct-slice-donut-solid {
                fill: #179BD1;
            }

            .ct-series-b .ct-slice-donut-solid {
                fill: #E4E8EB;
            }

            .ct-series-a .ct-line {
                stroke-width: 2px;
                stroke: #179BD1;
            }

            .theme-dark .ct-label {
                color: #758193;
            }

            [v-cloak] {
                display: none;
            }
        </style>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-142139195-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-142139195-1');

            $( document ).ready(function() {
                $("input[data-type='currency']").on({
                    keyup: function() {
                        formatCurrency($(this));
                    },
                    blur: function() {
                        formatCurrency($(this), "blur");
                    }
                });
            });



            function formatNumber(n) {
                // format number 1000000 to 1,234,567
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            }


            function formatCurrency(input, blur) {
                // appends $ to value, validates decimal side
                // and puts cursor back in right position.

                // get input value
                var input_val = input.val();

                // don't validate empty input
                if (input_val === "") { return; }

                // original length
                var original_len = input_val.length;

                // initial caret position
                var caret_pos = input.prop("selectionStart");

                // check for decimal
                if (input_val.indexOf(".") >= 0) {

                    // get position of first decimal
                    // this prevents multiple decimals from
                    // being entered
                    var decimal_pos = input_val.indexOf(".");

                    // split number by decimal point
                    var left_side = input_val.substring(0, decimal_pos);
                    var right_side = input_val.substring(decimal_pos);

                    // add commas to left side of number
                    left_side = formatNumber(left_side);

                    // validate right side
                    right_side = formatNumber(right_side);

                    // On blur make sure 2 numbers after decimal
                    if (blur === "blur") {
                        right_side += "00";
                    }

                    // Limit decimal to only 2 digits
                    right_side = right_side.substring(0, 2);

                    // join number by .
                    input_val = left_side + "." + right_side;

                } else {
                    // no decimal entered
                    // add commas to number
                    // remove all non-digits
                    input_val = formatNumber(input_val);
                    input_val = input_val;

                    // final formatting
                    if (blur === "blur") {
                        input_val += ".00";
                    }
                }

                // send updated string to input
                input.val(input_val);

                // put caret back in the right position
                var updated_len = input_val.length;
                caret_pos = updated_len - original_len + caret_pos;
                input[0].setSelectionRange(caret_pos, caret_pos);
            }

        </script>
        @yield('styles')
    </head>
    <body class="theme-{{ Auth::check() ? Auth::user()->theme : 'light' }}">
        <div id="app">
            @if (Auth::check())
                <div class="navigation">
                    <div class="wrapper">
                        <ul class="navigation__menu">
                            <li>
                                <a href="/dashboard" {!! (Request::path() == 'dashboard') ? 'class="active"' : '' !!}><img class="active" src="/storage/logo_250.png" style="height: 40px;"> <span class="hidden ml-05">{{ __('general.dashboard') }}</span></a>
                            </li>
                            <li>
                                <a href="/transactions" {!! (Request::path() == 'transactions') ? 'class="active"' : '' !!}><i class="far fa-exchange-alt fa-sm color-green"></i> <span class="hidden ml-05">{{ __('models.transactions') }}</span></a>
                            </li>
                            <li>
                                <a href="/tags" {!! (Request::path() == 'tags') ? 'class="active"' : '' !!}><i class="far fa-tag fa-sm color-red"></i> <span class="hidden ml-05">{{ __('models.tags') }}</span></a>
                            </li>
                            <li>
                                <a href="/reports" {!! (Request::path() == 'reports') ? 'class="active"' : '' !!}><i class="far fa-chart-line fa-sm color-blue"></i> <span class="hidden ml-05">Raporlar</span></a>
                            </li>
                        </ul>
                        <ul class="navigation__menu">
                            <li>
                                <button-dropdown>
                                    <a slot="button" href="/transactions/create">{{ __('actions.create_transaction') }}</a>
                                    <ul slot="menu" v-cloak>
                                        <li>
                                            <a href="/tags/create">{{ __('actions.create') }} {{ __('models.tag') }}</a>
                                        </li>
                                        <li>
                                            <a href="/imports/create">{{ __('actions.create') }} {{ __('models.import') }}</a>
                                        </li>
                                    </ul>
                                </button-dropdown>
                            </li>
                            <li>
                                <a href="/activities">
                                    <i class="far fa-clock"></i>
                                </a>
                            </li>
                            @if (Auth::user()->spaces->count() > 1)
                                <li>
                                    <dropdown>
                                        <span slot="button">
                                            {{ str_limit(session('space')->name, 3) }} <i class="fas fa-caret-down fa-sm"></i>
                                        </span>
                                        <ul slot="menu" v-cloak>
                                            @foreach (Auth::user()->spaces as $space)
                                                <li>
                                                    <a href="/spaces/{{ $space->id }}">{{ $space->name }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </dropdown>
                                </li>
                            @endif
                            <li>
                                <dropdown>
                                    <span slot="button">
                                        <img src="{{ Auth::user()->avatar }}" class="avatar mr-05" /> <i class="fas fa-caret-down fa-sm"></i>
                                    </span>
                                    <ul slot="menu" v-cloak>
                                        <li>
                                            <a href="/imports">{{ __('models.imports') }}</a>
                                        </li>
                                        <li>
                                            <a href="/settings">{{ __('pages.settings') }}</a>
                                        </li>
                                        <li>
                                            <a href="/logout">{{ __('pages.log_out') }}</a>
                                        </li>
                                    </ul>
                                </dropdown>
                            </li>
                        </ul>
                    </div>
                </div>
            @else
                <div class="navigation">
                    <div class="wrapper">
                        <ul class="navigation__menu">
                            <li>
                                <a href="/" {!! (Request::path() == 'dashboard') ? 'class="active"' : '' !!}><img class="active" src="/storage/logo_250.png" style="height: 40px;"> <span class="hidden ml-05">{{ env('APP_NAME') }}</span></a>
                            </li>
                            <li>
                                <a href="/login" {!! (Request::path() == 'transactions') ? 'class="active"' : '' !!}><i class="far fa-sign-in-alt fa-sm color-green"></i> <span class="hidden ml-05">{{ __('general.login') }}</span></a>
                            </li>
                            <li>
                                <a href="/register" {!! (Request::path() == 'transactions') ? 'class="active"' : '' !!}><i class="far fa-user-plus fa-sm color-green"></i> <span class="hidden ml-05">{{ __('general.register') }}</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
            @if (Auth::check() && Auth::user()->verification_token)
                <div class="text-center" style="
                    padding: 15px;
                    color: #FFF;
                    background: #F86380;
                ">{!! __('general.verify_account') !!}</div>
            @endif
            @if (Auth::check() && Auth::user()->api_token == null)
                <div class="text-center" style="
                padding: 15px;
                color: #FFF;
                background: #F86380;
            ">{!! __('general.chrome_extension_warning') !!}</div>
            @endif
            @yield('body')
            @if (auth()->check())
                <div class="text-center mb-3">
                    <a class="fs-sm" href="/ideas/create">{{ __('general.know_how_to_make_this_app_better') }}</a>
                </div>
            @endif
        </div>
        <script src="/js/app.js"></script>
        @yield('scripts')
    </body>
</html>
