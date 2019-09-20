@extends('layout')

@section('title', __('actions.edit') . ' ' . __('models.tag'))

@section('body')
    <div class="wrapper my-3">
        <h2>{{ __('models.bank_accounts') }}</h2>
        <div class="box">
            @if (count($accounts))
                <div class="box__section box__section--header row">
                    <div class="row__column row__column--compact mr-2" style="width: 20px;"></div>
                    <div class="row__column row__column--double">{{ __('fields.provider') }}</div>
                    <div class="row__column " style="">{{ __('fields.account_name') }}</div>
                    <div class="row__column row row--right">{{ __('fields.synchronize') }}</div>
                </div>
                @foreach ($accounts as $account)
                    <div class="box__section row">
                        <div class="row__column row__column--compact row__column--middle mr-2">
                            <img style="width: 15px; height: 15px; border-radius: 2px;" src="{{ $account->icon }}"/>
                        </div>
                        <div class="row__column row__column--double">{{ $account->name }}</div>
                        <div class="row__column ">-</div>
                        <div class="row__column row row--right">
                            <div class="row__column row__column--compact">
                                <input name="account-{{$account->id}}" @if($account->status == 1) checked @endif class="account-toggle" type="checkbox">
                            </div>
                            <div id="account-{{$account->id}}" class="row__column row__column--compact ml-2" style="display: none;">
                                <form method="POST" action="/accounts/{{ $account->id }}/status">
                                    {{ method_field('POST') }}
                                    {{ csrf_field() }}
                                    <input hidden id="check-account-{{$account->id}}" name="status" class="account-toggle" type="checkbox">
                                    <button class="button link">
                                        {{ __('actions.save') }}</i>
                                    </button>
                                </form>
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


@section('scripts')
<script>
    $('input.account-toggle').change(function() {
        $("#" + $(this).attr('name')).show();
        $("#check-" + $(this).attr('name')).prop("checked", this.checked);
    });
</script>

@endsection