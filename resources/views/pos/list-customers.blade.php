@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Customer List</h4>
                            <p class="mb-0">The customer list effectively dictates customers presentation and provides
                                space<br> to list your customers and offering in the most appealing way.</p>
                        </div>
                        <a href="/dashboard/customer/create" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Add Customer</a>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="data-table table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Customer name</th>
                                    <th class="text-start">Address</th>
                                    <th class="text-start">Phone number</th>
                                    <th class="text-start">Added on</th>
                                    <th class="text-start">Action</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($customers && $customers->count() > 0)
                                @foreach ($customers as $item)
                                <tr id="category{{ $item->id }}">
                                    <td class="text-start">{{ $item->name }}</td>
                                    <td class="text-start">{{ $item->address }}</td>
                                    <td class="text-start">{{ $item->phone }}</td>
                                    <td class="text-start">{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                    <td class="text-start">
                                        <div class="d-flex align-items-center list-action justify-content-start">
                                            <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="Edit product"
                                                data-original-title="Edit" href="/dashboard/customer/edit/{{ $item->id }}"><i class="ri-pencil-line mr-0"></i></a>
                                            @if (isAdmin())
                                            <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete product"
                                            data-original-title="Delete" href="javascript:void(0)" onclick="deleteProduct('{{ $item->id }}')"><i class="ri-delete-bin-line mr-0"></i></a>
                                            @endif
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
            <!-- Page end  -->
        </div>
    </div>
    <script>
        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete?')) {
                $.ajax({
                    type: "delete",
                    url: "/dashboard/customers/delete",
                    data: {id: id, _token: '{{ csrf_token() }}'},
                    dataType: "json",
                    success: function (response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, "Success");
                            $("#category"+id).remove();
                        }
                        else {
                            toastr.error(response.msg, "Error");
                        }
                    }
                });
            }
            return 0;
        }
    </script>
@endsection
