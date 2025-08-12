<!DOCTYPE html>

<html
    lang="en"
    class="light-style layout-wide customizer-hide"
    dir="{{ url('ltr') }}"
    data-theme="{{ url('theme-default') }}"
    data-assets-path="{{ url('backend/assets/') }}"
    data-template="{{ url('vertical-menu-template') }}">

  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Selamat Datang di Borneotelemetry</title>

    <meta name="description" content="" />

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

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <!-- Vendor -->
    <link rel="stylesheet" href="{{ url('backend/assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />

    <style>
      .bg-linear {
          background: linear-gradient(to right, #f7f7f9, #56c4f7);
          padding: 20px;
      }
    </style>
    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ url('backend/assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ url('backend/assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ url('backend/assets/vendor/js/template-customizer.js') }}"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ url('backend/assets/js/config.js') }}"></script>
  </head>

  <body>
    <!-- Content -->

    <div class="position-relative">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
          <!-- Login -->
          <div class="card p-2">
            <!-- Logo -->
            <div class="app-brand justify-content-center mt-2 mb-2">
              <img src="{{ url('backend/img/borneo-logo.png') }}" class="img-fluid" style="width: 250px;">
            </div>
            <!-- /Logo -->

            <div class="card-body mt-2 justify-content-center text-center">
              <h5 class="mb-2">Welcome to Borneotelemetry! 👋</h5>
              <p class="mb-4">Please sign-in to your account and start the adventure</p>

              @if(session('pesan'))
              <div class="alert alert-danger alert-dismissible" role="alert">
                  <i class="mdi mdi-alert"></i> {{ session('pesan') }}.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                  </button>
              </div>
              @endif

              @if(session('status'))
              <div class="alert alert-success alert-dismissible" role="alert">
                  <i class="mdi mdi-check-circle"></i> {{ session('status') }}.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                  </button>
              </div>
              @endif
              
              <form class="mb-3" action="/login" method="POST">
                  @csrf
                <div class="form-floating form-floating-outline mb-3">
                  <input
                    type="text"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    placeholder="Enter your email or username"
                    autofocus />
                  <label for="email">Email or Username</label>
                  @error('email')<div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                  <div class="form-password-toggle">
                    <div class="input-group input-group-merge">
                      <div class="form-floating form-floating-outline">
                        <input
                          type="password"
                          id="password"
                          class="form-control"
                          name="password"
                          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                          aria-describedby="password" />
                        <label for="password">Password</label>
                        @error('password')<div class="text-danger">{{ $message }}</div> @enderror
                      </div>
                      <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
                    </div>
                  </div>
                </div>
                
                <div class="mb-3">
                  <button class="btn btn-dark d-grid w-100"><i class="mdi mdi-lock me-sm-1"></i> Sign in</button>
                </div>
              </form>
            </div>
          </div>
          <!-- /Login -->
          <img
            alt="mask"            
            src="{{ url('backend/assets/img/illustrations/auth-basic-login-mask-light.png') }}"
            class="authentication-image d-none d-lg-block"
            />
        </div>
      </div>
    </div>

    <!-- / Content -->

    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ url('backend/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/js/menu.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ url('backend/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ url('backend/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ url('backend/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ url('backend/assets/js/pages-auth.js') }}"></script>
  </body>
</html>
