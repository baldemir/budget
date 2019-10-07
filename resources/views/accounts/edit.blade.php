@extends('layout')

@section('title', __('actions.edit') . ' ' . __('models.tag'))

@section('body')
    <div class="wrapper my-3">
        <h2>{{ __('actions.edit') }} {{ __('models.account') }}</h2>
        <div class="row row--responsive box">
            <div class="row__column mr-3" style="">
                <form method="POST" action="/accounts/{{ $account->id }}">
                    {{ method_field('PATCH') }}
                    {{ csrf_field() }}
                    <div class="box__section">
                        <div class="input input--small">
                            <label>{{ __('fields.name') }}</label>
                            <input type="text" name="name" value="{{ $account->name }}" />
                            @include('partials.validation_error', ['payload' => 'name'])
                        </div>
                        <div class="input input--small">
                            <label>{{ __('fields.description') }}</label>
                            <input type="text" name="description" value="{{ $account->description }}" />
                            @include('partials.validation_error', ['payload' => 'description'])
                        </div>
                        <div class="input input--small mb-0">
                            <label>{{ __('fields.color') }}</label>
                            <color-picker initial-color="{{ $account->color }}"></color-picker>
                            @include('partials.validation_error', ['payload' => 'color'])
                        </div>
                    </div>
                    <div class="box__section box__section--highlight row row--right">
                        <div class="row__column row__column--compact row__column--middle">
                            <a href="/tags">{{ __('actions.cancel') }}</a>
                        </div>
                        <div class="row__column row__column--compact ml-2">
                            <button class="button">{{ __('actions.save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row__column mr-3" style="">
                <div class="box__section">
                    <div class="input input--small">
                        <label>{{ __('fields.branch_name') }}</label>
                        <input disabled type="text" name="name" value="{{ $account->branch_name }}" />
                    </div>
                    <div class="input input--small">
                        <label>{{ __('fields.open_date') }}</label>
                        <input disabled type="text" name="description" value="{{ date('d-m-Y', strtotime($account->open_date)) }}" />
                    </div>
                    <div class="input input--small">
                        <label>{{ __('fields.balance') }}</label>
                        <input disabled type="text" name="description" value="{{ $account->balance }}" />
                    </div>
                    <div class="input input--small">
                        <label>{{ __('fields.last_sync') }}</label>
                        <input disabled type="text" name="description" value="{{ date('d-m-Y h:i:s', strtotime($account->last_sync)) }}" />
                    </div>
                    <div class="input input--small">
                        <label>{{ __('fields.currency') }}</label>
                        <input disabled type="text" name="description" value="{{ $account->currency_id }}" />
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
