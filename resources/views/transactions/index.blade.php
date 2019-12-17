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

                                            <select name="tag_id" transaction_id="{{$transaction->id}}" transaction_type="{{ get_class($transaction) == 'App\Spending' ? 1 : 2}}" class="tag-auto-select" onchange="categoryChanged(this, this.value)">
                                                <option value="">-</option>
                                                @foreach ($tags as $tag)
                                                    @if((get_class($transaction) == 'App\Spending' && $tag->type == 1) || (get_class($transaction) == 'App\Earning' && $tag->type == 2))
                                                        <option @if($transaction->tag_id == $tag->id) selected @endif value="{{ $tag->id }}">{{ $tag->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>

                                    </div>
                                    <div class="amount-field row__column row__column--compact row__column--middle {{ get_class($transaction) == 'App\Earning' ? 'color-green' : 'color-red' }}">{!! $currency !!} {{ $transaction->formatted_amount }}</div>
                                    <div class="row__column row__column--compact row__column--middle ml-1 tooltip {{ get_class($transaction) == 'App\Earning' ? 'color-green' : 'color-red' }}">
                                        @if($transaction->additional_desc)
                                            @if (get_class($transaction) == 'App\Earning')
                                                <i class="fas fa-info-circle fa-sm"></i>
                                                <span class="tooltiptext green-desc">{{ $transaction->additional_desc }}</span>
                                            @else
                                                <i class="fas fa-info-circle fa-sm"></i>
                                                <span class="tooltiptext red-desc">{{ $transaction->additional_desc }}</span>
                                            @endif
                                        @endif

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
@section('scripts')
    <script >
        function categoryChanged(elem, val){
            console.log(elem.getAttribute('transaction_id'));
            console.log(val);
            var url = "/transactions/update";
            if(elem.getAttribute('transaction_type') == 1){
                url += "Spending/";
            }else{
                url += "Earning/";
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: url + elem.getAttribute('transaction_id'),
                data: { tag_id: val}
            }).done(function( msg ) {
                    
                });
        }
    </script>
@endsection
@section('styles')
    <style>
        .amount-field{
            width: 85px;
        }
        .tooltip {
            position: relative;
            display: inline-block;
            width: 20px;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 120px;
            opacity: 1;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 0;
            position: absolute;
            z-index: 1;
            top: -5px;
            left: 110%;
        }
        .red-desc {
            background-color: #f57c7e;
        }
        .green-desc {
            background-color: #82cc6e;
        }
        .red-desc:after{
            border-color: transparent #f57c7e transparent transparent;
        }
        .green-desc:after{
            border-color: transparent #82cc6e transparent transparent;
        }

        .tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            top: 50%;
            right: 100%;
            margin-top: -5px;
            border-width: 5px;
            border-style: solid;

        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
        }

        .tag-auto-select {
            max-width: 180px;
            width: inherit;
            cursor: pointer;
        }
        .tag-auto-select:focus {
            box-shadow: none;
        }
    </style>
@endsection