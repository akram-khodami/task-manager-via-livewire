<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'TaskManager' }}</title>
    <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        .gradient-custom-2 {
            /* fallback for old browsers */
            background: #fccb90;

            /* Chrome 10-25, Safari 5.1-6 */
            background: -webkit-linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);

            /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
            background: linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);
        }

        @media (min-width: 768px) {
            .gradient-form {
                height: 100vh !important;
            }
        }

        @media (min-width: 769px) {
            .gradient-custom-2 {
                border-top-right-radius: .3rem;
                border-bottom-right-radius: .3rem;
            }
        }

        /*For RTL Style*/
        .btn-group-reverse {
            direction: ltr;
        }

        th[style*="cursor: pointer"]:hover {
            background-color: #f0f0f0;
            transition: background-color 0.2s;
        }

        .modal-header .btn-close {
            margin: 0;
        }

        [dir="rtl"] .modal-header .btn-close {
            margin-left: 0 !important;
            margin-right: auto !important;
        }
    </style>
    @livewireStyles
</head>

<body dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
@include('components.layouts.sidebar')
<div class="container mt-5 mb-5">
    @include('components.layouts.messages')
    <livewire:message-box/>

    <div class="row">
        <div class="col-md-12">
            {{ $slot }}
        </div>
    </div>
</div>
<!---start scripts--->
<script src="{{ url('js/bootstrap.bundle.min.js') }}"></script>
@livewireScripts
<script></script>
<!---end scripts--->
</body>

</html>
