@extends('layouts.app')

@section('content')
    <div class="container py-3">
        {{-- Header --}}
        <div class="card mb-3">
            <div class="card-body">
                <h3 class="mb-3">Dashboard</h3>
                <p>Welcome back, {{ Auth::user()->name }}!</p>
            </div>
        </div>


        @role(\App\Enums\SystemDefinedRole::ADMIN)
        @else
            {{-- Statics Card --}}
            <livewire:statistic-cards />

            {{-- Collected Amount Bar Char --}}
            <livewire:collected-amount-bar-chart />
        @endrole

    </div>
@endsection
