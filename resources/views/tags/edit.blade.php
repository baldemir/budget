@extends('layout')

@section('title', __('actions.edit') . ' ' . __('models.tag'))

@section('body')
    <div class="wrapper my-3">
        <h2>{{ __('actions.edit') }} {{ __('models.tag') }}</h2>
        <div class="box mt-3">
            <form method="POST" action="/tags/{{ $tag->id }}" enctype="multipart/form-data">
                {{ method_field('PATCH') }}
                {{ csrf_field() }}
                <div class="box__section">
                    <div class="input input--small">
                        <label>{{ __('fields.name') }}</label>
                        <input type="text" name="name" value="{{ $tag->name }}" />
                        @include('partials.validation_error', ['payload' => 'name'])
                    </div>
                    <div class="input input--small mb-0">
                        <label>{{ __('fields.color') }}</label>
                        <color-picker initial-color="{{ $tag->color }}"></color-picker>
                        @include('partials.validation_error', ['payload' => 'color'])
                    </div>

                    <div class="input input--small">
                        <label>{{ __('fields.image') }}</label>
                        <img src="{{ $tag->image }}" style="width: 200px; height: 200px; border-radius: 5px; object-fit: cover;" />
                        <input type="file" name="image" />
                        @include('partials.validation_error', ['payload' => 'image'])
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
    </div>
@endsection
