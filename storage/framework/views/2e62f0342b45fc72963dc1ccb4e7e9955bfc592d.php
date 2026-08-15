<?php $__env->startSection('title'); ?>
<?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('css'); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
  <?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('breadcrumb_title'); ?>
      <h3><?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?></h3>
    <?php $__env->endSlot(); ?>

    
    <?php $__env->slot('breadcrumb_action_buttons'); ?>

        <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li>
        <a href="<?php echo e(route('pending-invoice-download', 355)); ?>"
            class="btn btn-outline-primary btn-xs">
             Print Pending Inxxxxxpvoice
         </a>


    <?php $__env->endSlot(); ?>
    <li class="breadcrumb-item"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[0])); ?></li>
    <li class="breadcrumb-item active"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[1])); ?></li>
  <?php echo $__env->renderComponent(); ?>
  
  <div class="container-fluid">
  <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt txt-danger"></i>
                        <?php echo e($error); ?>x
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  
                  <?php if($message = Session::get('success')): ?>
                    <div class="alert alert-success outline alert-dismissible fade show" role="alert">
                    <i class="icofont icofont-check-circled"></i>
                        <?php echo $message; ?>

                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    <br />
					<?php endif; ?>
      <div class="row">
        <?php if(isset($Customers)): ?>
          <div class="col-sm-12">
              <div class="card">
                  <div class="card-body">
                    <ul class="nav nav-tabs border-tab" id="top-tab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" id="vehicles-tab" data-bs-toggle="tab" href="#vehicles" role="tab" aria-controls="vehicles" aria-selected="true"><i class="icofont icofont-user-alt-3"></i>Invoices</a></li>
                        <li class="nav-item"><a class="nav-link" id="contact-top-tab" data-bs-toggle="tab" href="#policies" role="tab" aria-controls="policies" aria-selected="false"><i class="icofont icofont-shield"></i>Payments</a></li>
                    </ul>
                    <div class="tab-content" id="top-tabContent">
                        <div class="tab-pane fade active show" id="vehicles" role="tabpanel" aria-labelledby="vehicles-tab"> 
                      
                       
                        <div class="table-responsive">
                            <?php if(count($Invoice) > 0): ?>
                            <table class="table table-xs table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Invoxxxxxxxxxice No</th>
                                        <th>Type</th>
                                        <th>Customer</th>
                                        <th>Invoice Date</th>
                                        <th>Total Quantity</th>
                                        <th>Total Amount</th>
                                        <th>Total Paid</th>
                                        <th>Total Remained</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            
                                <tbody>
                                    <?php $__currentLoopData = $Invoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $totalQty = $user->invoice_items->sum('qty');
                                    ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration); ?></td>
                            
                                        <td>
                                            <a href="#">
                                                <small>SF000<?php echo e($user->id); ?>/025</small>
                                            </a>
                                        </td>
                            
                                        <td>
                                            <?php if($user->status == 'Pending'): ?>
                                                PROFORMA INVOICE
                                            <?php else: ?>
                                                INVOICE
                                            <?php endif; ?>
                                        </td>
                            
                                        <td>
                                            <small><?php echo e($user->Customer->name); ?></small>
                                        </td>
                            
                                        <td>
                                            <?php echo e(\Carbon\Carbon::parse($user->invoice_date)->format('d/m/Y H:i:s')); ?>

                                        </td>
                            
                                        <td><?php echo e(number_format($totalQty, 2)); ?></td>
                            
                                        <td><?php echo e(number_format($user->total_invoice_amount, 2)); ?></td>
                            
                                        <td><?php echo e(number_format($user->amount_paid, 2)); ?></td>
                            
                                        <td><?php echo e(number_format($user->amount_remained, 2)); ?></td>
                            
                                        <td>
                                            <span class="badge bg-<?php echo e($user->status == 'Paid' ? 'success' : 'warning'); ?>">
                                                <?php echo e($user->status); ?>

                                            </span>
                                        </td>
                            
                                        <td><?php echo e($user->User->first_name); ?></td>
                            
                                        <td>
                                            <a href="<?php echo e(route('invoice-download', $user->id)); ?>"
                                               class="btn btn-outline-primary btn-xs">
                                                Print
                                            </a>
                            
                                            <a href="<?php echo e(route('invoice-preview', $user->id)); ?>"
                                               class="btn btn-outline-info btn-xs">
                                                View
                                            </a>
                            
                                            <?php if($user->status == 'Pending'): ?>
                                                <a href="<?php echo e(route('invoice-status-update', $user->id)); ?>"
                                                   class="btn btn-outline-success btn-xs">
                                                    Confirm
                                                </a>
                                            <?php else: ?>
                                                <a class="btn btn-primary btn-xs"
                                                   data-bs-toggle="modal"
                                                   data-bs-target="#approvalModal<?php echo e($user->id); ?>">
                                                    Receive Payment
                                                </a>
                            
                                                <a class="btn btn-info btn-xs"
                                                   data-bs-toggle="modal"
                                                   data-bs-target="#approvalModal_payment<?php echo e($user->id); ?>">
                                                    Payment Details
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            
                                <tfoot class="table-secondary fw-bold">
                                    <tr>
                                        <td colspan="5" class="text-end">TOTAL</td>
                                        <td><?php echo e(number_format($grandTotalQty, 2)); ?></td>
                                        <td><?php echo e(number_format($grandTotalAmount, 2)); ?></td>
                                        <td><?php echo e(number_format($grandTotalPaid, 2)); ?></td>
                                        <td><?php echo e(number_format($grandTotalRemained, 2)); ?></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                            
                            <?php else: ?>
                            <div class="alert alert-danger">
                                No Records Found
                            </div>
                            <?php endif; ?>
                            
					</div>

                        </div>
                        <div class="tab-pane fade" id="policies" role="tabpanel" aria-labelledby="policies-tab"> 
                        
                        <div class="table-responsive">
                        <?php if(count($sales)>0): ?>
						<table class="table table-xs">
							<thead>
								<tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Barcode</th>
                                <th>Qnty</th>
                                <th>Price</th>
                                <th>Sub Total</th>
								</tr>
							</thead>
							<tbody>

                                <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
                                <th scope="row"><?php echo e($loop->index + 1); ?>.</th>
                                    <td><?php echo e($user->Product->product_name); ?></td>
                                    <td><?php echo e($user->Product->barcode); ?></td> 
                                    <td><?php echo e($user->qty); ?></td>
                                    <td><?php echo e(number_format($user->price, 2)); ?> TZS</td> 
                                    <td><?php echo e(number_format($user->price*$user->qty, 2)); ?> TZS</td> 
                                    <td>
                                </td> 
                                    
							</td>
                                   	</tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
                        <br />
                        <?php echo e($sales->links()); ?>


                        <?php else: ?> 
                        <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                            <i class="icon-info-alt txt-danger"></i>
								No Records Found yet
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
                       	</div>
                        <?php endif; ?>
					</div>
                        </div>
                      </div>
                  </div>
              </div>
          </div>
          <?php else: ?>
          <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt txt-danger"></i>
                       Employee Does not exit
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
          <?php endif; ?>
      </div>
  </div>




  <?php $__env->startPush('scripts'); ?>
  <script>
    Livewire.on('InvoiceUpdated', () => {
        // Optional: Close the modal
        $('#newModal').modal('hide');

        // Navigate back
        setTimeout(() => {
            window.history.back();
        }, 500); // Slight delay ensures smooth transition
    });
