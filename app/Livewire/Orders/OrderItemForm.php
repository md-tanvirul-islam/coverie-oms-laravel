<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Item;

class OrderItemForm extends Component
{
    use WithFileUploads;

    public array $items = [];
    public array $availableItems = [];
    public float $discount = 0;

    public function mount(): void
    {
        // Load items with their attribute schema
        $this->availableItems = Item::with([
            'attributes:id,item_id,label,type,options'
        ])
            ->where('is_active', true)
            ->get()
            ->toArray();

        $this->addItem();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'item_id'    => null,
            'quantity'   => 1,
            'price'      => null,
            'schema'     => [],
            'attributes' => [],
            'documents'  => [],
        ];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) === 1) {
            return;
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    /**
     * React only when item_id changes
     */
    public function updatedItems($value, string $key): void
    {
        if (!str_ends_with($key, '.item_id')) {
            return;
        }

        $index = (int) explode('.', $key)[0];

        $item = collect($this->availableItems)->firstWhere('id', $value);

        if (!$item) {
            $this->resetRow($index);
            return;
        }

        $schema = [];
        $attributes = [];

        foreach ($item['attributes'] ?? [] as $attr) {
            // 🔑 Runtime-safe attribute key
            $attrKey = 'attr_' . $attr['id'];

            $schema[] = [
                'id'      => $attr['id'],
                'label'   => $attr['label'],
                'type'    => $attr['type'],
                'options' => $attr['options'] ?? [],
                'key'     => $attrKey,
            ];

            $attributes[$attrKey] = null;
        }

        $this->items[$index]['schema'] = $schema;
        $this->items[$index]['attributes'] = $attributes;
        $this->items[$index]['price'] = $item['price'] ?? null;
    }

    private function resetRow(int $index): void
    {
        $this->items[$index]['schema'] = [];
        $this->items[$index]['attributes'] = [];
        $this->items[$index]['price'] = null;
    }

    public function getTotalProperty(): float
    {
        $sum = collect($this->items)->sum(fn ($item) =>
            ($item['price'] ?? 0) * ($item['quantity'] ?? 0)
        );

        return max($sum - $this->discount, 0);
    }

    public function render()
    {
        return view('livewire.orders.order-item-form');
    }
}
