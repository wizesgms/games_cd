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
                            <th class="text-white text-center">@lang('id')</th>
                            <th class="text-white text-center">@lang('Player Name')</th>
                            <th class="text-white text-center">@lang('Agent Code')</th>
                            <th class="text-white text-center">@lang('Balance')</th>
                            <th class="text-white text-center">@lang('Register Date')</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
@endsection

@push('script')
<script>
    $(function () {
        var table = $('#basic-datatable').DataTable({
          processing: true,
          "lengthMenu": [[50, 100, 150, 200,500,1000,"All"], [50, 100, 150,200,500,1000, "All"]],
          serverSide: true,
          ajax: window.location.href,
          columns: [
              {data: 'id', name: 'id', orderable: false},
              {data: 'ext_player', name: 'ext_player', orderable: false},
              {data: 'agent_code', name: 'agent_code', orderable: false},
              {data: 'balance', name: 'balance', orderable: false},
              {data: 'created_at', name: 'created_at', orderable: false},
          ] ,
          columnDefs: [
        { className: 'text-center text-sm fw-bold', targets: '_all' }]
      });
    });
</script>
@endpush
