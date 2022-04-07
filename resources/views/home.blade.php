@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Create Ticket') }}</div>

                <div class="card-body">
                    @if (\Session::has('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger" role="alert">
                            {{ $error }}
                        </div>
                    @endforeach

                    <form action="{{ route('saveTicket') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Message</label>
                                <input type="text" name="subject" class="form-control">
                            </div>
                            <div class="col-lg-12">&nbsp;</div>
                            <div class="col-lg-12">
                                <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
