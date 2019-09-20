@extends('layout')

@section('title', __('models.tags'))

@section('body')
    <div class="wrapper my-3">
        <div class="row mb-3">
            <div class="row__column row__column--middle">
                <h2>{{ __('models.bank_accounts') }}</h2>
            </div>
            <div class="row__column row__column--compact row__column--middle">
                <a href="/providers/create" class="button">{{ __('actions.create') }} {{ __('models.tag') }}</a>
            </div>
        </div>
        <div class="box">
            @if (count($providers))
                <div class="box__section box__section--header row">
                    <div class="row__column row__column--compact mr-2" style="width: 20px;"></div>
                    <div class="row__column">{{ __('fields.name') }}</div>
                    <div class="row__column row__column--double" style="flex: 2;">{{ __('models.accounts') }}</div>
                </div>
                @foreach ($providers as $provider)
                    <div class="box__section row">
                        <div class="row__column row__column--compact row__column--middle mr-2">
                            <img style="width: 15px; height: 15px; border-radius: 2px;" src="{{ $provider->icon }}"/>
                        </div>
                        <div class="row__column row__column--middle">{{ $provider->name }}</div>
                        <div class="row__column row__column--middle">{{ $provider->accounts(1)->count() }}</div>
                        <div class="row__column row__column--middle row row--right">
                            <div class="row__column row__column--compact">
                                <a href="/tags/{{ $provider->id }}/edit">
                                    <i class="far fa-pencil"></i>
                                </a>
                            </div>
                            <div class="row__column row__column--compact ml-2">
                                @if ($provider->accounts(1)->count())
                                    <i class="far fa-trash-alt"></i>
                                @else
                                    <form method="POST" action="/providers/{{ $provider->id }}">
                                        {{ method_field('DELETE') }}
                                        {{ csrf_field() }}
                                        <button class="button link">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                @include('partials.empty_state', ['payload' => 'tags'])
            @endif
        </div>
    </div>


@endsection
