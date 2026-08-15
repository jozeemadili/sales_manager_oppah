<div>
    <style>
        .pos-search-input{
    position:relative;
}

.pos-search-input i{
    position:absolute;
    left:14px;
    top:50%;
    transform:translateY(-50%);
    color:#6c757d;
}

.pos-search-input .form-control{
    padding-left:40px;
}

.pos-product-list{
    max-height:320px;
    overflow-y:auto;
    border:1px solid var(--pos-border,#dee2e6);
    border-radius:.75rem;
    background:#fff;
    margin-top:.4rem;
}

.pos-product-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:.75rem;
    padding:.75rem .9rem;
    border-bottom:1px solid #f1f1f1;
    cursor:pointer;
    background:#fff;
    transition:.15s ease;
}

.pos-product-item:last-child{
    border-bottom:none;
}

.pos-product-item:hover{
    border-color:var(--pos-primary,#4f46e5);
    box-shadow:0 3px 10px rgba(79,70,229,.12);
    transform:translateY(-1px);
    background:#f8f9ff;
}

.pos-product-item .pname{
    font-weight:600;
    color:var(--pos-text,#212529);
    margin-bottom:.15rem;
}

.pos-product-item .pmeta{
    font-size:.78rem;
    color:var(--pos-muted,#6c757d);
}

.pos-product-item .pprice{
    font-weight:700;
    color:var(--pos-primary-dark,#4338ca);
    white-space:nowrap;
}

.badge-instock{
    background:#d1fae5;
    color:#065f46;
}

.badge-outstock{
    background:#fee2e2;
    color:#991b1b;
}
    </style>
    <div class="position-relative">

        <label class="form-label fw-semibold small">Search Product</label>

        <div class="pos-search-input">
            <i class="bi bi-search"></i>

            <input
                type="text"
                class="form-control"
                wire:model="search"
                placeholder="Search Product...">
        </div>

        <input
            type="hidden"
            name="product_name"
            value="{{ $selectedProduct ?: $search }}">

        @if(count($products) > 0)

            <div class="pos-product-list position-absolute w-100 shadow bg-white" style="z-index:9999;">

                @foreach($products as $product)

                    <a href="javascript:void(0)"
                       class="pos-product-item text-decoration-none"
                       wire:click="selectProduct('{{ $product->product_name }}')">

                        <div>
                            <div class="pname">
                                {{ strtoupper($product->product_name) }}
                            </div>

                            <div class="pmeta">

                                @if(!empty($product->barcode))
                                    <i class="bi bi-upc-scan"></i>
                                    {{ $product->barcode }}
                                @endif

                                @if(isset($product->qty_remained))
                                    &nbsp;·&nbsp;

                                    @if($product->qty_remained > 0)
                                        <span class="badge badge-instock">
                                            {{ $product->qty_remained }} in stock
                                        </span>
                                    @else
                                        <span class="badge badge-outstock">
                                            Out of stock
                                        </span>
                                    @endif
                                @endif

                            </div>
                        </div>

                        @if(isset($product->selling_price))
                            <div class="pprice">
                                {{ number_format($product->selling_price,2) }}<br>
                                <small class="text-muted">TZS</small>
                            </div>
                        @endif

                    </a>

                @endforeach

            </div>

        @endif

    </div>

    @if($showNewProduct)

        <div class="alert alert-warning outline alert-dismissible fade show mt-3" role="alert">
            <i class="icon-info-alt txt-warning"></i>

            Product <strong>{{ $search }}</strong> haijapatikana kwenye mfumo.
            Tafadhali endelea kujaza taarifa zake hapa chini ili isajiliwe kama product mpya kwenye inventory.

            <button class="btn-close"
                type="button"
                data-bs-dismiss="alert">
            </button>
        </div>

        <div class="form-group mt-2">
            <label>New Product Name</label>

            <input
                type="text"
                class="form-control"
                name="new_product_name"
                value="{{ $search }}"
                readonly>
        </div>

    @endif

</div>