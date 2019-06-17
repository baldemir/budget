@extends('layout')

@section('title', 'Reset Password')

@section('body')
    <div class="wrapper wrapper--narrow my-3">
        <h2 class="text-center mb-3">{{ __('general.reset_password') }}</h2>
        <div class="box">
            <div class="box__section">
                <form method="POST">
                    {{ csrf_field() }}
                    @if ($token)
                        <input type="hidden" name="token" value="{{ $token }}" />
                        <div class="input">
                            <label>{{ __('fields.password') }}</label>
                            <input type="password" name="password" />
                        </div>
                        <div class="input">
                            <label>{{ __('general.verify_password') }}</label>
                            <input type="password" name="password_confirmation" />
                        </div>
                    @else
                        <div class="input">
                            <label>{{ __('fields.email') }}</label>
                            <input type="email" name="email" />
                        </div>
                    @endif
                    <button class="button">{{ __('general.submit') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection