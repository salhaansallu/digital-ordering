@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Table List</h4>
                            <p class="mb-0">The table list effectively dictates table presentation and provides
                                space<br> to list your tables and offering in the most appealing way.</p>
                        </div>
                        <a href="javascript:void(0)" onclick="createTable()" class="btn btn-primary add-list"><i
                                class="fa-solid fa-plus mr-3"></i>Add Table</a>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Table Number</th>
                                    <th class="text-start">Status</th>
                                    <th class="text-start">Action</th>
                                    <th class="text-start">QR Code</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($tables && $tables->count() > 0)
                                    @foreach ($tables as $item)
                                        <tr id="category{{ $item->id }}">
                                            <td class="text-start">{{ $item->id }}</td>
                                            <td class="text-start">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        onchange="statusChange({{ $item->id }})"
                                                        {{ $item->status == 'active' ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center list-action justify-content-start">
                                                    <a class="badge bg-danger mr-2" data-toggle="tooltip"
                                                        data-placement="top" title="Delete product"
                                                        data-original-title="Delete" href="javascript:void(0)"
                                                        onclick="deleteProduct('{{ $item->id }}')"><i
                                                            class="ri-delete-bin-line mr-0"></i></a>
                                                </div>
                                            </td>
                                            <td>{!! generateQR(env('APP_URL').'/table/'.encrypt($item->id))->html !!}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>
    <script>
        function createTable() {
            $.ajax({
                type: "post",
                url: "/dashboard/table/create",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(response) {
                    if (response.error == 0) {
                        toastr.success(response.msg, "Success");
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.msg, "Error");
                    }
                }
            });
        }

        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete?')) {
                $.ajax({
                    type: "delete",
                    url: "/dashboard/tables/delete",
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, "Success");
                            $("#category" + id).remove();
                        } else {
                            toastr.error(response.msg, "Error");
                        }
                    }
                });
            }
            return 0;
        }

        function statusChange(id) {
            $.ajax({
                type: "post",
                url: "/dashboard/tables/update",
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(response) {
                    if (response.error == 0) {
                        toastr.success(response.msg, "Success");
                    } else {
                        toastr.error(response.msg, "Error");
                    }
                }
            });
        }
    </script>
@endsection
