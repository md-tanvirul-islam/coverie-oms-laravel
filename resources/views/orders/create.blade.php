@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Add Order</h4>

    <div class="card shadow-sm p-4">
        @livewire('orders.create-form', ['employees' => $employees, 'items' => $items])
    </div>
@endsection