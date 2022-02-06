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

        <!-- bootstrap-daterangepicker -->
        <link href="<?php echo SURL;?>assets/libs/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" />
        
        <!-- App css -->
        <link href="<?php echo SURL;?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SURL;?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SURL;?>assets/css/app.min.css" rel="stylesheet" type="text/css" />
        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->
        <!-- <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-flag-fill" viewBox="0 0 16 16">
        <path d="M14.778.085A.5.5 0 0 1 15 .5V8a.5.5 0 0 1-.314.464L14.5 8l.186.464-.003.001-.006.003-.023.009a12.435 12.435 0 0 1-.397.15c-.264.095-.631.223-1.047.35-.816.252-1.879.523-2.71.523-.847 0-1.548-.28-2.158-.525l-.028-.01C7.68 8.71 7.14 8.5 6.5 8.5c-.7 0-1.638.23-2.437.477A19.626 19.626 0 0 0 3 9.342V15.5a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 1 0v.282c.226-.079.496-.17.79-.26C4.606.272 5.67 0 6.5 0c.84 0 1.524.277 2.121.519l.043.018C9.286.788 9.828 1 10.5 1c.7 0 1.638-.23 2.437-.477a19.587 19.587 0 0 0 1.349-.476l.019-.007.004-.002h.001"/>
        </svg> -->

        <!-- DROP DOWN STYLE -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />

        <!-- Sweetalert2 -->
        <link href="<?php echo SURL;?>assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

        <!-- Global admin style -->
        <link href="<?php echo SURL;?>assets/css/styles.css" rel="stylesheet" type="text/css" />

        <style>

            .userNameColorChange{

                color: black;
            }
            .filters_style {
                border-radius: 25px;
                border: 2px solid #e9ecef;
                /* padding: 20px; */
                width: 100%;
                /* height: 15px; */
            }

            table {
                border-collapse: collapse;
                width: 100%;
            }

            td, th {
                /* border: 1px solid #dddddd; */
                border-bottom: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }
            
            .table thead tr, .table tbody tr {
                border-bottom: 1px solid #dddddd;
            }
            .table tbody tr:last-child { border-bottom: none; }

            .styleHeader{

                color: black;
                font-weight : bold
            }
            .button{
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
            #sidebar-menu ul li a.active{
                border-right-color: transparent;
            }
            .btn-flag{
                border: none;
            }
            li span.link-disabled{
                margin-top: .5rem!important;
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
                    <div class="container-fluid main-container" style="padding-left: 4%; padding-right:4%">
                        <div class="row">           
                            <div class="col-12 mt-3">
                                <h4 class="page-title styleHeader titleStyle">Flag users</h4>
                                <p class="titleStyle2">Buyer</p>
                            </div>  
                        </div>

                    <?php $flagBuyerUsers = $this->session->userdata('flagBuyerUsers'); ?>
                        
                        <div id="divError"></div>
                        
                        <form id="formFilter" class="mt-2" method="POST" action="<?php echo base_url();?>index.php/admin/FlagUsers/index">
                            <div class="row filter-row">
                               
                                <div class="col-xl-5">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="form-group">
                                                <label class="col-form-label">From:</label>
                                                <input id="inputFrom" type="date" class="form-control filters_style" placeholder="start date" 
                                                name="start_date"  value="<?=(!empty($flagBuyerUsers['start_date']) ? $flagBuyerUsers['start_date'] : "")?>" />
                                            </div>
                                        </div> <!-- end col -->

                                        <div class="col-xl-6">
                                            <div class="form-group">
                                                <label class="col-form-label">To:</label>
                                                <input id="inputTo" type="date" class="form-control filters_style" placeholder="end date"  
                                                name="end_date"  value="<?=(!empty($flagBuyerUsers['end_date']) ? $flagBuyerUsers['end_date'] : "")?>" />
                                            </div>
                                        </div> <!-- end col -->
                                    </div>
                                </div>

                                <!--<div class="col-xl-3">
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <input class="form-control filters_style"
                                            value="<?=(!empty($flagBuyerUsers['daterange']) ? $flagBuyerUsers['daterange'] : "")?>"
                                            type="text" name="daterange" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>-->

                                <div class="col-xl-3">           
                                    <div class="form-group">
                                        <label class="col-form-label">Search by Name</label>
                                        <input type="text" id ="full_name" class="form-control filters_style" placeholder="Search Name"  
                                        name="full_name" value="<?=(!empty($flagBuyerUsers['full_name']) ? $flagBuyerUsers['full_name'] : "")?>" autocomplete="off" />
                                    </div>
                                </div> 

                                <div class="col-xl-4 mt-1">
                                    <div class="form-group">
                                        <label style="display: block;">Search</label>
                                        <button id="btnFilter" type="button" class="btn btn-sm btn-submit">Filter</button>
                                        <a class= "btn-reset" href="<?php echo base_url();?>index.php/admin/FlagUsers/resetFilterBuyers">Reset</a>
                                        <i class="glyphicon glyphicon-calendar"></i>
                                    </div>
                                </div> <!-- end col -->

                            </div>
                        </form>
                        <div class = "row mt-2">
                            <div class="col">
                                <table class="content-table">
                                    <thead>
                                        <tr>
                                            <th scope="col">
                                                <div class="row">
                                                    <div class="col-2">
                                                        <input type="checkbox" id="checkAll" name="checkAll"/>
                                                        <label class="" for="checkAll"></label>
                                                    </div>
                                                    <div class="col-10 mt-2">
                                                        Select All
                                                    </div>
                                                </div>
                                            </th>
                                            <th scope="col" class="table-col-profile"></th>
                                            <th scope="col" class="text-center">Full Name</th>
                                            <th scope="col" class="text-center">Email</th>
                                            <th scope="col" class="text-center">Location</th>
                                            <th scope="col" class="text-center">Flag</th>
                                        </tr>
                                    </thead>
                                    <body>
                                        <?php foreach($flagUsers as $buyerFlag) { ?>
                                            <tr>
                                                <th scope="row">
                                                    <input type="checkbox" data-id="<?php echo $buyerFlag['_id']; ?>" id="check<?php echo $buyerFlag['_id']; ?>"/>
                                                    <label for="check<?php echo $buyerFlag['_id']; ?>"></label>
                                                </th>
                                                <td>
                                                    <?php if(empty($buyerFlag['profile_image']) || $buyerFlag['profile_image'] == ''|| is_null($buyerFlag['profile_image']) ){ 
                                                        
                                                        $imageSource = SURL.'assets/images/male.png';;
                                                    }else{

                                                        $imageSource = $buyerFlag['profile_image'];
                                                    } ?>
                                                    <img src="<?php echo $imageSource;?>" alt="" class="ml-4 rounded-circle images avatar-sm bx-shadow-lg image2">
                                                </td>
                                                <td class="userNameColorChange text-center"> <?php echo $buyerFlag['full_name'];?> </td>
                                                <td class="text-center"> <?php echo $buyerFlag['email_address'];?> </td>
                                                <td class="text-center"> <?php echo isset($buyerFlag['location']) && !empty($buyerFlag['location'] && !is_null($buyerFlag['location'])) ? $buyerFlag['location'] : 'N/A';?> </td>
                                                <td class="text-center">
                                                    <button data-id="<?php echo $buyerFlag['_id']; ?>" class="btn-flag" type="button">
                                                    <?php if(isset($buyerFlag['flag_reported']) && ($buyerFlag['flag_reported'] == true || $buyerFlag['flag_reported'] == 1)){ ?>
                                                            
                                                            <img src="<?php echo SURL;?>assets/images/flag1.png" alt="" class="images avatar-sm bx-shadow-lg image2">
                                                        <?php }else{ ?>

                                                            <img src="<?php echo SURL;?>assets/images/flag6.png" alt="" class="images avatar-sm bx-shadow-lg image2">
                                                        <?php }
                                                        ?>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </body>
                                </table>

                                <?=($total_rows === 0)? '<center><p class="mt-5" style="font-size: 16px;">No results found.</p></center>' : ''?>
                                <?php $thisPagination = $this->session->userdata('paginationData'); ?>
                                <div class="pagination-container d-flex justify-content-end align-items-center">
                                    <span class="rows-per-page">
                                        Rows per page:
                                        <form class="form-per-page" method="POST" action="<?php echo base_url();?>index.php/admin/FlagUsers/index">
                                            <select name="per_page" id="per_page">
                                                <option value="3" <?=((is_null($thisPagination) || !isset($thisPagination['per_page']) || $thisPagination['per_page']  ==  "3") ? "selected" : "")?>>3</option>
                                                <option value="6" <?=((!is_null($thisPagination) && isset($thisPagination['per_page']) && $thisPagination['per_page']  ==  "6") ? "selected" : "")?>>6</option>
                                                <option value="12" <?=((!is_null($thisPagination) && isset($thisPagination['per_page']) && $thisPagination['per_page']  ==  "12") ? "selected" : "")?>>12</option>
                                                <option value="20" <?=((!is_null($thisPagination) && isset($thisPagination['per_page']) && $thisPagination['per_page']  ==  "20") ? "selected" : "")?>>20</option>
                                                <option value="50" <?=((!is_null($thisPagination) && isset($thisPagination['per_page']) && $thisPagination['per_page']  ==  "50") ? "selected" : "")?>>50</option>
                                            </select>
                                        </form>
                                    </span>
                                    
                                    <?php
                                    $start = ($total_rows > 0)? $index + 1 : 0;
                                    $end = ($total_rows - $per_page >= $start)? $index + $per_page : $total_rows;
                                    $pagination_msg = $start.'-'.$end.' of '.$total_rows;
                                    ?>
                                    <span class="pagination-msg"><?=$pagination_msg?></span>
                                    <?=$links?>
                                </div>

                                <!--<div class="mt-4 pagination float-right"><?php  echo $this->pagination->create_links(); ?></div>-->
                            </div>
                        </div>

                        <!-- end page title --> 

                        <!-- end row -->                       
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

        <!-- Moment -->
        <script src="<?php echo SURL;?>assets/libs/moment/moment.min.js"></script>

        <!--bootstrap-daterangepicker-->
        <script src="<?php echo SURL;?>assets/libs/bootstrap-daterangepicker/daterangepicker.js"></script>

        <!-- Datatable js -->
        <script src="<?php echo SURL;?>assets/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo SURL;?>assets/libs/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="<?php echo SURL;?>assets/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="<?php echo SURL;?>assets/libs/datatables/responsive.bootstrap4.min.js"></script>
        <!-- Dashboard Init JS -->
        <script src="<?php echo SURL;?>assets/js/pages/dashboard.init.js"></script>
        <!-- App js -->
        <script src="<?php echo SURL;?>assets/js/app.min.js"></script>
        
        <!-- Sweetalert2 -->
        <script src="<?php echo SURL;?>assets/libs/sweetalert2/sweetalert2.min.js"></script>

        <script>
            $("#checkAll").click(function(){
                $('input:checkbox').not(this).prop('checked', this.checked);
            });
        </script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
            <script>
            $(function() {
                availableTags = [];
                $.ajax({
                'url': '<?php echo SURL ?>index.php/admin/users/getFullNames',
                'type': 'POST',
                'data': "",
                'success': function (response) {
                    availableTags = JSON.parse(response);
                    $("#full_name").autocomplete({
                    source: availableTags
                    });
                }
                });
            });
        </script>
        <script type="text/javascript">
            //var flagTable;
            function setError(error){
                var errorAlert='<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                            +error+
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
                                '<span aria-hidden="true">&times;</span>'
                            '</button>'
                        '</div>';
                return errorAlert;
            }
            $(function(){

                /*$('input[name="daterange"]').daterangepicker({
                    autoApply: true
                });

                <?php if(empty($flagBuyerUsers['daterange'])){ ?>
                    $('input[name="daterange"]').val('');
                    $('input[name="daterange"]').attr("placeholder","Select dates");
                <?php }?>

                $('input[name="daterange"]').on('apply.daterangepicker', function(){
                    //alert($(this).val());
                    let dateRange = $(this).val()
                    const dates = dateRange.split('-');

                    let startDate=dates[0].trim();
                    let endDate=dates[1].trim();

                    $('#start_date').val(startDate);
                    $('#end_date').val(endDate);
                });*/

                $('#divError').html('');

                $('#btnFilter').click(function(){
                    let datefrom = $('#inputFrom').val();
                    let dateto = $('#inputTo').val();

                    if(!!datefrom || !!dateto){
                        if(datefrom===''){
                            $('#divError').html(setError('Invalid value date from'));
                            return;
                        }
                        if(dateto===''){
                            $('#divError').html(setError('Invalid value date to'));
                            return;
                        }

                        if(!moment(dateto).isAfter(datefrom, 'day') && !moment(dateto).isSame(datefrom, 'day')){
                            $('#divError').html(setError('Date from must be greater than date to'));
                            return;
                        }
                    }

                    $('#formFilter').submit();
                })

                //var flagTable = $('#buyersTable').DataTable({
                //    dom: '',
                //    ordering: false,
                //});

                $("#per_page").change(function() {
                    $("form.form-per-page").submit();
                });

                $('.btn-flag').on('click', function(){
                    var userId = $(this).attr('data-id');
                    //alert(userId)
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.value==true) {
                                Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                                )
                            }
                    })

                });    
            })
        </script>
    </body>
</html>
