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
                        <input disabled type="text" name="description" value="{{ __('general.currency_' .$account->currency_id) }}" />
                    </div>
                </div>

            </div>
        </div>

        <div class="row mb-3 mt-3">
            <div class="row__column row__column--middle">
                <h2>{{ __('models.transactions') }}</h2>
            </div>
        </div>

        <div class="row__column">

            @if ($yearMonths)
                @foreach ($yearMonths as $key => $transactions)
                    <h2 class="{{ key($yearMonths) != $key ? 'mt-3' : '' }} mb-2">{{ __('calendar.months.' . ltrim(explode('-', $key)[1], 0)) }}, {{ explode('-', $key)[0] }}</h2>
                    <div class="box">
                        @foreach ($transactions as $transaction)
                            <div class="box__section row">
                                <div class="row__column row__column--middle">{{ date('d-m-Y', strtotime($transaction->happened_on))}}</div>
                                <div class="row__column row__column--middle">{{ $transaction->description }}</div>
                                <div class="row__column">
                                    @if ($transaction->tag)
                                        <div class="row">
                                            <div class="row__column row__column--compact row__column--middle mr-05" style="font-size: 12px;">
                                                <i class="fas fa-tag" style="color: #{{ $transaction->tag->color }};"></i>
                                            </div>
                                            <div class="row__column row__column--compact row__column--middle">{{ $transaction->tag->name }}</div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row__column row__column--compact row__column--middle {{ get_class($transaction) == 'App\Earning' ? 'color-green' : 'color-red' }}">{!! $currency !!} {{ $transaction->formatted_amount }}</div>
                                <div class="row__column row__column--compact row__column--middle ml-1 {{ get_class($transaction) == 'App\Earning' ? 'color-green' : 'color-red' }}">
                                    @if (get_class($transaction) == 'App\Earning')
                                        <i class="fas fa-arrow-alt-left fa-sm"></i>
                                    @else
                                        <i class="fas fa-arrow-alt-right fa-sm"></i>
                                    @endif
                                </div>

                                <div class="row__column row__column--compact row__column--middle ml-1 color-red">
                                    <img alt="{{ $transaction->account->provider->name }}" src="{{ $transaction->account->provider->icon }}" style="width: 30px; border-radius: 50%;">
                                </div>

                                <div class="row__column row__column--middle row row--right">
                                    <div class="row__column row__column--compact">
                                        <a @if (get_class($transaction) == 'App\Earning')href="/earnings/{{ $transaction->id }}/edit" @endif @if (get_class($transaction) == 'App\Spending')href="/spendings/{{ $transaction->id }}/edit" @endif>
                                            <i class="far fa-pencil"></i>
                                        </a>
                                    </div>
                                    <div class="row__column row__column--compact ml-2">
                                        <form method="POST" @if (get_class($transaction) == 'App\Earning')action="/earnings/{{ $transaction->id }}" @endif @if(get_class($transaction) == 'App\Spending')action="/spendings/{{ $transaction->id }}" @endif>
                                            {{ method_field('DELETE') }}
                                            {{ csrf_field() }}
                                            <button class="button link">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <div class="box">
                    <div class="box__section text-center">
                        <div class="mb-1">{{ __('general.no_transactions_yet') }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
