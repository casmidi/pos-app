@extends('adminlte::auth.auth-page', ['type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
@stop

@php( $login_url    = route('login') )
@php( $register_url = null )

@section('auth_header', 'POS App – Masuk')

@section('auth_body')
    <form action="{{ route('login') }}" method="POST">
        @csrf

        {{-- Email --}}
        <div class="input-group mb-3">
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="Email"
                autofocus
                required
            >
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
            </div>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="input-group mb-3">
            <input
                type="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Password"
                required
            >
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-lock"></span></div>
            </div>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Remember --}}
        <div class="row mb-3">
            <div class="col-8">
                <div class="icheck-primary">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Ingat saya</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">Masuk</button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    <p class="mb-0 text-center text-muted small">POS &copy; {{ date('Y') }}</p>
@stop
