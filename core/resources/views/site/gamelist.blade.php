@extends('site.layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-info waves-effect waves-light btn-sm" data-bs-toggle="modal" data-bs-target="#add_modal">add</button>
                    <table id="basic-datatable"
                        class="table dt-responsive nowrap table-sm table-bordered table-hover" style="width: 100%;">
                        <thead class="bg-dark">
                            <tr>
                                <th class="text-white text-center" style="width: 11px;">@lang('#')</th>
                                <th class="text-white text-center">@lang('Provider')</th>
                                <th class="text-white text-center" style="width: 70px;">@lang('GameName')</th>
                                <th class="text-white text-center" style="width: 90px;">@lang('GameCode')</th>
                                <th class="text-white text-center">@lang('GameType')</th>
                                <th class="text-white text-center">@lang('ProviderCode')</th>
                                <th class="text-white text-center">@lang('GameImage')</th>
                                <th class="text-white text-center">@lang('Category')</th>
                                <th class="text-white text-center">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($games as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $item->Provider }}</td>
                                <td class="text-center">{{ $item->GameName }}</td>
                                <td class="text-center">{{ $item->GameCode }}</td>
                                <td class="text-center">{{ $item->GameType }}</td>
                                <td class="text-center">{{ $item->ProviderCode }}</td>
                                <td class="text-center">
                                    <img src="{{ $item->GameImage }}" alt="{{ $item->GameName }}" width="90">
                                </td>
                                <td class="text-center">{{ $item->Category }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-info waves-effect waves-light btn-sm" data-bs-toggle="modal" data-bs-target="#edit{{ $item->id }}_modal">Edit</button>
                                    <a href="{{ route('image.update',$item->id) }}" class="btn btn-danger waves-effect waves-light btn-sm" target="_blank">Delete</a>
                                </td>
                            </tr>

                            <div class="modal fade" id="edit{{ $item->id }}_modal" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header py-3 px-4 border-bottom-0 d-block">
                                            <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></button>
                                            <h5 class="modal-title" id="modal-title">Event</h5>
                                        </div>
                                        <div class="modal-body px-4 pb-4 pt-0">
                                            <form class="needs-validation" action="{{ route('game.update',$item->id) }}" method="POST">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Images</label>
                                                            <input class="form-control" placeholder="Images" type="url" name="image" value="{{ $item->GameImage }}" required />
                                                            <div class="invalid-feedback">Please provide a valid event name</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-6 col-4">
                                                        <button type="button" class="btn btn-danger" id="btn-delete-event" style="display:none;">Delete</button>
                                                    </div>
                                                    <div class="col-md-6 col-8 text-end">
                                                        <button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-success" id="btn-save-event">Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div> <!-- end modal-content-->
                                </div> <!-- end modal dialog-->
                            </div>
                            @endforeach
                        </tbody>
                    </table>

                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <div class="modal fade" id="add_modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-3 px-4 border-bottom-0 d-block">
                    <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="modal-title">Event</h5>
                </div>
                <div class="modal-body px-4 pb-4 pt-0">
                    <form class="needs-validation" action="{{ route('add.provider') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Provider</label>
                                    <input class="form-control" placeholder="Provider" type="text" name="Provider" required />
                                    <div class="invalid-feedback">Please provide a valid event name</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Game Name</label>
                                    <input class="form-control" placeholder="GameName" type="text" name="GameName" required />
                                    <div class="invalid-feedback">Please provide a valid event name</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">GameCode</label>
                                    <input class="form-control" placeholder="GameCode" type="text" name="GameCode" required />
                                    <div class="invalid-feedback">Please provide a valid event name</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">GameType</label>
                                    <input class="form-control" placeholder="GameType" type="text" name="GameType" required />
                                    <div class="invalid-feedback">Please provide a valid event name</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ProviderCode</label>
                                    <input class="form-control" placeholder="ProviderCode" type="text" name="ProviderCode" required />
                                    <div class="invalid-feedback">Please provide a valid event name</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Images</label>
                                    <input class="form-control" placeholder="Images" type="url" name="image" required />
                                    <div class="invalid-feedback">Please provide a valid event name</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <input class="form-control" placeholder="Category" type="text" name="Category" required />
                                    <div class="invalid-feedback">Please provide a valid event name</div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6 col-4">
                                <button type="button" class="btn btn-danger" id="btn-delete-event" style="display:none;">Delete</button>
                            </div>
                            <div class="col-md-6 col-8 text-end">
                                <button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success" id="btn-save-event">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div> <!-- end modal-content-->
        </div> <!-- end modal dialog-->
    <!-- end row-->
@endsection

@push('style-lib')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <!-- third party css end -->
@endpush

@push('script-lib')
    <!-- third party js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-select/js/dataTables.select.min.js') }}"></script>
    <!-- third party js ends -->
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $("#basic-datatable").DataTable({
                language: {
                    paginate: {
                        previous: "<i class='mdi mdi-chevron-left'>",
                        next: "<i class='mdi mdi-chevron-right'>"
                    }
                },
                "lengthMenu": [[1000, 2000, 5000, "All"], [1000, 2000, 5000, "All"]]
            })
        });
    </script>
@endpush
