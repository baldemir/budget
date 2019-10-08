@extends('layout')

@section('title', __('models.transactions'))

@section('body')
    <div class="wrapper my-3">
        @if (session('restorableEarning'))
            <div class="mt-3">{{ __('general.you_deleted_earning') }}</div>
            <form method="POST" action="/earnings/{{ session('restorableEarning') }}/restore" class="mt-05">
                {{ csrf_field() }}
                <button class="button link">{{ __('general.undo') }}</button>
            </form>
        @endif
        @if (session('restorableSpending'))
            <div class="mt-3">{{ __('general.you_deleted_spending') }}</div>
            <form method="POST" action="/earnings/{{ session('restorableSpending') }}/restore" class="mt-05">
                {{ csrf_field() }}
                <button class="button link">{{ __('general.undo') }}</button>
            </form>
        @endif
        <div class="row mb-3">
            <div class="row__column row__column--middle">
                <h2>{{ __('models.transactions') }}</h2>
            </div>
            <div class="row__column row__column--compact row__column--middle">
                <a href="/transactions/create" class="button">{{ __('actions.create') }} {{ __('models.transactions') }}</a>
            </div>
        </div>
        <div class="row row--responsive">
            <div class="row__column mr-3" style="max-width: 300px;">
                <div class="box">
                    <div class="box__section">
                        <div class="mb-2">
                            <a href="/transactions">Reset</a>
                        </div>
                        <span>Filter by Tag</span>
                        @foreach ($tags as $tag)
                            <div class="mt-1 ml-1">
                                <a href="/transactions?filterBy=tag-{{ $tag->id }}">{{ $tag->name }}</a>
                            </div>
                        @endforeach
                    </div>
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
                        @include('partials.empty_state', ['payload' => 'transactions'])
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
