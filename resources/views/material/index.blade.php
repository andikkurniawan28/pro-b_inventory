@extends('template.master')

@section('title')
    {{ ucwords(str_replace('_', ' ', 'material')) }}
@endsection

@section('content')
    <h1 class="h3 mb-4 text-gray-800">@yield('title')</h1>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-body">
                <div class="btn-group" role="group" aria-label="manage">
                    @php $permissionsNeeded = ['material.create']; $hasAccess = array_intersect($permissionsNeeded, $setting->list_of_permission); @endphp
                    @if ($hasAccess)
                    <a href="{{ route('material.create') }}" class="btn btn-sm btn-primary">Create</a>
                    @endif
                </div>
                <div class="table-responsive">
                    <br>
                    <table class="table table-bordered table-hovered" id="material_table" width="100%">
                        <thead>
                            <tr>
                                <th>{{ strtoupper(str_replace('_', ' ', 'id')) }}</th>
                                <th>{{ ucwords(str_replace('_', ' ', 'name')) }}</th>
                                <th>{{ ucwords(str_replace('_', ' ', 'bulk_unit')) }}</th>
                                <th>{{ ucwords(str_replace('_', ' ', 'retail_unit')) }}</th>
                                <th>{{ ucwords(str_replace('_', ' ', 'brand')) }}</th>
                                <th>{{ ucwords(str_replace('_', ' ', 'material_category')) }}</th>
                                <th>{{ ucwords(str_replace('_', ' ', 'bulk_barcode')) }}</th>
                                <th>{{ ucwords(str_replace('_', ' ', 'retail_barcode')) }}</th>
                                <th>{{ ucwords(str_replace('_', ' ', 'bulk_buy_price')) }}<sub>({{ $setting->currency->symbol }})</sub></th>
                                <th>{{ ucwords(str_replace('_', ' ', 'retail_buy_price')) }}<sub>({{ $setting->currency->symbol }})</sub></th>
                                <th>{{ ucwords(str_replace('_', ' ', 'bulk_sell_price')) }}<sub>({{ $setting->currency->symbol }})</sub></th>
                                <th>{{ ucwords(str_replace('_', ' ', 'retail_sell_price')) }}<sub>({{ $setting->currency->symbol }})</sub></th>
                                <th>{{ ucwords(str_replace('_', ' ', 'action')) }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('additional_script')
    <script type="text/javascript">
        const permissions = @json($setting->list_of_permission);
        $(document).ready(function() {
            $('#material_table').DataTable({
                layout: {
                    bottomStart: {
                        buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],
                    },
                },
                processing: true,
                serverSide: true,
                ajax: "{{ route('material.index') }}",
                order: [
                    [0, 'desc']
                ],
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'bulk_unit_id',
                        name: 'bulk_unit.symbol'
                    },
                    {
                        data: 'retail_unit_id',
                        name: 'retail_unit.symbol'
                    },
                    {
                        data: 'brand_id',
                        name: 'brand.name'
                    },
                    {
                        data: 'material_category_id',
                        name: 'material_category.name'
                    },
                    {
                        data: 'bulk_barcode',
                        name: 'bulk_barcode'
                    },
                    {
                        data: 'retail_barcode',
                        name: 'retail_barcode'
                    },
                    {
                        data: 'bulk_buy_price',
                        name: 'bulk_buy_price',
                        render: $.fn.dataTable.render.number('{{ $setting->thousand_separator }}', '{{ $setting->decimal_separator }}', 2, '')
                    },
                    {
                        data: 'retail_buy_price',
                        name: 'retail_buy_price',
                        render: $.fn.dataTable.render.number('{{ $setting->thousand_separator }}', '{{ $setting->decimal_separator }}', 2, '')
                    },
                    {
                        data: 'bulk_sell_price',
                        name: 'bulk_sell_price',
                        render: $.fn.dataTable.render.number('{{ $setting->thousand_separator }}', '{{ $setting->decimal_separator }}', 2, '')
                    },
                    {
                        data: 'retail_sell_price',
                        name: 'retail_sell_price',
                        render: $.fn.dataTable.render.number('{{ $setting->thousand_separator }}', '{{ $setting->decimal_separator }}', 2, '')
                    },
                    {
                        data: null,
                        name: 'actions',
                        render: function(data, type, row) {
                            let actions = '<div class="btn-group" role="group" aria-label="manage">';
                            if (permissions.includes('material.edit')) {
                                actions += `<a href="{{ url('material') }}/${row.id}/edit" class="btn btn-secondary btn-sm">Edit</a>`;
                            }
                            if (permissions.includes('material.destroy')) {
                                actions += `<button type="button" class="btn btn-danger btn-sm delete-btn" data-id="${row.id}" data-name="${row.id}">Delete</button>`;
                            }
                            actions += '</div>';
                            return actions;
                        }
                    }
                ]
            });

            // Event delegation for delete buttons
            $(document).on('click', '.delete-btn', function(event) {
                event.preventDefault();
                const materialId = $(this).data('id');
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = $('<form>', {
                            method: 'POST',
                            action: `{{ url('material') }}/${materialId}`
                        });

                        $('<input>', {
                            type: 'hidden',
                            name: '_method',
                            value: 'DELETE'
                        }).appendTo(form);

                        $('<input>', {
                            type: 'hidden',
                            name: '_token',
                            value: csrfToken
                        }).appendTo(form);

                        form.appendTo('body').submit();
                    }
                });
            });
        });
    </script>
@endsection
