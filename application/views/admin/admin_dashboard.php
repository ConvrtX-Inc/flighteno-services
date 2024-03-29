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

        <!-- Global admin style -->
        <link href="<?php echo SURL;?>assets/css/styles.css" rel="stylesheet" type="text/css" />

        <style>

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

            .cardDisplay{
                display: inline-block;
                width: max-content;
            }
            .boxStyle {
                border-radius: 25px;
                border: 2px solid #e9ecef;
                width: 100%;
            }
            .nameDesign{
                position: absolute;
                width: 158px;
                height: 22px;
                font-style: normal;
                font-weight: 600;
                font-size: 18px;
                line-height: 22px;
                color: #18243C;
            }

            .desigination{
              
                position: absolute;
                width: 158px;
                color: #7A7A7A;
            }

            .dashboardStyle{
                font-style: normal;
                font-weight: 800;
                font-size: 40px;
                color: #18243C;
                margin-top: 1%;
                margin-left: 0%;
                margin-bottom: 1%;
            }
            .divStyleRecet{
                display: flex;
                flex-direction: column;
                min-height: 92vh;
            }

            body{
                background-color: #f8f8f8;
            }
            .cardImageDesign{
                width: 43%;
                height: 215%;
                position: absolute;
                left: 34.53%;
                top: -81.34%;
            }

            .textColor{
                width: 67px;
                height: 48px;
                left: 366px;
                top: 373px;
                font-style: normal;
                font-weight: bold;
                font-size: 40px;
                line-height: 48px;
                letter-spacing: 1px;
                color: #18243C;
            }

            .muteText{
                width: 179px;
                height: 16px;
                left: 366px;
                top: 345px;
                font-style: normal;
                font-weight: 500;
                font-size: 18px;
                line-height: 16px;
                color: #686868;
            }

            .numberCard{
                width: 50px;
                height: 31px;
                left: 366px;
                top: 433px;
                font-style: normal;
                font-weight: bold;
                font-size: 26px;
                line-height: 31px;
                /* color: #16DB4D; */
            }

            .numberCardLast{
                width: 50px;
                height: 31px;
                left: 366px;
                top: 433px;
                font-style: normal;
                font-weight: bold;
                font-size: 26px;
                line-height: 31px;
                /* color: #FB0000; */
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
                <div class="content dashboard-page">
                    <!-- Start Content-->
                    <div class="container-fluid">
                        <div class="row dashboardStyle">
                            <h4 class="page-title styleHeader titleStyle">Dashboard</h4>
                        </div>

                        <div class="row statistics mb-4">
                            <div class="col-xl-4">
                                <div class="this-box">
                                    <h5 class="mt-0 mb-1 this-title">Total Users Signed Up</h5>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="m-0 this-total"><?php echo $users; ?></p>
                                        <img src="<?php echo SURL;?>assets/images/vector01.png" class="" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4">
                                <div class="this-box">
                                    <h5 class="mt-0 mb-1 this-title">Active Users</h5>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="m-0 this-total"><?php echo $active_users; ?></p>
                                        <img src="<?php echo SURL;?>assets/images/vector02.png" class="" />
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-4">
                                <div class="this-box">
                                    <h5 class="mt-0 mb-1 this-title">Inactive Users</h5>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="m-0 this-total"><?php echo $inActive_users; ?></p>
                                        <img src="<?php echo SURL;?>assets/images/vector03.png" class="" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-8">
                                <div class="chart-box mb-4">
                                    <div class="this-title mb-3">
                                        <h4 class="mt-0">User Activity</h4>
                                    </div>

                                    <div class="this-chart user-activity">
                                        <canvas id="user_activity" width="800" height="300"></canvas>
                                    </div>
                                </div>

                                <div class="chart-box">
                                    <div class="this-title mb-3 d-flex align-items-center justify-content-between">
                                        <h4 class="mt-0">Total order of buyers</h4>
                                        <a href="<?php echo base_url();?>index.php/admin/Trasection/index">See all</a>
                                    </div>

                                    <div class="this-chart traveler-orders">
                                        <canvas id="travelers" width="800" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="recent-users mb-4">
                                    <div class="this-box">
                                        <h4 class="mt-0 mb-1">Recent Users</h4>
                                        <p class="date-now text-gray mt-0 mb-1"><?php echo date('d M Y'); ?></p>

                                        <div class="activities-list">
                                            <?php 
                                            foreach ($recentActivity as $activity) { ?>
                                                <div class="activities-list-item">
                                                    <div class="d-flex">
                                                        <?php 
                                                        if(empty($activity['profileData'][0]['profile_image']) || $activity['profileData'][0]['profile_image'] == ''|| is_null($activity['profileData'][0]['profile_image']) ){ 
                                                            $imageSource = SURL.'assets/images/male.png';
                                                        } else {
                                                            $imageSource = $activity['profileData'][0]['profile_image'];
                                                        } 

                                                        $fullname = $activity['profileData'][0]['full_name'];
                                                        if (empty($fullname) || is_null($fullname)) {
                                                            $fullname = $activity['profileData'][0]['email_address'];
                                                        }
                                                        ?>
                                                        <div class="this-profile">
                                                            <img src="<?php echo $imageSource;?>" class="rounded-circle avatar-sm bx-shadow-lg">
                                                        </div>
                                                        <div class="this-details">
                                                            <p class="this-name"><?php echo $fullname; ?></p>
                                                            <p class="mb-0 this-message"><?php echo  $activity['message']; ?></p> 
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php 
                                            } ?>
                                        </div>

                                        <a href="<?php echo base_url();?>index.php/admin/Users/index" class="btn-see-all">See all</a>
                                    </div>
                                </div>

                                <div class="total-30 d-flex align-items-center justify-content-between">
                                    <div class="this-details">
                                        <h5 class="m-0">30 Days</h5>
                                        <p class="">Money Make Last</p>
                                        <?php
                                        $totalEarnedCost = (is_null($totalEarnedCost) || empty($totalEarnedCost)? '0.00' : $totalEarnedCost );
                                        ?>
                                        <h4 class="m-0"><?php echo '$'.$totalEarnedCost; ?></h4>
                                    </div>

                                    <div class="this-image">
                                        <img src="<?php echo SURL;?>assets/images/money.png" class="" />
                                    </div>
                                </div>
                            </div>
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
        <!-- <script src="<?php echo SURL;?>assets/libs/chart-js/Chart.bundle.min.js"></script> -->
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

        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js" integrity="sha512-QSkVNOCYLtj73J4hbmVoOV6KVZuMluZlioC+trLpewV8qMjsWqlIQvkn1KGX2StWvPMdWGBqim1xlC8krl1EKQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            let recentActivityToday = '<?=json_encode($recentActivityToday)?>';
            let countOrderBuyersToday = '<?=json_encode($countOrderBuyersToday)?>';
            recentActivityToday = JSON.parse(recentActivityToday);
            countOrderBuyersToday = JSON.parse(countOrderBuyersToday);

            let dataUserActivity = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            let dataBuyers = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            
            recentActivityToday.forEach(function (item, index) {
                let thisHour = item['_id'];
                let thisCount = item['count'];
                let dataIndex = Math.floor(thisHour / 2);
                dataUserActivity[dataIndex] += thisCount;
            });
            
            countOrderBuyersToday.forEach(function (item, index) {
                let thisHour = item['_id'];
                let thisCount = item['count'];
                let dataIndex = Math.floor(thisHour / 2);
                dataBuyers[dataIndex] += thisCount;
            });

            window.onload = function() {
                new Chart(document.getElementById("user_activity"), {
                    type: 'bar',
                    data: {
                        labels:  ['12AM', '2AM', '4AM', '6AM', '8PM', '10AM', '12PM','14PM','16PM', '18PM','20PM','22PM'],
                        datasets: [
                            {    
                                data: dataUserActivity,
                                backgroundColor: ["#ffffff" ,"#ffffff" ,"#ffffff","#ffffff","#ffffff", "#ffffff","#ffffff", "#ffffff","#ffffff","#ffffff","#ffffff", "#ffffff"],
                                label: "user",
                                display:false,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Last 24 Hours',
                                color: "white",
                                align: "end"
                            },  
                            legend: {
                                display: false
                            },
                        },
                        scales: {
                            y: {
                                ticks: {
                                    color: "white",
                                    fontSize: 12,
                                    stepSize: 200,
                                },
                                grid: {
                                    display: false,
                                    borderColor: "white",
                                },
                                title: {
                                    text: "Users",
                                    color: "white",
                                    display: true,
                                }
                            },
                            x: {
                                ticks: {
                                    color: "white",
                                    fontSize: 12,
                                    stepSize: 1,
                                },
                                grid: {
                                    display: false,
                                    borderColor: "white",
                                }   
                            }
                        },
                        borderRadius: 8,
                        maxBarThickness: 8
                    }
                });

                new Chart(document.getElementById("travelers"), {
                    type: 'bar',
                    data: {
                        labels:  ['12AM', '2AM', '4AM', '6AM', '8PM', '10AM', '12PM','14PM','16PM', '18PM','20PM','22PM'],
                        datasets: [
                            { 
                                data: dataBuyers,
                                backgroundColor: ["#0F6FC8" ,"#0F6FC8" ,"#0F6FC8","#0F6FC8","#0F6FC8", "#0F6FC8","#0F6FC8", "#0F6FC8","#0F6FC8","#0F6FC8","#0F6FC8", "#0F6FC8"],
                                label: "orders",
                                display: false,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: false,
                            },  
                            legend: {
                                display: false
                            },
                        },
                        scales: {
                            y: {
                                ticks: {
                                    color: "#CFCFCF",
                                    fontSize: 12,
                                    stepSize: 200,
                                },
                                grid: {
                                    display: false,
                                    borderColor: "#CFCFCF",
                                },
                                title: {
                                    text: "Orders",
                                    color: "#CFCFCF",
                                    display: true,
                                }
                            },
                            x: {
                                ticks: {
                                    color: "#CFCFCF",
                                    fontSize: 12,
                                    stepSize: 1,
                                },
                                grid: {
                                    display: false,
                                    borderColor: "#CFCFCF",
                                }   
                            }
                        },
                        borderRadius: 8,
                        maxBarThickness: 8
                    }
                });
            
            }
        </script>
    </body>
</html>