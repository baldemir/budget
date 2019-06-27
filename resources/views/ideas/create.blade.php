@extends('layout')

@section('body')
    <div class="wrapper my-3">
        <div class="box">
            <form method="POST" action="/ideas">
                {{ csrf_field() }}
                <div class="box__section">
                    <div class="input input--small">
                        <label>{{ __('general.type') }}</label>
                        <select name="type">
                            <option value="bug">{{ __('general.bug_or_error') }}</option>
                            <option value="feature_request">{{ __('general.suggestion') }}</option>
                        </select>
                        @include('partials.validation_error', ['payload' => 'type'])
                    </div>
                    <div class="input input--small">
                        <label>{{ __('fields.description') }}</label>
                        <textarea name="body"></textarea>
                        @include('partials.validation_error', ['payload' => 'body'])
                    </div>
                    <button class="button">{{ __('general.submit') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
