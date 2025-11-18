@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <h3 class="mb-3">Dashboard</h3>
        <p>Welcome back, {{ Auth::user()->name }}!</p>
    </div>
</div>
@endsection
