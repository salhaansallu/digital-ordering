@extends('layouts.app')

@section('content')
    <div class="sitehero" style="background-image: url('/assets/images/hero/bg.jpg');">
        <div class="caption">
            <div class="container">
                <div class="inner">
                    <h2>Welcome to {{ company()->company_name }} Food Ordering System</h2>
                    <a href="javascript:void(0)" class="primary-btn order_btn">Order Now</a>

                    <div class="copyrights text-white text-center w-100" style="position: absolute;bottom: 10px;">Software by <a href="https://nmsware.com" class="m-0 d-inline text-decoration-none fw-bold">NMSware Technologies</a></div>
                </div>
            </div>
        </div>

        <div class="foodMenu" id="foodMenu">
            <food-menu :table_id="{{ isset($table_id)?$table_id:0 }}" :orders="{{ json_encode(get_Cookie('orders')) }}" />
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.order_btn').click(function (e) {
                e.preventDefault();
                $('#foodMenu').addClass('open');
            });
        });
    </script>
@endsection
