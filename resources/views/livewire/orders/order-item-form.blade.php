<div>
    <h4 class="mb-3">Invoice</h4>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th>Attributes</th>
                    <th>Unit Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($items as $index => $row)
                    <tr>
                        {{-- Item --}}
                        <td style="min-width: 220px">
                            <x-dropdowns.select-item class="select2" wire:model="items.{{ $index }}.item_id" :name="'items.' . $index . '.item_id'"/>
                        </td>

                        {{-- Attributes --}}
                        <td style="min-width: 260px">
                            @forelse ($row['schema'] as $attr)
                                <div class="mb-2">
                                    <label class="small fw-semibold">
                                        {{ $attr['label'] }}
                                    </label>

                                    @if ($attr['type'] === 'select')
                                        <select class="form-select form-select-sm"
                                            wire:model="items.{{ $index }}.attributes.{{ $attr['key'] }}">
                                            <option value="">Select One</option>
                                            @foreach ($attr['options'] as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="{{ $attr['type'] }}" class="form-control form-control-sm"
                                            wire:model.lazy="items.{{ $index }}.attributes.{{ $attr['key'] }}">
                                    @endif
                                </div>
                            @empty
                                <span class="text-muted">—</span>
                            @endforelse
                        </td>

                        {{-- Price --}}
                        <td>
                            <input type="number" class="form-control form-control-sm"
                                wire:model="items.{{ $index }}.price" step="0.01">
                        </td>

                        {{-- Quantity --}}
                        <td>
                            <input type="number" class="form-control form-control-sm"
                                wire:model="items.{{ $index }}.quantity" min="1">
                        </td>

                        {{-- Subtotal --}}
                        <td class="fw-semibold">
                            {{ number_format(($row['price'] ?? 0) * ($row['quantity'] ?? 0), 2) }}
                        </td>

                        {{-- Action --}}
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm" wire:click="removeItem({{ $index }})"
                                @disabled(count($items) === 1)>
                                ✕
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="4" class="text-end">Discount</td>
                    <td colspan="2">
                        <input type="number" class="form-control form-control-sm" wire:model="discount" step="0.01">
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end fw-bold">Total</td>
                    <td colspan="2" class="fw-bold">
                        {{ number_format($this->total, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <button class="btn btn-outline-primary mt-2" wire:click="addItem">
        + Add Item
    </button>
</div>
