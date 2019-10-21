@extends('layout')

@section('title', __('models.providers'))

@section('body')
    <div class="wrapper my-3">
        <div class="row mb-3">
            <div class="row__column row__column--middle">
                <h2>{{ __('models.connected_providers') }}</h2>
            </div>
            <div class="row__column row__column--compact row__column--middle">
                <a href="/providers/create" class="button">{{ __('actions.add_provider') }}</a>
            </div>
        </div>
        <div class="box">
            @if (count($connectedProviders ))
                <div class="box__section box__section--header row">
                    <div class="row__column row__column--compact mr-2" style="width: 20px;"></div>
                    <div class="row__column">{{ __('fields.name') }}</div>
                    <div class="row__column row__column--double" style="flex: 2;">{{ __('models.accounts') }}</div>
                </div>
                @foreach ($connectedProviders as $connectedProvider)
                    <div class="box__section row">
                        <div class="row__column row__column--compact row__column--middle mr-2">
                            <img style="width: 15px; height: 15px; border-radius: 2px;" src="{{ $connectedProvider->provider->icon }}"/>
                        </div>
                        <div class="row__column row__column--middle">{{ $connectedProvider->provider->name }}</div>
                        <div class="row__column row__column--middle">{{ $connectedProvider->accounts()->count() }}</div>
                        <div class="row__column row__column--middle row row--right">
                            <div class="row__column row__column--compact">
                                <a href="/providers/{{ $connectedProvider->id }}/edit">
                                    <i class="far fa-pencil"></i>
                                </a>
                            </div>
                            <div class="row__column row__column--compact ml-2">
                                @if ($connectedProvider->accounts()->count())
                                    <i class="far fa-trash-alt"></i>
                                @else
                                    <form method="POST" action="/providers/{{ $connectedProvider->id }}">
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
