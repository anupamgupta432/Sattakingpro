<nav class="sidebar sidebar-offcanvas " id="sidebar">
  <ul class="nav">
<li class="nav-item nav-profile margine-t">
  <a href="#" class="nav-link">
    <div class="nav-profile-image">
      <img src="{{ asset($admin->image ?? 'images/profile.jpg') }}" alt="profile" />
      <span class="login-status online"></span>
      <!--change to offline or busy as needed-->
    </div>
    <div class="nav-profile-text d-flex flex-column">
      <span class="font-weight-bold mb-2">{{ $admin->name ?? 'Guest Admin' }}</span>
      <span class="text-secondary text-small">Admin Panel</span>
    </div>
    <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
  </a>
</li>

    
    <li class="nav-item">
      <a class="nav-link" href="/">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>

    <!-- User Management -->
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#management" aria-expanded="false" aria-controls="management">
        <span class="menu-title">User Management</span>
        <i class="mdi mdi-account-multiple menu-icon"></i>
      </a>
      <div class="collapse" id="management">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link" href="/users">View All Users List</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/userregister">New User Register</a>
          </li>
      <!-- <li class="nav-item">
            <a class="nav-link" href="/curd">Varification</a>
          </li> -->
        </ul>
      </div>
    </li>
    <!--agent managment-->
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#agentmanagement" aria-expanded="false" aria-controls="agentmanagement">
        <span class="menu-title">Vendor Management</span>
        <i class="mdi mdi-account-tie-outline menu-icon"></i>
      </a>
      <div class="collapse" id="agentmanagement">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link" href="/agent">View All Vendor List</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/agentregister">New Vendor Register</a>
          </li>
        </ul>
      </div>
    </li>    
  <!-- Profile Approvals -->
  <li class="nav-item">
    <a class="nav-link" data-bs-toggle="collapse" href="#approvals" aria-expanded="false" aria-controls="approvals">
      <span class="menu-title">Appointment History</span>
      <i class="mdi mdi-calendar-clock menu-icon"></i>
    </a>
    <div class="collapse" id="approvals">
      <ul class="nav flex-column sub-menu">
        <li class="nav-item">
          <a class="nav-link" href="/slotcreate">Slots Create</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/slotbook">Slots Book</a>
        </li>
      </ul>
    </div>
  </li> 
  <!-- Membership Plans -->
  <!--<li class="nav-item">
    <a class="nav-link" data-bs-toggle="collapse" href="#plans" aria-expanded="false" aria-controls="plans">
      <span class="menu-title">Membership Plans</span>
      <i class="mdi mdi-cash-multiple menu-icon"></i>
    </a>
    <div class="collapse" id="plans">
      <ul class="nav flex-column sub-menu">
        <li class="nav-item">
          <a class="nav-link" href="/plans">Plans</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/upgradepremium">View Subscriptions</a>
        </li>
      </ul>
    </div>
  </li> -->
  <!-- Match Requests-->
 <!-- <li class="nav-item">
    <a class="nav-link" data-bs-toggle="collapse" href="#request" aria-expanded="false" aria-controls="request">
      <span class="menu-title">Match Requests</span>
      <i class="mdi mdi-heart-outline menu-icon"></i>
    </a>
    <div class="collapse" id="request">
      <ul class="nav flex-column sub-menu">
        <li class="nav-item">
          <a class="nav-link" href="/connections">Connections</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/request">Requests</a>
        </li>
                <li class="nav-item">
          <a class="nav-link" href="/block">Blocked User</a>
        </li>
      </ul>
    </div>
  </li>  --> 
   <!-- Payment Management-->
   <li class="nav-item">
    <a class="nav-link" data-bs-toggle="collapse" href="#management1" aria-expanded="false" aria-controls="management1">
      <span class="menu-title">Transaction Section</span>
      <i class="mdi mdi-credit-card menu-icon"></i>
    </a>
    <div class="collapse" id="management1">
      <ul class="nav flex-column sub-menu">
        <li class="nav-item">
          <a class="nav-link" href="/transactions">Transaction List</a>
        </li>
       <!-- <li class="nav-item">
          <a class="nav-link" href="/wallet">Wallet</a>
        </li>-->
      </ul>
    </div>
  </li> 
  <!--Notification-->
    <li class="nav-item">
    <a class="nav-link" data-bs-toggle="collapse" href="#notification" aria-expanded="false" aria-controls="notification">
      <span class="menu-title">Notification</span>
      <i class="mdi mdi-bell-outline menu-icon"></i>
    </a>
   <div class="collapse" id="notification">
      <ul class="nav flex-column sub-menu">
        <li class="nav-item">
          <a class="nav-link" href="/shownotification">Show Notification</a>
        </li>
         <li class="nav-item">
          <a class="nav-link" href="/text-slider">Text Slider </a>
        </li>
         <li class="nav-item">
          <a class="nav-link" href="/sendnotificationto">Send Notification</a>
        </li>
      </ul>
    </div>
  </li>
   <!--G-->
   <li class="nav-item">
    <a class="nav-link" data-bs-toggle="collapse" href="#likes" aria-expanded="false" aria-controls="notification">
      <span class="menu-title">Grievance Section
