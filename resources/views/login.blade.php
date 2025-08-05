<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Admin Login</title>
  
  @include('components.head')

  <!-- plugins:css -->
  <link rel="stylesheet" href="{{ asset('assets/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">
  <!-- Layout styles -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth">
        <div class="row flex-grow">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left p-5">
              <div class="brand-logo">
                <img src="{{ asset('assets/images/logo_.png') }}">
              </div>
              <h4>Hello! Let's get started</h4>
              <h6 class="font-weight-light">Sign in to continue.</h6>

              <!-- ✅ Laravel login form -->
              <form class="pt-3" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                  <input type="email" name="email" class="form-control form-control-lg" placeholder="Email" required>
                </div>

                <div class="form-group position-relative">
                  <input type="password" name="password" class="form-control form-control-lg pr-5" id="exampleInputPassword1" placeholder="Password" required>
                  <i class="fa fa-eye toggle-password" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;"></i>
                </div>

                <div class="mt-3 d-grid gap-2">
                  <button class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn" type="submit">Sign in</button>
                </div>

                <div class="my-2 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input type="checkbox" class="form-check-input"> Keep me signed in
                    </label>
                  </div>
                </div>
              </form>
              <!-- End of form -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- plugins:js -->
  <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
  <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
  <script src="{{ asset('assets/js/misc.js') }}"></script>
  <script src="{{ asset('assets/js/settings.js') }}"></script>
  <script src="{{ asset('assets/js/todolist.js') }}"></script>
  <script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>

  <!-- Toggle password visibility -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const toggle = document.querySelector(".toggle-password");
      const passwordInput = document.querySelector("#exampleInputPassword1");

      toggle.addEventListener("click", function () {
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
        passwordInput.setAttribute("type", type);
        this.classList.toggle("fa-eye");
        this.classList.toggle("fa-eye-slash");
      });
    });
  </script>

  <!-- ✅ Flash messages -->
  @if(session('error'))
    <script>alert("{{ session('error') }}");</script>
  @endif

  @if(session('success'))
    <script>alert("{{ session('success') }}");</script>
  @endif

  @include('components.script')
</body>
</html>
