<header class="main-nav">
    <div class="sidebar-user text-center">
        {{-- <a class="setting-primary" href="javascript:void(0)"><i data-feather="settings"></i></a> --}}
        <img class="img-50 rounded-circle" src="{{asset('assets/images/dashboard/1.png')}}" alt="" />
        <a href="{{ Route('home') }}"> <h6 class="mt-3 f-14 f-w-600">{{ucfirst(Auth::user()->first_name)}}</h6></a>
        <!-- <p class="mb-0 font-roboto">{{ucfirst(Auth::user()->role)}} ({{Auth::user()->branch_id}})</p> -->
        <p class="mb-0 font-roboto"> <small>{{Auth::user()->Company->name}}</small></p>
        <!-- <p class="mb-0 font-roboto"><small>{{substr(strtoupper(Auth::user()->company->name), 0, 34)}}</small></p> -->
    </div>
    <nav>
        <div class="main-navbar">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="mainnav">
                <ul class="nav-menu custom-scrollbar">
                    <li class="back-btn">
                        <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/dashboard') || request()->is('v1/summary')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="bar-chart"></i><span>Summary</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/dashboard') || request()->is('v1/summary')) ? 'block' : '' }};">
                            @if(Auth::user()->hasFullAccess())
                            <li><a href="{{route('home-overview')}}" class="{{routeActive('home-overview')}}"> - Overview (All Modules)</a></li>
                            @endif

                            @if(Auth::user()->role == 'Mbao' || Auth::user()->hasFullAccess())
                            <li><a href="{{route('home')}}" class="{{routeActive('home')}}"> - Summary Mbao</a></li>
                            @endif

                            @if(Auth::user()->hasFullAccess() || Auth::user()->role == 'Driver')
                            <li><a href="{{route('home-truck')}}" class="{{routeActive('home-truck')}}"> - Summary Trucks</a></li>
                            @endif

                            @if(Auth::user()->role == 'Hardware' || Auth::user()->hasFullAccess())
                            <li><a href="{{route('home-hardcore')}}" class="{{routeActive('home-hardcore')}}"> - Summary Hardware</a></li>
                            @endif

                            <!-- <li><a href="{{route('home-hotel')}}" class="{{routeActive('home-hotel')}}">  - Summary hotel </a></li> -->
                          
                        </ul>
                    </li>
                     <!-- <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/products/*')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="grid"></i><span>Personal Details</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/products/*')) ? 'block' : '' }};">
                        <li><a href="{{route('staff-profile', ['id' => Auth::user()->id])}}" class="{{routeActive('staff-profile')}}"> - Personal Details</a></li>
                        <li><a href="{{route('leave-management')}}" class="{{routeActive('leave-management')}}"> - Leave</a></li>
                        </ul>
                    </li>  -->
                    @if(Auth::user()->hasFullAccess())
                    <!-- <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/products/*')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="grid"></i><span>HRMS</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/products/*')) ? 'block' : '' }};">
                            <li><a href="{{route('directorates-management')}}" class="{{routeActive('directorates-management')}}">  - Directorates </a></li>
                            <li><a href="{{route('section-management')}}" class="{{routeActive('section-management')}}">  - Section </a></li>
                            <li><a href="{{route('branches-management')}}" class="{{routeActive('branches-management')}}">  - Branches</a></li>
                            <li><a href="{{route('employees-management')}}" class="{{routeActive('employees-management')}}">  - Employee </a></li>
                            <li><a href="{{route('employees-management')}}" class="{{routeActive('employees-management')}}">  - Leave Management </a></li>
                        </ul>
                    </li> -->
                    @endif
                    
                @if(Auth::user()->role != 'Driver')
                @if(Auth::user()->role == 'Mbao' || Auth::user()->hasFullAccess())
                  <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/products/*')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="grid"></i><span>Inventory (mbao)</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/products/*')) ? 'block' : '' }};">
                        <li><a href="{{route('stores-management')}}" class="{{routeActive('stores-management')}}">  - Stores</a></li>
                        <li><a href="{{route('categories-management')}}" class="{{routeActive('categories-management')}}">  - Category</a></li>
                        <li><a href="{{route('invetories-management')}}" class="{{routeActive('invetories-management')}}">  - Invetories</a></li>
                        <li><a href="{{route('my-suppliers')}}" class="{{routeActive('my-suppliers')}}">  - My Suppliers</a></li>
                        <li><a href="{{route('expenses-management')}}" class="{{routeActive('expenses-management')}}">  - Expenses</a></li>
                        <li><a href="{{route('product-registration')}}" class="{{routeActive('product-registration')}}">  - Stock Management</a></li>
                        <!-- <li><a href="{{route('operate-sale')}}" class="{{routeActive('operate-sale')}}">  - Operate Sale</a></li> -->
                        <li><a href="{{route('customers-management')}}" class="{{routeActive('customers-management')}}">  - Sales/Customers</a></li>
                        <li><a href="{{route('quick-sale-mbao')}}" class="{{routeActive('quick-sale-mbao')}}">  - Quick / Cash Sale</a></li>

                        <!-- <li><a href="#" class="#"> - Operate Sales</a></li>
                        <li><a href="#" class="#"> - Sales Report</a></li> -->
                        </ul>
                    </li>
                    @endif
                    @if(Auth::user()->role == 'Hardware' || Auth::user()->hasFullAccess())
                    <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/products/*')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="grid"></i><span>Inventory (hard ware)</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/products/*')) ? 'block' : '' }};">
                        <li><a href="{{route('stores-management-tuli')}}" class="{{routeActive('stores-management-tuli')}}">  - Stores</a></li>
                        <li><a href="{{route('categories-management-tuli')}}" class="{{routeActive('categories-management-tuli')}}">  - Category</a></li>
                        <li><a href="{{route('invetories-management-tuli')}}" class="{{routeActive('invetories-management-tuli')}}">  - Invetories</a></li>
                        <li><a href="{{route('my-suppliers-tuli')}}" class="{{routeActive('my-suppliers-tuli')}}">  - My Suppliers</a></li>
                        <li><a href="{{route('expenses-management-tuli')}}" class="{{routeActive('expenses-management-tuli')}}">  - Expenses</a></li>
                        <li><a href="{{route('product-registration-tuli')}}" class="{{routeActive('product-registration-tuli')}}">  - Stock Management</a></li>
                        <!-- <li><a href="{{route('operate-sale')}}" class="{{routeActive('operate-sale')}}">  - Operate Sale</a></li> -->
                        <li><a href="{{route('customers-management-tuli')}}" class="{{routeActive('customers-management-tuli')}}">  - Sales/Customers</a></li>
                        <li>
                            <a href="{{ route('quick-sale') }}" class="{{ routeActive('quick-sale') }}">
                                - Quick / Cash Sale
                            </a>
                        </li>
                        <li><a href="{{route('sales-report-tuli')}}" class="{{routeActive('sales-report-tuli')}}">  - Sales</a></li>
                        <!-- <li><a href="#" class="#"> - Operate Sales</a></li>
                        <li><a href="#" class="#"> - Sales Report</a></li> -->
                        </ul>
                    </li>
                    @endif
                   
                     <!-- <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/products/*')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="grid"></i><span>Hotel  Management</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/products/*')) ? 'block' : '' }};"> -->
                        <!-- <li><a href="{{route('home-hotel')}}" class="{{routeActive('home-hotel')}}">  - Dasboard </a></li> -->
                        <!-- <li><a href="{{route('hotel-management')}}" class="{{routeActive('hotel-management')}}">  - My Hotels </a></li>
                        <li><a href="{{route('room-category-management')}}" class="{{routeActive('room-category-management')}}">  - Rooms Categories </a></li>
                        <li><a href="{{route('room-management')}}" class="{{routeActive('room-management')}}">  - Rooms Managements </a></li>
                        <li><a href="{{route('mycustomers-management')}}" class="{{routeActive('mycustomers-management')}}">  - Customers & Bookings </a></li>
                        <li><a href="#" class="#"> - Customers</a></li> -->
                        <!-- </ul> -->
                    </li>
                    @if(Auth::user()->hasFullAccess())
                    <!-- <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/products/*')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="grid"></i><span>Vending Machine</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/products/*')) ? 'block' : '' }};">
                        <li><a href="#" class="#"> - All Vending</a></li>
                        <li><a href="#" class="#"> - Vending Management</a></li>
                        </ul>
                    </li> -->
                   
                    @endif

                    <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/products/*')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="grid"></i><span>Reports</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/products/*')) ? 'block' : '' }};">
                        <li><a href="{{route('invoices')}}" class="{{routeActive('invoices')}}">  - Invoices</a></li>
                        <li><a href="{{route('sales-report')}}" class="{{routeActive('sales-report')}}">  - Sales</a></li>
                          <li><a href="{{route('product-edited')}}" class="{{routeActive('product-edited')}}">  - Product Edited</a></li>
                        <li><a href="{{route('product-transfered')}}" class="{{routeActive('product-transfered')}}">  - Product Transfered</a></li>
                        </ul>
                    </li>
                    @endif

                    @if(Auth::user()->role == 'Driver')
                    <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/products/*')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="grid"></i><span>Logistics</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/products/*')) ? 'block' : '' }};">
                       
                        {{-- <li><a href="{{route('truck-drivers')}}" class="{{routeActive('truck-drivers')}}">  - Trucks & Drivers</a></li> --}}
                       
                        {{-- @if(Auth::user()->hasFullAccess() || Auth::user()->role == 'Driver') --}}
                        <li><a href="{{route('trips-management')}}" class="{{routeActive('trips-management')}}">  - Trips</a></li>
                        <li><a href="{{route('truck-reports')}}" class="{{routeActive('truck-reports')}}">  - Truck Reports</a></li>
                        {{-- <li><a href="{{route('truck-ejy')}}" class="{{routeActive('truck-ejy')}}">  - Report for T821EJY</a></li> --}}
                        {{-- @endif --}}
                        {{-- <li><a href="{{route('bank-deposit')}}" class="{{routeActive('bank-deposit')}}">  - Bank Deposit</a></li> --}}
                        {{-- <li><a href="{{route('sales-report')}}" class="{{routeActive('sales-report')}}">  - Sales</a></li>
                          <li><a href="{{route('product-edited')}}" class="{{routeActive('product-edited')}}">  - Product Edited</a></li>
                        <li><a href="{{route('product-transfered')}}" class="{{routeActive('product-transfered')}}">  - Product Transfered</a></li> --}}
                        </ul>
                    </li>
                    @endif

                    @if(Auth::user()->hasFullAccess())
                    <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/products/*')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="grid"></i><span>Logistics</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/products/*')) ? 'block' : '' }};">
                       
                        <li><a href="{{route('truck-drivers')}}" class="{{routeActive('truck-drivers')}}">  - Trucks & Drivers</a></li>
                       
                        <li><a href="{{route('trips-management')}}" class="{{routeActive('trips-management')}}">  - Trips</a></li>
                        <li><a href="{{route('truck-reports')}}" class="{{routeActive('truck-reports')}}">  - Truck Reports</a></li>
                        <li><a href="{{route('truck-ejy')}}" class="{{routeActive('truck-ejy')}}">  - Report for T821EJY</a></li>
                        <li><a href="{{route('truck-erw')}}" class="{{routeActive('truck-erw')}}">  - Report for T343ERW</a></li>
                       
                        <li><a href="{{route('bank-deposit')}}" class="{{routeActive('bank-deposit')}}">  - Bank Deposit</a></li>
                        {{-- <li><a href="{{route('sales-report')}}" class="{{routeActive('sales-report')}}">  - Sales</a></li>
                          <li><a href="{{route('product-edited')}}" class="{{routeActive('product-edited')}}">  - Product Edited</a></li>
                        <li><a href="{{route('product-transfered')}}" class="{{routeActive('product-transfered')}}">  - Product Transfered</a></li> --}}
                        </ul>
                    </li>
                    @endif
                    <li class="dropdown">
                        <a class="nav-link menu-title {{(request()->is('v1/security/*')) ? 'active' : ''}}" href="javascript:void(0)"><i data-feather="settings"></i><span>Security & Settings</span></a>
                        <ul class="nav-submenu menu-content" style="display: {{ (request()->is('v1/security/*')) ? 'block' : '' }};">
                            <li><a href="{{route('security-user-profile')}}" class="{{routeActive('security-user-profile')}}"> - Your Profile</a></li>
                            @if(Auth::user()->hasFullAccess() )
                                <li><a href="{{route('portal-users')}}" class="{{routeActive('portal-users')}}"> - System Users</a></li>
                            @endif
                        </ul>
                    </li>   
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>  
    </nav>
</header>
