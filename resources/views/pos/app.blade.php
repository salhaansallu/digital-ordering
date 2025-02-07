<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Digital Ordering System - NMSware Technologies</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/images/brand/favicon3.ico') }}" type="image/x-icon">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/assets/css/backend-plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assets/css/backende209.css?v=1.0.0') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assets/vendor/remixicon/fonts/remixicon.css') }}">

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    @vite(['resources/views/pos/sass/app.scss', 'resources/js/app.js'])
    <script src="{{ asset('assets/assets/js/backend-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/assets/js/table-treeview.js') }}"></script>
    <script src="{{ asset('assets/assets/js/customizer.js') }}"></script>
    <script async src="{{ asset('assets/assets/js/chart-custom.js') }}"></script>
    <script src="{{ asset('assets/assets/js/app.js') }}"></script>
    <script>
        window.addEventListener('offline', () => {
            alert('No internet connection, please check your network');
        });

        window.addEventListener('click', () => {
            if (!window.navigator.onLine) {
                alert('No internet connection, please check your network');
            }
        });
    </script>
</head>

<body class=" ">

    <div class="wrapper">
        <div class="iq-sidebar  sidebar-default ">
            <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
                <a href="/dashboard" class="header-logo">
                    {{-- <img src="{{ asset('assets/images/brand/logo.png') }}" class="light-logo" alt="logo"> --}}
                    <h5 class="logo-title light-logo ml-3">Dashboard</h5>
                </a>
                <div class="iq-menu-bt-sidebar ml-0">
                    <i class="fa-solid fa-bars wrapper-menu"></i>
                </div>
            </div>
            <div class="custom-scroller">
                <nav class="iq-sidebar-menu">
                    <ul id="iq-sidebar-toggle" class="iq-menu">
                        <li class="">
                            <a href="/pos" class="svg-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" fill="currentColor"
                                    height="20" viewBox="0 0 640 512">
                                    <path
                                        d="M64 64l0 288 512 0 0-288L64 64zM0 64C0 28.7 28.7 0 64 0L576 0c35.3 0 64 28.7 64 64l0 288c0 35.3-28.7 64-64 64L64 416c-35.3 0-64-28.7-64-64L0 64zM128 448l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-384 0c-17.7 0-32-14.3-32-32s14.3-32 32-32z" />
                                </svg>
                                <span class="ml-4">POS</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                            <a href="/dashboard" class="svg-icon">
                                <svg class="svg-icon" id="p-dash1" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                                    </path>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                                <span class="ml-4">Dashboard</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="/dashboard/tables">
                                <svg xmlns="http://www.w3.org/2000/svg" class="svg-icon" viewBox="0 0 512 512" width="20" height="20" fill="currentColor">
                                    <path
                                        d="M64 256l0-96 160 0 0 96L64 256zm0 64l160 0 0 96L64 416l0-96zm224 96l0-96 160 0 0 96-160 0zM448 256l-160 0 0-96 160 0 0 96zM64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32z" />
                                </svg>
                                <span class="ml-4">Tables</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('dashboard/products*') ? 'active' : '' }}">
                            <a href="#product" class="collapsed" data-toggle="collapse" aria-expanded="false">
                                <svg class="svg-icon" id="p-dash2" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <span class="ml-4">Products</span>
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </a>
                            <ul id="product"
                                class="iq-submenu collapse {{ Request::is('dashboard/products*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('dashboard/products') ? 'active' : '' }}">
                                    <a href="/dashboard/products">
                                        <i class="fa-solid fa-minus"></i><span>List Product</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/products/create') ? 'active' : '' }}">
                                    <a href="/dashboard/products/create">
                                        <i class="fa-solid fa-minus"></i><span>Add Product</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="{{ Request::is('dashboard/categor*') ? 'active' : '' }}">
                            <a href="#category" class="collapsed" data-toggle="collapse" aria-expanded="false">
                                <svg class="svg-icon" id="p-dash3" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2">
                                    </rect>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                </svg>
                                <span class="ml-4">Categories</span>
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </a>
                            <ul id="category"
                                class="iq-submenu collapse {{ Request::is('dashboard/categor*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('dashboard/categories') ? 'active' : '' }}">
                                    <a href="/dashboard/categories">
                                        <i class="fa-solid fa-minus"></i><span>List Category</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/category/create') ? 'active' : '' }}">
                                    <a href="/dashboard/category/create">
                                        <i class="fa-solid fa-minus"></i><span>Add Category</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="{{ Request::is('dashboard/customer*') ? 'active' : '' }}">
                            <a href="#people" class="collapsed" data-toggle="collapse" aria-expanded="false">
                                <svg class="svg-icon" id="p-dash8" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <span class="ml-4">Customers</span>
                                <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="10 15 15 20 20 15"></polyline>
                                    <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                                </svg>
                            </a>
                            <ul id="people"
                                class="iq-submenu collapse {{ Request::is('dashboard/customer*') || Request::is('dashboard/users*') || Request::is('dashboard/supplier*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('dashboard/customers') ? 'active' : '' }}">
                                    <a href="/dashboard/customers">
                                        <i class="fa-solid fa-minus"></i><span>Customers</span>
                                    </a>
                                </li>
                                <li class="{{ Request::is('dashboard/customer/create') ? 'active' : '' }}">
                                    <a href="/dashboard/customer/create">
                                        <i class="fa-solid fa-minus"></i><span>Add Customers</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="{{ Request::is('dashboard/user/update') ? 'active' : '' }}">
                            <a href="/dashboard/user/update">
                                <svg class="svg-icon" id="p-dash10" width="20" height="20"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="8.5" cy="7" r="4"></circle>
                                    <polyline points="17 11 19 13 23 9"></polyline>
                                </svg>
                                <span class="ml-4">User Details</span>
                            </a>
                        </li>
                    </ul>
                    <div class="p-3"></div>
                </nav>
            </div>
        </div>
        <div class="iq-top-navbar">
            <div class="iq-navbar-custom">
                <nav class="navbar navbar-expand-lg navbar-light p-0">
                    <div class="iq-navbar-logo d-flex align-items-center justify-content-between">
                        <i class="ri-menu-line wrapper-menu"></i>
                        <a href="/dashboard" class="header-logo">
                            {{-- <img src="{{ asset('assets/assets/images/logo.png') }}" class="img-fluid"
                                alt="logo"> --}}
                            {{-- <h5 class="logo-title ml-3"></h5> --}}

                        </a>
                    </div>
                    {{-- <div class="iq-search-bar device-search">
                        <form action="#" class="searchbox">
                            <a class="search-link" href="#"><i class="ri-search-line"></i></a>
                            <input type="text" class="text search-input" placeholder="Search here...">
                        </form>
                    </div> --}}
                    <div class="d-flex align-items-center">
                        {{-- <div class="change-mode">
                          <div class="custom-control custom-switch custom-switch-icon custom-control-inline">
                              <div class="custom-switch-inner">
                                  <p class="mb-0"> </p>
                                  <input type="checkbox" class="custom-control-input" id="dark-mode" data-active="true">
                                  <label class="custom-control-label" for="dark-mode" data-mode="toggle">
                                      <span class="switch-icon-left"><i class="a-left ri-moon-clear-line"></i></span>
                                      <span class="switch-icon-right"><i class="a-right ri-sun-line"></i></span>
                                  </label>
                              </div>
                          </div>
                      </div> --}}
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-label="Toggle navigation">
                            <i class="ri-menu-3-line"></i>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ml-auto navbar-list align-items-center">
                                {{-- <li class="nav-item nav-icon dropdown">
                                    <a href="#" class="search-toggle dropdown-toggle btn border add-btn"
                                        id="dropdownMenuButton02" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <img src="../assets/images/small/flag-01.png" alt="img-flag"
                                            class="img-fluid image-flag mr-2">En
                                    </a>
                                    <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                        <div class="card shadow-none m-0">
                                            <div class="card-body p-3">
                                                <a class="iq-sub-card" href="#"><img
                                                        src="../assets/images/small/flag-02.png" alt="img-flag"
                                                        class="img-fluid mr-2">French</a>
                                                <a class="iq-sub-card" href="#"><img
                                                        src="../assets/images/small/flag-03.png" alt="img-flag"
                                                        class="img-fluid mr-2">Spanish</a>
                                                <a class="iq-sub-card" href="#"><img
                                                        src="../assets/images/small/flag-04.png" alt="img-flag"
                                                        class="img-fluid mr-2">Italian</a>
                                                <a class="iq-sub-card" href="#"><img
                                                        src="../assets/images/small/flag-05.png" alt="img-flag"
                                                        class="img-fluid mr-2">German</a>
                                                <a class="iq-sub-card" href="#"><img
                                                        src="../assets/images/small/flag-06.png" alt="img-flag"
                                                        class="img-fluid mr-2">Japanese</a>
                                            </div>
                                        </div>
                                    </div>
                                </li> --}}
                                {{-- <li>
                                    <a href="#" class="btn border add-btn shadow-none mx-2 d-none d-md-block"
                                        data-toggle="modal" data-target="#new-order"><i
                                            class="las la-plus mr-2"></i>New
                                        Order</a>
                                </li> --}}
                                {{-- <li class="nav-item nav-icon search-content">
                                    <a href="#" class="search-toggle rounded" id="dropdownSearch"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ri-search-line"></i>
                                    </a>
                                    <div class="iq-search-bar iq-sub-dropdown dropdown-menu"
                                        aria-labelledby="dropdownSearch">
                                        <form action="#" class="searchbox p-2">
                                            <div class="form-group mb-0 position-relative">
                                                <input type="text" class="text search-input font-size-12"
                                                    placeholder="type here to search...">
                                                <a href="#" class="search-link"><i
                                                        class="ri-search-line"></i></a>
                                            </div>
                                        </form>
                                    </div>
                                </li> --}}
                                <li class="nav-item nav-icon dropdown">
                                    <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton2"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-mail">
                                            <path
                                                d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                            </path>
                                            <polyline points="22,6 12,13 2,6"></polyline>
                                        </svg>
                                        <span class="bg-primary"></span>
                                    </a>
                                    <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                        <div class="card shadow-none m-0">
                                            <div class="card-body p-0 ">
                                                <div class="cust-title p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <h5 class="mb-0">All Messages</h5>
                                                        <a class="badge badge-primary badge-card" href="#">0</a>
                                                    </div>
                                                </div>
                                                <div class="px-3 pt-0 pb-0 sub-card">
                                                    <div class="iq-sub-card text-center p-3">
                                                        <i>No new messages</i>
                                                    </div>
                                                    {{-- <a href="#" class="iq-sub-card">
                                                        <div class="media align-items-center cust-card py-3">
                                                            <div class="">
                                                                <img class="avatar-50 rounded-small"
                                                                    src="{{ asset('assets/assets/images/user/01.jpg') }}"
                                                                    alt="03">
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between">
                                                                    <h6 class="mb-0">Kianna Carder</h6>
                                                                    <small class="text-dark"><b>11 : 21 pm</b></small>
                                                                </div>
                                                                <small class="mb-0">Lorem ipsum dolor sit
                                                                    amet</small>
                                                            </div>
                                                        </div>
                                                    </a> --}}
                                                </div>
                                                <a class="right-ic btn btn-primary btn-block position-relative p-2 disabled"
                                                    href="" role="button">
                                                    View All
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="nav-item nav-icon dropdown">
                                    <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-bell">
                                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                        </svg>
                                        <span class="bg-primary "></span>
                                    </a>
                                    <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                        <div class="card shadow-none m-0">
                                            <div class="card-body p-0 ">
                                                <div class="cust-title p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <h5 class="mb-0">All Notifications</h5>
                                                        <a class="badge badge-primary badge-card" href="#">0</a>
                                                    </div>
                                                </div>
                                                <div class="px-3 pt-0 pb-0 sub-card">
                                                    <div class="iq-sub-card text-center p-3">
                                                        <i>No new notifications</i>
                                                    </div>
                                                    {{-- <a href="#" class="iq-sub-card">
                                                        <div class="media align-items-center cust-card py-3">
                                                            <div class="">
                                                                <img class="avatar-50 rounded-small"
                                                                    src="{{ asset('assets/assets/images/user/01.jpg') }}"
                                                                    alt="03">
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between">
                                                                    <h6 class="mb-0">Kianna Carder</h6>
                                                                    <small class="text-dark"><b>11 : 21 pm</b></small>
                                                                </div>
                                                                <small class="mb-0">Lorem ipsum dolor sit
                                                                    amet</small>
                                                            </div>
                                                        </div>
                                                    </a> --}}
                                                </div>
                                                <a class="right-ic btn btn-primary btn-block position-relative p-2 disabled"
                                                    href="" role="button">
                                                    View All
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="nav-item nav-icon dropdown caption-content">
                                    <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton4"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <img src="{{ profileImage(userData()->profile) }}" class="img-fluid rounded"
                                            alt="user">
                                    </a>
                                    <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <div class="card shadow-none m-0">
                                            <div class="card-body p-0 text-center">
                                                <div class="media-body profile-detail text-center">
                                                    <img src="{{ asset('assets/assets/images/page-img/profile-bg.jpg') }}"
                                                        alt="profile-bg" class="rounded-top img-fluid mb-4">
                                                    <img src="{{ profileImage(userData()->profile) }}"
                                                        alt="profile-img"
                                                        class="rounded profile-img img-fluid avatar-70">
                                                </div>
                                                <div class="p-3">
                                                    <h5 class="mb-1">{{ Auth::user()->email }}</h5>
                                                    <p class="mb-0">Since
                                                        {{ date('d M Y', strtotime(Auth::user()->created_at)) }}</p>
                                                    <div class="d-flex align-items-center justify-content-center mt-3">
                                                        <a href="/dashboard/user/update"
                                                            class="btn border mr-2">Update Profile</a>
                                                        <a href="/account/logout" class="btn border">Sign Out</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        {{-- <div class="modal fade" id="new-order" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="popup text-left">
                            <h4 class="mb-3">New Order</h4>
                            <div class="content create-workform bg-body">
                                <div class="pb-3">
                                    <label class="mb-2">Email</label>
                                    <input type="text" class="form-control" placeholder="Enter Name or Email">
                                </div>
                                <div class="col-lg-12 mt-4">
                                    <div class="d-flex flex-wrap align-items-ceter justify-content-center">
                                        <div class="btn btn-primary mr-4" data-dismiss="modal">Cancel</div>
                                        <div class="btn btn-outline-primary" data-dismiss="modal">Create</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        @yield('dashboard')
    </div>

    <!-- Wrapper End-->
    <footer class="iq-footer">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item"><strong>Version: </strong>1.0.0</li>
                            </ul>
                        </div>
                        <div class="col-lg-6 text-right">
                            <span class="mr-1">
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> &copy;
                            </span> Digital Ordering System by <a href="https://nmsware.com" target="_blank">NMSware
                                Technologies</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>
