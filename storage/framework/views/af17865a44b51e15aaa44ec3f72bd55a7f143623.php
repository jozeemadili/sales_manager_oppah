<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="viho admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities." />
        <meta name="keywords" content="admin template, viho admin template, dashboard template, flat admin template, responsive admin template, web app" />
        <meta name="author" content="pixelstrap" />
        <link href="<?php echo e(public_path().'/assets/css/bootstrap.css'); ?>"  rel="stylesheet" id="bootstrap-css">
        <script src="<?php echo e(public_path().'/assets/js/bootstrap/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo e(public_path().'/assets/js/jquery-3.5.1.min.js'); ?>"></script>
        <title>safeafrica</title>
        <style>
     #invoice {
  padding: 30px;
}

.invoice {
  position: relative;
  background-color: #FFF;
  min-height: 680px;
  padding: 15px;
}

.invoice header {
  padding: 10px 0;
  margin-bottom: 20px;
  border-bottom: 1px solid #223673;
}

.invoice .company-details {
  text-align: left;
}

.invoice .company-details .name {
  margin-top: 0;
  margin-bottom: 0;
}

.invoice .contacts {
  margin-bottom: 20px;
}

.invoice .invoice-to {
  text-align: left;
}

.qrcode {
  text-align: right;
}

.invoice .invoice-to .to {
  margin-top: 0;
  margin-bottom: 0;
}

.invoice .invoice-details {
  text-align: right;
}

.invoice .invoice-details .invoice-id {
  margin-top: 0;
  color: #223673;
}

.invoice main {
  padding-bottom: 50px;
}

.invoice main .thanks {
  margin-top: -110px;
  font-size: 2em;
  margin-bottom: 10px;
}

.invoice main .notices {
  padding-left: 6px;
  border-left: 6px solid #223673;
}
.invoice main .notice2 {
  padding-right: 6px;
  border-right: 6px solid #223673;
}

.invoice main .notices .notice {
  font-size: 1.2em;
}

.invoice table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 20px;
}

.invoice table td,
.invoice table th {
  padding: 15px;
  /* border-bottom: 1px solid #fff; */
  background: none; /* Background removed */
}

.invoice table th {
  white-space: nowrap;
  font-weight: 400;
  font-size: 16px;
}

.invoice table td h3 {
  margin: 0;
  font-weight: 400;
  font-size: 1em;
}

.invoice table .qty,
.invoice table .total,
.invoice table .unit {
  text-align: center;
  font-size: 1.2em;
}

.invoice table .no {
  font-size: 1.2em;
}

.invoice table .unit {
  background: none; /* Background removed */
}

.invoice table .total {
  color: #fff;
}

.invoice table tbody tr:last-child td {
  /* border: none; */
}

.invoice table tfoot td {
  background: none; /* Background removed */
  border-bottom: none;
  white-space: nowrap;
  text-align: right;
  padding: 10px 20px;
  font-size: 1.2em;
  border-top: 1px solid #aaa;
}

.invoice table tfoot tr:first-child td {
  /* border-top: none; */
}

.invoice table tfoot tr:last-child td {
  font-size: 1.4em;
}

.invoice table tfoot tr td:first-child {
  /* border: none; */
}

.invoice footer {
  width: 100%;
  text-align: center;
  font-size: 12px;
  color: #777;
  border-top: 1px solid #aaa;
  padding: 8px 0;
}
.invoice .company-details-heading {
        text-align: center;
        font-size: 15px
        
        }
.invoice footer {
  position: absolute;
  bottom: 10px;
}

.invoice>div:last-child {
}

.th {
  background-color: white;
  color: black;
}

.page-break {
  page-break-after: always;
}

                        </style>
    </head>
    <body style="margin: 20px auto;">
      <div id="invoice">
        <div class="invoice overflow-auto">
            <div style="min-width: 600px">
                <header>
                    <div class="row">
                        <div class="col company-details">
                            <table>
                                <tr>
                                    <td>
                                    <img src="<?php echo e(public_path().'/assets/images/logo/'.strtolower($quotation->company->short_form).'.png'); ?>" data-holder-rendered="true" width="20%" />
                                    </td>
                                    <td>
                                    <div><?php echo e(strtoupper($quotation->company->name)); ?> </div>
                                    <div> +255<?php echo e($quotation->company->phone_number); ?>/+255763414192 | <?php echo e($quotation->company->email_address); ?></div>
                                    <div>P.O.BOX <?php echo e($quotation->company->postal_address); ?> </div>
                                   
                                    </td>
                                </tr>
                            </table>
                        </div>
  
                    </div>
                </header>

                <main>
                <div class="col company-details-heading">
                <?php if($quotation->status == 'Pending'): ?>
                  <div class="text-gray-light"><b> PROFOMAL INVOICE </b></div>
                  <?php elseif($quotation->status == 'Confirmed'): ?>
                  <div class="text-gray-light"><b> INVOICE </b></div>
                  <?php elseif($quotation->status == 'Paid'): ?>
                  <div class="text-gray-light"><b> INVOICE </b></div>
                  <?php else: ?>
                  <div class="text-gray-light"><b> INVOICE </b></div>
                  <?php endif; ?>
                </div>
                <div class="col invoice-details">
                <div><b>Invoice No : </b> OP000<?php echo e($quotation->id); ?>/025 </div>
                <div><b>Date : </b> <?php echo e($quotation->invoice_date->format('d M Y')); ?> </div>
                </div>

                <div class="row contacts">
               

                        <div class="col invoice-to">
                            <div class="text-gray-light"> THE BUYER (INVOICE TO) :</div>
                            <h6 class="to"> <?php echo e(strtoupper($quotation->customer->name)); ?>  </h6>
                            <div class="address">+255<?php echo e($quotation->customer->phone); ?> | <?php echo e($quotation->customer->email); ?></div>
                            <div>TIN: <?php echo e($quotation->customer->tin); ?> VRN : <?php echo e($quotation->customer->tin); ?> </div>
                            <div>P.O.BOX <?php echo e($quotation->customer->physical_addres); ?></div>
                        </div>
