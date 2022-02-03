<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Dashboard | Flighteno</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Flighteno" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo SURL;?>assets/images/favicon.png">

        <!-- jvectormap -->
        <link href="<?php echo SURL;?>assets/libs/jqvmap/jqvmap.min.css" rel="stylesheet" />

        <!-- DataTables -->
        <link href="<?php echo SURL;?>assets/libs/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo SURL;?>assets/libs/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
        
        <!-- App css -->
        <link href="<?php echo SURL;?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SURL;?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SURL;?>assets/css/app.min.css" rel="stylesheet" type="text/css" />
        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->


        <style>
            .userNameColorChange{

                color: black;
            }
            .filters_style {
                border-radius: 25px;
                border: 2px solid #e9ecef;
                width: 100%;
            }

            table {
                border-collapse: collapse;
                width: 100%;
            }

            td, th {
                border-bottom: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }

            .table thead tr, .table tbody tr {
                border-bottom: 1px solid #dddddd;
            }
            .table tbody tr:last-child { border-bottom: none; }

            .checkInput {
                border: 2px solid #898A8D;
                box-sizing: border-box;
                border-radius: 4px;
            }
            .checkInput:checked {
                accent-color: #69C200;
                border-radius: 4px;
                filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));
            }

            .customStyle{
                font-size : 15px;
            }

            .styleHeader{

                color: black;
                font-weight : bold
            }

            /* pagination */
            .pagination a {
                color: black;
                float: left;
                padding: 8px 16px;
                text-decoration: none;
            }

            .pagination a.active {
                background-color: #4CAF50;
                color: white;
                border-radius: 5px;
            }

            .pagination a:hover:not(.active) {
                background-color: #ddd;
                border-radius: 5px;
            }

            .buttonNew{
              	border-radius: 25px;
				background-color:  #57B0AF;
				color: white;
				font-weight: bold;
                text-decoration: none;
                display: inline-block;
                cursor: pointer;
                width: 44%;
            }

            .buttonReset{
                border-radius: 25px;
				color: white;
				font-weight: bold;
                text-decoration: none;
                display: inline-block;
                cursor: pointer;
                width: 44%;
                background-color:  #ff0e0e;
                text-align: center;
            }
            .titleStyle{
                font-style: normal;
                font-weight: 800;
                font-size: 40px;
                color: #18243C;
                margin-top: 2%;
                margin-left: 0%;
                margin-bottom: 1%;
            }
            .titleStyle2{
                left: 350px;
                top: 251px;
                font-weight: bold;
                font-size: 16px;
                line-height: 19px;
                color: #959595
            }
            body{
                background-color: #f8f8f8;
            }
        </style>
    </head>
    <body>
        <!-- Begin page -->
        <div id="wrapper">

            <!-- Topbar Start -->
            <?php include('includes/topbar.php');?>
            <!-- end Topbar -->
            <!-- ========== Left Sidebar Start ========== -->
            <?php include('includes/sidebar.php');?>
            <!-- Left Sidebar End -->
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <div class="content">
                    <!-- Start Content-->
                    <div class="container-fluid" style="padding-left:4%; padding-right: 4%;">
                        <div class= "row">
                            <div class="col-12 mt-3">
                                <h4 class="page-title styleHeader titleStyle">Transaction</h4>
                                <p class="titleStyle2">Buyer</p>
                            </div>  
                        </div>

                    <?php $buyerTransactionsFilter = $this->session->userdata('buyerTransactionsFilter'); ?>
                        <!-- start page title -->
                        <form class="mt-2" method="POST" action="<?php echo base_url();?>index.php/admin/Trasection/index">
                            <div class="row">
                                <div class="col-xl-3">
                                    <div class="form-group">
                                        <label class="col-form-label">From: </label>
                                        <input type="date" class="form-control filters_style" placeholder="start date" name="start_date" 
                                        value="<?=(!empty($buyerTransactionsFilter['start_date']) ? $buyerTransactionsFilter['start_date'] : "")?>" />
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-xl-3">
                                    <div class="form-group">
                                        <label class="col-form-label">To:</label>
                                        <input type="date" class="form-control filters_style" placeholder="end date" name="end_date" 
                                        value="<?=(!empty($buyerTransactionsFilter['end_date']) ? $buyerTransactionsFilter['end_date'] : "")?>" />
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-xl-3">
                                    <div class="form-group">
                                        <label class="col-form-label">Price:</label>
                                        <input type="input" class="form-control filters_style" placeholder="0"  name="price" 
                                        value="<?=(!empty($buyerTransactionsFilter['price']) ? $buyerTransactionsFilter['price'] : "")?>" />
                                    </div>
                                </div> <!-- end col -->

                                <!--<div class="col-xl-2">
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <input type="input" class="form-control filters_style" placeholder="Search"  name="search" value="<?=(!empty($buyerTransactionsFilter['search']) ? $buyerTransactionsFilter['search'] : "")?>" />
                                        </div>
                                    </div>
                                </div>-->

                                <div class="col-xl-3 mt-1">
                                    <div class="form-group">
                                        <label style="display: block;">Search</label>
                                        <input type="submit" class="form-control filters_style_input filter buttonNew" value="Filter" />
                                        <a class= "form-control filters_style_input filter buttonReset"href="<?php echo base_url();?>index.php/admin/Trasection/resetFilterBuyers">Reset</a>
                                        <i class="glyphicon glyphicon-calendar"></i>
                                    </div>
                                </div> <!-- end col -->

                            </div>
                        </form>

                        <div class = "row mt-2">
                            <div class="col">
                                <table class="table table-borderless" id="buyersTable" style="width:100% !important">
                                    <thead>
                                        <tr>
                                            <th class="text-left"><input class="checkInput" type="checkbox" id="checkAll" name="checkAll" value="all"> Select All</th>
                                            <th class="text-center"></th>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Order ID</th>
                                            <th class="text-center">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($buyers_payment as $value){?>
                                            <tr>
                                                <td class="text-left"><input class="checkInput" type="checkbox" data-id="<?php echo $value['_id']; ?>" /></td>
                                                <td class="text-center">
                                                    <?php if(empty($value['profileData'][0]['profile_image']) || $value['profileData'][0]['profile_image'] == ''|| is_null($value['profileData'][0]['profile_image']) ){ 
                                                        
                                                        $imageSource = SURL.'assets/images/male.png';;
                                                        
                                                    }else{

                                                        $imageSource = $value['profileData'][0]['profile_image'];
                                                    
                                                    } ?>

                                                    <img src="<?php echo $imageSource;?>" alt="" class="rounded-circle images avatar-sm bx-shadow-lg image2">
                                                </td>
                                                <td class= "userNameColorChange text-center"><?php echo $value['profileData'][0]['full_name']; ?></td>
                                                <td class="text-center"><?php  $orderDate = $value['created_date']->toDateTime()->format("d M Y"); echo $orderDate; ?></td>
                                                <td class="text-center" style = "font-weight:bold"><?php echo $value['order_id']; ?></td>
                                                <td class="text-center" style = "font-weight:bold"><?php echo '$'.$value['price']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <div class="pagination float-right"><?php  echo $this->pagination->create_links(); ?></div>
                            </div>
                        </div>                    
                    </div> <!-- container -->

                </div> <!-- content -->

                <!-- Footer Start -->
                <?php include('includes/footer.php');?>
                <!-- end Footer -->
            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->
        </div>
        <!-- END wrapper -->
        <!-- Vendor js -->
        <script src="<?php echo SURL;?>assets/js/vendor.min.js"></script>
        <!-- KNOB JS -->
        <script src="<?php echo SURL;?>assets/libs/jquery-knob/jquery.knob.min.js"></script>
        <!-- Chart JS -->
        <script src="<?php echo SURL;?>assets/libs/chart-js/Chart.bundle.min.js"></script>
        <!-- Jvector map -->
        <script src="<?php echo SURL;?>assets/libs/jqvmap/jquery.vmap.min.js"></script>
        <script src="<?php echo SURL;?>assets/libs/jqvmap/jquery.vmap.usa.js"></script>
        <!-- Datatable js -->
        <script src="<?php echo SURL;?>assets/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo SURL;?>assets/libs/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="<?php echo SURL;?>assets/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="<?php echo SURL;?>assets/libs/datatables/responsive.bootstrap4.min.js"></script>
        <!-- Dashboard Init JS -->
        <script src="<?php echo SURL;?>assets/js/pages/dashboard.init.js"></script>

        <!--dataTables page load more-->
        <script src="<?php echo SURL;?>assets/libs/jquery-datatables-pageLoadMore/js/dataTables.pageLoadMore.min.js"></script>

        <!-- App js -->
        <script src="<?php echo SURL;?>assets/js/app.min.js"></script>
        <script>
            $("#checkAll").click(function(){
                $('input:checkbox').not(this).prop('checked', this.checked);
            });
        </script>
        <script type="text/javascript">
            $(function(){
                var table = $('#buyersTable').DataTable({
                    //dom: "<'row float-left mb-2'<'col-sm-8 toolbar'><'col-sm-4' f>>rt",
                    dom: '',
                    //language: {
                        //search: '<span class="fa fa-search form-control-feedback"></span>',
                    //    search: '',
                    //    searchPlaceholder: "Search"
                    //},
                    //autoWidth: false
                    ordering: false,
                    //columnDefs: [
                    //    {
                    //        targets: [ 4 ],
                    //        visible: false,
                    //        searchable: false
                    //    },
                    //],
                    //drawCallback: function(){
                        // If there is some more data
                    //    if($('#btn-example-load-more').is(':visible')){
                            // Scroll to the "Load more" button
                    //        $('html, body').animate({
                    //        scrollTop: $('#btn-example-load-more').offset().top
                    //        }, 1000);
                    //    }

                        // Show or hide "Load more" button based on whether there is more data available
                    //    $('#btn-example-load-more').toggle(this.api().page.hasMore());
                    //}      
                //});
                //$('#btn-example-load-more').on('click', function(){  
                    // Load more data
                //    table.page.loadMore();
                //});
            })
        </script>
    </body>
</html>