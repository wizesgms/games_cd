@extends('site.layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-9">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('changepw') }}" method="POST" class="form-horizontal">
                    @csrf
                    <div class="form-group">
                        <label class="control-label" for="OldPassword">Existing Password</label>
                        <input class="form-control" name="OldPassword" type="password" required/>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="NewPassword">New Password</label>
                        <input class="form-control" name="NewPassword" type="password" required/>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="ConfirmPassword">Confirm Password</label>
                        <input class="form-control" name="ConfirmPassword" type="password" required/>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-8">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
