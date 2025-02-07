@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card card-transparent card-block card-stretch card-height border-none">
                        <div class="card-body p-0 mt-lg-2 mt-0">
                            <h3 class="mb-3">Hello {{ Auth::user()->fname }}, Welcome back</h3>
                            <p class="mb-0 mr-4">Your dashboard gives you views of key performance or business process. </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <div class="card card-block card-stretch card-height">
                                <div class="card-body">
                                    <div class="d-flex align-items-center card-total-sale">
                                        <div class="icon iq-icon-box-2 bg-info-light">
                                            <i class="fa-solid fa-chart-line"></i>
                                        </div>
                                        <div>
                                            <p class="mb-2">Today's Orders</p>
                                            <h4>{{ $todaysales }}</h4>
                                        </div>
                                    </div>
                                    {{-- <div class="iq-progress-bar mt-2">
                                    <span class="bg-info iq-progress progress-1" data-percent="85">
                                    </span>
                                </div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="card card-block card-stretch card-height">
                                <div class="card-body">
                                    <div class="d-flex align-items-center card-total-sale">
                                        <div class="icon iq-icon-box-2 bg-danger-light">
                                            <i class="fa-solid fa-coins"></i>
                                        </div>
                                        <div>
                                            <p class="mb-2">Pending Orders</p>
                                            <h4>{{ $pendingOrders }}</h4>
                                        </div>
                                    </div>
                                    {{-- <div class="iq-progress-bar mt-2">
                                    <span class="bg-danger iq-progress progress-1" data-percent="70">
                                    </span>
                                </div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="card card-block card-stretch card-height">
                                <div class="card-body">
                                    <div class="d-flex align-items-center card-total-sale">
                                        <div class="icon iq-icon-box-2 bg-success-light">
                                            <i class="fa-solid fa-hand-holding-dollar"></i>
                                        </div>
                                        <div>
                                            <p class="mb-2">Completed Orders</p>
                                            <h4>{{ $completedOrders }}</h4>
                                        </div>
                                    </div>
                                    {{-- <div class="iq-progress-bar mt-2">
                                    <span class="bg-success iq-progress progress-1" data-percent="75">
                                    </span>
                                </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Overview</h4>
                            </div>
                            {{-- <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton001"
                                    data-toggle="dropdown">
                                    This Month<i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none"
                                    aria-labelledby="dropdownMenuButton001">
                                    <a class="dropdown-item" href="#">Year</a>
                                    <a class="dropdown-item" href="#">Month</a>
                                    <a class="dropdown-item" href="#">Week</a>
                                </div>
                            </div>
                        </div> --}}
                        </div>
                        <div class="card-body">
                            <div id="overviewChart">
                                <dashboard-overview v-bind:overviewsales="{{ $sales }}" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card-transparent card-block card-stretch mb-4">
                        <div class="card-header d-flex align-items-center justify-content-between p-0">
                            <div class="header-title">
                                <h4 class="card-title mb-0">Top Selling Items</h4>
                            </div>
                            {{-- <div class="card-header-toolbar d-flex align-items-center">
                                <div><a href="#" class="btn btn-primary view-btn font-size-14">View All</a></div>
                            </div> --}}
                        </div>
                    </div>
                    @if ($best_sellings && $best_sellings->count() > 0)
                        @foreach ($best_sellings as $sell)
                            <div class="card card-block card-stretch card-height-helf">
                                <div class="card-body card-item-right">
                                    <div class="d-flex align-items-top">
                                        <div class="bg-primary-light rounded">
                                            <img src="{{ getProductImage($sell->sku) }}"
                                                class="style-img img-fluid m-auto best_product_image" alt="image">
                                        </div>
                                        <div class="style-text text-left">
                                            <h5 class="mb-2">{{ $sell->pro_name }}</h5>
                                            <p class="mb-2">Total Sold : {{ getTotalOrderQTY($sell->sku) }}</p>
                                            <p class="mb-0">Total Earned :
                                                {{ currency(getTotalOrderSale($sell->sku), $company->currency) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>
@endsection
