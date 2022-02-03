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
                    
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12 mt-3 mb-2">
                                <h4 class="page-title styleHeader titleStyle">Signed up users</h4>
                                <p class="titleStyle2">Traveler</p>
                            </div> 
                        </div> 

                        <?php $travelerData = $this->session->userdata('travelerUsersFilter'); ?>
                        <form class="form-filter" method="POST" action="<?php echo base_url();?>index.php/admin/users/traveler">
                            <div class="row filter-row">
                                <div class="col-xl-3">  
                                    <select id="select-state" name="location" class="form-control filters_style" placeholder="Location">
                                        <option value="" selected>Select Country</option>
                                        <?php foreach ($getAllCountries as $country) {?>
                                            <option value="<?php echo $country['code'];?>"<?=((!is_null($travelerData) && $travelerData['location'] == $country['code']) ? "selected" : "")?>><?php echo $country['name'];?></option>
                                        <?php } ?>
                                    </select>
                                </div> <!-- end col -->
                                
                                <div class="col-xl-4">       
                                    <div class="inner-addon left-addon filter-search">
                                        <img src="<?php echo SURL.'assets/images/icon-search.png';?>" alt="" class="image-icon">
                                        <input type="text" id ="full_name" class="form-control filters_style" placeholder="Search"  name="full_name"  value="<?=(!empty($travelerData['full_name']) ? $travelerData['full_name'] : "")?>" />
                                    </div>
                                </div> 
                                
                                <div class="col-xl-4">
                                    <a href="<?php echo base_url();?>index.php/admin/users/resetFilterTravelers" class="btn-reset">Reset</a>
                                </div> <!-- end col -->
                            </div>
                        </form>

                        <div class = "row mt-3 mb-5">
                            <div class="col-12">
                                <table class="content-table">
                                    <tr>
                                        <th class="table-col-small"><input type="checkbox" id="checkAll" name="checkAll"/><label for="checkAll"></label></th>
                                        <th class="table-col-profile">Select All</th>
                                        <th class="table-col-name">Name</th>
                                        <th class="table-col-large">Area</th>
                                        <th class="table-col-medium">Country</th>
                                        <th class="table-col-small"></th>
                                    </tr>
                                    <?php foreach ($traveler as $key=>$value){ ?>
                                    <tr>
                                        <td><input type="checkbox" data-id="<?php echo $value['_id']; ?>" id="check<?php echo $value['_id']; ?>"/><label for="check<?php echo $value['_id']; ?>"></label></td>
                                        <td> 
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
                                        <td class="more-options-col">
                                            <a class="more-options" href="#""><img src="<?php echo SURL;?>assets/images/icon-options.png" alt="" /></a>
                                            <div class="more-options-box" style="display: none;">
                                                <p><a class="option-chat" href="#">Chat User</a></p>
                                                <p><a class="option-disable" href="#">Disable User</a></p>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </table>
                                
                                <!-- 
                                    FLIGHT-29 fix
                                    As suggested, removed 'Load more' pagination.
                                    Added the usual table pagination of numbers.
                                -->

                                <div class="mt-4"><?php  echo $this->pagination->create_links(); ?></div>
                                <?php
                                $start = $index + 1;
                                $end = ($total - $per_page >= $start)? $index + $per_page : $total;
                                ?>
                                <p class="pagination-results">Displaying results <strong><?=$start . ' - ' . $end . '</strong> of ' . $total?></p>

                                <!-- <center>
                                    <p class="mt-4 mb-0 last-page" style="display: none;">No more results found.</p>
                                    <div class="mt-4 spinner spinner-border text-dark" role="status" style="display: none;">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <a href="#" class="mt-4 btn-load" style="display: none;">Load more</a>
                                </center> -->
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
        <!-- App js -->
        <script src="<?php echo SURL;?>assets/js/app.min.js"></script>

         <!-- APPLY SEARCH OPTIN IN DROP dOWN -->
         <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>

        <script>
            $("#checkAll").click(function(){
                $('input:checkbox').not(this).prop('checked', this.checked);
                    
                let optionBox = $(".more-options-visible");
                if (optionBox.length)
                    optionBox.slideToggle("fast").removeClass("more-options-visible");
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

<script>
            $(function() {
                $(".form-filter select.form-control").change(function() {
                    filterSubmit();
                });

                $(".form-filter input.form-control").keypress(function(e) {
                    if(e.which == 13) {
                        filterSubmit();
                    }
                });
                
                const filterSubmit = () => {
                    $(".form-filter").submit();
                }

                $(".content-table").on("click", "td input[type='checkbox']", function(){
                    $("#checkAll").prop("checked", false);
                    
                    let optionBox = $(".more-options-visible");
                    if (optionBox.length)
                        optionBox.slideToggle("fast").removeClass("more-options-visible");
                });

                // More options
                $(".content-table").on("click", ".more-options", function(e) {
                    e.preventDefault();

                    // show/hide more options
                    let optionBox = $(this).closest(".more-options-col").find(".more-options-box");
                    if (optionBox.hasClass("more-options-visible")) {
                        $(".more-options-visible").slideToggle("fast").removeClass("more-options-visible");
                    } else {
                        // show/hide Chat user option
                        let checkCount = $(".content-table").find("td input[type='checkbox']:checked").length;
                        (checkCount <= 1) 
                            ? optionBox.find(".option-chat").show()
                            : optionBox.find(".option-chat").hide();

                        $(".more-options-visible").slideToggle("fast").removeClass("more-options-visible");
                        optionBox.slideToggle("fast").addClass("more-options-visible");
                    }
                });

                /*
                * FLIGHT-29 fix
                * Removed 'Load more' pagination.
                */

                /*
                // Load More Custom AJAX Pagination
                const url = "<?php echo SURL ?>index.php/admin/users/loadMore";
                let currentIndex = <?php echo $index; ?>;
                let per_page = <?php echo $per_page; ?>;
                let total = <?php echo $total; ?>;

                if (per_page >= total) {
                    $(".last-page").show();
                } else {
                    $(".btn-load").show();
                }

                $(".btn-load").click(function(e) {
                    e.preventDefault();

                    // toggle spinner & button
                    $(".spinner").show();
                    $(".btn-load").hide();

                    // prepare data parameters
                    let data = {
                        index: currentIndex,
                        per_page: per_page,
                        total: total,
                        findArray: JSON.stringify(<?php echo json_encode($findArray); ?>)
                    };

                    // load more data
                    $.get(url, data, function(response) {
                        // append new data in the table
                        $(".content-table tr:last").after(response);

                        // update data index
                        currentIndex = currentIndex + per_page;

                        // toggle spinner & load more button
                        $(".spinner").hide();

                        if(total - per_page <= currentIndex) {
                            $(".btn-load").hide();
                            $(".last-page").show();
                        } else {
                            $(".btn-load").show();
                        }
                    });
                });
                */
            });
        </script>
        
    </body>
</html>