</span>
      <i class="mdi mdi-clipboard-alert-outline menu-icon"></i>
    </a>
   <div class="collapse" id="likes">
      <ul class="nav flex-column sub-menu">
       <!-- <li class="nav-item">
          <a class="nav-link" href="/sendlikes">Send Grievance</a>
        </li>-->
        <li class="nav-item">
          <a class="nav-link" href="/feedback">Show Grievance</a>
        </li>
      </ul>
    </div>
  </li> 
<!-- Term & Condition -->
<li class="nav-item">
  <a class="nav-link" data-bs-toggle="collapse" href="#reported" aria-expanded="false" aria-controls="reported">
    <span class="menu-title">Privacy & Policy</span>
    <i class="mdi mdi-clipboard-text menu-icon"></i>
  </a>
  <div class="collapse" id="reported">
    <ul class="nav flex-column sub-menu">
      <li class="nav-item">
        <a class="nav-link" href="/termcondition"> Term & Condition </a>
      </li>
    <!--  <li class="nav-item">
        <a class="nav-link" href="/reports"> Repots </a>
      </li> -->
    </ul>
  </div>
</li>
<!--Banner-->
<li class="nav-item">
  <a class="nav-link" data-bs-toggle="collapse" href="#banner" aria-expanded="false" aria-controls="banner">
    <span class="menu-title">UI Banner</span>
    <i class="mdi mdi-image-area menu-icon"></i>
  </a>
  <div class="collapse" id="banner">
    <ul class="nav flex-column sub-menu">
      <li class="nav-item">
        <a class="nav-link" href="/banner-ui"> Banner images </a>
      </li>
    <!--  <li class="nav-item">
        <a class="nav-link" href="/reports"> Repots </a>
      </li> -->
    </ul>
  </div>
</li>
<!-- Settings -->
<!--<li class="nav-item">
  <a class="nav-link" data-bs-toggle="collapse" href="#settings" aria-expanded="false" aria-controls="settings">
    <span class="menu-title">Settings</span>
    <i class="menu-arrow"></i>
    <i class="mdi mdi-cog-outline menu-icon"></i>
  </a>
  <div class="collapse" id="settings">
    <ul class="nav flex-column sub-menu">
      <li class="nav-item">
        <a class="nav-link" href="/religion"> Religion </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/blood"> Blood Group </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/marital"> Marital Status </a>
      </li>
    </ul>
  </div>
</li>-->
<!-- user profile -->
<!--<li class="nav-item">
  <a class="nav-link" data-bs-toggle="collapse" href="#profile" aria-expanded="false" aria-controls="profile">
    <span class="menu-title">User profile</span>
    <i class="menu-arrow"></i>
    <i class="mdi mdi-account-details  menu-icon"></i>
  </a>
  <div class="collapse" id="profile">
    <ul class="nav flex-column sub-menu">
      <li class="nav-item">
        <a class="nav-link" href="/profile"> profile </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/blood"> Blood Group </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/marital"> Marital Status </a>
      </li>
    </ul>
  </div>
