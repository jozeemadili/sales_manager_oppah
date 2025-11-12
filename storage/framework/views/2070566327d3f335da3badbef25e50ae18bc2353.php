<header class="main-nav">
    <div class="sidebar-user text-center">
        
        <img class="img-50 rounded-circle" src="<?php echo e(asset('assets/images/dashboard/1.png')); ?>" alt="" />
        <a href="<?php echo e(Route('home')); ?>"> <h6 class="mt-3 f-14 f-w-600"><?php echo e(ucfirst(Auth::user()->first_name)); ?></h6></a>
        <!-- <p class="mb-0 font-roboto"><?php echo e(ucfirst(Auth::user()->role)); ?> (<?php echo e(Auth::user()->branch_id); ?>)</p> -->
        <p class="mb-0 font-roboto"> <small><?php echo e(Auth::user()->Company->name); ?></small></p>
        <!-- <p class="mb-0 font-roboto"><small><?php echo e(substr(strtoupper(Auth::user()->company->name), 0, 34)); ?></small></p> -->
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
                        <a class="nav-link menu-title <?php echo e((request()->is('v1/dashboard') || request()->is('v1/summary')) ? 'active' : ''); ?>" href="javascript:void(0)"><i data-feather="bar-chart"></i><span>Summary</span></a>
                        <ul class="nav-submenu menu-content" style="display: <?php echo e((request()->is('v1/dashboard') || request()->is('v1/summary')) ? 'block' : ''); ?>;">
                            <li><a href="<?php echo e(route('home')); ?>" class="<?php echo e(routeActive('home')); ?>"> - Summary</a></li>
                            <!-- <li><a href="<?php echo e(route('home-hotel')); ?>" class="<?php echo e(routeActive('home-hotel')); ?>">  - Summary hotel </a></li> -->
                          
                        </ul>
                    </li>
                     <!-- <li class="dropdown">
                        <a class="nav-link menu-title <?php echo e((request()->is('v1/products/*')) ? 'active' : ''); ?>" href="javascript:void(0)"><i data-feather="grid"></i><span>Personal Details</span></a>
                        <ul class="nav-submenu menu-content" style="display: <?php echo e((request()->is('v1/products/*')) ? 'block' : ''); ?>;">
                        <li><a href="<?php echo e(route('staff-profile', ['id' => Auth::user()->id])); ?>" class="<?php echo e(routeActive('staff-profile')); ?>"> - Personal Details</a></li>
                        <li><a href="<?php echo e(route('leave-management')); ?>" class="<?php echo e(routeActive('leave-management')); ?>"> - Leave</a></li>
                        </ul>
                    </li>  -->
                    <?php if(Auth::user()->role == 'ADMIN'): ?>
                    <!-- <li class="dropdown">
                        <a class="nav-link menu-title <?php echo e((request()->is('v1/products/*')) ? 'active' : ''); ?>" href="javascript:void(0)"><i data-feather="grid"></i><span>HRMS</span></a>
                        <ul class="nav-submenu menu-content" style="display: <?php echo e((request()->is('v1/products/*')) ? 'block' : ''); ?>;">
                            <li><a href="<?php echo e(route('directorates-management')); ?>" class="<?php echo e(routeActive('directorates-management')); ?>">  - Directorates </a></li>
                            <li><a href="<?php echo e(route('section-management')); ?>" class="<?php echo e(routeActive('section-management')); ?>">  - Section </a></li>
                            <li><a href="<?php echo e(route('branches-management')); ?>" class="<?php echo e(routeActive('branches-management')); ?>">  - Branches</a></li>
                            <li><a href="<?php echo e(route('employees-management')); ?>" class="<?php echo e(routeActive('employees-management')); ?>">  - Employee </a></li>
                            <li><a href="<?php echo e(route('employees-management')); ?>" class="<?php echo e(routeActive('employees-management')); ?>">  - Leave Management </a></li>
                        </ul>
                    </li> -->
                    <?php endif; ?>
                   
                  <li class="dropdown">
                        <a class="nav-link menu-title <?php echo e((request()->is('v1/products/*')) ? 'active' : ''); ?>" href="javascript:void(0)"><i data-feather="grid"></i><span>Store & Inventory</span></a>
                        <ul class="nav-submenu menu-content" style="display: <?php echo e((request()->is('v1/products/*')) ? 'block' : ''); ?>;">
                        <li><a href="<?php echo e(route('stores-management')); ?>" class="<?php echo e(routeActive('stores-management')); ?>">  - Stores</a></li>
                        <li><a href="<?php echo e(route('categories-management')); ?>" class="<?php echo e(routeActive('categories-management')); ?>">  - Category</a></li>
                        <li><a href="<?php echo e(route('invetories-management')); ?>" class="<?php echo e(routeActive('invetories-management')); ?>">  - Invetories</a></li>
                        <li><a href="<?php echo e(route('my-suppliers')); ?>" class="<?php echo e(routeActive('my-suppliers')); ?>">  - My Suppliers</a></li>
                        <li><a href="<?php echo e(route('expenses-management')); ?>" class="<?php echo e(routeActive('expenses-management')); ?>">  - Expenses</a></li>
                        <li><a href="<?php echo e(route('product-registration')); ?>" class="<?php echo e(routeActive('product-registration')); ?>">  - Stock Management</a></li>
                        <!-- <li><a href="<?php echo e(route('operate-sale')); ?>" class="<?php echo e(routeActive('operate-sale')); ?>">  - Operate Sale</a></li> -->
                        <li><a href="<?php echo e(route('customers-management')); ?>" class="<?php echo e(routeActive('customers-management')); ?>">  - Sales/Customers</a></li>
                        
                        <!-- <li><a href="#" class="#"> - Operate Sales</a></li>
                        <li><a href="#" class="#"> - Sales Report</a></li> -->
                        </ul>
                    </li>
                   
                     <!-- <li class="dropdown">
                        <a class="nav-link menu-title <?php echo e((request()->is('v1/products/*')) ? 'active' : ''); ?>" href="javascript:void(0)"><i data-feather="grid"></i><span>Hotel  Management</span></a>
                        <ul class="nav-submenu menu-content" style="display: <?php echo e((request()->is('v1/products/*')) ? 'block' : ''); ?>;"> -->
                        <!-- <li><a href="<?php echo e(route('home-hotel')); ?>" class="<?php echo e(routeActive('home-hotel')); ?>">  - Dasboard </a></li> -->
                        <!-- <li><a href="<?php echo e(route('hotel-management')); ?>" class="<?php echo e(routeActive('hotel-management')); ?>">  - My Hotels </a></li>
                        <li><a href="<?php echo e(route('room-category-management')); ?>" class="<?php echo e(routeActive('room-category-management')); ?>">  - Rooms Categories </a></li>
                        <li><a href="<?php echo e(route('room-management')); ?>" class="<?php echo e(routeActive('room-management')); ?>">  - Rooms Managements </a></li>
                        <li><a href="<?php echo e(route('mycustomers-management')); ?>" class="<?php echo e(routeActive('mycustomers-management')); ?>">  - Customers & Bookings </a></li>
                        <li><a href="#" class="#"> - Customers</a></li> -->
                        <!-- </ul> -->
                    </li>
                    <?php if(Auth::user()->role == 'ADMIN'): ?>
                    <!-- <li class="dropdown">
                        <a class="nav-link menu-title <?php echo e((request()->is('v1/products/*')) ? 'active' : ''); ?>" href="javascript:void(0)"><i data-feather="grid"></i><span>Vending Machine</span></a>
                        <ul class="nav-submenu menu-content" style="display: <?php echo e((request()->is('v1/products/*')) ? 'block' : ''); ?>;">
                        <li><a href="#" class="#"> - All Vending</a></li>
                        <li><a href="#" class="#"> - Vending Management</a></li>
                        </ul>
                    </li> -->
                   
                    <?php endif; ?>
                    <li class="dropdown">
                        <a class="nav-link menu-title <?php echo e((request()->is('v1/products/*')) ? 'active' : ''); ?>" href="javascript:void(0)"><i data-feather="grid"></i><span>Reports</span></a>
                        <ul class="nav-submenu menu-content" style="display: <?php echo e((request()->is('v1/products/*')) ? 'block' : ''); ?>;">
                        <li><a href="<?php echo e(route('invoices')); ?>" class="<?php echo e(routeActive('invoices')); ?>">  - Invoices</a></li>
                        <li><a href="<?php echo e(route('sales-report')); ?>" class="<?php echo e(routeActive('sales-report')); ?>">  - Sales</a></li>
                          <li><a href="<?php echo e(route('product-edited')); ?>" class="<?php echo e(routeActive('product-edited')); ?>">  - Product Edited</a></li>
                        <li><a href="<?php echo e(route('product-transfered')); ?>" class="<?php echo e(routeActive('product-transfered')); ?>">  - Product Transfered</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title <?php echo e((request()->is('v1/products/*')) ? 'active' : ''); ?>" href="javascript:void(0)"><i data-feather="grid"></i><span>Logistics</span></a>
                        <ul class="nav-submenu menu-content" style="display: <?php echo e((request()->is('v1/products/*')) ? 'block' : ''); ?>;">
                       
                        <li><a href="<?php echo e(route('truck-drivers')); ?>" class="<?php echo e(routeActive('truck-drivers')); ?>">  - Trucks & Drivers</a></li>
                        <li><a href="<?php echo e(route('trips-management')); ?>" class="<?php echo e(routeActive('trips-management')); ?>">  - Trips</a></li>
                        
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title <?php echo e((request()->is('v1/security/*')) ? 'active' : ''); ?>" href="javascript:void(0)"><i data-feather="settings"></i><span>Security & Settings</span></a>
                        <ul class="nav-submenu menu-content" style="display: <?php echo e((request()->is('v1/security/*')) ? 'block' : ''); ?>;">
                            <li><a href="<?php echo e(route('security-user-profile')); ?>" class="<?php echo e(routeActive('security-user-profile')); ?>"> - Your Profile</a></li>
                            <?php if(Auth::user()->role == 'ADMIN' ): ?>
                                <li><a href="<?php echo e(route('portal-users')); ?>" class="<?php echo e(routeActive('portal-users')); ?>"> - System Users</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>   
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>  
    </nav>
</header>
<?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/layouts/admin/partials/sidebar.blade.php ENDPATH**/ ?>