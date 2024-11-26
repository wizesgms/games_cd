@extends('site.layouts.main')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="basic-datatable" class="table dt-responsive nowrap table-sm table-bordered table-hover"
                    style="width: 100%;">
                    <thead class="bg-dark">
                        <tr>
                            <th class="text-white text-center" style="width: 11px;">@lang('#')</th>
                            <th class="text-white text-center">@lang('Provider')</th>
                            <th class="text-white text-center">@lang('Type')</th>
                            <th class="text-white text-center">@lang('Apikey')</th>
                            <th class="text-white text-center">@lang('Secretkey')</th>
                            <th class="text-white text-center">@lang('Endpoint')</th>
                            <th class="text-white text-center">@lang('Status')</th>
                            <th class="text-white text-center">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($api as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center"><b>{{ $item->provider }}</b></td>
                            <td class="text-center"><b>{{ $item->type }}</b></td>
                            <td class="text-center">{{ $item->apikey }}</td>
                            <td class="text-center">{{ $item->secretkey }}</td>
                            <td class="text-center">{{ $item->url }}</td>
                            <td class="text-center">
                                @if($item->id == $apia->provider_id)
                                <span class="btn btn-success btn-xs"><i class="fas fa-check"></i></span>
                                @else
                                <span class="btn btn-warning btn-xs"><i class="fas fa-exclamation"></i></span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($item->id != $apia->provider_id)
                                <a href="{{ route('api.use',$item->id) }}" class="btn btn-success btn-xs"
                                    onclick="return confirm('Are you sure want to Use this API?');"><i
                                        class="fas fa-check-double"></i></a>
                                @endif
                                <button type="button" class="btn btn-primary waves-effect waves-light btn-xs"
                                    data-toggle="modal" data-target="#edit{{ $item->id }}"><i
                                        class="fas fa-pencil-alt"></i></button>
                            </td>
                        </tr>

                        <div class="modal fade" id="edit{{ $item->id }}">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Edit Api {{ $item->provider }}</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('api.update',$item->id) }}" method="post">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Provider</label>
                                                <input class="form-control" placeholder="Provider" type="text"
                                                    value="{{ $item->provider }}" required disabled />
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="form-label">Apikey / Agentcode</label>
                                                <input class="form-control" placeholder="Apikey / Agentcode"
                                                    type="text" name="apikey" value="{{ $item->apikey }}"
                                                    required />
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="form-label">Secretkey / Token</label>
                                                <input class="form-control" placeholder="Secretkey / Token"
                                                    type="text" name="secretkey" value="{{ $item->secretkey }}"
                                                    required />
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="form-label">Endpoint / Url</label>
                                                <input class="form-control" placeholder="Endpoint / Url"
                                                    type="url" name="url" value="{{ $item->url }}" required />
                                                <div class="invalid-feedback">Please provide a valid event name
                                                </div>
                                            </div>
                                            <div class="modal-footer justify-content-between">
                                                <button type="button" class="btn btn-default"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
            $("#basic-datatable").DataTable({
                "lengthMenu": [[50, 100, 150, "All"], [50, 100, 150, "All"]]
            })
        });
</script>
@endpush
