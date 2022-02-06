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
        
        <!-- DROP DOWN STYLE -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />

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

            .styleHeader{

                color: black;
                font-weight : bold
            }

            body{
                background-color : '';
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
            li span.link-disabled{
                margin-top: .5rem!important;
            }
            .btn-flag{
                border: none;
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
                    <div class="container-fluid main-container" style="padding-left: 4%; padding-right: 4%;">
                        <div class= "row">
                            <div class="col-12 mt-3">
                                <h4 class="page-title styleHeader titleStyle">Flag users</h4>
                                <p class="titleStyle2">Traveler</p>
                            </div>  
                        </div>
                        <!-- start page title -->
                
                    <?php $flagTravelerUser = $this->session->userdata('flagTravelerUser'); ?>
                        
                        <div id="divError"></div>

                        <form id="formFilter" class="mt-2" method="POST" action="<?php echo base_url();?>index.php/admin/FlagUsers/flagTraveler">
                            <div class="row filter-row"> 
                               
                                <div class="col-xl-5">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="form-group">
                                                <label class="col-form-label">From:</label>
                                                <input id="inputFrom" type="date" class="form-control filters_style" placeholder="start date" name="start_date" 
                                                value="<?=(!empty($flagTravelerUser['start_date']) ? $flagTravelerUser['start_date'] : "")?>" />
                                            </div>
                                        </div> <!-- end col -->

                                        <div class="col-xl-6">
                                            <div class="form-group">
                                                <label class="col-form-label">To:</label>
                                                <input id="inputTo" type="date" class="form-control filters_style" placeholder="end date"  name="end_date" 
                                                value="<?=(!empty($flagTravelerUser['end_date']) ? $flagTravelerUser['end_date'] : "")?>" />
                                            </div>
                                        </div> <!-- end col -->
                                    </div>
                                </div>

                                <!--<div class="col-xl-3">
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <input class="form-control filters_style"
                                            value="<?=(!empty($flagTravelerUser['daterange']) ? $flagTravelerUser['daterange'] : "")?>"
                                            type="text" name="daterange" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>-->

                                <div class="col-xl-3"> 
                                    <div class="form-group">
                                        <label class="col-form-label">Search by Name</label>
                                        <input type="text" id ="full_name" class="form-control filters_style" placeholder="Search Name"  name="full_name" 
                                        value="<?=(!empty($flagTravelerUser['full_name']) ? $flagTravelerUser['full_name'] : "")?>" autocomplete="off" />
                                    </div>
                                </div> 

                                <div class="col-xl-4 mt-1">
                                    <div class="form-group">
                                        <label style="display: block;">Search</label>
                                        <button id="btnFilter" type="button" class="btn btn-submit">Filter</button>
                                        <a class= "btn-reset" href="<?php echo base_url();?>index.php/admin/FlagUsers/resetFilterTravel">Reset</a>
                                        <i class="glyphicon glyphicon-calendar"></i> 
                                    </div>
                                </div> <!-- end col -->
                            </div>
                        </form>

                        <div class = "row mt-2">
                            <div class="col">
                                <table class="content-table">
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
                                        <th class="table-col-profile"></th>
                                        <th class="text-center">Full Name</th>
                                        <th class="text-center">Email</th>
                                        <th class="text-center">Location</th>
                                        <th class="text-center">Flag</th>
                                    </tr>
                                    <?php foreach($flagTravelerUsers as $travelerUsers) { ?>
                                        <tr>
                                                <td><input type="checkbox" data-id="<?php echo $travelerUsers['_id']; ?>" id="check<?php echo $travelerUsers['_id']; ?>"/><label for="check<?php echo $travelerUsers['_id']; ?>"></label></td>
                                                <td>
                                                <?php if(empty($travelerUsers['profile_image']) || $travelerUsers['profile_image'] == ''|| is_null($travelerUsers['profile_image']) ){ 
                                                    
                                                    $imageSource = SURL.'assets/images/male.png';
                                                }else{

                                                    $imageSource = $travelerUsers['profile_image'];
                                                } ?>
                                                <img src="<?php echo $imageSource;?>" alt="" class="rounded-circle images avatar-sm bx-shadow-lg image2">
                                            </td>
                                            <td class= "userNameColorChange text-center"><?php echo $travelerUsers['full_name']; ?></td>
                                            <td class="text-center"><?php echo $travelerUsers['email_address']; ?></td>
                                            <td class="text-center"><?php echo isset($travelerUsers['location']) && !empty($travelerUsers['location'] && !is_null($travelerUsers['location'])) ? $travelerUsers['location'] : 'N/A';?> </td>
                                            <td class="text-center">
                                                <?php if(isset($travelerUsers['flag_reported']) && ($travelerUsers['flag_reported'] == true || $travelerUsers['flag_reported'] == 1)){ ?>
                                                    
                                                    <!-- $class = 'fas'; -->
                                                    <img src="<?php echo SURL;?>assets/images/flag1.png" alt="" class="images avatar-sm bx-shadow-lg image2">
                                                <?php }else{ ?>

                                                    <!-- $class = 'far'; -->
                                                    <img src="<?php echo SURL;?>assets/images/flag6.png" alt="" class="images avatar-sm bx-shadow-lg image2">
                                                <?php }
                                                ?>
                                                <!-- <span><i class="<?php echo $class; ?> fa-flag fa-3x"></i></span> -->
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </table>
                                <?=($total_rows === 0)? '<center><p class="mt-5" style="font-size: 16px;">No results found.</p></center>' : ''?>
                                <?php $thisPagination = $this->session->userdata('paginationData'); ?>
                                <div class="pagination-container d-flex justify-content-end align-items-center">
                                    <span class="rows-per-page">
                                        Rows per page:
                                        <form class="form-per-page" method="POST" action="<?php echo base_url();?>index.php/admin/FlagUsers/flagTraveler">
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

                $('#travTable').DataTable({
                    dom: '',
                    ordering: false,
                });
                $("#per_page").change(function() {
                    $("form.form-per-page").submit();
                });
            })
        </script>
    </body>
</html>