</script>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<!-- NEW QUOTATION MODAL START -->
<div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
        <div class="modal-header bg-primary text-white">
    <h5 class="modal-title">
        Operate Sales For <b><?php echo e($Customers->name); ?></b>
    </h5>

    <!-- Refresh Button -->
    <button class="btn btn-light btn-sm me-2" onclick="window.location.reload();">
        Refresh Page
    </button>

    <!-- Close Button -->
    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
            <div class="modal-body">
            <div style="padding-right: 2em;padding-left: 2em;">
                        <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('sales-management.oparate-sales',['Customers_details'=>$Customers])->html();
} elseif ($_instance->childHasBeenRendered('Z3NuTMG')) {
    $componentId = $_instance->getRenderedChildComponentId('Z3NuTMG');
    $componentTag = $_instance->getRenderedChildComponentTagName('Z3NuTMG');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('Z3NuTMG');
} else {
    $response = \Livewire\Livewire::mount('sales-management.oparate-sales',['Customers_details'=>$Customers]);
    $html = $response->html();
    $_instance->logRenderedChild('Z3NuTMG', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?> 
                    </div>
            </div>
        </div>
    </div>
    </div>
 <!-- NEW QUOTATION MODAL END -->
 <!-- NEW MODAL END -->
 <?php $__currentLoopData = $Invoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="approvalModal_payment<?php echo e($user->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Payment Details for Invoice #<?php echo e($user->id); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <?php if($user->invoice_payment_details->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Amount Submitted</th>
                                    <th>Date Paid</th>
                                    <th>Status</th>
                                    <th>Payer Name</th>
                                    <th>Receipt</th>
                                    <th>Channel</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $user->invoice_payment_details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td><?php echo e(number_format($payment->amount_submitted, 2)); ?> TZS</td>
                                        <td><?php echo e(\Carbon\Carbon::parse($payment->date_payed)->format('d-m-Y H:i')); ?></td>
                                        <td>
                                            <span class="badge <?php echo e($payment->status == 'Paid' ? 'bg-success' : 'bg-warning'); ?>">
                                                <?php echo e($payment->status); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e($payment->payer_id); ?></td>
                                        <td><?php echo e($payment->receipt_number); ?></td>
                                        <td><?php echo e($payment->channel); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        No payments recorded for this invoice yet.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


<?php $__currentLoopData = $Invoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<!-- Payment Modal -->
<div class="modal fade" id="approvalModal<?php echo e($user->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
            <!-- Modal Header -->
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Enter Amount Received</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
                <form method="post" action="<?php echo e(route('receive-invoice-payment')); ?>" id="paymentForm_<?php echo e($user->id); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label for="discount_amount_<?php echo e($user->id); ?>" class="form-label">Enter New Amount</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="discount_amount_<?php echo e($user->id); ?>" 
                            name="discount_amount" 
                            required 
                            min="0" 
                            max="<?php echo e($user->amount_remained); ?>" 
                            step="0.01" 
                            placeholder="Enter amount (max <?php echo e(number_format($user->amount_remained, 2)); ?>)"
                        >
                    </div>

                    <div class="mb-3">
                        <strong>Total Invoiced Amount:</strong> <?php echo e(number_format($user->total_invoice_amount, 2)); ?> TZS<br>
                        <strong>Total Paid Amount:</strong> <?php echo e(number_format($user->amount_paid, 2)); ?> TZS<br>
                        <strong>Amount Remaining will be:</strong> 
                        <span id="remaining_amount_<?php echo e($user->id); ?>" class="text-danger"><?php echo e(number_format($user->amount_remained, 2)); ?> TZS</span>
                    </div>
                    
                    <input type="hidden" name="item_id" value="<?php echo e($user->id); ?>">
                    
                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="confirmPayment(<?php echo e($user->id); ?>)">Confirm & Apply</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Confirm Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">
                <!-- Will be updated dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, edit</button>
                <button type="button" class="btn btn-success" id="confirmYesBtn">Yes, submit</button>
            </div>
        </div>
    </div>
</div>


<!-- Toast Container (Top Right) -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
    <div id="exceedToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Entered amount exceeds remaining amount!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- JS: Validation + Confirmation + Duplicate Click Prevention -->
<script>
let formToSubmit = null;
let isSubmitting = false;

// Function to handle "Confirm & Apply" button
function confirmPayment(userId) {
    if (isSubmitting) return; // prevent double click

    const input = document.getElementById('discount_amount_' + userId);
    const maxAmount = parseFloat(input.max);

    if (!input.value || parseFloat(input.value) <= 0) {
        alert("Please enter a valid amount.");
        return;
    }

    if (parseFloat(input.value) > maxAmount) {
        // Show toast warning
        const toastEl = document.getElementById('exceedToast');
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();

        // Reset input to max allowed
        input.value = maxAmount;
        return;
    }

    // Update confirmation modal text with the entered amount
    const amountEntered = parseFloat(input.value).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
    const modalBody = document.getElementById('confirmModalBody');
    modalBody.textContent = `You are about to submit this payment with amount ${amountEntered} TZS. Do you want to proceed?`;

    // Store form reference and show confirmation modal
    formToSubmit = document.getElementById('paymentForm_' + userId);
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    confirmModal.show();
}

// Triggered when user clicks "Yes, submit"
document.getElementById('confirmYesBtn').addEventListener('click', function() {
    if (formToSubmit && !isSubmitting) {
        isSubmitting = true; // prevent double submission
        formToSubmit.submit();
    }
});

// Real-time remaining amount update & toast if exceeded
<?php $__currentLoopData = $Invoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
const input<?php echo e($user->id); ?> = document.getElementById('discount_amount_<?php echo e($user->id); ?>');
const remainingDisplay<?php echo e($user->id); ?> = document.getElementById('remaining_amount_<?php echo e($user->id); ?>');
const maxAmount<?php echo e($user->id); ?> = parseFloat(input<?php echo e($user->id); ?>.max);

input<?php echo e($user->id); ?>.addEventListener('input', function() {
    let value = parseFloat(this.value);
    if (isNaN(value) || value < 0) value = 0;

    if (value > maxAmount<?php echo e($user->id); ?>) {
        const toastEl = document.getElementById('exceedToast');
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
        this.value = maxAmount<?php echo e($user->id); ?>;
        value = maxAmount<?php echo e($user->id); ?>;
    }

    const newRemaining = maxAmount<?php echo e($user->id); ?> - value;
    remainingDisplay<?php echo e($user->id); ?>.textContent = newRemaining.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' TZS';
});
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</script>




 

  <!-- NEW MODAL START -->
  <div class="modal fade" id="newModalDetils" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Invoice Items</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
            <table class="table table-xs">
    <thead>
        <tr>
        <th>#</th>
            <th>Product Name</th>
            <th>Barcode</th>
            <th>Qnty</th>
            <th>Price</th>
            <th>Sub Total</th>
        </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $Invoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $user->invoice_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
            <th scope="row"><?php echo e($loop->index + 1); ?>.</th>
                <td><?php echo e($item->Product->product_name); ?></td>
                <td><?php echo e($item->Product->barcode); ?></td> 
                <td><?php echo e($item->qty); ?></td>
                <td><?php echo e(number_format($item->price, 2)); ?> TZS</td> 
                <td><?php echo e(number_format($item->price*$item->qty, 2)); ?> TZS</td> 
                <td>
    		  </td> 
            </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table> 
                </div>
        
       
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
               
                </form>
            </div>
        </div>
    </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
	<script src="<?php echo e(asset('assets/js/sweet-alert/sweetalert.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/sweet-alert/sweetalert.min.js')); ?>"></script>
	<script>
	
	  window.addEventListener('swal:modal', event => { 
		  swal({
			title: event.detail.message,
			text: event.detail.text,
			icon: event.detail.type,
			buttons:false,
			customClass:'swal-wide'
		  });
	  });
		
	  window.addEventListener('swal:confirm', event => { 
		  swal({
			title: event.detail.message,
			text: event.detail.text,
			icon: event.detail.type,
			buttons: true,
			dangerMode: true,
		  })
		  .then((willDelete) => {
			if (willDelete) {
			  window.livewire.emit('remove');
			}
		  });
	  });
	   </script>
       <script>
    window.addEventListener('show-print-button', event => {
        const btn = document.createElement('a');
        btn.href = event.detail.url;
        btn.className = 'btn btn-outline-primary btn-xs';
        btn.style = 'margin-top: 10px;';
        btn.target = '_blank';
        btn.innerHTML = 'Print Invoice <i class="icofont icofont-printer"></i>';

        // Append the button somewhere appropriate
        const alertContainer = document.querySelector('.swal-modal');
        if (alertContainer) {
            const div = document.createElement('div');
            div.style.marginTop = '15px';
            div.appendChild(btn);
            alertContainer.appendChild(div);
        }
    });
</script>

    <?php $__env->stopPush(); ?>
  
<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/sales_management/pending-invoices-pdf.blade.php ENDPATH**/ ?>