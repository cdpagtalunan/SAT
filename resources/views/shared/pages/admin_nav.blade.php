
<aside class="main-sidebar sidebar-dark-navy elevation-4" style="height: 100vh">

    <!-- System title and logo -->
    <a href="" class="brand-link text-center">
        <span class="brand-text font-weight-light font-size"><h5>Shipment Confirmation</h5></span> 
    </a> <!-- System title and logo -->

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview">
                    <a href="/RapidX/" class="nav-link">
                        <i class="nav-icon fa-solid fa-arrow-left"></i>
                        <p>RapidX</p>
                    </a>
                </li>
            </ul>
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                {{-- <li class="nav-item has-treeview">
                    <a href="{{ route('home') }}" class="nav-link">
                        <i class="nav-icon fa-solid fa-gauge-high"></i>
                        <p>Home</p>
                    </a>
                </li> --}}

                <li class="nav-item has-treeview">
                    <a href="{{ route('sat') }}" class="nav-link">
                        <i class="nav-icon fa-regular fa-file-lines"></i>
                        <p>SAT</p>
                    </a>
                </li>
                @if (session('is_approver'))
                    <li class="nav-item has-treeview">
                        <a href="{{ route('sat_approval') }}" class="nav-link">
                            <i class="nav-icon fa-regular fa-file-lines"></i>
                            <p>SAT Approval</p>
                        </a>
                    </li>
                @endif
                
                @if ($_SESSION['rapidx_user_id'] == 216)
                    <li class="nav-header font-weight-bold">&nbsp;Configuration</li>
                     <li class="nav-item has-treeview">
                            <a href="{{ route('user') }}" class="nav-link">
                            <i class="fa-solid fa-cog"></i>
                            <p>Users</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                            <a href="{{ route('dropdown_maintenance') }}" class="nav-link">
                            <i class="fa-solid fa-cog"></i>
                            <p>Dropdown Maintenance</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                            <a href="{{ route('approver_list') }}" class="nav-link">
                            <i class="fa-solid fa-cog"></i>
                            <p>Approvers</p>
                        </a>
                    </li>
                    
                @endif

                
            </ul>
        </nav>
    </div><!-- Sidebar -->
</aside>