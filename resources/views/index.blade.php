<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- pluging:Tittle , Icon -->
    @include('components.head')
    <!-- plugins:css -->
<!-- CSS Files -->
<link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">

<!-- Plugin CSS for this page -->
<link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">

    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css')}}">
    <!-- End layout styles -->
<!--   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> -->
  <style>
  @media (max-width: 768px) {
    .content-wrapper {
      width: 104% !important;
    }
  }
</style>

  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_navbar.html -->
      <!-- <%- include('../partials/_navbar') %>  -->
      @include('components.navbar')
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <!-- <%- include('../partials/_sidebar') %>  -->
         @include('components.sidebar')
        <!-- partial -->
        <div class="main-panel">
            <!-- content-wrapper -->
           <div class="content-wrapper" style="width: 102.5%;">
                <div class="page-header">
                  <h3 class="page-title">
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                      <i class="mdi mdi-home"></i>
                    </span> Dashboard
                  </h3>
                  <nav aria-label="breadcrumb">
                    <ul class="breadcrumb">
                      <li class="breadcrumb-item active" aria-current="page">
                        <span></span><i class="mdi alert-circle-outline icon-sm text-primary align-middle"></i>
                      </li>
                    </ul>
                  </nav>
                </div>
               <!-- content Add -->
               <div class="row">
                <div class="col-md-4 stretch-card grid-margin">
                  <div class="card bg-gradient-danger card-img-holder text-white">
                    <div class="card-body">
                      <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                      <h4 class="font-weight-normal mb-3">Total Users <i class="mdi mdi-account-group  mdi-24px float-end"></i>
                      </h4>
                      <h2 class="mb-0">{{ $userCount }}</h2>
                     <!-- <h6 class="card-text">Increased by 60%</h6>-->
                    </div>
                  </div>
                </div>
               <div class="col-md-4 stretch-card grid-margin">
                  <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                      <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                      <h4 class="font-weight-normal mb-3">Total Vendors<i class="mdi mdi-account-tie-outline mdi-24px float-end"></i>
                      </h4>
                      <h2 class="mb-1">{{ $userCount }}</h2>
                      <h6 class="card-text"></h6>
                    </div>
                  </div>
                </div>
              <!--  <div class="col-md-4 stretch-card grid-margin">
                  <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                      <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                      <h4 class="font-weight-normal mb-3">Match Profile<i class="fa fa-handshake-o mdi-24px float-end"></i>
                      </h4>
                      <h2 class="mb-0">454</h2>
                      <h6 class="card-text"></h6>
                    </div>
                  </div>
                </div>-->
                <div class="col-md-4 stretch-card grid-margin">
                  <div class="card bg-gradient-success card-img-holder text-white">
                    <div class="card-body">
                      <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                      <h4 class="font-weight-normal mb-3">Active Users <i class="mdi  mdi-account-multiple  mdi-24px float-end"></i>
                      </h4>
                      <h2 class="mb-0">{{ $activeCount }}</h2>
                      <!--<h6 class="card-text">Increased by 5%</h6>-->
                    </div>
                  </div>
                </div>
              </div>
              <!-- second row -->
           <!--   <div class="row">
                <div class="col-md-4 stretch-card grid-margin">
                  <div class="card bg-gradient-danger card-img-holder text-white">
                    <div class="card-body">
                      <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                      <h4 class="font-weight-normal mb-3">Ignored Profile <i class="mdi mdi-heart-broken  mdi-24px float-end"></i>
                      </h4>
                      <h2 class="mb-4">15</h2>
                      <h6 class="card-text">Increased by 60%</h6>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 stretch-card grid-margin">
                  <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                      <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                      <h4 class="font-weight-normal mb-3">Subscribe Users <i class="mdi mdi-account-check mdi-24px float-end"></i>
                      </h4>
                      <h2 class="mb-4">334</h2>
                      <h6 class="card-text">Decreased by 10%</h6>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 stretch-card grid-margin">
                  <div class="card bg-gradient-success card-img-holder text-white">
                    <div class="card-body">
                      <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                      <h4 class="font-weight-normal mb-3">Unsubcribe Users<i class="mdi  mdi-bell-off mdi-24px float-end"></i>
                        <!-- icon end -->
                        <!-- <svg viewBox="0 0 24 24" height="24" width="24" preserveAspectRatio="xMidYMid meet" class="" fill="none"><title>status-outline</title><path d="M13.5627 3.13663C13.6586 2.59273 14.1793 2.22466 14.7109 2.37438C15.7904 2.67842 16.8134 3.16256 17.7359 3.80858C18.9322 4.64624 19.9304 5.73574 20.6605 7.0005C21.3906 8.26526 21.8348 9.67457 21.9619 11.1294C22.06 12.2513 21.9676 13.3794 21.691 14.4662C21.5548 15.0014 20.9756 15.2682 20.4567 15.0793C19.9377 14.8903 19.6769 14.317 19.7996 13.7785C19.9842 12.9693 20.0421 12.1343 19.9695 11.3035C19.8678 10.1396 19.5124 9.01218 18.9284 8.00038C18.3443 6.98857 17.5457 6.11697 16.5887 5.44684C15.9055 4.96844 15.1535 4.601 14.3605 4.3561C13.8328 4.19314 13.4668 3.68052 13.5627 3.13663Z" fill="currentColor"></path><path d="M18.8943 17.785C19.3174 18.14 19.3758 18.7749 18.9803 19.1604C18.1773 19.9433 17.2465 20.5872 16.2257 21.0631C14.9022 21.6802 13.4595 22 11.9992 21.9999C10.5388 21.9998 9.09621 21.6798 7.77275 21.0625C6.75208 20.5865 5.82137 19.9424 5.01843 19.1595C4.62302 18.7739 4.68155 18.139 5.10467 17.784C5.52779 17.4291 6.15471 17.4898 6.55964 17.8654C7.16816 18.4298 7.86233 18.8974 8.61817 19.25C9.67695 19.7438 10.831 19.9998 11.9993 19.9999C13.1676 20 14.3217 19.7442 15.3806 19.2505C16.1365 18.898 16.8307 18.4304 17.4393 17.8661C17.8443 17.4906 18.4712 17.43 18.8943 17.785Z" fill="currentColor"></path><path d="M3.54265 15.0781C3.02367 15.267 2.44458 15.0001 2.30844 14.4649C2.03202 13.3781 1.93978 12.2502 2.03794 11.1283C2.16521 9.67361 2.60953 8.26444 3.33966 6.99984C4.06979 5.73523 5.06802 4.64587 6.2642 3.80832C7.18668 3.1624 8.20962 2.67833 9.28902 2.37434C9.82063 2.22462 10.3413 2.59271 10.4372 3.1366C10.5331 3.6805 10.1671 4.19311 9.63938 4.35607C8.84645 4.60094 8.09446 4.96831 7.41133 5.44663C6.45439 6.11667 5.65581 6.98816 5.0717 7.99985C4.4876 9.01153 4.13214 10.1389 4.03032 11.3026C3.95764 12.1334 4.01547 12.9683 4.19986 13.7775C4.32257 14.3159 4.06162 14.8892 3.54265 15.0781Z" fill="currentColor"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M11.9999 16C14.2091 16 15.9999 14.2092 15.9999 12C15.9999 9.79088 14.2091 8.00002 11.9999 8.00002C9.7908 8.00002 7.99994 9.79088 7.99994 12C7.99994 14.2092 9.7908 16 11.9999 16ZM11.9999 18C15.3136 18 17.9999 15.3137 17.9999 12C17.9999 8.68631 15.3136 6.00002 11.9999 6.00002C8.68623 6.00002 5.99994 8.68631 5.99994 12C5.99994 15.3137 8.68623 18 11.9999 18Z" fill="currentColor"></path></svg>-->
                       
                      </h4>
                      <h2 class="mb-4"></h2>
                      <h6 class="card-text"></h6>
                    </div>
                  </div>
                </div>
              </div>
               <!--content end -->
              </div>
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
          <!-- <%- include('../partials/_footer') %> -->
           @include('components.footer')
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
<script src="{{ asset('assets/js/auth.js') }}"></script>
<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>

<!-- Plugin js for this page -->
<script src="{{ asset('assets/vendors/chart.js/chart.umd.js') }}"></script>
<script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<!-- inject:js -->
<script src="{{ asset('assets/js/off-canvas.js') }}"></script>
<script src="{{ asset('assets/js/misc.js') }}"></script>
<script src="{{ asset('assets/js/settings.js') }}"></script>
<script src="{{ asset('assets/js/todolist.js') }}"></script>
<script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>
<!-- endinject -->

<!-- Custom js for this page -->
<script src="{{ asset('assets/js/dashboard.js') }}"></script>


     <!-- <%- include('../partials/_script') %>  -->
      @include('components.script')
    <!-- End custom js for this page -->
  </body>
</html>