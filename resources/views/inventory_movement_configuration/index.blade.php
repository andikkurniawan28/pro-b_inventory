@extends('template.master')

@section('title')
    {{ ucwords(str_replace('_', ' ', 'inventory_movement_configuration')) }}
@endsection

@section('content')
    <h1 class="h3 mb-4 text-gray-800">@yield('title')</h1>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-body">
                <div class="btn-group" role="group" aria-label="manage">
                    @php $permissionsNeeded = ['inventory_movement_configuration.create']; $hasAccess = array_intersect($permissionsNeeded, $setting->list_of_permission); @endphp
                    @if ($hasAccess)
                    <a href="{{ route('inventory_movement_configuration.create') }}" class="btn btn-sm btn-primary">Create</a>
                    @endif
                </div>
                <div class="table-responsive">
                    <br>
                    <table class="table table-bordered table-hovered" id="inventory_movement_configuration_table" width="100%">
                        <thead>
                            <tr>
                                <th>{{ strtoupper(str_replace('_', ' ', 'id')) }}</th>
                                <th>{{ ucwords(str_replace('_', ' ', 'code')) }}</th>
                                <th>{{ ucwords(str_replace('_', ' ', 'name')) }}</th>
                                <th>{{ ucwords(str_replace('_', ' ', 'stock')) }}</th>
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
            $('#inventory_movement_configuration_table').DataTable({
                layout: {
                    bottomStart: {
                        buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],
                    },
                },
                processing: true,
                serverSide: true,
                ajax: "{{ route('inventory_movement_configuration.index') }}",
                order: [
                    [0, 'desc']
                ],
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'stock',
                        name: 'stock'
                    },
                    {
                        data: null,
                        name: 'actions',
                        render: function(data, type, row) {
                            let actions = '<div class="btn-group" role="group" aria-label="manage">';
                            if (permissions.includes('inventory_movement_configuration.edit')) {
                                actions += `<a href="{{ url('inventory_movement_configuration') }}/${row.id}/edit" class="btn btn-secondary btn-sm">Edit</a>`;
                            }
                            if (permissions.includes('inventory_movement_configuration.destroy')) {
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
                const inventory_movement_configurationId = $(this).data('id');
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
                            action: `{{ url('inventory_movement_configuration') }}/${inventory_movement_configurationId}`
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