@extends('layout')

@section('title', 'Register')

@section('body')
    <div class="wrapper wrapper--narrow my-3">
        <h2 class="text-center mb-3">Register</h2>
        <div class="box">
            <div class="box__section">
                <form method="POST">
                    {{ csrf_field() }}
                    <div class="input">
                        <label>Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" />
                        @include('partials.validation_error', ['payload' => 'name'])
                    </div>
                    <div class="input">
                        <label>E-mail</label>
                        <input type="email" name="email" value="{{ old('email') }}" />
                        @include('partials.validation_error', ['payload' => 'email'])
                    </div>
                    <div class="input">
                        <label>Password</label>
                        <input type="password" name="password" />
                        @include('partials.validation_error', ['payload' => 'password'])
                    </div>
                    <div class="input">
                        <label>Verify Password</label>
                        <input type="password" name="password_confirmation" />
                        @include('partials.validation_error', ['payload' => 'password_confirmation'])
                    </div>
                    <div class="input">
                        <label>Currency</label>
                        <searchable
                            name="currency"
                            size="2"
                            :items='@json($currencies)'
                            initial="{{ old('currency') }}"></searchable>
                        @include('partials.validation_error', ['payload' => 'currency'])
                    </div>
                    <button class="button">Register</button>
                </form>
            </div>
        </div>
        <div class="text-center mt-2">
            <a class="fs-sm" href="/login">Already on Kolay Bütçe?</a>
        </div>
    </div>
@endsection
