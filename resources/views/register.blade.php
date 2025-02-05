@extends('layout')

@section('title'){{ __('general.register') }}@endsection

@section('body')
    <div class="wrapper wrapper--narrow my-3">
        <h2 class="text-center mb-3">{{ __('general.register') }}</h2>
        <div class="box">
            <div class="box__section">
                <form method="POST">
                    {{ csrf_field() }}
                    <div class="input">
                        <label>{{ __('fields.name') }}</label>
                        <input type="text" name="name" value="{{ old('name') }}" />
                        @include('partials.validation_error', ['payload' => 'name'])
                    </div>
                    <div class="input">
                        <label>{{ __('fields.email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}" />
                        @include('partials.validation_error', ['payload' => 'email'])
                    </div>
                    <div class="input">
                        <label>{{ __('fields.password') }}</label>
                        <input type="password" name="password" />
                        @include('partials.validation_error', ['payload' => 'password'])
                    </div>
                    <div class="input">
                        <label>{{ __('general.verify_password') }}</label>
                        <input type="password" name="password_confirmation" />
                        @include('partials.validation_error', ['payload' => 'password_confirmation'])
                    </div>
                    <div class="input">
                        <label>{{ __('fields.currency') }}</label>
                        <searchable
                            name="currency"
                            size="2"
                            :items='@json($currencies)'
                            initial="{{ old('currency') }}"></searchable>
                        @include('partials.validation_error', ['payload' => 'currency'])
                    </div>
                    <button class="button">{{ __('general.register') }}</button>
                </form>
            </div>
        </div>
        <div class="text-center mt-2">
            <a class="fs-sm" href="/login">Kolay Bütçe'ye kayıtlı mısın?</a>
        </div>
    </div>
@endsection
