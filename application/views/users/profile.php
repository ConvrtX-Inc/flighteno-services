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
                    <div class="container-fluid main-container user-profile pb-5" style="padding-left: 4%; padding-right: 4%;">
                        <!-- start page title -->
                        <div class="row">
                          <div class="col-12 mt-3 mb-2">
                            <div class="user-title d-flex align-items-center">
                              <a href="#" class="back"><img src="<?php echo SURL;?>assets/images/arrow-back.png"></a>
                              <h4 class="page-title styleHeader titleStyle m-0"><?=$user['full_name']?></h4>
                            </div>
                          </div>  
                        </div>

                        <div class = "row mt-4">
                          <div class="col-12">
                            <center>
                              <?php if (empty($user['profile_image']) || $user['profile_image'] == ''|| is_null($user['profile_image']) ){ 
                                $imageSource = SURL.'assets/images/male.png';;
                              } else {
                                $imageSource = $user['profile_image'];
                              } ?>                     
                              <img src="<?php echo $imageSource;?>" alt="" class="rounded-circle user-image">
                              <h4 class="page-heading">Profile Information</h4>
                            </center>
                          </div>
                        </div>

                        <?php
                        function get_content($user, $field) {
                          if (!empty($user[$field]) && !is_null($user[$field]) && $user[$field] != '') {
                            echo $user[$field];
                          } else {
                            if ($field == 'id_front') {
                              echo SURL . 'assets/images/sample-id-front.png';
                            } elseif ($field == 'id_back') {
                              echo SURL . 'assets/images/sample-id-back.png';
                            } else {
                              echo 'N/A';
                            }
                          }
                        }
                        ?>
                        
                        <div class="row ml-5 mr-5">
                          <div class="col-5">
                            <div class="d-flex">
                              <div class="w-25">
                                <p class="this-label">Name:</p>
                              </div>
                              <div class="w-75">
                                <p class="this-content"><?=$user['full_name']?></p>
                              </div>
                            </div>

                            <div class="d-flex">
                              <div class="w-25">
                                <p class="this-label">Birthday:</p>
                              </div>
                              <div class="w-75">
                                <p class="this-content"><?=get_content($user, 'birthday')?></p>
                              </div>
                            </div>

                            <div class="d-flex">
                              <div class="w-25">
                                <p class="this-label m-0">Address:</p>
                              </div>
                              <div class="w-75">
                                <p class="this-content m-0"><?=get_content($user, 'location')?></p>
                              </div>
                            </div>
                          </div>
                          <div class="col-5 offset-2">
                            <div class="d-flex">
                              <div class="w-25">
                                <p class="this-label">Email:</p>
                              </div>
                              <div class="w-75">
                                <p class="this-content"><?=get_content($user, 'email_address')?></p>
                              </div>
                            </div>

                            <div class="d-flex">
                              <div class="w-25">
                                <p class="this-label">Number:</p>
                              </div>
                              <div class="w-75">
                                <p class="this-content"><?=get_content($user, 'phone_number')?></p>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class = "row">
                          <div class="col-12">
                            <center>
                              <h4 class="page-heading">ID Information</h4>
                            </center>
                          </div>
                        </div>

                        <div class="row ml-5 mr-5">
                          <div class="col-5">
                            <div class="d-flex">
                              <div class="w-25">
                                <p class="this-label">ID Type:</p>
                              </div>
                              <div class="w-75">
                                <p class="this-content"><?=get_content($user, 'id_type')?></p>
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <div class="row ml-5 mr-5 id-info">
                          <div class="col-5">
                            <p class="this-label mb-1">Front ID</p>
                            <img src="<?=get_content($user, 'id_front')?>" alt="" class="">
                          </div>
                          <div class="col-5 offset-2">
                            <p class="this-label mb-1">Back ID</p>
                            <img src="<?=get_content($user, 'id_back')?>" alt="" class="">
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
        <!-- App js -->
        <script src="<?php echo SURL;?>assets/js/app.min.js"></script>

        <!-- APPLY SEARCH OPTIN IN DROP dOWN -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <script>
            $(function() {
              $(".back").click(function(e) {
                e.preventDefault();
                history.back();
              });
            });
        </script>

    </body>
</html>
