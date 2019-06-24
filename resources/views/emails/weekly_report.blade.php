@extends('emails.template')

@section('content')
    {{ $space->name }}, işte haftalık raporun.

    Bu hafta (#{{ $week }})
    <ul>
        <li>Harcadığın {!! $space->currency->symbol !!} {{ number_format($totalSpent / 100, 2) }}</li>
        @if (count($largestSpendingWithTag))
            <li>En çok harcama yaptığın kategori: {{ $largestSpendingWithTag[0]->tag_name }} ({!! $space->currency->symbol !!} {{ number_format($largestSpendingWithTag[0]->amount / 100, 2) }})</li>
        @endif
    </ul>

    Bu raporlardan sıkıldın mı? <a href="{{ config('app.url') . '/settings/preferences' }}">Tercihlerini buradan değiştirebilirsin.</a>.
@endsection
