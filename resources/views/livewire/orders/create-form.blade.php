<div>
    <form wire:submit.prevent="submit" enctype="multipart/form-data">

        {{-- Store --}}
        <select wire:model="store_id" class="form-control" required>
            <option value="">Select Store</option>
            @foreach ($stores as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>

        {{-- Invoice ID --}}
        <input type="text" wire:model="invoice_id" class="form-control" required>

        {{-- Customer Info, Employee etc. --}}

        {{-- Items --}}
        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Attributes</th>
                    <th>Documents</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $index => $item)
                    <tr>
                        <td>
                            <select wire:model="items.{{ $index }}.item_id" class="form-control">
                                <option value="">-- Select --</option>
                                @foreach ($allItems as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </td>

                        <td>
                            @foreach ($item['schema'] ?? [] as $attr)
                                <div>
                                    <label>{{ $attr['label'] }}</label>
                                    @if ($attr['type'] == 'select')
                                        <select wire:model="items.{{ $index }}.attributes.{{ $attr['key'] }}">
                                            <option value="">-- Select --</option>
                                            @foreach ($attr['options'] as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="{{ $attr['type'] }}"
                                            wire:model="items.{{ $index }}.attributes.{{ $attr['key'] }}">
                                    @endif
                                </div>
                            @endforeach
                        </td>

                        <td>
                            <input type="file" wire:model="items.{{ $index }}.documents" multiple>
                            @if (!empty($item['documents']))
                                <ul>
                                    @foreach ($item['documents'] as $f)
                                        <li>{{ $f->getClientOriginalName() ?? '' }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>

                        <td><input type="number" wire:model="items.{{ $index }}.unit_price"></td>
                        <td><input type="number" wire:model="items.{{ $index }}.quantity"></td>
                        <td>{{ $item['unit_price'] * $item['quantity'] }}</td>
                        <td><button type="button" wire:click="removeItem({{ $index }})">✕</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" wire:click="addItem()">+ Add Item</button>

        <div>Total: {{ $this->total }}</div>

        <button type="submit">Create</button>
    </form>
</div>
