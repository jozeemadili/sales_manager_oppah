<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ProductsTuli;

class ProductSearch extends Component
{
    public $search = '';
    public $selectedProduct = '';
    public $products = [];

    public $showNewProduct = false;

    public function updatedSearch()
    {
        $this->products = ProductsTuli::where('product_name', 'like', "%{$this->search}%")
            ->limit(10)
            ->get();

        $this->showNewProduct = strlen($this->search) > 0 && $this->products->count() == 0;
    }

    public function selectProduct($name)
    {
        $this->selectedProduct = $name;
        $this->search = $name;
        $this->products = [];
        $this->showNewProduct = false;
    }

    public function render()
    {
        return view('livewire.product-search');
    }
}