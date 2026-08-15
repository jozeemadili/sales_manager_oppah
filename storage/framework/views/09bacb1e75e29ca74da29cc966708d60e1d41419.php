<div>

    
    
    
    <style>
        :root{
            --pos-primary:#4f46e5;
            --pos-primary-dark:#3730a3;
            --pos-success:#059669;
            --pos-danger:#dc2626;
            --pos-warning:#d97706;
            --pos-bg:#f4f5fa;
            --pos-card:#ffffff;
            --pos-border:#e5e7eb;
            --pos-text:#1f2937;
            --pos-muted:#6b7280;
        }
        .pos-wrap{ background:var(--pos-bg); padding:1rem; border-radius:.75rem; }

        /* Stat cards */
        .pos-stat{
            border:none; border-radius:1rem; overflow:hidden;
            box-shadow:0 2px 10px rgba(17,24,39,.06);
            transition:transform .15s ease, box-shadow .15s ease;
        }
        .pos-stat:hover{ transform:translateY(-2px); box-shadow:0 8px 20px rgba(17,24,39,.10); }
        .pos-stat .card-body{ display:flex; align-items:center; gap:.9rem; padding:1.1rem 1.25rem; }
        .pos-stat-icon{
            width:46px; height:46px; border-radius:.75rem;
            display:flex; align-items:center; justify-content:center;
            background:rgba(255,255,255,.22); font-size:1.3rem; flex-shrink:0;
        }
        .pos-stat h6{ margin:0; font-size:.75rem; letter-spacing:.03em; text-transform:uppercase; opacity:.85; }
        .pos-stat h4{ margin:0; font-weight:700; font-size:1.25rem; }
        .pos-stat.stat-sales{ background:linear-gradient(135deg,#4f46e5,#4338ca); color:#fff; }
        .pos-stat.stat-profit{ background:linear-gradient(135deg,#059669,#047857); color:#fff; }
        .pos-stat.stat-items{ background:linear-gradient(135deg,#0891b2,#0e7490); color:#fff; }
        .pos-stat.stat-tx{ background:linear-gradient(135deg,#d97706,#b45309); color:#fff; }

        /* Panels */
        .pos-card{
            border:1px solid var(--pos-border); border-radius:1rem; background:var(--pos-card);
            box-shadow:0 1px 4px rgba(17,24,39,.05); overflow:hidden;
        }
        .pos-card-header{
            padding:.9rem 1.15rem; font-weight:600; font-size:1rem;
            display:flex; align-items:center; gap:.5rem;
            border-bottom:1px solid var(--pos-border);
        }
        .pos-card-header.head-search{ background:#eef2ff; color:var(--pos-primary-dark); }
        .pos-card-header.head-cart{ background:#ecfdf5; color:#065f46; }
        .pos-card-header.head-history{ background:#111827; color:#fff; }

        /* Search panel */
        .pos-search-input{ position:relative; }
        .pos-search-input i{
            position:absolute; left:.9rem; top:50%; transform:translateY(-50%); color:var(--pos-muted);
        }
        .pos-search-input input{ padding-left:2.3rem; }

        .pos-qty-input{ max-width:120px; }

        .pos-product-list{ max-height:520px; overflow-y:auto; }
        .pos-product-item{
            display:flex; justify-content:space-between; align-items:center; gap:.75rem;
            padding:.75rem .9rem; border:1px solid var(--pos-border); border-radius:.75rem;
            margin-bottom:.6rem; cursor:pointer; background:#fff; transition:.15s ease;
        }
        .pos-product-item:hover{ border-color:var(--pos-primary); box-shadow:0 3px 10px rgba(79,70,229,.12); transform:translateY(-1px); }
        .pos-product-item .pname{ font-weight:600; color:var(--pos-text); margin-bottom:.15rem; }
        .pos-product-item .pmeta{ font-size:.78rem; color:var(--pos-muted); }
        .pos-product-item .pprice{ font-weight:700; color:var(--pos-primary-dark); white-space:nowrap; }

        /* Cart table */
        .pos-cart-table th{
            background:#f9fafb; font-size:.75rem; text-transform:uppercase; letter-spacing:.03em;
            color:var(--pos-muted); border-bottom:2px solid var(--pos-border);
        }
        .pos-cart-table td{ vertical-align:middle; }
        .pos-qty-stepper{ display:flex; align-items:center; gap:.35rem; }
        .pos-qty-stepper input{ width:60px; text-align:center; }
        .pos-qty-stepper .btn{ width:34px; height:34px; padding:0; border-radius:.6rem; font-weight:700; }

        /* Checkout summary — sticky so it's always visible while scrolling the cart */
        .pos-checkout{
            position:sticky; bottom:0; background:#fff; border-top:1px solid var(--pos-border);
            padding:1rem 1.15rem; margin-top:.5rem;
        }
        .pos-total-row{ display:flex; justify-content:space-between; align-items:baseline; margin-bottom:.9rem; }
        .pos-total-row .label{ color:var(--pos-muted); font-size:.9rem; }
        .pos-total-row .amount{ font-size:1.6rem; font-weight:800; color:var(--pos-success); }
        .pos-pay-btn{
            border-radius:.75rem; padding:.85rem; font-weight:700; font-size:1.05rem;
            background:var(--pos-success); border:none; box-shadow:0 4px 12px rgba(5,150,105,.25);
        }
        .pos-pay-btn:hover{ background:#047857; }

        .pos-empty-cart{
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            padding:3rem 1rem; color:var(--pos-muted); text-align:center;
        }
        .pos-empty-cart i{ font-size:2.6rem; margin-bottom:.75rem; opacity:.5; }

        .badge-discount{ background:var(--pos-danger); }
        .badge-nodiscount{ background:#9ca3af; }
        .badge-instock{ background:var(--pos-success); }
        .badge-outstock{ background:var(--pos-danger); }

        .btn-icon-sm{ width:32px; height:32px; padding:0; border-radius:.5rem; }

        .pos-history-table thead th{ background:#f9fafb; font-size:.78rem; text-transform:uppercase; color:var(--pos-muted); }
    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <div class="pos-wrap">

        
        
        
        <div class="row mb-3 g-3">

            <div class="col-6 col-md-3">
                <div class="card pos-stat stat-sales">
                    <div class="card-body">
                        <div class="pos-stat-icon"><i class="bi bi-cash-coin"></i></div>
                        <div>
                            <h6>Today's Sales</h6>
                            <h4><?php echo e(number_format($todaySales,2)); ?> TZS</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card pos-stat stat-profit">
                    <div class="card-body">
                        <div class="pos-stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
                        <div>
                            <h6>Today's Profit</h6>
                            <h4><?php echo e(number_format($todayProfit,2)); ?> TZS</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card pos-stat stat-items">
                    <div class="card-body">
                        <div class="pos-stat-icon"><i class="bi bi-box-seam"></i></div>
                        <div>
                            <h6>Items Sold</h6>
                            <h4><?php echo e($todayItems); ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card pos-stat stat-tx">
                    <div class="card-body">
                        <div class="pos-stat-icon"><i class="bi bi-receipt"></i></div>
                        <div>
                            <h6>Transactions</h6>
                            <h4><?php echo e($todayTransactions); ?></h4>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-3">

            
            
            
            <div class="col-lg-4">

                <div class="pos-card">

                    <div class="pos-card-header head-search">
                        <i class="bi bi-lightning-charge-fill"></i> Quick / Cash Sale
                    </div>

                    <div class="card-body">

                        <div class="row g-2 mb-3">
                           
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Search Product</label>
                                <div class="pos-search-input">
                                    <i class="bi bi-search"></i>
                                    <input
                                        type="text"
                                        class="form-control"
                                        wire:model="productquery"
                                        placeholder="Name or barcode">
                                </div>
                            </div>
                        </div>

                        <div class="pos-product-list">

                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <a href="javascript:void(0)"
                               class="pos-product-item text-decoration-none"
                               wire:click="selectProduct(<?php echo e($product); ?>)">

                                <div>
                                    <div class="pname"><?php echo e(strtoupper($product->product_name)); ?></div>
                                    <div class="pmeta">
                                        <i class="bi bi-upc-scan"></i> <?php echo e($product->barcode); ?>

                                        &nbsp;·&nbsp;
                                        <?php if($product->qty_remained > 0): ?>
                                            <span class="badge badge-instock"><?php echo e($product->qty_remained); ?> in stock</span>
                                        <?php else: ?>
                                            <span class="badge badge-outstock">Out of stock</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="pprice">
                                    <?php echo e(number_format($product->selling_price,2)); ?><br>
                                    <small class="text-muted">TZS</small>
                                </div>

                            </a>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php if(count($products)==0): ?>
                            <div class="pos-empty-cart py-4">
                                <i class="bi bi-search"></i>
                                <div>No products match your search.</div>
                            </div>
                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>


            
            
            
            <div class="col-lg-8">

                <div class="pos-card">

                    <div class="pos-card-header head-cart">
                        <i class="bi bi-cart3"></i> Cart
                    </div>

                    <div class="card-body p-0">

                        <?php if(count($sale)>0): ?>

                        <div class="table-responsive">

                            <table class="table pos-cart-table mb-0">

                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Discount</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody>

                                <?php $__currentLoopData = $sale; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <tr>

                                    <td>
                                        <?php echo e($item->products_tuli->product_name); ?>

                                        <br>
                                        <small class="text-muted"><?php echo e($item->products_tuli->barcode); ?></small>
                                    </td>

                                    <td>
                                        <div class="pos-qty-stepper">

                                            <button
                                                class="btn btn-outline-secondary"
                                                wire:click="removeItem(<?php echo e($item); ?>)">
                                                −
                                            </button>

                                            <input
                                                type="number"
                                                min="1"
                                                class="form-control"
                                                value="<?php echo e($item->quantity); ?>"
                                                wire:change="updateQuantity(<?php echo e($item->id); ?>,$event.target.value)">

                                            <button
                                                class="btn btn-outline-secondary"
                                                wire:click="addItem(<?php echo e($item); ?>)">
                                                +
                                            </button>

                                        </div>
                                    </td>

                                    <td>
                                        <?php echo e(number_format($item->selling_price,2)); ?>

                                        <?php if($item->discount_amount > 0): ?>
                                        <br>
                                        <small class="text-danger text-decoration-line-through">
                                            <?php echo e(number_format($item->original_price,2)); ?>

                                        </small>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if($item->discount_amount >0): ?>
                                            <span class="badge badge-discount"><?php echo e($item->discount_percent); ?>%</span>
                                        <?php else: ?>
                                            <span class="badge badge-nodiscount">None</span>
                                        <?php endif; ?>
                                        <br>
                                        <button
                                            class="btn btn-link btn-sm p-0 mt-1"
                                            wire:click="openDiscount(<?php echo e($item->id); ?>)">
                                            <i class="bi bi-tag"></i> Discount
                                        </button>
                                    </td>

                                    <td class="fw-semibold">
                                        <?php echo e(number_format($item->selling_price * $item->quantity,2)); ?>

                                    </td>

                                    <td>
                                        <button
                                            class="btn btn-outline-danger btn-icon-sm"
                                            title="Remove item"
                                            wire:click="deleteItem(<?php echo e($item); ?>)">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>

                                </tr>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </tbody>

                            </table>

                        </div>

                        
                        <div class="pos-checkout">

                            <div class="pos-total-row">
                                <span class="label">Total due</span>
                                <span class="amount"><?php echo e(number_format($total,2)); ?> TZS</span>
                            </div>

                            <div class="row g-2 mb-3">

                                <div class="col-6">
                                   
                                    <label class="form-label small fw-semibold">
                                        Amount Received : <?php echo e(number_format((float) $received, 2)); ?> TZS
                                    </label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        wire:model="received">
                                </div>

                                <div class="col-6">
                                    <label class="form-label small fw-semibold">Change</label>
                                    <input
                                        class="form-control fw-bold"
                                        readonly
                                        value="<?php echo e(number_format($change,2)); ?>">
                                </div>

                            </div>

                            <button
                                class="btn pos-pay-btn w-100 text-white"
                                wire:click="completeSale"
                                wire:loading.attr="disabled">
                                <i class="bi bi-check-circle-fill"></i> Complete Cash Sale
                            </button>

                        </div>

                        <?php else: ?>

                        <div class="pos-empty-cart">
                            <i class="bi bi-cart-x"></i>
                            <div>Cart is empty.</div>
                            <small>Search and select a product to add it here.</small>
                        </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>


        
        
        
        <div class="pos-card mt-3">

            <div class="pos-card-header head-history">
                <i class="bi bi-clock-history"></i> Today's Sold Products
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover pos-history-table mb-0">

                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Status</th>
                                <th>Date sold</th>
                                <th>Qty</th>
                                <th>Selling Price</th>
                                <th>Total</th>
                                <th>Profit</th>
                                <th>Sold By</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php $__currentLoopData = $todayProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <tr>

                            <td class="fw-semibold"><?php echo e($product->products_tuli->product_name); ?></td>

                            <td class="text-muted"><?php echo e($product->status); ?></td>
                            <td><?php echo e($product->date_sold); ?></td>

                            <td><?php echo e($product->quantity); ?></td>

                            <td><?php echo e(number_format($product->selling_price,2)); ?></td>

                            <td><?php echo e(number_format($product->selling_price * $product->quantity,2)); ?></td>

                            <td class="text-success fw-semibold">
                                <?php echo e(number_format(
                                    ($product->selling_price - $product->products_tuli->purchasing_price)
                                    * $product->quantity
                                ,2)); ?>

                            </td>
                            <td><?php echo e($product->sold_by); ?></td>
                        </tr>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php if(count($todayProducts)==0): ?>

                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox"></i> No sales today
                            </td>
                        </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>


    
    
    
    <div class="modal fade" id="discountModal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content" style="border-radius:1rem; overflow:hidden; border:none;">

                <div class="modal-header" style="background:#eef2ff;">
                    <h5 class="modal-title text-indigo">
                        <i class="bi bi-tag-fill"></i> Product Discount
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body p-4">

                    <label class="form-label fw-semibold">Discount Percentage (%)</label>
                    <input
                        type="number"
                        class="form-control mb-3"
                        wire:model.defer="discount_percentage">

                    <div class="text-center text-muted small my-2">— OR —</div>

                    <label class="form-label fw-semibold">New Selling Price</label>
                    <input
                        type="number"
                        class="form-control"
                        wire:model.defer="discount_price">

                </div>

                <div class="modal-footer">
                    <button
                        class="btn pos-pay-btn text-white w-100"
                        wire:click="applyDiscount"
                        wire:loading.attr="disabled">
                        <i class="bi bi-check2"></i> Apply Discount
                    </button>
                </div>

            </div>

        </div>

    </div>

</div>

<?php $__env->startPush('scripts'); ?> 
<?php $__env->stopPush(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/livewire/tuli-sales-management/quick-sale.blade.php ENDPATH**/ ?>