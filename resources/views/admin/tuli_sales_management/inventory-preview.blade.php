@extends('layouts.admin.master')
@section('title')
{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}
@endsection
@push('css')
@endpush

@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}</h3>
    @endslot

    
    @slot('breadcrumb_action_buttons')
   @if($Inventory->status === 'Active')
         <li><a href='{!! Route('send-stock-tuli', ['id' => $Inventory->id]) !!}' class='btn btn-outline-primary'>Send To Stock</a> </li>
         <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New Product <i class="icofont icofont-plus-circle"></i></button></li>
         @else
         <li><a href='#' class='btn btn-outline-danger'>Request submitted to stock — product cannot be added</a> </li>
         @endif
    
        <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal_expense">Record Expense  <i class="icofont icofont-plus-circle"></i></button></li>



    @endslot
    <li class="breadcrumb-item">{{ucfirst(explode('-', Route::currentRouteName())[0])}}</li>
    <li class="breadcrumb-item active">{{ucfirst(explode('-', Route::currentRouteName())[1])}}</li>
  @endcomponent
  
  <div class="container-fluid">
  @foreach ($errors->all() as $error)
                  <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt txt-danger"></i>
                        {{ $error }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                  @endforeach
                  
                  @if($message = Session::get('success'))
                    <div class="alert alert-success outline alert-dismissible fade show" role="alert">
                    <i class="icofont icofont-check-circled"></i>
                        {!! $message !!}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    <br />
					@endif
      <div class="row">
      <div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header bg-primary text-white">Inventory Summary</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>From / Company / Suplier</span>
                    <strong> {{$Inventory->company_name}}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Vihecle</span>
                    <strong> {{$Inventory->vihecle_no}}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Date Received</span>
                    <strong> {{$Inventory->inventory_date}}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total Expenses</span>
                    <strong>
                        {{ number_format($ExpensesRecord->sum(fn($p) => $p->amount_used), 2) }}
                    </strong>
                </li>

                <!-- <li class="list-group-item d-flex justify-content-between">
                    <span>Registered By</span>
                    <strong> {{$Inventory->reg_by}}</strong>
                </li> -->
                
            </ul>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
        <div class="card-header bg-primary text-white">Inventory Summary</div>
            <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between">
                    <span>Total  Product</span>
                    <strong>{{ count($AllProducts) }}</strong>
                </li>
            <li class="list-group-item d-flex justify-content-between">
                    <span>Inventory Value As Of Today</span>
                    <strong>
                        {{ number_format($AllProducts->sum(fn($p) => $p->qty_remained * $p->purchasing_price), 2) }}
                    </strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Inventory Expected Profit Before expenses</span>
                    <strong>
                        {{ number_format($AllProducts->sum(fn($p) => ($p->qty * $p->selling_price) - ($p->qty * $p->purchasing_price)), 2) }}
                    </strong>
                </li>
                @php
    $totalProfitBeforeExpenses = $AllProducts->sum(fn($p) => ($p->qty * $p->selling_price) - ($p->qty * $p->purchasing_price));
    $totalExpenses = $ExpensesRecord->sum(fn($e) => $e->amount_used);
    $netProfitAfterExpenses = $totalProfitBeforeExpenses - $totalExpenses;
@endphp

<li class="list-group-item d-flex justify-content-between">
    <span>Inventory Expected Profit After All Expenses</span>
    <strong>{{ number_format($netProfitAfterExpenses, 2) }}</strong>
</li>
              
               
            </ul>
        </div>
    </div>
</div>

          <div class="col-sm-12">
              <div class="card">
                  <div class="card-body">
                    <ul class="nav nav-tabs border-tab" id="top-tab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" id="vehicles-tab" data-bs-toggle="tab" href="#vehicles" role="tab" aria-controls="vehicles" aria-selected="true"><i class="icofont icofont-user-alt-3"></i>Inventories</a></li>
                        <li class="nav-item"><a class="nav-link" id="contact-top-tab" data-bs-toggle="tab" href="#policies" role="tab" aria-controls="policies" aria-selected="false"><i class="icofont icofont-shield"></i>Expenses</a></li>
                    </ul>
                    <div class="tab-content" id="top-tabContent">
                        <div class="tab-pane fade active show" id="vehicles" role="tabpanel" aria-labelledby="vehicles-tab"> 
                        <div class="table-responsive">
                        @if(count($Branch)>0)
						<table class="table table-xs">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">product Name</th>
                                    <th scope="col">category</th>
                                    <th scope="col">store</th>
                                    <th scope="col">barcode</th>
                                    <th scope="col">quantity Recorded</th>
                                    <th scope="col">quantity Remained</th>
                                    @if(Auth::user()->role == 'ADMIN')
                                    <th scope="col">purchasing price</th>
                                    @endif
                                    <th scope="col">selling price</th>
                                    @if(Auth::user()->role == 'ADMIN')
                                    <th scope="col"> Expected profit/loss</th>
                                    @endif
                                    
                                    <th scope="col">Status</th>
                                    <th scope="col">Created By</th>
                                    <!-- <th scope="col">Action</th> -->
								</tr>
							</thead>
							<tbody>
                                @foreach($Branch as $user)
			<tr>
									<th scope="row">{{$loop->index + 1}}.</th>
									<td><a href=""><small>{{strtoupper($user->product_name)}}</small></a></td>
                                    <td><a href=""><small>{{strtoupper($user->categories_tuli->name)}}</small></a></td>
                                    <td><a href=""><small>{{strtoupper($user->stores_tuli->name)}}</small></a></td>
                                    <td>{{$user->barcode}}</td>
                                    <td>{{number_format($user->qty, 2)}}</td>
                                    <td>{{number_format($user->qty_remained, 2)}}</td>
                                    @if(Auth::user()->role == 'ADMIN')
                                    <td>{{number_format($user->purchasing_price, 2)}}</td>
                                    @endif
                                    <td>{{number_format($user->selling_price, 2)}}</td>
                                    @if(Auth::user()->role == 'ADMIN')
                                    <td style="color: {{ ($user->qty * $user->selling_price) - ($user->qty * $user->purchasing_price)   < 0 ? 'red' : 'green' }};">
                                        {{ number_format(($user->qty * $user->selling_price) - ($user->qty * $user->purchasing_price), 2) }}
                                    </td>
                                    @endif

                                    <td>{{$user->status}}</td>
                                    
                                    <td>{{$user->user->first_name}}</td>
                                    <td>
                                    @if($user->status == 'Waiting')
                                    <div class="pull-right">
                                        <a href="{!! Route('delete-unsubmited-product', ['id' => $user->id, 'status' => 'Inactive']) !!}" 
                                           class="btn btn-outline-danger btn-xs"
                                           onclick="return confirm('Are you sure you want to delete Thi Product ?')">
                                           Delete <i class="icofont icofont-ui-delete"></i>
                                        </a>
                                      </div> 
                                    @else
                                    <i>can not be edited !!</i>
                                    @endif
                                    
							</td>
                                   	</tr>
                                @endforeach
							</tbody>
						</table>
                        <br />
                        {{ $Branch->links() }}

                        @else 
                        <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                            <i class="icon-info-alt txt-danger"></i>
								No Records Found yet
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
                       	</div>
                        @endif
					</div>
                        </div>
                        <div class="tab-pane fade" id="policies" role="tabpanel" aria-labelledby="policies-tab"> 
                        
                    
                       <div class="table-responsive">
                        @if(count($ExpensesRecord)>0)
						<table class="table table-xs">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">Expense Name</th>
                                    <th scope="col">Amount Used</th>
                                    <th scope="col">Descr</th>                                    
                                    <th scope="col">Status</th>
                                    <th scope="col">Created By</th>
                                  
								</tr>
							</thead>
							<tbody>
                                @foreach($ExpensesRecord as $b)
			            <tr>
         						<th scope="row">{{$loop->index + 1}}.</th>
									<td><a href=""><small>{{strtoupper($b->expenses_tuli->e_name)}}</small></a></td>
                                    <td>{{number_format($b->amount_used, 2)}}</td>
                                    <td><a href=""><small>{{strtoupper($b->desr)}}</small></a></td>
                                    <td>{{$b->status}}</td>
                                    <td>{{$b->user->first_name}}</td>
                            </tr>
                                @endforeach
							</tbody>
						</table>
                        <br />
                       

                        @else 
                        <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                            <i class="icon-info-alt txt-danger"></i>
								No Records Found yet
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
                       	</div>
                        @endif
					</div>

                        </div>
                      </div>
                  </div>
              </div>
          </div>
         
      </div>
  </div>




  @push('scripts')
  @endpush
@endsection

 <!-- NEW MODAL START -->
 <div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Produc Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ Route('add-product-inventory-tuli') }}">
                    @csrf
                    <div class="row">
                    <div class="col-lg-4">
                    <div class="form-group">
                            <label class="col-form-label"> Choose Product</label>
                            @livewire('product-search')
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Qnty Receiced</label>
                            <input class="form-control" type="number" required name="qty" value="1">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Unit Of Measure</label>
                            <input class="form-control" type="text" required name="unit_of_measuer" value="">
                        </div>
                    </div>
                   
                    
                </div>

                <div class="row">
                    

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Purchasing Price</label>
                            <input class="form-control" type="text" required name="purchasing_price" placeholder="Purchasing Price...">
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Selling Price</label>
                            <input class="form-control" type="text" required name="selling_price" placeholder="Selling Price...">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        
                    </div>
                    
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Details</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Briefly describe the product...">na</textarea>
                        </div>
                        
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Barcode</label>
                            <input class="form-control" type="text" required name="barcode" value="{{ $barcodeValue }}">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Category</label>
                            <select class="form-control" required name="category">
                            <option value="">--- Choose Category ---</option>  
                                        @foreach ($Categories as $cat)
                                    <option value='{{$cat->id}}'>{{strtoupper($cat->name)}} </option>
                                    @endforeach
                            </select>
                        </div>
                    </div>
                   <input type="text" hidden value="{{ $Inventory->id }}" name="inventory_id">
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm & Register</button>
                </form>
            </div>
        </div>
    </div>
    </div>

     <!-- NEW MODAL START -->
 
     <div class="modal fade" id="newModal_expense" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Expense Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ Route('record-expense-tuli') }}">
                    @csrf
                    <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="col-form-label">Expenses</label>
                            <select class="form-control" required name="expense_id">
                            <option value="">--- Choose Expense ---</option>  
                                        @foreach ($Expense as $st)
                                    <option value='{{$st->id}}'>{{strtoupper($st->e_name)}}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="col-form-label">Amount Used</label>
                            <input class="form-control" type="number" required name="amount_used" >
                        </div>
                    </div>
                   
                    
                </div>

                <div class="row">
                <div class="col-lg-12">
                        <div class="form-group">
                            <label class="col-form-label">Details</label>
                            <textarea class="form-control" name="desr" rows="3" placeholder="Briefly describe ...">na</textarea>
                        </div>
                        
                    </div>
                   <input type="text" hidden value="{{ $Inventory->id }}" name="inventory_id">
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm & Register</button>
                </form>
            </div>
        </div>
    </div>
    </div>

