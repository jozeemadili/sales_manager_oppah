<div>

    <!-- Summary Cards -->
    
    <div class="col-lg-12">
        <div class="card income-card card-secondary text-center">
            <div class="card-body">
                <div class="round-box mb-2">
                    <i class="icofont icofont-abacus-alt" style="font-size:40px;"></i>
                </div>
    
                <h5>{{ number_format($summary['sumProduct'], 2) }}</h5>
    
                <p>Stock Value</p>
            </div>
        </div>
    </div>
        <div class="row mb-4">

            
            <!-- Generated Today -->
            <div class="col-lg-3">
                <div class="card income-card card-secondary text-center">
        
                    <div class="card-body">
        
                        <div class="round-box mb-2">
                            <i class="icofont icofont-money-bag"
                               style="font-size:40px;"></i>
                        </div>
        
                        <h5>
                            {{ number_format($summary['generated_amount'],2) }}
                        </h5>
        
                        <p>Total Generated Amount (Today)</p>
        
                    </div>
        
                </div>
            </div>
        
        
        
            <!-- Paid Today -->
            <div class="col-lg-3">
                <div class="card income-card card-success text-center">
        
                    <div class="card-body">
        
                        <div class="round-box mb-2">
                            <i class="icofont icofont-tick-boxed"
                               style="font-size:40px;"></i>
                        </div>
        
                        <h5>
                            {{ number_format($summary['paid_amount'],2) }}
                        </h5>
        
                        <p>Total Paid Amount (Today)</p>
        
                    </div>
        
                </div>
            </div>
        
        
        
        
            <!-- Remaining Today -->
            <div class="col-lg-3">
                <div class="card income-card card-warning text-center">
        
                    <div class="card-body">
        
                        <div class="round-box mb-2">
                            <i class="icofont icofont-warning-alt"
                               style="font-size:40px;"></i>
                        </div>
        
                        <h5>
                            {{ number_format($summary['unpaid_amount'],2) }}
                        </h5>
        
                        <p>Remaining (Unpaid) Amount (Today)</p>
        
                    </div>
        
                </div>
            </div>
        
        
        
        
            <!-- All Time Unpaid -->
            <div class="col-lg-3">
                <div class="card income-card card-danger text-center">
        
                    <div class="card-body">
        
                        <div class="round-box mb-2">
                            <i class="icofont icofont-bank-alt"
                               style="font-size:40px;"></i>
                        </div>
        
                        <h5>
                            {{ number_format($summary['unpaid_overall'],2) }}
                        </h5>
        
                        <p>Total Unpaid Amount (All Time)</p>
        
                    </div>
        
                </div>
            </div>
        
        
        </div>



    <!-- Sales Chart -->

    <div class="card">

        <div class="card-header">

            <div class="header-top d-sm-flex align-items-center">

                <h5>
                    Sales Trends
                </h5>

            </div>

        </div>


        <div class="card-body p-0">

            <figure class="highcharts-figure">

                <div id="container_sales"></div>

            </figure>

        </div>


    </div>


</div>



@push('css')

<style>

#container_sales{
    height:39vh;
}


.highcharts-figure,
.highcharts-data-table table{

    min-width:510px;
    max-width:900px;
    margin:1em auto;

}

</style>

@endpush




@push('scripts')


<script src="{{asset('assets/js/highcharts/highcharts.js')}}"></script>

<script src="{{asset('assets/js/highcharts/highcharts-3d.js')}}"></script>

<script src="{{asset('assets/js/highcharts/exporting.js')}}"></script>

<script src="{{asset('assets/js/highcharts/accessibility.js')}}"></script>



<script>


document.addEventListener('livewire:load', function(){


    function renderSalesChart(){


        Highcharts.chart('container_sales', {


            chart:{

                type:'column',

                options3d:{

                    enabled:true,

                    alpha:10,

                    beta:25,

                    depth:70

                }

            },


            title:{

                text:'',

                align:'left'

            },


            subtitle:{

                text:'Monthly Sales {{date("Y")}}',

                align:'left'

            },


            plotOptions:{

                column:{

                    depth:25

                }

            },


            xAxis:{


                categories:[

                    'Jan','Feb','Mar','Apr',
                    'May','Jun','Jul','Aug',
                    'Sep','Oct','Nov','Dec'

                ],


                labels:{

                    skew3d:true,

                    style:{

                        fontSize:'16px'

                    }

                }


            },



            yAxis:{


                title:{

                    text:'TZS',

                    margin:20

                }


            },



            tooltip:{


                valueSuffix:' TZS'


            },



            series:[{


                name:'Total Sales',

                data:@json($sales)


            }]


        });


    }



    renderSalesChart();



    Livewire.hook('message.processed',()=>{

        renderSalesChart();

    });



});


</script>


@endpush