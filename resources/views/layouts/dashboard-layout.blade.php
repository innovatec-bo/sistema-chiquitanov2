<!DOCTYPE html>
<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="dark-style layout-navbar-fixed layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{asset('admin-theme')}}/"
  data-template="vertical-menu-template-no-customizer"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <meta name="description" content="" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('admin-theme/img/favicon/favicon.ico')}}" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    @vite(['resources/sass/serebo.dashboard.scss'])
    @livewireStyles
    <script src="{{asset('admin-theme/vendor/js/helpers.js')}}"></script>
    <script src="{{asset('admin-theme/js/config.js')}}"></script>
    <style>
      .overlay {
          height: 100%;
          left: 0;
          position: absolute;
          top: 0;
          width: 100%;
          border-radius: 0.25rem;
          align-items: center;
          background-color: rgb(37 41 60 / 74%);
          /* display: flex; */
          justify-content: center;
          z-index: 50;
      }
    </style>
    @yield('css')
  </head>
  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        <x-dashboard-menu/>
        <!-- / Menu -->
        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          <x-dashboard-navbar/>
          <!-- / Navbar -->
          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              {{-- {{$slot}} --}}
              @yield('content')
            </div>
            <!-- / Content -->
            <!-- Footer -->
            <x-dashboard-footer/>
            <!-- / Footer -->
            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>
      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    @vite(['resources/js/serebo.dashboard.core.js'])
    <script src="{{ asset('admin-theme/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('admin-theme/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('admin-theme/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('admin-theme/vendor/libs/i18n/i18n.js') }}"></script>
    {{-- <script src="{{ asset('admin-theme/vendor/libs/typeahead-js/typeahead.js') }}"></script> --}}
    <script src="{{ asset('admin-theme/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('admin-theme/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('admin-theme/js/main.js') }}"></script>
    @vite(['resources/js/serebo.dashboard.js'])
    <livewire:modals/>
    @livewireScripts
    @stack('scripts')
    @yield('js')
  </body>
</html>