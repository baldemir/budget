@extends('layout')

@section('title', __('models.accounts'))

@section('body')
    <div class="wrapper my-3">
        <div class="row mb-3">
            <div class="row__column row__column--middle">
                <h2>{{ __('models.accounts') }}</h2>
            </div>
            <div class="row__column row__column--compact row__column--middle">
                <a href="/accounts/create" class="button">{{ __('actions.create') }} {{ __('models.account') }}</a>
            </div>
        </div>
        <div class="box">
            @if (count($accounts))
                <div class="box__section box__section--header row">
                    <div class="row__column row__column--compact mr-2" style="width: 20px;"></div>
                    <div class="row__column">{{ __('fields.name') }}</div>
                    <div class="row__column row__column--double" style="flex: 2;">{{ __('models.spendings') }}</div>
                </div>
                @foreach ($accounts as $account)
                    <div class="box__section row">
                        <div class="row__column row__column--compact row__column--middle mr-2">
                            <div style="width: 15px; height: 15px; border-radius: 2px; background: #F25C68;"></div>
                        </div>
                        <div class="row__column row__column--middle">{{ $account->name }}</div>
                        <div class="row__column row__column--middle">{{ $account->spendings->count() }}</div>
                        <div class="row__column row__column--middle row row--right">
                            <div class="row__column row__column--compact">
                                <a href="/accounts/{{ $account->id }}/edit">
                                    <i class="far fa-pencil"></i>
                                </a>
                            </div>
                            <div class="row__column row__column--compact ml-2">
                                @if ($account->spendings->count())
                                    <i class="far fa-trash-alt"></i>
                                @else
                                    <form method="POST" action="/accounts/{{ $account->id }}">
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
                @include('partials.empty_state', ['payload' => 'accounts'])
            @endif
        </div>
    </div>
@endsection
