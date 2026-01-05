@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Edit Item</h4>

    @php
        if (old('item_attributes')) {
            $attributes = collect(old('item_attributes'))
                ->map(function ($attr) {
                    return [
                        'id' => $attr['id'] ?? null,
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
        } else {
            $attributes = $item->attributes
                ->map(function ($attr) {
                    return [
                        'id' => $attr->id,
                        'label' => $attr->label,
                        'type' => $attr->type,
                        'options' => is_array($attr->options) ? implode(', ', $attr->options) : $attr->options ?? '',
                        'is_required' => $attr->is_required,
                    ];
                })
                ->values();
        }
    @endphp

    <div class="card shadow-sm p-4" x-data="itemAttributesForm({{ $attributes->isNotEmpty() ? $attributes->toJson() : 'null' }})">

        <form method="POST" action="{{ route('items.update', $item->id) }}">
            @csrf
            @method('PUT')

            {{-- Item Basic Info --}}
            <div class="mb-3">
                <label class="form-label">Store</label>
                <x-dropdowns.select-store class="select2" name="store_id" :selected="old('store_id', $item->store_id)" required />
            </div>

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Code</label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $item->code) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $item->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Is Active</label>
                <x-radio-inputs.app-model-status name="is_active" :checked="old('is_active', $item->is_active)" required />
            </div>

            <hr>

            {{-- Item Attributes --}}
            <h5 class="mb-3">Item Attributes</h5>

            <template x-for="(attr, index) in attributes" :key="index">
                <div class="border rounded p-3 mb-3 bg-light">

                    {{-- Preserve existing attribute id --}}
                    <input type="hidden" :name="`item_attributes[${index}][id]`" x-model="attr.id">

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
                <button class="btn btn-primary">Update</button>
                <a href="{{ route('items.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>

    {{-- Alpine Component --}}
    <script>
        function itemAttributesForm(existingAttributes = null) {
            return {
                attributes: existingAttributes && existingAttributes.length ?
                    existingAttributes :
                    [{
                        id: null,
                        label: '',
                        type: 'text',
                        options: '',
                        is_required: 0
                    }],

                add() {
                    this.attributes.push({
                        id: null,
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
