@extends('layout')

@section('title', 'Dashboard')

@section('body')
    <div class="wrapper my-3">
        <h2>{{ __('general.dashboard') }}</h2>
        <searchable
                name="tag"
                initial="{{$monthYear}}"
                :items='@json($tags)'></searchable>

        <div class="row row--gutter row--responsive my-3">
            <div class="row__column">
                <div class="card card--blue">
                    <h2 style="font-size: 20px;">{!! $currency !!} {{ number_format($totalEarnt / 100, 2) }}</h2>
                    <div class="mt-1" style="color: #A7AEBB;">{{ __('general.total_earnt') }}</div>
                </div>
            </div>
            <div class="row__column">
                <div class="card card--red">
                    <h2 style="font-size: 20px;">{!! $currency !!} {{ number_format($totalSpent / 100, 2) }}</h2>
                    <div class="mt-1" style="color: #A7AEBB;">{{ __('general.total_spent') }}</div>
                </div>
            </div>
            <div class="row__column">
                <div class="card card--green">
                    <h2 style="font-size: 20px;">{!! $currency !!} {{ number_format(($totalEarnt - $totalSpent) / 100, 2) }}</h2>
                    <div class="mt-1" style="color: #A7AEBB;">{{ __('general.difference') }}</div>
                </div>
            </div>
        </div>

        <div class="row row--gutter row--responsive my-3">
            <div class="row__column">
                <div class="card card--blue">
                    <h2 style="font-size: 20px;">{{ __('general.earnings_distribution') }}</h2>
                    <div class="mt-1" style="color: #A7AEBB;">
                        <canvas id="earningsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="row__column">
                <div class="card card--blue">
                    <h2 style="font-size: 20px;">{{ __('general.spendings_distribution') }}</h2>
                    <div class="mt-1" style="color: #A7AEBB;">
                        <canvas id="spendingsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @if (count($mostExpensiveTags))
            <div class="box mt-3">
                <div class="box__section box__section--header">{{ __('general.most_expensive') }} {{ __('models.tags') }}</div>
                @foreach ($mostExpensiveTags as $index => $tag)
                    <div class="box__section row row--seperate">
                        <div class="row__column row__column--middle color-dark">
                            @include('partials.tag', ['payload' => $tag])
                        </div>
                        <div class="row__column row__column--middle">
                            <progress max="{{ $totalSpent }}" value="{{ $tag->amount }}"></progress>
                        </div>
                        <div class="row__column row__column--middle text-right">{!! $currency !!} {{ number_format($tag->amount / 100, 2) }}</div>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="box mt-3">
            <div class="box__section box__section--header">Daily Balance</div>
            <div class="box__section">
                <div class="ct-chart ct-major-twelfth"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function monthYearUpdated(payload) {
            alert("j");
        }
        new Chartist.Line('.ct-chart', {
            labels: [{!! implode(',', range(1, $daysInMonth)) !!}],
            series: [[{!! implode(',', $dailyBalance) !!}]]
        }, {
            showPoint: false,
            lineSmooth: false,

            axisX: {
                showGrid: false
            },

            axisY: {
                labelInterpolationFnc: function (value) {
                    return value.toFixed(0);
                }
            }
        });
        $(document).ready(function () {
            data = {
                datasets: [{
                    data: @json($earningAmounts),
                    backgroundColor: @json($earningColors),
                    label: 'Dataset 1'
                }],
                // These labels appear in the legend and in the tooltips when hovering different arcs
                labels: @json($earningCats)
            };

            Chart.Doughnut('earningsChart', {
                type: 'pie',
                data: data,
                options: {
                    responsive: true,
                    legend: {
                        display: false,
                    },
                }
            });

            data2 = {
                datasets: [{
                    data: @json($spendingAmounts),
                    backgroundColor: @json($spendingColors),
                    label: 'Dataset 1'
                }],
                // These labels appear in the legend and in the tooltips when hovering different arcs
                labels: @json($spendingCats)
            };

            Chart.Doughnut('spendingsChart', {
                type: 'pie',
                data: data2,
                options: {
                    responsive: true,
                    legend: {
                        display: false,
                    },
                }
            });

        });
    </script>

@endsection
