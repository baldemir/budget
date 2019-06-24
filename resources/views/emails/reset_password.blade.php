@extends('emails.template')

@section('content')
    <a href="{{ config('app.url') }}/reset_password?token={{ $token }}">Şifrenizi sıfırlamak için bu bağlantıya tıklayın.</a>
@endsection