<br/>

                        <table border="1" cellspacing="0" cellpadding="0">
                        <thead >
                            <tr>
                                <th style="background-color: <?php echo e($quotation->company->color); ?>; color:white;">#</th>
                                <th style="background-color: <?php echo e($quotation->company->color); ?>; color:white;">PRODUCT NAME</th>
                                <th style="background-color: <?php echo e($quotation->company->color); ?>; color:white;">BARCODE</th>
                                <th style="background-color: <?php echo e($quotation->company->color); ?>; color:white;">QUANTITY</th>
                                <th style="background-color: <?php echo e($quotation->company->color); ?>; color:white;">SUB TOTAL</th>
                                <th style="background-color: <?php echo e($quotation->company->color); ?>; color:white;">TOTAL</th>
                            </tr>
                           
                           
                        </thead>
                        <tbody>
        <?php
            $total = 0;
        ?>

        
        <tbody>
        <?php
            $total = 0;
        ?>

        <?php $__currentLoopData = $quotation->invoice_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $subTotal = $item->price * $item->qty;
                $total += $subTotal;
            ?>
            <tr>
                <td><?php echo e($loop->index + 1); ?>.</td>
                <td><?php echo e($item->Product->product_name); ?></td>
                <td><?php echo e($item->Product->barcode); ?></td> 
                <td><?php echo e($item->qty); ?></td>
                <td><?php echo e(number_format($item->price, 2)); ?> TZS</td> 
                <td><?php echo e(number_format($subTotal, 2)); ?> TZS</td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      
    </tbody>
    <tfoot>
      <tr>
                          
                            <td colspan="5">Subtotal</td>
                              <td><?php echo e(number_format($total,2,'.',',')); ?> TZS</td>
                            </tr>
                            <tr>
                              <td></td>
                              <td colspan="4">Tax %</td>
                              <td>0</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="4">GRAND TOTAL</td>
                                <td><?php echo e(number_format($total,2,'.',',')); ?> TZS</td>
                            </tr>
                        </tfoot>
                    </table>
                        
                    </div>
                    <div class="col company-details-heading">
                                 <?php if(isset($qrcode)): ?>
                                  <img src="<?php echo e($qrcode); ?>" />
                                <?php endif; ?>
                                <br/>
                                <b>Scan To validate </b>
                            </div>
                    <table>
                        <tr style="font-size: 9px; ">
                            <td>
                            <div class="notices">
                        <div>NOTICE:</div>
                        <ul>
                          <li> <div class="notice">All Figures above are in Tanzania Shillings (TZS)</div></li>
                          <li><div class="notice">Terms Of Payment : <b>Full Amount (Cash)</b></div></li>
                        </ul>
                       
                        
                    </div>
                    <!-- <div class="notices">
                        <div>BANK INFROMATION:</div>
                        <ul>
                          <li><div class="notice">Beneficiary: SAFE EQUIPMENT AND GENERAL SUPPLY LIMITED</div></li>
                          <li><div class="notice">Beneficiary Bank: NMB BANK</div></li>
                          <li><div class="notice">Address of Bank: NMB HOUSE, DAR ES SALAAM</div></li>
                          <li><div class="notice">Swift Code: NMIBTZTZ</div></li>
                          <li><div class="notice">Account No: TZS 20110077201</div></li>
                          <li><div class="notice">USD 20110078202</div></li>
                        </ul>
                    </div> -->
                            </td>
                            <td style="text-align: right;">
    <div class="notice2">
    <div class="notice"><i>www.oppah01.co.tz</i></div>
    <div class="notice"><i>info@oppah01.co.tz</i></div>
    <div class="notice"><i>Dar Es Dalaam, Tanzania</i></div>
        
    </div>
</td>
                        </tr>
                      </table>
                   
                </main>
                <footer>
                    Smart Generation in Smart Bussines
                </footer>
            </div>
            <div></div>
        </div>
    </div>
    

 
    </body>
</html>
<?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/sales_management/invoice-download.blade.php ENDPATH**/ ?>