
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>SAT | @yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" type="image/png" href="">
        {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
        <!-- CSS LINKS -->
        @include('shared.css_links.css_links')
        <style>
            .modal-xl-custom{
                width: 95% !important;
                min-width: 90% !important;
            }
        </style>
        
    </head>
    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            @include('shared.pages.header')
            @include('shared.pages.admin_nav')
            <!-- Global Spinner -->
         
            @yield('content_page')
            @include('shared.pages.footer')
            <div class="modal" id="modalSpinner" data-bs-backdrop="static">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content pt-3">
                        <p class="spinner-border spinner-border-xl text-center mx-auto"></p>
                        {{-- <p class="mx-auto">Logging out...</p> --}}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- JS LINKS -->
        @include('shared.js_links.js_links')
       
        @yield('js_content')
       
    </body>
    
</html>