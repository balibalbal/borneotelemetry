<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="{{ url('ltr') }}"
  data-theme="{{ url('theme-default') }}"
  data-assets-path="{{ url('backend/assets/') }}"
  data-template="{{ url('vertical-menu-template') }}">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Borneotelemetry - @yield('title', 'Dashboard')</title>
    

    <meta name="description" content="" />
    @include('includes.style')
    
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
      <div class="layout-container">
        <!-- Navbar -->
        @include('includes.navbar')
        <!-- / Navbar -->

        <!-- Layout container -->
        <div class="main-content">
          <!-- Content wrapper -->
          {{-- <div class="content-wrapper"> --}}
            <!-- Menu -->
            {{-- @include('includes.menu') --}}
            <!-- / Menu -->

            <!-- Content -->

            {{-- <div class="container-xxl flex-grow-1 container-p-y"> --}}
              @yield('content')
            {{-- </div> --}}
            <!--/ Content -->

            <!-- Footer -->
            @include('includes.footer')
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          {{-- </div> --}}
          <!--/ Content wrapper -->
        </div>

        <!--/ Layout container -->
      </div>
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>

    @include('includes.script')
  </body>
  @vite('resources/js/app.js')
</html>
