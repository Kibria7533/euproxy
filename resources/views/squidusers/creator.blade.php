@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Create SquidUser') }}</div>

                    <div class="card-body">

                        @if(Auth::user()->is_administrator)
                            <form method="post" action="{{ route('squiduser.create',request()->user()->id) }}">
                                @include('squidusers.commons.form',['submit'=>'Create'])
                                @csrf
                            </form>
                        @else
                            <form method="post" action="{{ route('user.squiduser.create') }}">
                                @include('squidusers.commons.form',['submit'=>'Create'])
                                @csrf
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
