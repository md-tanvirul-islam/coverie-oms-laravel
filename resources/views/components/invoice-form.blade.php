<div x-data="invoiceForm()" class="mt-3">
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
                        {{-- Item --}}
                        <td style="min-width:220px">
                            <x-dropdowns.select-item x-model="item.item_id" @change="loadAttributes(index)"
                                name="item.item_id" />
                        </td>

                        {{-- Attributes --}}
                        <td style="min-width:260px">
                            <template x-for="attr in item.schema" :key="attr.key">
                                <div class="mb-2">
                                    <label class="small fw-semibold" x-text="attr.label"></label>

                                    <template x-if="attr.type === 'select'">
                                        <select class="form-select form-select-sm" x-model="item.attributes[attr.key]">
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

                            <template x-if="item.schema.length === 0">
                                <span class="text-muted">—</span>
                            </template>
                        </td>

                        {{-- Documents --}}
                        <td style="min-width:260px">
                            <!-- Hidden file input -->
                            <input type="file" class="d-none" multiple @change="addFiles($event, index)">

                            <!-- Add file button -->
                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1"
                                @click="$el.previousElementSibling.click()">
                                + Add document
                            </button>

                            <!-- File list -->
                            <template x-if="item.documents.length">
                                <ul class="small mt-2 mb-0 ps-3">
                                    <template x-for="(file, fIndex) in item.documents" :key="fIndex">
                                        <li class="d-flex justify-content-between align-items-center">
                                            <span class="text-truncate" x-text="file.name"></span>

                                            <button type="button" class="btn btn-link text-danger btn-sm p-0"
                                                @click="removeFile(index, fIndex)">
                                                ✕
                                            </button>
                                        </li>
                                    </template>
                                </ul>
                            </template>

                            <template x-if="!item.documents.length">
                                <div class="text-muted small mt-1">No documents</div>
                            </template>
                        </td>


                        {{-- Price --}}
                        <td>
                            <input type="number" class="form-control form-control-sm" x-model.number="item.price">
                        </td>

                        {{-- Quantity --}}
                        <td>
                            <input type="number" class="form-control form-control-sm" min="1"
                                x-model.number="item.quantity">
                        </td>

                        {{-- Subtotal --}}
                        <td class="fw-semibold">
                            <span x-text="format(item.price * item.quantity)"></span>
                        </td>

                        {{-- Action --}}
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm" @click="removeItem(index)"
                                :disabled="items.length === 1">
                                ✕
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="4" class="text-end">Discount</td>
                    <td colspan="2">
                        <input type="number" class="form-control form-control-sm" x-model.number="discount">
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

    <button type="button" class="btn btn-outline-primary mt-2" @click="addItem">
        + Add Item
    </button>
</div>

@push('scripts')
    <script>
        window.invoiceForm = function() {
            return {
                items: [{
                    item_id: '',
                    price: 0,
                    quantity: 1,
                    schema: [],
                    attributes: {},
                    documents: []
                }],

                discount: 0,

                addItem() {
                    this.items.push({
                        item_id: '',
                        price: 0,
                        quantity: 1,
                        schema: [],
                        attributes: {},
                        documents: []
                    });
                },

                removeItem(index) {
                    if (this.items.length === 1) return;
                    this.items.splice(index, 1);
                },

                addFiles(event, index) {
                    const files = Array.from(event.target.files);

                    files.forEach(file => {
                        this.items[index].documents.push(file);
                    });

                    // reset input so same file can be added again if needed
                    event.target.value = '';
                },

                removeFile(itemIndex, fileIndex) {
                    this.items[itemIndex].documents.splice(fileIndex, 1);
                },

                async loadAttributes(index) {
                    const itemId = this.items[index].item_id;
                    if (!itemId) return;

                    const res = await fetch(`/items/${itemId}/attributes`);
                    const data = await res.json();

                    this.items[index].schema = data.attributes || [];
                    this.items[index].price = data.price ?? 0;

                    this.items[index].attributes = {};
                    this.items[index].schema.forEach(attr => {
                        this.items[index].attributes[attr.label] = null;
                    });
                },

                total() {
                    const sum = this.items.reduce(
                        (t, i) => t + (i.price * i.quantity),
                        0
                    );
                    return Math.max(sum - this.discount, 0);
                },

                format(value) {
                    return Number(value || 0).toFixed(2);
                }
            };
        };
    </script>
@endpush
