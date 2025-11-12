<div class="col-lg-12">
  <a href='#'> 
<div class="card income-card card-secondary">
 <br />
 <div class="card-body text-center">
   <div class="round-box">
       <i class="icofont icofont-abacus-alt" style="font-size: 40px;"></i>
   </div>
   <h5><?php echo e($summary['sumProduct']); ?></h5>
   <p>Stock Value </p>
   
 </div><br />
</div>
</a>

</div>
<!-- 🧾 Invoice Summary Section -->
<div class="row">
  <div class="col-lg-3">
      <div class="card income-card card-secondary text-center">
          <div class="card-body">
              <div class="round-box">
                  <i class="icofont icofont-abacus-alt" style="font-size: 40px;"></i>
              </div>
              <h5><?php echo e($summary['generated_amount']); ?></h5>
              <p>Total Generated Amount (Today)</p>
          </div>
      </div>
  </div>

  <div class="col-lg-3">
      <div class="card income-card card-primary text-center">
          <div class="card-body">
              <div class="round-box">
                  <i class="icofont icofont-tick-boxed" style="font-size: 40px;"></i>
              </div>
              <h5><?php echo e($summary['paid_amount']); ?></h5>
              <p>Total Paid Amount (Today)</p>
          </div>
      </div>
  </div>

  <div class="col-lg-3">
      <div class="card income-card card-warning text-center">
          <div class="card-body">
              <div class="round-box">
                  <i class="icofont icofont-exclamation-circle" style="font-size: 40px;"></i>
              </div>
              <h5><?php echo e($summary['unpaid_amount']); ?></h5>
              <p>Remaining (Unpaid) Amount (Today)</p>
          </div>
      </div>
  </div>

  <!-- 🆕 All-Time Unpaid Amount -->
  <div class="col-lg-3">
      <div class="card income-card card-danger text-center">
          <div class="card-body">
              <div class="round-box">
                  <i class="icofont icofont-bank-alt" style="font-size: 40px;"></i>
              </div>
              <h5><?php echo e($summary['unpaid_overall']); ?></h5>
              <p>Total Unpaid Amount (All Time)</p>
          </div>
      </div>
  </div>
</div>



<!-- 👥 Customer Summary Section -->
<hr class="mt-4 mb-3">

<h5 class="text-center mb-3"><strong>Customer Summary</strong></h5>
<div class="row">
  <div class="col-lg-4">
      <div class="card income-card card-primary text-center">
          <div class="card-body">
              <div class="round-box"><i class="icofont icofont-users-alt-2" style="font-size: 40px;"></i></div>
              <h5><?php echo e($summary['total_customers']); ?></h5>
              <p>Total Customers</p>
          </div>
      </div>
  </div>

  <div class="col-lg-4">
      <div class="card income-card card-success text-center">
          <div class="card-body">
              <div class="round-box"><i class="icofont icofont-user-alt-3" style="font-size: 40px;"></i></div>
              <h5><?php echo e($summary['active_customers']); ?></h5>
              <p>Active Customers</p>
          </div>
      </div>
  </div>

  <div class="col-lg-4">
      <div class="card income-card card-danger text-center">
          <div class="card-body">
              <div class="round-box"><i class="icofont icofont-user-suited" style="font-size: 40px;"></i></div>
              <h5><?php echo e($summary['inactive_customers']); ?></h5>
              <p>Inactive Customers</p>
          </div>
      </div>
  </div>
</div>
<?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/livewire/components/reports/summary.blade.php ENDPATH**/ ?>