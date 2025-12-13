@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Modify User') }}</div>

                    <div class="card-body">

                        @if(Auth::user()->is_administrator)
                            <form method="post" action="{{ route('squiduser.modify',$id) }}">
                                @include('squidusers.commons.form',['submit'=>'Modify'])
                                @csrf
                            </form>
                        @else
                            <form method="post" action="{{ route('user.squiduser.modify',$id) }}">
                                @include('squidusers.commons.form',['submit'=>'Modify'])
                                @csrf
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
