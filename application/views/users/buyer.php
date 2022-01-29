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

        <!-- DROP DOWN STYLE -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />

        <!-- Global admin style -->
        <link href="<?php echo SURL;?>assets/css/styles.css" rel="stylesheet" type="text/css" />

        <style>

            .userNameColorChange{
                color: black;
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

            .styleHeader{

                color: black;
                font-weight : bold
            }
            
            /* paggination  */
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

            .styleShow{
                color       : black;
                font-weight : bold;
                margin-left : -11%;
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

            .totalBoxStyle{
                background-color: #57b0af;
                border: none;
                color: white;
                padding: 20px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: 4px 2px;
                cursor: pointer;
                width: auto;
                font-size: 20px;
                border-radius: 27px;
                font-weight: bold;
                text-align: center;
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
                        <div class="row">
                            <div class="col-12 mt-3 mb-2">
                                <h4 class="page-title styleHeader titleStyle">Signed up users</h4>
                                <p class="titleStyle2">Buyer</p>
                            </div>  
                        </div>
                        <!-- start page title -->
                
                        <?php $buyerData = $this->session->userdata('buyerUsersFilter'); ?>
                        <form method="POST" action="<?php echo base_url();?>index.php/admin/users/index">
                            <div class="row filter-row">
                                <div class="col-xl-3">  
                                    <select id="select-state" name="location" class="form-control filters_style" placeholder="Location">
                                        <option value="" selected>Select Country</option>

                                        <?php foreach ($getAllCountries as $country) {?>
                                            <option value="<?php echo $country['code']; ?>"<?=((!is_null($buyerData) && $buyerData['location']  ==  $country['code']) ? "selected" : "")?>><?php echo $country['name'];?></option>
                                        <?php } ?>
                                    </select>
                                </div> <!-- end col -->
                                
                                <div class="col-xl-4">   
                                    <div class="inner-addon left-addon filter-search">
                                        <img src="<?php echo SURL.'assets/images/icon-search.png';?>" alt="" class="image-icon">
                                        <input type="text" id ="full_name" class="form-control filters_style" placeholder="Search"  name="full_name"  value="<?=(!empty($buyerData['full_name']) ? $buyerData['full_name'] : "")?>" />
                                    </div>
                                </div> <!-- end col -->
                                
                                <div class="col-xl-4" style="/* background: black; */">           
                                    <!-- <button type="submit" class="form-control filters_style_input filter button">Filter</button>
                                    <a class= "form-control filters_style_input filter buttonReset"href="<?php echo base_url();?>index.php/admin/users/resetFilterBuyers">Reset</a>
                                    <i class="glyphicon glyphicon-calendar"></i>  -->
                                </div> <!-- end col -->
                            </div>

                        </form>

                        <div class = "row mt-3 mb-5">
                            <div class="col-12">
                                <table class="content-table">
                                    <tr>
                                        <th><input type="checkbox" id="checkAll" name="checkAll"/><label for="checkAll"></label></th>
                                        <th>Select All</th>
                                        <th>Name</th>
                                        <th>Area</th>
                                        <th>Country</th>
                                        <th></th>
                                    </tr>
                                    <?php foreach ($buyers as $value){ ?>
                                    <tr>
                                        <td class="table-col-small"><input type="checkbox" data-id="<?php echo $value['_id']; ?>" id="check<?php echo $value['_id']; ?>"/><label for="check<?php echo $value['_id']; ?>"></label></td>
                                        <td class="table-col-profile"> 
                                            <center>
                                                <?php if(empty($value['profile_image']) || $value['profile_image'] == ''|| is_null($value['profile_image']) ){ 
                                                    
                                                    $imageSource = SURL.'assets/images/male.png';;
                                                }else{

                                                    $imageSource = $value['profile_image'];
                                                } ?>
                                                                            
                                                <img src="<?php echo $imageSource;?>" alt="" class="rounded-circle images avatar-sm bx-shadow-lg image2">
                                            </center>
                                        </td>
                                        <td class ="userNameColorChange"><?php echo $value['full_name']; ?></td>
                                        <td><?php echo empty($value['location']) || is_null($value['location']) ? 'N/A' : $value['location']; ?></td>
                                        <td><?php echo $value['country']; ?></td>
                                        <td class="table-col-small"><a class="more-options" href="#""><img src="<?php echo SURL;?>assets/images/icon-options.png" alt="" /></a></td>
                                    </tr>
                                    <?php } ?>
                                </table>
                                <div class="pagination"><?php echo $this->pagination->create_links(); ?></div>
                            </div>
                        </div>

                        <!-- <div class="row totalBoxStyle">
                            <div class="col-xl-12">
                                <span><?php echo "Total :".$total; ?> </span>
                            </div>
                        </div> -->
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
        <!-- App js -->
        <script src="<?php echo SURL;?>assets/js/app.min.js"></script>

        <!-- APPLY SEARCH OPTIN IN DROP dOWN -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>

        <script>
            $("#checkAll").click(function(){
                $('input:checkbox').not(this).prop('checked', this.checked);
            });

            $(document).ready(function () {
                $('select').selectize({
                    sortField: 'text'
                });
            });
            $(document).ready(function () {
                $(".selectize-input").addClass("filters_style");
                $(".selectize-dropdown").addClass("filters_style");
             
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

    </body>
</html>
