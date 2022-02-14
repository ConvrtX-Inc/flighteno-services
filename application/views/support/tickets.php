<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Dashboard | Flighteno</title>

        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo SURL;?>assets/images/favicon.png">

        <!-- jvectormap -->
        <link href="<?php echo SURL;?>assets/libs/jqvmap/jqvmap.min.css" rel="stylesheet" />

        <!-- emoji picker style -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.css" />

        <!-- App css -->
        <link href="<?php echo SURL;?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SURL;?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SURL;?>assets/css/app.min.css" rel="stylesheet" type="text/css" />      

        <!-- Global admin style -->
        <link href="<?php echo SURL;?>assets/css/styles.css" rel="stylesheet" type="text/css" />

        <style> 

            .filters_style {
                border-radius: 25px;
                border: 2px solid #e9ecef;
                background-color:  #f8f8f8;
            }
            .styleShow{
                border-radius: 25px;
                border: 2px solid #e9ecef;
                background-color:  #F18BB1;
            }
            /* .container{max-width:1170px; margin:auto;} */
            img{ max-width:100%; }
            .messaging { width: 100%}
            .inbox_people {
                background: #f8f8f8 none repeat scroll 0 0;
                float: left;
                overflow: hidden;
                width: 40%; 
                border-right:1px solid #c4c4c4;
            }
            .inbox_msg {
                border: 1px solid #c4c4c4;
                clear: both;
                overflow: hidden;
            }
            .top_spac{ margin: 20px 0 0;}
            .recent_heading {float: left; width:40%;}

            .subject {float: left;}

            .srch_bar {
                display: inline-block;
                text-align: right;
                width: 60%;
            }
            .headind_srch{ padding:10px 29px 10px 20px; overflow:hidden; border-bottom:1px solid #c4c4c4;}

            .recent_heading h4 {
                color: #05728f;
                font-size: 21px;
                margin: auto;
            }
            .srch_bar input{ border:1px solid #cdcdcd; border-width:0 0 1px 0; width:80%; padding:2px 0 4px 6px; background:none;}
            .srch_bar .input-group-addon button {
                background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
                border: medium none;
                padding: 0;
                color: #707070;
                font-size: 18px;
            }
            .srch_bar .input-group-addon { margin: 0 0 0 -27px;}

            .chat_ib h5{ font-size:15px; color:#464646; margin:0 0 8px 0;}
            .chat_ib h5 span{ font-size:13px; float:right;}
            .chat_ib p{ font-size:14px; color:#989898; margin:auto}
            .chat_img {
                float: left;
                width: 11%;
            }
            .chat_ib {
                float: left;
                padding: 0 0 0 15px;
                width: 88%;
            }

            .chat_people{ overflow:hidden; clear:both;}
            .chat_list {
                border-bottom: 1px solid #c4c4c4;
                margin: 0;
                padding: 18px 16px 10px;
            }
            .inbox_chat { height: 90%; overflow-y: scroll;}

            .active_chat{ background:#ebebeb;}

            .incoming_msg_img {
                display: inline-block;
                width: 6%;
            }
            .received_msg {
                display: inline-block;
                padding: 0 0 0 10px;
                vertical-align: top;
                width: auto;
            }
            .received_withd_msg p {
                background: #ebebeb none repeat scroll 0 0;
                border-radius: 3px;
                color: #646464;
                font-size: 14px;
                margin: 0;
                padding: 5px 10px 5px 12px;
                width: auto;
            }
            .time_date {
                color: #747474;
                display: block;
                font-size: 12px;
                margin: 8px 0 0;
            }
            .received_withd_msg { width: auto;}

            .sent_msg_msg { width: auto;}
            .mesgs {
                float: left;
                padding: 30px 15px 0 25px;
                width: 60%;
            }

            .sent_msg p {
                background: #05728f none repeat scroll 0 0;
                border-radius: 3px;
                font-size: 14px;
                margin: 0; color:#fff;
                padding: 5px 10px 5px 12px;
                width:auto;
            }
            .outgoing_msg{ overflow:hidden; margin:26px 0 26px;}
            .sent_msg {
                float: right;
                width: 50%;
            }
            .input_msg_write input {
                background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
                border: medium none;
                color: #4c4c4c;
                font-size: 15px;
                width: 100%;
            }

            .type_msg {
                position: relative;
            }
            .msg_send_btn {
                background: #F18BB1 none repeat scroll 0 0;
                border: medium none;
                border-radius: 50%;
                color: #fff;
                cursor: pointer;
                font-size: 17px;
                height: 33px;
                position: absolute;
                right: 6px;
                top: 15px;
                width: 33px;
            }
            .fileIcon {
                border: medium none;
                color: #625c5c;
                cursor: pointer;
                font-size: 15px;
                height: auto;
                position: absolute;
                left: 8px;
                margin-left : 3%;
                top: 1%;
                width: 52px;
                background-color: #f8f8f8;
            }  
            .imageIcon {
                border: medium none;
                color: #625c5c;
                cursor: pointer;
                font-size: 15px;
                height: auto;
                position: absolute;
                margin-left: 7%;
                top: 1%;
                width: 52px;
                background-color: #f8f8f8;
            } 
            .msg_history {
                height: 740px;
                overflow-y: auto;
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
            table {border: none;}
            .titleStyle{
                font-style: normal;
                font-weight: 800;
                font-size: 40px;
                color: #18243C;
                margin-top: 2%;
                margin-left: 0%;
                margin-bottom: 1%;
            }

            .topnav {
                overflow: hidden;
                background-color: #f8f8f8;
            }

            .topnav a {
                float: left;
                display: block;
                color: black;
                text-align: center;
                padding: 14px 16px;
                text-decoration: none;
                font-size: 17px;
                border-bottom: 3px solid transparent;
            }

            .topnav a:hover {
                border-bottom: 3px solid #E12082;
            }

            .topnav a.active {
                border-bottom: 3px solid #E12082;
                color: black;
            }
            .image-upload>input {
                display: none;
            }

            .positionStyle{
                font-size:30px; 
                margin-left: 6%;
                position: absolute; 
                /* background-color: #eeeeee; */
                height: 100%;
                /* width: 7%; */
                text-align: center;
               
            }

            .positionFile{
                font-size:30px; 
                position: absolute; 
                /* background-color: #eeeeee; */
                height: 100%;
                /* width: 7%; */
                text-align: center;
                /* border-radius: 18px;
                border: 2px solid #e9ecef; */
            }
        </style>
    </head>

    <body>
        <!-- Begin page -->
        <div id="wrapper">
            <!-- Topbar Start -->
            <?php include('includes/topbar.php');?>
            <!-- end Topbar -->
            <?php include('includes/sidebar.php');?>

            <div class="content-page">
                <div class="content">
                    <!-- Start Content-->
                    <div class="container-fluid main-container" style="padding-left: 4%; padding-right: 4%;">
                        <div class="row">
                            <div class="col-12 mt-3 mb-3">
                                <h4 class="page-title styleHeader titleStyle">Messages</h4>
                            </div> 
                        </div>
                        <div class="row mb-5 tickets-container">
                            <div class="col-lg-5 col-xxl-4">
                                <div class="tickets-list d-flex flex-column">
                                    <div class="tickets-list-tabs">
                                         <a href="<?php echo base_url();?>admin/Support/buyer/tickets" class="<?=($profile_status === 'buyer')? 'active':''?>">Buyers</a>
                                         <a href="<?php echo base_url();?>admin/Support/traveler/tickets" class="<?=($profile_status === 'traveler')? 'active':''?>">Travelers</a>
                                    </div>

                                    <div class="tickets-list-main">
                                        <?php
                                        foreach ($tickets as $ticket) {
                                            if (empty($ticket['ticketUserData'][0]['profile_image'])) {
                                                $imageSource = "https://ptetutorials.com/images/user-profile.png";
                                            } else {
                                                $imageSource = $ticket['ticketUserData'][0]['profile_image'];
                                            }

                                            if (!empty($ticket['created_date'])) {
                                                $time_zone = date_default_timezone_get();
                                                $date = $ticket['created_date']->toDateTime()->format("Y-m-d H:i:s");
                                                $last_time_ago = time_elapsed_string($date, $time_zone);
                                            } else {
                                                $last_time_ago = '---';
                                            }

                                            $unreadMessageCount = $ticket["unreadMessageCount"][0]['count'];
                                            if (empty($unreadMessageCount) || is_null($unreadMessageCount)) {
                                                $unreadMessageCount = 0;
                                            }
                                        ?>

                                        <div class="tickets-list-user d-flex justify-content-start align-items-center" data-id="<?=(string)$ticket['_id']?>" data-user-id="<?=(string)$ticket['admin_id']?>">
                                            <div class="tickets-list-user-left">
                                                <img src="<?=$imageSource?>" class="this-image rounded-circle bx-shadow-lg">
                                            </div>

                                            <div class="tickets-list-user-middle flex-fill">
                                                <h5 class="this-user"><?=$ticket['ticketUserData'][0]['full_name']?></h5>
                                                <p class="this-preview"><?=$ticket['subject']?></p>
                                            </div>

                                            <div class="tickets-list-user-right align-self-start">
                                                <h6 class="this-time mt-0"><?=$last_time_ago?></h6>
                                                <?php
                                                if ($unreadMessageCount != 0) { ?>
                                                    <!-- <span class="this-unread"><?=$unreadMessageCount?></span> -->
                                                <?php
                                                } ?>
                                            </div>
                                        </div>

                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-7 col-xxl-8">
                                <div class="tickets-messages d-flex flex-column loading">
                                    <div class="tickets-messages-info">
                                        <div class="this-top d-flex align-items-center">
                                            <div class="user-info d-flex flex-fill align-items-center">
                                                <div class="this-left">
                                                    <img src="https://ptetutorials.com/images/user-profile.png" class="this-image rounded-circle bx-shadow-lg">
                                                </div>
                                                <div class="this-right">
                                                    <h3 class="this-user">Margarette Smith</h3>
                                                    <p class="this-email underlined m-0">margarette@gmail.com</p>
                                                </div>
                                            </div>

                                            <div class="order-info w-25">
                                                <p class="m-0"><span class="color-pink text-small">Order No.</span> <span class="this-order">123456789</span></p>
                                            </div>
                                        </div>

                                        <div class="this-bottom">
                                            <p class="m-0"><span class="color-pink text-small">Subject:</span> <span class="this-subject">Shipping Complaint</span></p>
                                        </div>
                                    </div>

                                    <div class="tickets-messages-history flex-fill"></div>

                                    <div class="tickets-messages-textarea align-self-end">
                                        <div class="tickets-messages-textarea-container d-flex align-items-center">
                                            <form method="POST" enctype="multipart/form-data" action="<?php echo base_url();?>index.php/admin/Support/fileUpload">
                                                <div class="this-icon">
                                                    <label class="m-0" for="fileUploaded"> 
                                                        <img src="<?php echo SURL;?>assets/images/upload-file.png" >
                                                    </label>     
                                                    <input class="d-none" id="fileUploaded" type="file" accept="application/doc|application/csv|application/ppt|application/docx|application/txt|application/pdf" onchange="fileUpload(this)" />
                                                </div>
                                            </form>
                                            
                                            <div class="this-icon">
                                                <a href="">
                                                    <img src="<?php echo SURL;?>assets/images/smiley.png" >
                                                </a>
                                            </div>

                                            <form id="reg" method="POST" enctype="multipart/form-data" action="<?php echo base_url();?>index.php/admin/Support/imageSendUpload">
                                                <div class="this-icon">
                                                    <label class="m-0" for="upload-image"> 
                                                        <img src="<?php echo SURL;?>assets/images/upload-img.png" >
                                                    </label> 
                                                    <input class="d-none" id="upload-image" type="file" onchange="imageUpload(this)"  accept="application/gif|application/jpeg|application/png|application/jpg" />
                                                </div>
                                            </form>
                                                
                                            <textarea type="text" class="form-control flex-fill w-auto" placeholder="Write your message here" id="text-msg"></textarea>
                                            <a href="#" id="btn-send">
                                                <img class="position-absolute" src="<?php echo SURL;?>assets/images/btn-send.png" >
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--
                            <p class="mt-5 mb-5">end ....</p>

                            <div class="messaging ">
                                <div class="inbox_msg filters_style">

                                    <div class="headind_srch">
                                        <?php 
                                            $tabClass =  $this->session->userdata('tab');
                                            if($tabClass == 'traveler'){

                                                $buyerClass="";
                                                $travelerClass="active";
                                            }else{

                                                $buyerClass="active";
                                                $travelerClass="";
                                            }
                                        ?>
                                            
                                        <div class="topnav" style="width:75%">
                                            <a class="<?php echo $buyerClass;?>" style="width:25%"  href="<?php echo base_url();?>index.php/admin/Support/tickets?profile=buyer">Buyers</a>
                                            <a class="<?php echo $travelerClass;?>" style="width:25%" href="<?php echo base_url();?>index.php/admin/Support/tickets?profile=traveler">Travelers</a>
                                        </div>
                                    </div>
                                    <div class="inbox_people" style="height: 840px;overflow-y: auto;">
                                        <div class="inbox_chat">

                                            <?php foreach($tickets as $ticket) { ?>
                                                <?php
                                                    if( empty($ticket['ticketUserData'][0]['profile_image']) ){

                                                        $imageSource = "https://ptetutorials.com/images/user-profile.png";
                                                    }else{

                                                        $imageSource = $ticket['ticketUserData'][0]['profile_image'];
                                                    }


                                                    if(!empty($ticket['created_date'])){
                                                        
                                                        $time_zone = date_default_timezone_get();
                                                        $date = $ticket['created_date']->toDateTime()->format("Y-m-d H:i:s");
                                                        $last_time_ago = time_elapsed_string($date , $time_zone);
                                
                                                    }else{
                                                        $last_time_ago = '---';
                                                    }
                                                ?>
                                                <table width="100%" style="cursor:pointer">
                                                    <tr class="click" width="100%">
                                                        <td>
                                                            <div class="chat_list"> 
                                                                <input type="hidden" name="id" value="<?php echo (string)$ticket['_id']; ?>" />
                                                                <div class="chat_people">
                                                                    <div class="chat_img"> <img src="<?php echo $imageSource; ?>" alt="" class="rounded-circle images avatar-sm bx-shadow-lg image2"> </div>
                                                                    <div class="chat_ib">
                                                                        <h5><?php echo $ticket['ticketUserData'][0]['full_name']; ?> <span class="chat_date"><?php echo $last_time_ago; ?></span></h5>
                                                                        <p><?php echo $ticket['subject'];?></p>
                    
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            <?php } ?>
                                        </div>
                                    <div class="pagination" ><?php  echo $this->pagination->create_links(); ?></div>
                                </div>
                                <div class="mesgs">
                                    <div class="" id= "detailOrder">

                                    </div>
                                    
                                    <div class="msg_history" id="messagesData">
                                        
                                    </div>

                                    <div class="type_msg" id ="messageReply">
                                        <div class="input_msg_write image-upload" style="display: flex">

                                            <form method="POST" enctype="multipart/form-data" action="<?php echo base_url();?>index.php/admin/Support/fileUpload">
                                                <div class="image-upload">
                                                    <label for="fileUploaded" class="positionFile" > 
                                                        <i class="fas fa-paperclip"></i>
                                                    </label>     
                                                    <input class="fileIcon" id="fileUploaded" type="file" accept="application/doc|application/csv|application/ppt|application/docx|application/txt|application/pdf" onchange="fileUpload(this)" />
                                                </div>
                                            </form>

                                            <form id="reg" method="POST" enctype="multipart/form-data" action="<?php echo base_url();?>index.php/admin/Support/imageSendUpload">
                                                <div class="image-upload">
                                                    <label for="upload-image"class="positionStyle" > 
                                                        <i class="fas fa-image"></i>
                                                    </label> 
                                                    <input class="imageIcon" id="upload-image" type="file" onchange="imageUpload(this)"  accept="application/gif|application/jpeg|application/png|application/jpg" />
                                                </div>
                                            </form>
                                                
                                            <textarea type="text" style="margin-left:12%" class="write_msg form-control textarea-control filters_style" placeholder="Type your message..." id="sendMessage"></textarea>
                                            <button class="msg_send_btn" type="button"> <i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>         
                            -->                           
                        </div>
                    </div> <!-- container -->
                </div> <!-- content -->

                <!-- Footer Start -->
                <?php include('includes/footer.php');?>
                <!-- end Footer -->
            </div>
        </div>

        <script src="<?php echo SURL;?>assets/js/vendor.min.js"></script>
        <!-- KNOB JS -->
        <script src="<?php echo SURL;?>assets/libs/jquery-knob/jquery.knob.min.js"></script>
        <!-- Chart JS -->
        <script src="<?php echo SURL;?>assets/libs/chart-js/Chart.bundle.min.js"></script>
        <!-- Jvector map -->
        <script src="<?php echo SURL;?>assets/libs/jqvmap/jquery.vmap.min.js"></script>
        <script src="<?php echo SURL;?>assets/libs/jqvmap/jquery.vmap.usa.js"></script>
       
        <script src="<?php echo SURL;?>assets/js/pages/dashboard.init.js"></script>
        <!-- App js -->
        <script src="<?php echo SURL;?>assets/js/app.min.js"></script>

  
        <!-- App js -->
        <script src="<?php echo SURL;?>assets/js/app.min.js"></script>
        <script>
            $(document).ready(function(){
                $('.click').click(function(){
                    $("tr").removeClass('active_chat');
                    $(this).closest("tr").addClass('active_chat');

                    var currentRow =   $(this).closest("tr"); 
                    var ticketId   =   currentRow.find("input[type='hidden']").val();
                    $.ajax({
                        'url': '<?php echo base_url();?>index.php/admin/Support/getMessages',
                        'type': 'POST',
                        'data': {ticketId : ticketId},
                        'success': function (response) {
                            var data =  JSON.parse(response);
                            console.log(data);
                            // return;
                            
                            var orderDetails = '';
                            var htmlDesign = '';
                            var admin_id = data[0]['admin_id'];
                            var ticket_id = data[0]['_id'].toString();
                            var order_number = data[0]['order_number'];
                            var subject = data[0]['subject'];
                            var firstMessage = data[0]['message'];

                            var created_date = data[0]['created_date'];

                            var videos = data[0]['video'];
                            var images = data[0]['image'];

                            var fullName     =  data[0]['profileData'][0]['full_name'];
                            var emailAddress =  data[0]['profileData'][0]['email_address'];
                            var image;
                          
                            image = (data[0]['profileData'][0]['profile_image']) ? data[0]['profileData'][0]['profile_image'] : 'https://ptetutorials.com/images/user-profile.png';
                            
                            orderDetails += '<div class="inbox_chat">';
                            orderDetails += '<div class="chat_list">';
                            orderDetails += ' <div class="chat_people">';
                            orderDetails += '<div class="chat_img"> <img src="'+image+'" alt="" class="rounded-circle images avatar-sm bx-shadow-lg image2"> </div>';
                            orderDetails += '<div class="chat_ib">';
                            orderDetails += '<h5>'+fullName+'<span class="chat_date" style="color:#E1467D">Order No.<span style="color:#898A8D; margin-left:7px">'+order_number+'</span></span></h5>';
                            orderDetails += '<p>'+emailAddress+'</p>';
                            orderDetails += '</div>';
                            orderDetails += '</div>';
                            orderDetails += '</div>';
                            orderDetails += '<div class="subject" style="width : 100%">';
                            orderDetails += '<h5 style="color:#E1467D"> Subject:<span style="color:#898A8D; margin-left: 4%">'+subject+'</span></h5>'; 
                            orderDetails += '</div>';

                            orderDetails += '<input type="hidden" id="ticketId" value="' +ticket_id+ '" />';
                            $('#detailOrder').html(orderDetails);

                            if(videos != ''){

                                htmlDesign += '<div class="incoming_msg">';
                                htmlDesign += '<div class="received_msg">';
                                htmlDesign += '<div class="received_withd_msg">';
                                htmlDesign +='<video controls><source src="'+ videos['0'] +'" ></video>';
                                htmlDesign += '</div>';
                                htmlDesign += '</div>';
                                htmlDesign += '</div>';
                            }
                            if(images != ''){

                                htmlDesign += '<div class="incoming_msg" style="margin-top:2%">';
                                htmlDesign += '<div class="received_msg">';
                                htmlDesign += '<div class="received_withd_msg">';
                                htmlDesign += '<img src="'+images[0]+'" alt="user-image" class="img-rounded" width="125px" height="125px"/>'
                                htmlDesign += '</div>';
                                htmlDesign += '</div>';
                                htmlDesign += '</div>';
                            } 
                            htmlDesign += '<div class="incoming_msg" style="margin-top:2%">';
                            htmlDesign += '<div class="incoming_msg_img">';
                            htmlDesign += '<img src="'+image+'" class="rounded-circle images avatar-sm bx-shadow-lg image2" alt="">';
                            htmlDesign += '</div>';
                            htmlDesign += '<div class="received_msg">';
                            htmlDesign += '<div class="received_withd_msg">';
                            htmlDesign += '<p style="border-radius: 25px; border: 2px solid #e9ecef; background-color:#ebebeb;">'+ firstMessage +'</p>';
                            htmlDesign += '<span class="time_date">'+created_date+'</span>';
                            htmlDesign += '</div>';
                            htmlDesign += '</div>';
                            htmlDesign += '</div>';

                            for(i= 0; i < data[0]['messages'].length; i++){

                                if(admin_id != data[0]['messages'][i]['admin_id']){
                                    htmlDesign += '<div class="outgoing_msg">';
                                    htmlDesign += '<div class="sent_msg">';    

                                    if(data[0]['messages'][i]['image'] &&  data[0]['messages'][i]['image'] !== isNaN && data[0]['messages'][i]['image'] !== null ){

                                        htmlDesign += '<a href="'+data[0]['messages'][i]['image']+'" download><img src="'+data[0]['messages'][i]['image']+'" width="100" height="104">';
                                        htmlDesign += '</a>';
                                    }else if(data[0]['messages'][i]['file'] && data[0]['messages'][i]['file'] !== '' ){

                                        htmlDesign += '<a href="'+ data[0]['messages'][i]['file']+'" download> <i class="fas fa-file-download" style="font-size:55px"></i></a>';
                                        htmlDesign += '</a>';
                                    }else{

                                        htmlDesign += '<p style="border-radius: 25px; border: 2px solid #e9ecef; background-color:#F18BB1;">'+data[0]['messages'][i]['message']+'</p>';
                                    }

                                    htmlDesign += '<span class="time_date">'+ data[0]['messages'][i]['created_date'] +'</span>';
                                    htmlDesign += '</div>';
                                    htmlDesign += '</div>';
                                }else{
                                    var profileImage = (data[0]['messages'][i]['userData'][0]['profile_image']) ? data[0]['messages'][i]['userData'][0]['profile_image'] : 'https://ptetutorials.com/images/user-profile.png';
                                    htmlDesign += '<div class="incoming_msg">';
                                    htmlDesign += '<div class="incoming_msg_img">';
                                    htmlDesign += '<img src="'+profileImage+'" class="rounded-circle images avatar-sm bx-shadow-lg image2" alt="">';
                                    htmlDesign += '</div>';
                                    htmlDesign += '<div class="received_msg">';
                                    htmlDesign += '<div class="received_withd_msg">'; 
                                    htmlDesign += '<p style="border-radius: 25px; border: 2px solid #e9ecef; background-color:#ebebeb;">'+ data[0]['messages'][i]['message'] +'</p>';
                                    htmlDesign += '<span class="time_date">'+ data[0]['messages'][i]['created_date']+'</span>';
                                    htmlDesign += '</div>';
                                    htmlDesign += '</div>';
                                    htmlDesign += '</div>';
                                }
                            }//end loop
                            $('#messagesData').html(htmlDesign);

                            // var ticketReply = '<div class="input_msg_write" style="display: flex">';
                            // ticketReply +='<button class="fileIcon" type="button"><i class="fas fa-paperclip" aria-hidden="true"></i></button>';
                            // ticketReply +='<button class="emojiIcon" type="button"> <i class="far fa-frown openemoji" aria-hidden="true"></i></button>';
                            // ticketReply +='<textarea type="text" class="write_msg form-control textarea-control filters_style" placeholder="type Message" id="sendMessage"></textarea>';
                            // ticketReply +='<button class="msg_send_btn" type="button"> <i class="fa fa-paper-plane" aria-hidden="true"></i></button>';
                            // ticketReply +='</div>'; 
                            // $('#messageReply').html(ticketReply);
                        }
                    });
                })
            });
        </script>
        <!-- Datatable js -->
        <script src="<?php echo SURL;?>assets/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo SURL;?>assets/libs/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="<?php echo SURL;?>assets/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="<?php echo SURL;?>assets/libs/datatables/responsive.bootstrap4.min.js"></script>
        <!-- Dashboard Init JS -->
        <script src="<?php echo SURL;?>assets/js/pages/dashboard.init.js"></script>
        <!-- App js -->
        <script src="<?php echo SURL;?>assets/js/app.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>

        <script>
            // $('#sendMessage').emojioneArea({
            //     pickerPosition: "top"
            // });

            // Active ticket
            let activeTicketId = "";

            <?php $userArray = $this->session->userdata('user_data'); ?>
            const userSession = <?=json_encode($userArray)?>;
          
            // Admin profile placeholder
            if (!userSession["profile_image"])
                userSession["profile_image"] = "https://png.pngtree.com/png-clipart/20190924/original/pngtree-user-vector-avatar-png-image_4830521.jpg";

            $(document).ready(function(){

                // $("#sendMessage").removeClass("form-control");

                // Initialize message data containers
                const userImageContainer = $(".user-info .this-image");
                const userNameContainer = $(".user-info .this-user");
                const userEmailContainer = $(".user-info .this-email");
                const orderNumberContainer = $(".order-info .this-order");
                const subjectContainer = $(".this-subject");
                const ticketMessagesContainer = $(".tickets-messages");
                const ticketMessagesHistoryContainer = $(".tickets-messages-history");
                const textMessageContainer = $("#text-msg");

                // Loading selected ticket data
                $(".tickets-list").on("click", ".tickets-list-user:not(.active)", function() {
                    const activeTicket = $(this);
                    activeTicketId = activeTicket.data("id");
                    const userId = activeTicket.data("user-id");
                    const profileStatus = "<?=$profile_status?>";
                    const newUrl = "<?=SURL?>admin/Support/" + profileStatus + "/tickets/" + userId + "/" + activeTicketId;

                    // replace url
                    window.history.replaceState(null, null, newUrl);

                    ticketMessagesContainer.addClass("loading");
                    $(".tickets-list-user.active").removeClass("active");
                    activeTicket.addClass("active");

                    // load active ticket history
                    $.ajax({
                        'url': '<?=base_url()?>index.php/admin/Support/getMessages',
                        'type': 'POST',
                        'data': { ticketId : activeTicketId },
                        'success': function (response) {
                            const data =  JSON.parse(response)[0];
                            const profileData = data["profileData"][0];
                            console.log(data);

                            const profileImage = (profileData["profile_image"])? profileData["profile_image"] : "https://ptetutorials.com/images/user-profile.png";

                            // display data
                            userImageContainer.attr("src", profileImage);
                            userNameContainer.html(profileData["full_name"]);
                            userEmailContainer.html(profileData["email_address"]);
                            orderNumberContainer.html(data["order_number"]);
                            subjectContainer.html(data["subject"]);
                            ticketMessagesHistoryContainer.html(data["messages"]);

                            // scroll to latest message
                            scrollToLatest(ticketMessagesHistoryContainer);
                            ticketMessagesContainer.removeClass("loading");
                        }
                    });
                });

                const userID = "<?=$id_user?>";
                const ticketID = "<?=$id_ticket?>";
                if (userID == 0 && ticketID == 0) {
                    // Select the first ticket by default
                    $(".tickets-list-user").first().click();
                } else {
                    // Select the active ticket
                    const activeTicket = $(".tickets-list-user[data-id='" + ticketID + "']");
                    activeTicket.click();
                }

                // Sending message function
                $("#btn-send").on("click", function(e) {
                    e.preventDefault();

                    const textMessage = textMessageContainer.val();
                    const profileImage = userSession["profile_image"];

                    // Text message error validation
                    if (!textMessage.trim()) {
                        alert("Please input your message.");
                        textMessageContainer.val("");
                        textMessageContainer.focus();
                        return;
                    }

                    console.log("sending message to " + activeTicketId + "...")

                    $.ajax({
                        'url': '<?php echo base_url();?>index.php/admin/Support/sendMessage',
                        'type': 'POST',
                        'data': { ticketId : activeTicketId, sendMessage : textMessage, profileImage: profileImage },
                        'success': function (response) {
                            ticketMessagesHistoryContainer.append(response);
                            scrollToLatest(ticketMessagesHistoryContainer);
                            textMessageContainer.val("");
                        }
                    });
                });
            });

            // Sending image function
            function imageUpload(theForm) { 
                const ticketMessagesHistoryContainer = $(".tickets-messages-history");
                const ticketId = activeTicketId;
                const files = $("#upload-image").get(0).files;
                let data = new FormData();
                
                // Prepare data
                if (files.length > 0)
                    data.append("image", files[0]);
                data.append("ticketId", ticketId);
                data.append("profileImage", userSession["profile_image"]);

                console.log("sending message to " + activeTicketId + "...")
                
                $.ajax({
                    url: '<?php echo base_url();?>index.php/admin/Support/imageSendUpload',
                    type: "POST",
                    processData: false,
                    contentType: false,
                    data: data,
                    success: function (response) {
                        // const res = JSON.parse(response)["upload_data"];
                        // console.log(res) 
                        console.log(response)
                        
                        $('#upload-image').val('');
                        ticketMessagesHistoryContainer.append(response);
                        scrollToLatest(ticketMessagesHistoryContainer);
                    },
                    error: function (er) {
                        console.log(er)
                        if (er.status == 415)
                            alert("The filetype you are attempting to upload is not allowed.");
                    }
                });
            }

            // Sending file function
            function fileUpload(form) {
                const ticketMessagesHistoryContainer = $(".tickets-messages-history");
                const ticketId = activeTicketId;
                const files = $("#fileUploaded").get(0).files;
                let data = new FormData();

                // Prepare data
                if (files.length > 0)
                    data.append("file", files[0]);
                data.append("ticketId", ticketId);
                data.append("profileImage", userSession["profile_image"]);

                console.log("sending file to " + activeTicketId + "...")

                $.ajax({
                    url: '<?php echo base_url();?>index.php/admin/Support/fileSendUpload',
                    type: "POST",
                    processData: false,
                    contentType: false,
                    data: data,
                    success: function (response) {
                        // const res = JSON.parse(response)["upload_data"];
                        // console.log(res) 
                        console.log(response)
                        
                        $('#fileUploaded').val('');
                        ticketMessagesHistoryContainer.append(response);
                        scrollToLatest(ticketMessagesHistoryContainer);
                    },
                    error: function (er) {
                        console.log(er)
                        if (er.status == 415)
                            alert("The filetype you are attempting to upload is not allowed.");
                    }
                });        
            }

            function scrollToLatest(container) {
                setTimeout(() => {
                    container.scrollTop(container.prop("scrollHeight") + 1000);
                }, 50);
            }
        </script>
    </body>
</html>