<div id="kt_header" class="header flex-column header-fixed">
    <div class="header-top">
        <div class="container">
            <div class="d-none d-lg-flex align-items-center mr-3">
                <a href="{{ route('home')}}" class="mr-20">
                    <img alt="Logo" src="{{ asset('main-logo.png') }}" class="max-h-50px" />
                </a>
                <ul class="header-tabs nav align-self-end font-size-lg" role="tablist">
                    <li class="nav-item">
                        <a href="#" class="nav-link py-4 px-6 {{ request()->is('home/*') ? 'active' : '' }}" data-toggle="tab" data-target="#kt_header_tab_1" role="tab">Home</a>
                    </li>
                    @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 3 || session('user_role') == 4 || session('user_role') == 6)
                    <li class="nav-item mr-3">
                        <a href="#" class="nav-link py-4 px-6 {{ request()->is('master_data/*') ? 'active' : '' }}" data-toggle="tab" data-target="#kt_header_tab_2" role="tab">Master Data</a>
                    </li>
                    @endif
                    @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 3 || session('user_role') == 5 || session('user_role') == 6)
                    <li class="nav-item mr-3">
                        <a href="#" class="nav-link py-4 px-6 {{ request()->is('purchase_order/*') ? 'active' : '' }}" data-toggle="tab" data-target="#kt_header_tab_3" role="tab">Purchase Order</a>
                    </li>
                    @endif
                    @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 3 || session('user_role') == 4 || session('user_role') == 5 || session('user_role') == 6)
                    <li class="nav-item mr-3">
                        <a href="#" class="nav-link py-4 px-6 {{ request()->is('goods_movement/*') ? 'active' : '' }}" data-toggle="tab" data-target="#kt_header_tab_4" role="tab">Good Movements</a>
                    </li>
                    @endif
                    @if (session('user_role') == 1 || session('user_role') == 3 || session('user_role') == 4)
                    <li class="nav-item mr-3">
                        <a href="#" class="nav-link py-4 px-6 {{ request()->is('good_issue/*') ? 'active' : '' }}" data-toggle="tab" data-target="#kt_header_tab_5" role="tab">Stock Opname</a>
                    </li>
                    @endif
                    @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 3 || session('user_role') == 4 || session('user_role') == 5 || session('user_role') == 6)
                    <li class="nav-item mr-3">
                        <a href="#" class="nav-link py-4 px-6 {{ request()->is('report/*') ? 'active' : '' }}" data-toggle="tab" data-target="#kt_header_tab_6" role="tab">Report</a>
                    </li>
                    @endif
                </ul>
            </div>
            <div class="topbar bg-primary">
                <div class="topbar-item">
                    <div class="btn btn-icon btn-hover-transparent-white w-auto d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
                        <div class="d-flex flex-column text-right pr-3">
                            <span class="text-white opacity-50 font-weight-bold font-size-sm d-none d-md-inline">{{ session('name') }}</span>
                            <span class="text-white font-weight-bolder font-size-sm d-none d-md-inline">
                                @if (session('user_role') == 1)
                                    Admin
                                @elseif (session('user_role') == 2)
                                    PPIC
                                @elseif (session('user_role') == 3)
                                    Warehouse Mobile
                                @elseif (session('user_role') == 4)
                                    Costing
                                @elseif (session('user_role') == 5)
                                    Warehouse
                                @elseif (session('user_role') == 6)
                                    Head PPIC
                                @endif
                            </span>
                        </div>
                        <span class="symbol symbol-35">
                            <span class="symbol-label font-size-h5 font-weight-bold text-white bg-white-o-30">{{ session('user_initial_name') }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-bottom">
        <div class="container">
            <div class="header-navs header-navs-left" id="kt_header_navs">
                <ul class="header-tabs p-5 p-lg-0 d-flex d-lg-none nav nav-bold nav-tabs" role="tablist">
                    <li class="nav-item mr-2">
                        <a href="#" class="nav-link btn btn-clean {{ request()->is('home/*') ? 'active' : '' }}" data-toggle="tab" data-target="#kt_header_tab_1" role="tab">Home</a>
                    </li>
                    @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 3 || session('user_role') == 4 || session('user_role') == 6)
                    <li class="nav-item mr-2">
                        <a href="#" class="nav-link btn btn-clean {{ request()->is('master_data/*') ? 'active' : '' }}" data-toggle="tab" data-target="#kt_header_tab_2" role="tab">Master Data</a>
                    </li>
                    @endif
                    @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 3 || session('user_role') == 5 || session('user_role') == 6)
                    <li class="nav-item mr-2">
                        <a href="#" class="nav-link btn btn-clean {{ request()->is('purchase_order/*') ? 'active' : '' }}" data-toggle="tab" data-target="#kt_header_tab_3" role="tab">Purchase Order</a>
                    </li>
                    @endif
                    @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 3 || session('user_role') == 4 || session('user_role') == 5 || session('user_role') == 6)
                    <li class="nav-item mr-2">
                        <a href="#" class="nav-link btn btn-clean {{ request()->is('transaction/*') ? 'active' : '' }}" data-toggle="tab" data-target="#kt_header_tab_4" role="tab">Good Movements</a>
                    </li>
                    @endif
                    @if (session('user_role') == 1 || session('user_role') == 3 || session('user_role') == 4)
                    <li class="nav-item mr-2">
                        <a href="#" class="nav-link btn btn-clean {{ request()->is('transaction/*') ? 'active' : '' }}" data-toggle="tab" data-target="#kt_header_tab_5" role="tab">Stock Opname</a>
                    </li>
                    @endif
                    @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 3 || session('user_role') == 4 || session('user_role') == 5 || session('user_role') == 6)
                    <li class="nav-item mr-2">
                        <a href="#" class="nav-link btn btn-clean {{ request()->is('transaction/*') ? 'active' : '' }}" data-toggle="tab" data-target="#kt_header_tab_6" role="tab">Report</a>
                    </li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane py-5 p-lg-0 justify-content-between {{ request()->is('home/*') ? 'show active' : '' }}" id="kt_header_tab_1">
                        <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                            <ul class="menu-nav">
                                <li class="menu-item {{ request()->is('home/dashboard') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("home") }}" class="menu-link">
                                        <span class="menu-text">Dashboard</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-pane p-5 p-lg-0 justify-content-between {{ request()->is('master_data/*') ? 'show active' : '' }}" id="kt_header_tab_2">
                        <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                            <ul class="menu-nav">
                                @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 4 || session('user_role') == 5 || session('user_role') == 6)
                                <li class="menu-item {{ request()->is('master_data/plant/*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("master_data_plant_view") }}" class="menu-link">
                                        <span class="menu-text">Plant</span>
                                    </a>
                                </li>
                                
                                <li class="menu-item {{ request()->is('master_data/sloc/*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("master_data_sloc_view") }}" class="menu-link">
                                        <span class="menu-text">Storage Location</span>
                                    </a>
                                </li>
                                @endif
                                @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 6)
                                <li class="menu-item {{ request()->is('master_data/material/*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("master_data_material_view") }}" class="menu-link">
                                        <span class="menu-text">Material</span>
                                    </a>
                                </li>
                                @endif
                                @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 4 || session('user_role') == 5 || session('user_role') == 6)
                                <li class="menu-item {{ request()->is('master_data/material_uom/*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("master_data_material_uom_view") }}" class="menu-link">
                                        <span class="menu-text">Material UOM</span>
                                    </a>
                                </li>
                                @endif
                                @if (session('user_role') == 1)
                                <li class="menu-item {{ request()->is('master_data/vendor/*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("master_data_vendor_view") }}" class="menu-link">
                                        <span class="menu-text">Vendor</span>
                                    </a>
                                </li>
                                @endif
                                @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 4 || session('user_role') == 5 || session('user_role') == 6)
                                <li class="menu-item {{ request()->is('master_data/movement_type/*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("master_data_movement_type_view") }}" class="menu-link">
                                        <span class="menu-text">Movement Type</span>
                                    </a>
                                </li>
                                @endif
                                @if (session('user_role') == 1)
                                <li class="menu-item {{ request()->is('master_data/gl_account/*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("master_data_gl_account_view") }}" class="menu-link">
                                        <span class="menu-text">GL Account</span>
                                    </a>
                                </li>
                                @endif
                                @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 4 || session('user_role') == 5 || session('user_role') == 6)
                                <li class="menu-item {{ request()->is('master_data/cost_center/*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("master_data_cost_center_view") }}" class="menu-link">
                                        <span class="menu-text">Cost Center</span>
                                    </a>
                                </li>
                                @endif
                                @if (session('user_role') == 1)
                                <li class="menu-item {{ request()->is('master_data/users/*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("master_data_users_view") }}" class="menu-link">
                                        <span class="menu-text">User</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="tab-pane p-5 p-lg-0 justify-content-between {{ request()->is('purchase_order/*') ? 'show active' : '' }}" id="kt_header_tab_3">
                        <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                            <ul class="menu-nav">
                                @if (session('user_role') == 1)
                                <li class="menu-item {{ request()->is('purchase_order/master/*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("purchase_order_master_view") }}" class="menu-link">
                                        <span class="menu-text">Master PO</span>
                                    </a>
                                </li>
                                @endif
                                @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 3 || session('user_role') == 5 || session('user_role') == 6)
                                <li class="menu-item {{ request()->is('purchase_order/good_receipt/*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("purchase_order_good_receipt_view") }}" class="menu-link">
                                        <span class="menu-text">Good Receipt</span>
                                    </a>
                                </li>
                                
                                <li class="menu-item {{ request()->is('purchase_order/good_issue/*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("purchase_order_good_issue_view") }}" class="menu-link">
                                        <span class="menu-text">Good Issue</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="tab-pane p-5 p-lg-0 justify-content-between {{ request()->is('goods_movement/*') ? 'show active' : '' }}" id="kt_header_tab_4">
                        <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                            <ul class="menu-nav">
                                @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 3 || session('user_role') == 4 || session('user_role') == 5 || session('user_role') == 6)
                                <li class="menu-item {{ request()->is('goods_movement/transfer_posting/*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("goods_movement_transfer_posting_view") }}" class="menu-link">
                                        <span class="menu-text">Transfer Posting</span>
                                    </a>
                                </li>
                                @endif
                                @if (session('user_role') == 1 || session('user_role') == 2 || session('user_role') == 4 || session('user_role') == 5 || session('user_role') == 6)
                                <li class="menu-item {{ request()->is('goods_movement/cancellation/*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("transaction_goods_movement_cancellation_view") }}" class="menu-link">
                                        <span class="menu-text">Cancellation MVT</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="tab-pane p-5 p-lg-0 justify-content-between {{ request()->is('stock_opname/*') ? 'show active' : '' }}" id="kt_header_tab_5">
                        <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                            <ul class="menu-nav">
                                @if (session('user_role') == 1 || session('user_role') == 3 || session('user_role') == 4)
                                <li class="menu-item {{ request()->is('stock_opname/*') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("transaction_stock_opname_view") }}" class="menu-link">
                                        <span class="menu-text">Stock Opname</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    
                    <div class="tab-pane p-5 p-lg-0 justify-content-between {{ request()->is('report/*') ? 'show active' : '' }}" id="kt_header_tab_6">
                        <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                            <ul class="menu-nav">
                                <li class="menu-item {{ request()->is('report/stock') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("stock_report_view") }}" class="menu-link">
                                        <span class="menu-text">Stock Report</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('report/good_movement') ? 'menu-item-active' : '' }}" aria-haspopup="true">
                                    <a href="{{ route("good_movement_report_view") }}" class="menu-link">
                                        <span class="menu-text">Good Movement Report</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
