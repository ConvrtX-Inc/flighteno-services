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
                        </div>
                    </div> <!-- container -->
                </div> <!-- content -->

                <!-- Footer Start -->
                <?php include('includes/footer.php');?>
                <!-- end Footer -->
            </div>
        </div>

        <script src="<?php echo SURL;?>assets/js/vendor.min.js"></script>

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
                            ticketMessagesContainer.removeClass("loading");
                            scrollToLatest(ticketMessagesHistoryContainer);
                        }
                    });
                });

                const userID = "<?=$id_user?>";
                const ticketID = "<?=$id_ticket?>";
                if (userID == 0 && ticketID == 0) {
                    // Select the first ticket by default
                    // $(".tickets-list-user").first().click();
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
                }, 60);
            }
        </script>
    </body>
</html>