@extends('site.layouts.main')
@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h4>{{ number_format(auth()->user()->balance) }}</h4>
                <p>@lang('Balance')</p>
            </div>
            <div class="icon">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h4>{{ number_format($player) }}</h4>
                <p>@lang('Members Count')</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h4>{{ number_format($game_count) }}</h4>
                <p>@lang('Games Count')</p>
            </div>
            <div class="icon">
                <i class="fas fa-gamepad"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h4>{{ number_format($provider_count) }}</h4>
                <p>@lang('Provider Count')</p>
            </div>
            <div class="icon">
                <i class="fas fa-info"></i>
            </div>
        </div>
    </div>

    @if(auth()->user()->level == 1)
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h4>{{ number_format($user) }}</h4>
                <p>@lang('Agent Count')</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row">
    <div class="col-sm-9">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="bg-secondary">
                        <tr>
                            <th colspan="2" class="text-center"> Agent Detail </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td> <b>Agent Name</b> </td>
                            <td class="text-right"> <b>{{ auth()->user()->name }}</b> </td>
                        </tr>
                        <tr>
                            <td> <b>Agent Code</b> </td>
                            <td class="text-right" onclick="copy(this)"> <b>{{ auth()->user()->agentcode }}</b>  <i class="fas fa-copy text-xs text-blue"></i></td>
                        </tr>

                        <tr>
                            <td> <b>SecretKey</b> </td>
                            <td class="text-right" onclick="copy(this)"> <b>{{ auth()->user()->secretkey }}</b>  <i class="fas fa-copy text-xs text-blue"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    function copy(that){
        var inp =document.createElement('input');
        document.body.appendChild(inp)
        inp.value = that.textContent
        inp.select();
        document.execCommand('copy',false);
        alert("Copied :" + that.textContent)
        inp.remove();
    }
</script>
@endpush
