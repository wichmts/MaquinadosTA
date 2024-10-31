<!--
=========================================================
 Paper Dashboard - v2.0.0
=========================================================

 Product Page: https://www.creative-tim.com/product/paper-dashboard
 Copyright 2019 Creative Tim (https://www.creative-tim.com)
 UPDIVISION (https://updivision.com)
 Licensed under MIT (https://github.com/creativetimofficial/paper-dashboard/blob/master/LICENSE)

 Coded by Creative Tim

=========================================================

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software. -->



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />

    <link rel="apple-touch-icon" sizes="76x76" href="/paper/img/favicon.png">
    <link rel="icon" type="image/png" href="/paper/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>
        {{ __('Multi-Channel') }}
    </title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
        name='viewport' />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>    
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oxygen:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@200&family=Yeseva+One&display=swap" rel="stylesheet"> 


    {{-- <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.css">


    <link href="{{ asset('paper') }}/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('paper') }}/css/paper-dashboard.css?v=2.0.0" rel="stylesheet" />
    <link href="{{ asset('paper') }}/demo/demo.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" /> 
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">   
    
    {{-- <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@200&display=swap" rel="stylesheet">    --}}

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@voerro/vue-tagsinput@2.7.0/dist/style.css">

    {{-- <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" /> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>


    <script src="https://unpkg.com/vue-select@latest"></script>
    <link rel="stylesheet" href="https://unpkg.com/vue-select@latest/dist/vue-select.css">

    <style >
    
    /* i{
        font-size: 16px !important;
        letter-spacing: 4px;
    } */
    button, a{
        margin-right: 5px !important;
    }
    .table td{
        /* font-size: 14px !important; */
        font-size: calc(.5vw + .35rem) !important;
    }
    @font-face {
        font-family: 'HKGrotesk';
        src: url('/paper/fonts/HKGrotesk-Medium.otf') format('opentype');
        font-weight: normal;
        font-style: normal;
    }
    @font-face {
        font-family: 'HKGrotesk';
        src: url('/paper/fonts/HKGrotesk-Bold.otf') format('opentype');
        font-weight: bold;
        font-style: normal;
    }
    * {
       font-family: 'HKGrotesk', sans-serif;
    }
    </style>

</head>

<body class="{{ $class }}">
    @if (session('error'))
    @endif

    @if(auth()->check())
        @include('layouts.page_templates.auth')
    @else
        @include('layouts.page_templates.guest')
    @endif


    <!--   Core JS Files   -->
    
    <script src="{{ asset('paper') }}/js/core/jquery.min.js"></script>
    <script src="{{ asset('paper') }}/js/core/popper.min.js"></script>
    <script src="{{ asset('paper') }}/js/core/bootstrap.min.js"></script>
    <script src="{{ asset('paper') }}/js/plugins/perfect-scrollbar.jquery.min.js"></script>
    <!-- Chart JS -->
    <script src="{{ asset('paper') }}/js/plugins/chartjs.min.js"></script>
    
    <!--  Notifications Plugin    -->
    <script src="{{ asset('paper') }}/js/plugins/bootstrap-notify.js"></script>
    <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('paper') }}/js/paper-dashboard.min.js?v=2.0.0" type="text/javascript"></script>
    <!-- Paper Dashboard DEMO methods, don't include it in your project! -->
    <script src="{{ asset('paper') }}/demo/demo.js"></script>
    
    {{-- VUE JS --}}
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    {{-- SWEET ALERT  --}}
    <script src="{{ asset('paper') }}/js/plugins/sweetalert.min.js"></script>
    
    {{-- AXIOS --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    {{-- VUE CURRENCY --}}
    <script src="https://unpkg.com/vue-currency-filter@3.2.3/dist/vue-currency-filter.iife.js"></script>
    {{-- MOMENT --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    {{-- CALENDARIO  --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js"></script>
    {{-- PROGRESS BAR --}}
    <script src="https://rawgit.com/kimmobrunfeldt/progressbar.js/1.0.0/dist/progressbar.js"></script>
    {{-- SELECT2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    {{-- CKEDITOR --}}
    {{-- <script src="//cdn.ckeditor.com/4.20.2/basic/ckeditor.js"></script> --}}
    <script src="//cdn.ckeditor.com/4.20.2/standard/ckeditor.js"></script>
    
    {{-- GOOGLEMAPS  --}}
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAyceN_EFz-QqtEcZcLd0H1M7IxIGtUkJ8&libraries=places"></script>
   
    {{-- DATA TABLES --}}
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@voerro/vue-tagsinput@2.7.0/dist/voerro-vue-tagsinput.js"></script>

    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

    {{-- OPENAPAY --}}
    <script type="text/javascript" src="https://js.openpay.mx/openpay.v1.min.js"></script>
    <script type='text/javascript' src="https://js.openpay.mx/openpay-data.v1.min.js"></script>

    

    </body>
    
    <script type="text/javascript">
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + '{{auth()->user() ? auth()->user()->api_token : ""}}';
        if (VueCurrencyFilter) {
                Vue.use(VueCurrencyFilter, {
                    symbol: "$",
                    thousandsSeparator: ",",
                    fractionCount: 2,
                    fractionSeparator: ".",
                    symbolPosition: "front",
                    symbolSpacing: false
                })
            }

        Vue.directive('select2', {
          inserted(el) {
                $(el).on('select2:select', () => {
                    const event = new Event('change', { bubbles: true, cancelable: true });
                    el.dispatchEvent(event);
                });
                $(el).on('select2:unselect', () => {
                    const event = new Event('change', {bubbles: true, cancelable: true})
                    el.dispatchEvent(event)
                })
            },
        });


        
    </script>

    @stack('scripts')

    @yield('vuescripts')

    <script>
        // const DOMstrings = {stepsBtnClass:"multisteps-form__progress-btn",stepsBtns:document.querySelectorAll(".multisteps-form__progress-btn"),stepsBar:document.querySelector(".multisteps-form__progress"),stepsForm:document.querySelector(".multisteps-form__form"),stepsFormTextareas:document.querySelectorAll(".multisteps-form__textarea"),stepFormPanelClass:"multisteps-form__panel",stepFormPanels:document.querySelectorAll(".multisteps-form__panel"),stepPrevBtnClass:"js-btn-prev",stepNextBtnClass:"js-btn-next"},removeClasses=(s,t)=>{s.forEach(s=>{s.classList.remove(t)})},findParent=(s,t)=>{let e=s;for(;!e.classList.contains(t);)e=e.parentNode;return e},getActiveStep=s=>Array.from(DOMstrings.stepsBtns).indexOf(s),setActiveStep=s=>{removeClasses(DOMstrings.stepsBtns,"js-active"),DOMstrings.stepsBtns.forEach((t,e)=>{e<=s&&t.classList.add("js-active")})},getActivePanel=()=>{let s;return DOMstrings.stepFormPanels.forEach(t=>{t.classList.contains("js-active")&&(s=t)}),s},setActivePanel=s=>{removeClasses(DOMstrings.stepFormPanels,"js-active"),DOMstrings.stepFormPanels.forEach((t,e)=>{e===s&&(t.classList.add("js-active"),setFormHeight(t))})},formHeight=s=>{const t=s.offsetHeight;DOMstrings.stepsForm.style.height=`${t}px`},setFormHeight=()=>{const s=getActivePanel();formHeight(s)};DOMstrings.stepsBar.addEventListener("click",s=>{const t=s.target;if(!t.classList.contains(`${DOMstrings.stepsBtnClass}`))return;const e=getActiveStep(t);setActiveStep(e),setActivePanel(e)}),DOMstrings.stepsForm.addEventListener("click",s=>{const t=s.target;if(!t.classList.contains(`${DOMstrings.stepPrevBtnClass}`)&&!t.classList.contains(`${DOMstrings.stepNextBtnClass}`))return;const e=findParent(t,`${DOMstrings.stepFormPanelClass}`);let r=Array.from(DOMstrings.stepFormPanels).indexOf(e);t.classList.contains(`${DOMstrings.stepPrevBtnClass}`)?r--:r++,setActiveStep(r),setActivePanel(r)}),window.addEventListener("load",setFormHeight,!1),window.addEventListener("resize",setFormHeight,!1);
    </script>
    @include('layouts.navbars.fixed-plugin-js')
</body>

</html>
