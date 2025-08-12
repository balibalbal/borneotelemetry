<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ url('backend/assets/img/favicon/favicon.ico') }}" />

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
  href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
  rel="stylesheet" />

<!-- Icons -->
<link rel="stylesheet" href="{{ url('backend/assets/vendor/fonts/materialdesignicons.css') }}" />
<link rel="stylesheet" href="{{ url('backend/assets/vendor/fonts/flag-icons.css') }}" />

<!-- Menu waves for no-customizer fix -->
<link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/node-waves/node-waves.css') }}" />

<!-- Core CSS -->
<link rel="stylesheet" href="{{ url('backend/assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
<link rel="stylesheet" href="{{ url('backend/assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css" />

<link rel="stylesheet" href="{{ url('backend/assets/css/demo.css') }}" />
<link rel="stylesheet" href="{{ url('backend/assets/css/speedtrack.css') }}" />

<!-- Vendors CSS -->
<link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
<link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
<link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
<link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/swiper/swiper.css') }}" />
<link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
<link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/flatpickr/flatpickr.css') }}" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Page CSS -->
<link rel="stylesheet" href="{{ url('backend/assets/vendor/css/pages/cards-statistics.css') }}" />
<link rel="stylesheet" href="{{ url('backend/assets/vendor/css/pages/cards-analytics.css') }}" />

<!-- Row Group CSS -->
<link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />
<!-- Form Validation -->
<link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />

<!-- Helpers -->
<script src="{{ url('backend/assets/vendor/js/helpers.js') }}"></script>
<!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
<!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
<script src="{{ url('backend/assets/vendor/js/template-customizer.js') }}"></script>
<!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
<script src="{{ url('backend/assets/js/config.js') }}"></script>

@stack('style')