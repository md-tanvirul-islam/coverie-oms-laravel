@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Add Item</h4>

    @php
        $oldAttributes = collect(old('item_attributes', []))
            ->map(function ($attr) {
                return [
                    'label' => $attr['label'] ?? '',
                    'type' => $attr['type'] ?? 'text',
                    'options' => isset($attr['options'])
                        ? (is_array($attr['options'])
                            ? implode(', ', $attr['options'])
                            : $attr['options'])
                        : '',
                    'is_required' => $attr['is_required'] ?? 0,
                ];
            })
            ->values();
    @endphp

    <div class="card shadow-sm p-4" x-data="itemAttributesForm({{ $oldAttributes->isNotEmpty() ? $oldAttributes->toJson() : 'null' }})">

        <form method="POST" action="{{ route('items.store') }}">
            @csrf

            {{-- Item Basic Info --}}
            <div class="mb-3">
                <label class="form-label">Store</label>
                <x-dropdowns.select-store name="store_id" :selected="old('store_id')" required />
            </div>

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Code</label>
                <input type="text" name="code" class="form-control" value="{{ old('code') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Is Active</label>
                <x-radio-inputs.app-model-status name="is_active" :checked="old('is_active', '1')" required />
            </div>

            <hr>

            {{-- Item Attributes --}}
            <h5 class="mb-3">Item Attributes</h5>

            <template x-for="(attr, index) in attributes" :key="index">
                <div class="border rounded p-3 mb-3 bg-light">

                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Label</label>
                            <input type="text" class="form-control" :name="`item_attributes[${index}][label]`"
                                x-model="attr.label" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" :name="`item_attributes[${index}][type]`" x-model="attr.type"
                                required>
                                <option value="text">Text</option>
                                <option value="number">Number</option>
                                <option value="date">Date</option>
                                <option value="select">Select</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Required</label>
                            <select class="form-select" :name="`item_attributes[${index}][is_required]`"
                                x-model="attr.is_required">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm" @click="remove(index)"
                                x-show="attributes.length > 1">
                                Remove
                            </button>
                        </div>
                    </div>

                    {{-- Select Options --}}
                    <div class="mt-2" x-show="attr.type === 'select'">
                        <label class="form-label">Options (comma separated)</label>
                        <input type="text" class="form-control" :name="`item_attributes[${index}][options]`"
                            x-model="attr.options" placeholder="iPhone, Samsung, Xiaomi">
                    </div>

                    <input type="hidden" :name="`item_attributes[${index}][sort_order]`" :value="index">
                </div>
            </template>

            <button type="button" class="btn btn-outline-primary mb-3" @click="add">
                + Add Attribute
            </button>

            <div class="mt-4">
                <button class="btn btn-primary">Create</button>
                <a href="{{ route('items.index') }}" class="btn btn-secondary">Back</a>
            </div>

        </form>
    </div>

    {{-- Alpine Component --}}
    <script>
        function itemAttributesForm(oldAttributes = null) {
            return {
                attributes: oldAttributes && oldAttributes.length ?
                    oldAttributes :
                    [{
                        label: '',
                        type: 'text',
                        options: '',
                        is_required: 0
                    }],

                add() {
                    this.attributes.push({
                        label: '',
                        type: 'text',
                        options: '',
                        is_required: 0
                    })
                },

                remove(index) {
                    this.attributes.splice(index, 1)
                }
            }
        }
    </script>
@endsection
