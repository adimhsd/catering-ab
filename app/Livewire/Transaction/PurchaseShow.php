<?php

namespace App\Livewire\Transaction;

use App\Models\Purchase;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Detail Transaksi')]
class PurchaseShow extends Component
{
    public Purchase $purchase;

    public function mount(Purchase $purchase): void
    {
        $this->purchase = $purchase->load(['supplier', 'user', 'details.product.unit', 'attachments']);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.transaction.purchase-show');
    }
}
