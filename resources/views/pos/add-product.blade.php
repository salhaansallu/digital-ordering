@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Add Product</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="ProductCreate" action="" data-toggle="validator" onsubmit="return false;">
                                @csrf

                                @isset($product)
                                <input type="hidden" name="modelid" value="{{ $product->id }}">
                                @endisset

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Product Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Name"
                                                name="name"
                                                value="@isset($product){{ $product->pro_name }}@endisset"
                                                data-errors="Please Enter Name." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Code <span class="text-danger">*</span> <small
                                                    class="text-secondary">(Scan barcode from scanner to enter
                                                    automatically)</small></label>
                                            <input type="text" class="form-control" placeholder="Enter Code"
                                                name="code"
                                                value="@isset($product){{ $product->sku }}@endisset"
                                                data-errors="Please Enter Code." id="BarCodeValue" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category" class="selectpicker form-control" data-style="py-0">
                                                <option value="other">Other</option>
                                                @foreach (getCategory('all') as $cate)
                                                    <option
                                                        @isset($product) {{ $cate->id == $product->category ? 'selected' : '' }} @endisset
                                                        value="{{ $cate->id }}">{{ $cate->category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Price <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Price"
                                                name="price"
                                                value="@isset($product){{ $product->price }}@endisset"
                                                data-errors="Please Enter Price." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Stock <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Stock"
                                                name="stock"
                                                value="@isset($product){{ $product->qty }}@endisset"
                                                data-errors="Please Enter Stock." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Image</label>
                                            <input type="file" class="form-control image-file" name="product_image"
                                                accept="image/*" id="product_image">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="save_btn" class="btn btn-primary mr-2">@isset($product) Update product @else Add Product @endisset</button>
                                @if (isset($product) && $product->parent == 0)
                                    <button type="button" id="createVariant" class="btn btn-primary mr-2" data-bs-toggle="modal" data-bs-target="#VariantModel">Create Variant</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page end  -->

            @if (isset($product) && $product->parent == 0)
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-0">Variant List</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Product</th>
                                    <th class="text-start">Code (SKU)</th>
                                    <th class="text-start">Price</th>
                                    <th class="text-start">Stock</th>
                                    <th class="text-start">Action</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if (isset($variants) && $variants->count() > 0)
                                @foreach ($variants as $item)
                                <tr id="pro{{ $item->sku }}">
                                    <td class="text-start"><input type="text" id="name_{{ $item->id }}" value="{{ $item->pro_name }}" class="form-control"></td>
                                    <td class="text-start"><input type="text" id="sku_{{ $item->id }}" value="{{ $item->sku }}" class="form-control"></td>
                                    <td class="text-start"><input type="text" id="price_{{ $item->id }}" value="{{ $item->price }}" class="form-control"></td>
                                    <td class="text-start"><input type="text" id="qty_{{ $item->id }}" value="{{ $item->qty }}" class="form-control"></td>
                                    <td class="text-start">
                                        <div class="d-flex align-items-center list-action justify-content-start">
                                            <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="Update variant"
                                                data-original-title="Edit" href="javascript:void(0)" onclick="updateProduct('{{ $item->id }}')"><i class="fa-solid fa-check"></i></a>
                                            <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete variant"
                                                data-original-title="Delete" href="javascript:void(0)" onclick="deleteProduct('{{ $item->sku }}')"><i class="ri-delete-bin-line mr-0"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="VariantModel" tabindex="-1" aria-labelledby="VariantModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="VariantModelLabel">Create new variant</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="variantForm" action="" data-toggle="validator" onsubmit="return false;">
                    @csrf

                    @isset($product)
                    <input type="hidden" name="modelid" value="{{ $product->id }}">
                    @endisset

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter Name"
                                    name="name"value="" data-errors="Please Enter Name." required>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Code <span class="text-danger">*</span> <small
                                        class="text-secondary">(Scan barcode from scanner to enter
                                        automatically)</small></label>
                                <input type="text" class="form-control" placeholder="Enter Code"
                                    name="code"
                                    value=""
                                    data-errors="Please Enter Code." id="BarCodeValue" required>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Price <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter Price"
                                    name="price"
                                    value=""
                                    data-errors="Please Enter Price." required>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Stock <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Enter Stock"
                                    name="stock"
                                    value=""
                                    data-errors="Please Enter Stock." required>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" id="save_variant" class="btn btn-primary mr-2">Add Variant</button>
                </form>
            </div>
          </div>
        </div>
    </div>

    @isset($product)
        <script>
            $("#ProductCreate").submit(function(e) {
                e.preventDefault();

                if (document.getElementById("product_image").value != "" && !['png', 'jpeg', 'jpg'].includes(checkFileExtension('product_image'))) {
                    return toastr.error("Please select 'png', 'jpeg', or 'jpg' image", 'Error');
                }

                $('#save_btn').prop('disabled', true);

                var formData = new FormData(this);
                $.ajax({
                    type: "post",
                    url: '/dashboard/products/edit',
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, 'Success');
                            setInterval(() => {
                                location.href="/dashboard/products/edit/"+response.sku;
                            }, 3000);
                        } else {
                            toastr.error(response.msg, 'Error');
                        }
                    }
                });
                $('#save_btn').prop('disabled', false);
            });

            $('#variantForm').submit(function (e) {
                e.preventDefault();
                $('#save_variant').prop('disabled', true);

                var formData = new FormData(this);
                $.ajax({
                    type: "post",
                    url: '/dashboard/variants/create?id={{ $product->id }}',
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, 'Success');
                            setInterval(() => {
                                location.reload()
                            }, 3000);
                        } else {
                            toastr.error(response.msg, 'Error');
                        }
                    }
                });
                $('#save_variant').prop('disabled', false);
            });

            function deleteProduct(sku) {
                if (confirm('Are you sure you want to delete?')) {
                    $.ajax({
                        type: "delete",
                        url: "/dashboard/products/delete",
                        data: {sku: sku, _token: '{{ csrf_token() }}'},
                        dataType: "json",
                        success: function (response) {
                            if (response.error == 0) {
                                toastr.success(response.msg, "Success");
                                $("#pro"+sku).remove();
                            }
                            else {
                                toastr.error(response.msg, "Error");
                            }
                        }
                    });
                }
                return 0;
            }

            function updateProduct(id) {
                $.ajax({
                    type: "post",
                    url: "/dashboard/variants/update",
                    data: {
                        name: $('#name_'+id).val(),
                        sku: $('#sku_'+id).val(),
                        price: $('#price_'+id).val(),
                        qty: $('#qty_'+id).val(),
                        id: id,
                        _token: '{{ csrf_token() }}',
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, "Success");
                        }
                        else {
                            toastr.error(response.msg, "Error");
                        }
                    }
                });
                return 0;
            }
        </script>
    @else
        <script>
            $("#ProductCreate").submit(function(e) {
                e.preventDefault();

                if (document.getElementById("product_image").value != "" && !['png', 'jpeg', 'jpg'].includes(checkFileExtension('product_image'))) {
                    return toastr.error("Please select 'png', 'jpeg', or 'jpg' image", 'Error');
                }

                $('#save_btn').prop('disabled', true);

                var formData = new FormData(this);
                $.ajax({
                    type: "post",
                    url: '/dashboard/products/create',
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, 'Success');
                            location.href="/dashboard/products/edit/"+response.id;
                        } else {
                            toastr.error(response.msg, 'Error');
                        }
                    }
                });
                $('#save_btn').prop('disabled', false);
            });
        </script>
    @endisset
@endsection