</li>-->

<!-- Admin User -->
 <!-- <li class="nav-item">
    <a class="nav-link" data-bs-toggle="collapse" href="#admin" aria-expanded="false" aria-controls="admin">
      <span class="menu-title">Admin Users</span>
      <i class="mdi mdi-account-key menu-icon"></i>
    </a>
    <div class="collapse" id="admin">
      <ul class="nav flex-column sub-menu">
        <li class="nav-item">
          <a class="nav-link" href="/basic_elements">Manage Admin Roles</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/basic_elements">Permissions / Access Control</a>
        </li>
      </ul>
    </div>
  </li>--> 
<li class="nav-item">
  <a class="nav-link" href="/Logout">
     <span class="menu-title">Logout</span>
     <i class="mdi mdi-logout menu-icon"></i>
  </a>
</li>


  <!-- extra -->
<!--<li class="nav-item">
  <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
    <span class="menu-title">Basic UI Elements</span>
    <i class="menu-arrow"></i>
    <i class="mdi mdi-crosshairs-gps menu-icon"></i>
    <i class="fa-solid fa-crosshairs"></i>
  </a>
  <div class="collapse" id="ui-basic">
    <ul class="nav flex-column sub-menu">
      <li class="nav-item">
        <a class="nav-link" href="/buttons">Buttons</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/dropdowns">Dropdowns</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/typography">Typography</a>
      </li>
    </ul>
  </div>
</li>-->
<!--<li class="nav-item">
  <a class="nav-link" data-bs-toggle="collapse" href="#icons" aria-expanded="false" aria-controls="icons">
    <span class="menu-title">Icons</span>
    <i class="mdi mdi-contacts menu-icon"></i>
  </a>
  <div class="collapse" id="icons">
    <ul class="nav flex-column sub-menu">
      <li class="nav-item">
        <a class="nav-link" href="/font_awesome">Font Awesome</a>
      </li>
    </ul>
  </div>
</li>
<li class="nav-item">
  <a class="nav-link" data-bs-toggle="collapse" href="#forms" aria-expanded="false" aria-controls="forms">
    <span class="menu-title">Forms</span>
    <i class="mdi mdi-format-list-bulleted menu-icon"></i>
  </a>
  <div class="collapse" id="forms">
    <ul class="nav flex-column sub-menu">
      <li class="nav-item">
        <a class="nav-link" href="/basic_elements">Form Elements</a>
      </li>
    </ul>
  </div>
</li>-->
<!--<li class="nav-item">
  <a class="nav-link" data-bs-toggle="collapse" href="#charts" aria-expanded="false" aria-controls="charts">
    <span class="menu-title">Charts</span>
    <i class="mdi mdi-chart-bar menu-icon"></i>
  </a>
  <div class="collapse" id="charts">
    <ul class="nav flex-column sub-menu">
      <li class="nav-item">
        <a class="nav-link" href="/charts">ChartJs</a>
      </li>
    </ul>
  </div>
</li>-->

<!--<li class="nav-item">
  <a class="nav-link" data-bs-toggle="collapse" href="#tables" aria-expanded="false" aria-controls="tables">
    <span class="menu-title">Tables</span>
    <i class="mdi mdi-table-large menu-icon"></i>
  </a>
  <div class="collapse" id="tables">
    <ul class="nav flex-column sub-menu">
      <li class="nav-item">
        <a class="nav-link" href="/tables">Basic table</a>
      </li>
    </ul>
  </div>
</li>-->
<!--user page-->
<!--<li class="nav-item">
  <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
    <span class="menu-title">User Pages</span>
    <i class="menu-arrow"></i>
    <i class="mdi mdi-lock menu-icon"></i>
  </a>
  <div class="collapse" id="auth">
    <ul class="nav flex-column sub-menu">
      <li class="nav-item">
        <a class="nav-link" href="/blank-page"> Blank Page </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/signin"> Login </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/register"> Register </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/error-404"> 404 </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/error-500"> 500 </a>
      </li>
    </ul>
  </div>
</li>-->

  </ul>

   
</nav>