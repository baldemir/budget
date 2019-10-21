@extends('layout')

@section('title', __('actions.create') . ' ' . __('models.earning'))

@section('body')
    <div class="wrapper my-3">
        <h2>{{ __('actions.add_provider') }}</h2>

        @for ($i = 0; $i < ceil(count($providers)/4); $i++)

            <div class="row row--gutter row--responsive my-3">
                <div class="row__column">
                    @if($i*4<count($providers))
                        <div class="card card--red">
                            <img alt="{{ $providers[$i*4]->name }}" src="{{ $providers[$i*4]->icon }}" style="width: 60px; border-radius: 50%;display: block;margin: auto;">
                            <div class="mt-2">{{ $providers[$i*4]->name}}</div>
                            <div class="mt-1">
                                @if(count($connectedProviders->where('provider_id', $providers[$i*4]->id))>0)
                                    <span>{{__('general.added')}}</span>
                                @else
                                    <a href="{{$providers[$i*4]->login_url}}">{{ __('actions.add') }}</a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row__column">
                    @if($i*4+1<count($providers))
                        <div class="card card--red">
                            <img alt="{{ $providers[$i*4+1]->name }}" src="{{ $providers[$i*4+1]->icon }}" style="width: 60px; border-radius: 50%;display: block;margin: auto;">
                            <div class="mt-2">{{ $providers[$i*4+1]->name}}</div>
                            <div class="mt-1">
                                @if(count($connectedProviders->where('provider_id', $providers[$i*4+1]->id))>0)
                                    <span>{{__('general.added')}}</span>
                                @else
                                    <a href="{{$providers[$i*4+1]->login_url}}">{{ __('actions.add') }}</a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                <div class="row__column">
                    @if($i*4+2<count($providers))
                        <div class="card card--red">
                            <img alt="{{ $providers[$i*4+2]->name }}" src="{{ $providers[$i*4+2]->icon }}" style="width: 60px; border-radius: 50%;display: block;margin: auto;">
                            <div class="mt-2">{{ $providers[$i*4+2]->name}}</div>
                            <div class="mt-1">

                                @if(count($connectedProviders->where('provider_id', $providers[$i*4+2]->id))>0)
                                    <span>{{__('general.added')}}</span>
                                @else
                                    <a href="{{$providers[$i*4+2]->login_url}}">{{ __('actions.add') }}</a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                <div class="row__column">
                    @if($i*4+3<count($providers))
                        <div class="card card--red">
                            <img alt="{{ $providers[$i*4+3]->name }}" src="{{ $providers[$i*4+3]->icon }}" style="width: 60px; border-radius: 50%;display: block;margin: auto;">
                            <div class="mt-2">{{ $providers[$i*4+3]->name}}</div>
                            <div class="mt-1">
                                @if(count($connectedProviders->where('provider_id', $providers[$i*4+3]->id))>0)
                                    <span>{{__('general.added')}}</span>
                                @else
                                    <a href="{{$providers[$i*4+3]->login_url}}">{{ __('actions.add') }}</a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>


            </div>
        @endfor

    </div>
@endsection
