@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Edit Order</h4>

    <div class="card shadow-sm p-4">
        <form id="orderForm" enctype="multipart/form-data" @submit.prevent="submitForm">
            @csrf
            @method('PUT')

            {{-- Store --}}
            <div class="mb-3">
                <label class="form-label">Store</label>
                <x-dropdowns.select-store name="store_id" :selected="old('store_id', $order->store_id)" required />
            </div>

            {{-- Invoice ID --}}
            <div class="mb-3">
                <label class="form-label">Invoice ID</label>
                <input type="text" name="invoice_code" class="form-control"
                    value="{{ old('invoice_code', $order->invoice_code) }}" required>
            </div>

            {{-- Order Date --}}
            <div class="mb-3">
                <label class="form-label">Order Date</label>
                <input type="date" name="order_date" class="form-control"
                    value="{{ old('order_date', $order->order_date) }}" required>
            </div>

            {{-- Customer Info --}}
            <div class="mb-3">
                <label class="form-label">Customer Name</label>
                <input type="text" name="customer_name" class="form-control"
                    value="{{ old('customer_name', $order->customer_name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Customer Phone</label>
                <input type="text" name="customer_phone" class="form-control"
                    value="{{ old('customer_phone', $order->customer_phone) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Customer Address</label>
                <textarea name="customer_address" class="form-control" rows="2">{{ old('customer_address', $order->customer_address) }}</textarea>
            </div>

            {{-- Employee --}}
            <div class="mb-3">
                <label class="form-label">Order Taken By</label>
                <select name="taker_employee_id" class="form-control" required>
                    <option value="">Select Employee</option>
                    @foreach ($employees as $id => $name)
                        <option value="{{ $id }}"
                            {{ old('taker_employee_id', $order->taker_employee_id) == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @php
                $orderItems = old(
                    'items',
                    $order->items
                        ->map(function ($i) {
                            $parsed_attributes = [];
                            foreach ($i->attributes ?? [] as $key => $value) {
                                $parsed_attributes[] = parseItemAttribute($key, $value);
                            }
                            $docs = [];
                            foreach ($i->documents ?? [] as $doc) {
                                $docs[] = ['id' => $doc->id, 'name' => $doc->file_name];
                            }
                            return [
                                'id' => $i->id,
                                'item_id' => $i->item_id,
                                'unit_price' => $i->unit_price,
                                'quantity' => $i->quantity,
                                'schema' => $parsed_attributes ?? [],
                                'attributes' => $i->attributes ?? [],
                                'documents' => $docs,
                                'removed_document_ids' => [],
                            ];
                        })
                        ->toArray(),
                );
                // dd($orderItems);
            @endphp

            {{-- Invoice Items --}}
            <div x-data='invoiceForm(@json($orderItems), {{ $order->discount ?? 0 }})' class="mt-3">
                <h4 class="mb-3">Order Items</h4>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Attributes</th>
                                <th>Document</th>
                                <th>Unit Price</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td style="min-width:220px">
                                        <select class="form-select form-select-sm" x-model="item.item_id"
                                            @change="loadAttributes(index)">
                                            <option value="">-- Select Item --</option>
                                            @foreach ($items as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="min-width:260px">
                                        <template x-for="attr in item.schema" :key="attr.key">
                                            <div class="mb-2">
                                                <label class="small fw-semibold" x-text="attr.label"></label>

                                                <template x-if="attr.type === 'select'">
                                                    <select class="form-select form-select-sm"
                                                        x-model="item.attributes[attr.key]">
                                                        <option value="">-- Select --</option>
                                                        <template x-for="opt in attr.options" :key="opt">
                                                            <option x-text="opt"></option>
                                                        </template>
                                                    </select>
                                                </template>

                                                <template x-if="attr.type !== 'select'">
                                                    <input class="form-control form-control-sm" :type="attr.type"
                                                        x-model="item.attributes[attr.key]">
                                                </template>
                                            </div>
                                        </template>
                                    </td>

                                    <td style="min-width:260px">
                                        <input type="file" class="d-none" multiple @change="addFiles($event, index)">
                                        <button type="button" class="btn btn-outline-secondary btn-sm mb-1"
                                            @click="$el.previousElementSibling.click()">+ Add Document</button>

                                        <template x-if="item.documents.length">
                                            <ul class="small mt-2 mb-0 ps-3">
                                                <template x-for="(file, fIndex) in item.documents" :key="fIndex">
                                                    <li class="d-flex justify-content-between align-items-center">
                                                        <span class="text-truncate" x-text="file.name"></span>
                                                        <button type="button" class="btn btn-link text-danger btn-sm p-0"
                                                            @click="removeFile(index, fIndex)">✕</button>
                                                    </li>
                                                </template>
                                            </ul>
                                        </template>
                                        <template x-if="!item.documents.length">
                                            <div class="text-muted small mt-1">No documents</div>
                                        </template>
                                    </td>

                                    <td>
                                        <input type="number" min="0" step="0.01"
                                            class="form-control form-control-sm" x-model.number="item.unit_price">
                                    </td>

                                    <td>
                                        <input type="number" min="1" class="form-control form-control-sm"
                                            x-model.number="item.quantity">
                                    </td>

                                    <td class="fw-semibold">
                                        <span x-text="format(item.unit_price * item.quantity)"></span>
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm" @click="removeItem(index)"
                                            :disabled="items.length === 1">✕</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end">Discount</td>
                                <td colspan="2">
                                    <input type="number" min="0" class="form-control form-control-sm"
                                        x-model.number="discount">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total</td>
                                <td colspan="2" class="fw-bold">
                                    <span x-text="format(total())"></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <button type="button" class="btn btn-outline-primary mt-2" @click="addItem()">+ Add Item</button>

                <div class="mt-3">
                    <button type="button" class="btn btn-primary" @click="submitForm()">Update</button>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        window.invoiceForm = function(preItems = [], preDiscount = 0) {
            return {
                items: preItems.length ? preItems : [{
                    id: null,
                    item_id: null,
                    unit_price: 0,
                    quantity: 1,
                    schema: [],
                    attributes: {},
                    documents: [],
                    removed_document_ids: []
                }],

                discount: preDiscount,
                removed_order_item_ids: [],

                addItem() {
                    this.items.push({
                        id: null,
                        item_id: null,
                        unit_price: 0,
                        quantity: 1,
                        schema: [],
                        attributes: {},
                        documents: [],
                        removed_document_ids: []
                    });
                },

                removeItem(index) {
                    const item = this.items[index];
                    if (item.id) {
                        this.removed_order_item_ids.push(item.id);
                    }
                    if (this.items.length === 1) return;
                    this.items.splice(index, 1);
                },

                addFiles(event, index) {
                    Array.from(event.target.files).forEach(file => {
                        this.items[index].documents.push({
                            id: null,
                            name: file.name,
                            file: file
                        });
                    });
                    event.target.value = '';
                },

                removeFile(itemIndex, fileIndex) {
                    const doc = this.items[itemIndex].documents[fileIndex];
                    if (doc.id) {
                        this.items[itemIndex].removed_document_ids.push(doc.id);
                    }

                    this.items[itemIndex].documents.splice(fileIndex, 1);
                },


                async loadAttributes(index) {
                    const itemId = this.items[index].item_id;
                    if (!itemId) return;
                    const res = await fetch(`/items/${itemId}/attributes`);
                    const data = await res.json();
                    this.items[index].schema = data.attributes || [];
                    this.items[index].unit_price = data.unit_price ?? 0;
                    this.items[index].attributes = {};
                    this.items[index].schema.forEach(attr => {
                        this.items[index].attributes[attr.key] = null;
                    });
                },

                total() {
                    return Math.max(this.items.reduce((t, i) => t + (i.unit_price * i.quantity), 0) - this.discount, 0);
                },

                format(value) {
                    return Number(value || 0).toFixed(2);
                },

                submitForm() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('orders.update', $order->id) }}";
                    form.enctype = 'multipart/form-data';

                    form.appendChild(this._csrfInput());
                    form.appendChild(this._methodInput('PUT'));

                    form.appendChild(this._input('store_id', document.querySelector('select[name="store_id"]').value));
                    form.appendChild(this._input('invoice_code', document.querySelector('input[name="invoice_code"]')
                        .value));
                    form.appendChild(this._input('order_date', document.querySelector('input[name="order_date"]')
                        .value));
                    form.appendChild(this._input('customer_name', document.querySelector('input[name="customer_name"]')
                        .value));
                    form.appendChild(this._input('customer_phone', document.querySelector(
                        'input[name="customer_phone"]').value));
                    form.appendChild(this._input('customer_address', document.querySelector(
                        'textarea[name="customer_address"]').value));
                    form.appendChild(this._input('taker_employee_id', document.querySelector(
                        'select[name="taker_employee_id"]').value));
                    form.appendChild(this._input('discount', this.discount));
                    
                    this.removed_order_item_ids.forEach(id => {
                        form.appendChild(
                            this._input('removed_order_item_ids[]', id)
                        );
                    });

                    this.items.forEach((item, index) => {
                        console.log(item);
                        form.appendChild(this._input(`items[${index}][id]`, item.id));
                        form.appendChild(this._input(`items[${index}][item_id]`, item.item_id));
                        form.appendChild(this._input(`items[${index}][unit_price]`, item.unit_price));
                        form.appendChild(this._input(`items[${index}][quantity]`, item.quantity));

                        Object.keys(item.attributes).forEach(key => {
                            form.appendChild(this._input(`items[${index}][attributes][${key}]`, item
                                .attributes[key]));
                        });

                        // removed documents
                        item.removed_document_ids.forEach(id => {
                            form.appendChild(
                                this._input(`items[${index}][removed_document_ids][]`, id)
                            );
                        });

                        // new documents only
                        item.documents.forEach(doc => {
                            if (doc.file) {
                                form.appendChild(
                                    this._fileInput(`items[${index}][documents][]`, doc.file)
                                );
                            }
                        });
                    });

                    document.body.appendChild(form);
                    form.submit();
                },

                _csrfInput() {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '_token';
                    input.value = '{{ csrf_token() }}';
                    return input;
                },

                _methodInput(method) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '_method';
                    input.value = method;
                    return input;
                },

                _input(name, value) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value;
                    return input;
                },

                _fileInput(name, file) {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.name = name;
                    input.files = dt.files;
                    return input;
                }
            }
        }
    </script>
@endpush
