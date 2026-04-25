<?php

namespace App\Http\Livewire\Orders;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Item;
use App\Models\Employee;
use App\Models\Store;

class CreateForm extends Component
{
    use WithFileUploads;

    public $store_id;
    public $invoice_id;
    public $order_date;
    public $customer_name;
    public $customer_phone;
    public $customer_address;
    public $employee_id;
    public $discount = 0;

    public $items = [];

    public $allItems;      // id => name for dropdown
    public $employees;     // id => name for dropdown
    public $stores;        // id => name for dropdown

    public function mount()
    {
        $this->allItems = Item::pluck('name', 'id')->toArray();
        $this->employees = Employee::pluck('name', 'id')->toArray();
        $this->stores = Store::pluck('name', 'id')->toArray();

        // initialize with one item
        $this->items[] = $this->emptyItem();
    }

    public function emptyItem()
    {
        return [
            'item_id' => '',
            'unit_price' => 0,
            'quantity' => 1,
            'attributes' => [],
            'documents' => [],
            'schema' => []
        ];
    }

    public function addItem()
    {
        $this->items[] = $this->emptyItem();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $name)
    {
        // If item_id changes, load its attributes
        if(str_ends_with($name, 'item_id')) {
            $index = explode('.', $name)[1];
            $itemId = $value;
            $item = Item::find($itemId);

            if($item) {
                $this->items[$index]['unit_price'] = $item->unit_price;
                $this->items[$index]['schema'] = $item->attributes_schema; // define how attributes are stored
                $this->items[$index]['attributes'] = [];
                foreach($item->attributes_schema as $attr) {
                    $this->items[$index]['attributes'][$attr['key']] = null;
                }
            }
        }
    }

    public function getTotalProperty()
    {
        $sum = collect($this->items)->sum(fn($i) => $i['unit_price'] * $i['quantity']);
        return max($sum - $this->discount, 0);
    }

    public function submit()
    {
        $data = $this->validate([
            'store_id' => 'required',
            'invoice_id' => 'required',
            'order_date' => 'required|date',
            'customer_name' => 'required',
            'customer_phone' => 'required',
            'employee_id' => 'required',
            'items.*.item_id' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.documents.*' => 'file|max:10240', // max 10MB
        ]);


        dd($data);

        session()->flash('success', 'Order created successfully.');
        return redirect()->route('orders.index');
    }

    public function render()
    {
        return view('livewire.orders.create-form');
    }
}
