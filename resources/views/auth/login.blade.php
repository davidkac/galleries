@extends('layouts.default')

@section('title', 'Login')

@section('content')

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="email">Email:</label>
            <input type="text" name="email" id="email" value="{{ old('email') }}" autofocus>
            @error('email')
                <div>{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
            @error('password')
                <div>{{ $message }}</div>
            @enderror
        </div>

        <div class="my-2">
            If you don't have an account yet, you can create one
            <a href="{{ route('register') }}">here</a>.
        </div>
        
        <div>
            <button type="submit">Login</button>
        </div>
    </form>

    @error('invalidCredentials')
        <div>{{ $message }}</div>
    @enderror

@endsection