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

        <!-- App css -->
        <link href="<?php echo SURL;?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SURL;?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SURL;?>assets/css/app.min.css" rel="stylesheet" type="text/css" />      

        <!-- Global admin style -->
        <link href="<?php echo SURL;?>assets/css/styles.css" rel="stylesheet" type="text/css" />

        <style>
            .titleStyle {
                color: #18243C;
                font-size: 40px;
            }

            .filter-search {
                float: right;
                width: 350px;
            }

            .filter-search #filter_search {
                max-width: 350px;

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
                        <div class="row mt-3 mb-3">
                            <div class="col-lg-5 col-xxl-4">
                                <h4 class="page-title styleHeader titleStyle m-0">Messages</h4>
                            </div>

                            <div class="col-lg-7 col-xxl-8 filter-row">
                                <?php
                                    // initialize search form url
                                    $search_url = base_url().'index.php/admin';
                                    if ($profile_status == 'buyer') {
                                        $search_url .= '/Support/buyer/tickets';
                                    } elseif ($profile_status == 'traveler') {
                                        $search_url .= '/Support/traveler/tickets';
                                    }
                                ?>
                                <form method="POST" action="<?php echo $search_url;?>">
                                    <div class="inner-addon left-addon filter-search">
                                        <img src="<?php echo SURL.'assets/images/icon-search.png';?>" alt="" class="image-icon">
                                        <input type="text" id ="filter_search" class="form-control filters_style" placeholder="Search"  name="searchValue" value="<?=isset($_POST['searchValue']) ? $_POST['searchValue'] : ""  ?>"/>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row mb-5 tickets-container">
                            <div class="col-lg-5 col-xxl-4">
                                <div class="tickets-list d-flex flex-column">
                                    <div class="tickets-list-tabs">
                                         <a href="<?php echo base_url();?>index.php/admin/Support/buyer/tickets" class="<?=($profile_status === 'buyer')? 'active':''?>">Buyers</a>
                                         <a href="<?php echo base_url();?>index.php/admin/Support/traveler/tickets" class="<?=($profile_status === 'traveler')? 'active':''?>">Travelers</a>
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
                                                    <input class="d-none" id="fileUploaded" type="file" accept="application/doc|application/csv|application/ppt|application/docx|application/txt|application/pdf" onchange="fileUploadOnChange()" />
                                                </div>
                                            </form>
                                            
                                            <div class="this-icon">
                                                <div class="emoji-tooltip" role="tooltip">
                                                    <emoji-picker class="light"></emoji-picker>
                                                </div>
                                                <a class="add-emoji" href="#">
                                                    <img src="<?php echo SURL;?>assets/images/smiley.png" >
                                                </a>
                                            </div>

                                            <form id="reg" method="POST" enctype="multipart/form-data" action="<?php echo base_url();?>index.php/admin/Support/imageSendUpload">
                                                <div class="this-icon">
                                                    <label class="m-0" for="upload-image"> 
                                                        <img src="<?php echo SURL;?>assets/images/upload-img.png" >
                                                    </label> 
                                                    <input class="d-none" id="upload-image" type="file" onchange="imageUploadOnChange()"  accept="application/gif|application/jpeg|application/png|application/jpg" />
                                                </div>
                                            </form>
                                            
                                            <div class="flex-fill w-auto">
                                                <textarea type="text" class="form-control" placeholder="Write your message here" id="text-msg"></textarea>
                                                <p class="file-label m-0 ml-1 mr-1"><span class="this-text mr-1"></span><a class="file-close" href="#"><i class="mdi mdi-close-circle"></i></a></p>
                                            </div>
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

        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <!-- <script src="https://unpkg.com/@popperjs/core@2"></script> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js" integrity="sha512-2rNj2KJ+D8s1ceNasTIex6z4HWyOnEYLVC3FigGOmyQCZc2eBXKgOxQmo3oKLHyfcj53uz4QMsRCWNbLd32Q1g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

        <!-- sweetalert2 -->
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script type="module">
            import 'https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js'
            import insertText from 'https://cdn.jsdelivr.net/npm/insert-text-at-cursor@0.3.0/index.js'
            const tooltip = document.querySelector('.emoji-tooltip')

            document.querySelector('emoji-picker').addEventListener('emoji-click', e => {
                insertText(document.querySelector('#text-msg'), e.detail.unicode);
                tooltip.classList.toggle('shown');
            });
        </script>

        <!-- Firebase Setup -->
        <script type="module">
            // Import the functions you need from the SDKs you need
            import { initializeApp } from "https://www.gstatic.com/firebasejs/9.6.10/firebase-app.js";
            import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.6.10/firebase-analytics.js";
            import { getStorage, ref, uploadBytes, getDownloadURL } from "https://www.gstatic.com/firebasejs/9.6.10/firebase-storage.js";
            // TODO: Add SDKs for Firebase products that you want to use
            // https://firebase.google.com/docs/web/setup#available-libraries

            // Your web app's Firebase configuration
            // For Firebase JS SDK v7.20.0 and later, measurementId is optional
            const firebaseConfig = {
                apiKey: "AIzaSyCmN7lbq-oAVzfC2NAtrufFRajse7x5GZk",
                authDomain: "flighteno-convrtx.firebaseapp.com",
                projectId: "flighteno-convrtx",
                storageBucket: "flighteno-convrtx.appspot.com",
                messagingSenderId: "451344766522",
                appId: "1:451344766522:web:a37807be2143eadf150703",
                measurementId: "G-GX8L0EPGTT"
            };

            // Initialize Firebase
            const app = initializeApp(firebaseConfig);
            const analytics = getAnalytics(app);

            function firebaseUpload(file) {
                return new Promise(resolve => {
                    console.log("uploading file on firebase ...")
                    const storage = getStorage();
                    const storageRef = ref(storage, "admin/support/" + file.name);

                    // Create file metadata including the content type
                    const metadata = { contentType: file.type };

                    // Upload the file and metadata
                    const uploadTask = uploadBytes(storageRef, file, metadata);
                    uploadTask.then(() => {
                        console.log("getting download url ...")
                        getDownloadURL(storageRef).then(url => {
                            resolve(url);
                        });
                    });
                });
            }

            window.firebaseUpload = firebaseUpload;
        </script>

        <script type="text/javascript">
            // Initialize message data containers
            const userImageContainer = $(".user-info .this-image");
            const userNameContainer = $(".user-info .this-user");
            const userEmailContainer = $(".user-info .this-email");
            const orderNumberContainer = $(".order-info .this-order");
            const subjectContainer = $(".this-subject");
            const ticketMessagesContainer = $(".tickets-messages");
            const ticketMessagesHistoryContainer = $(".tickets-messages-history");
            const textMessageContainer = $("#text-msg");
            const fileLabelContainer = $(".file-label");
            const fileLabelTextContainer = $(".file-label .this-text");

            // Active ticket
            let activeTicketId = "";
            let activeFile;
            let isUploadingImage = false;
            let isUploadingFile = false;

            <?php $userArray = $this->session->userdata('user_data'); ?>
            const userSession = <?=json_encode($userArray)?>;

            fileLabelContainer.hide();
          
            // Admin profile placeholder
            if (!userSession["profile_image"])
                userSession["profile_image"] = "https://png.pngtree.com/png-clipart/20190924/original/pngtree-user-vector-avatar-png-image_4830521.jpg";

            $(document).ready(function(){
                const emojiButton = $(".add-emoji");
                const emojiTooltip = $(".emoji-tooltip");
                Popper.createPopper(emojiButton, emojiTooltip);

                $(".add-emoji").click(function(e) {
                    e.preventDefault();
                    emojiTooltip.toggleClass('shown');
                });

                // Loading selected ticket data
                $(".tickets-list").on("click", ".tickets-list-user:not(.active)", function() {
                    const activeTicket = $(this);
                    activeTicketId = activeTicket.data("id");
                    const userId = activeTicket.data("user-id");
                    const profileStatus = "<?=$profile_status?>";
                    // const newUrl = "<?=SURL?>admin/Support/" + profileStatus + "/tickets/" + userId + "/" + activeTicketId;

                    // replace url
                    // window.history.replaceState(null, null, newUrl);

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
                            resetMessageArea();
                        }
                    });
                });

                const userID = "<?=$id_user?>";
                const ticketID = "<?=$id_ticket?>";
                if (userID == 0 && ticketID == 0) {
                    // Select the first ticket by default
                    // $(".tickets-list-user").first().click();

                    // do nothing ...
                } else {
                    // Select the active ticket
                    const activeTicket = $(".tickets-list-user[data-id='" + ticketID + "']");
                    activeTicket.click();
                }

                // Sending message function
                $("#btn-send").on("click", function(e) {
                    e.preventDefault();

                    if (!isUploadingImage && !isUploadingFile) {
                        const textMessage = textMessageContainer.val();
                        const profileImage = userSession["profile_image"];

                        // Text message error validation
                        if (!textMessage.trim()) {
                            alert("Please input your message.");
                            resetMessageArea();
                            return;
                        }

                        toggleClassOnSend(1);
                        $.ajax({
                            'url': '<?php echo base_url();?>index.php/admin/Support/sendMessage',
                            'type': 'POST',
                            'data': { ticketId : activeTicketId, sendMessage : textMessage, profileImage: profileImage },
                            'success': function (response) {
                                ticketMessagesHistoryContainer.append(response);
                                scrollToLatest(ticketMessagesHistoryContainer);
                                toggleClassOnSend(1);
                                resetMessageArea();
                            }
                        });
                    } else {
                        toggleClassOnSend(2);
                        if (isUploadingImage) {
                            imageUpload();
                        } else if (isUploadingFile) {
                            fileUpload();
                        }
                    }
                });

                $(".file-close").on("click", function(e) {
                    e.preventDefault();
                    resetMessageArea();
                });

                $(".tickets-messages-history").on("click", ".link-download", function(e) {
                    e.preventDefault();
                    const downloadURL = $(this).attr("href");
                    // downloadImage(downloadURL);
                    downloadResource(downloadURL);
                });
            });

            function forceDownload(blob, filename) {
                var a = document.createElement('a');
                a.download = filename;
                a.href = blob;
                // For Firefox https://stackoverflow.com/a/32226068
                document.body.appendChild(a);
                a.click();
                a.remove();
            }

            // Current blob size limit is around 500MB for browsers
            function downloadResource(url, filename) {
                console.log("location origin", location.origin)
                if (!filename) filename = url.split('\\').pop().split('/').pop();
                fetch(url, {
                    headers: new Headers({
                        'Origin': location.origin
                    }),
                    mode: 'cors'
                })
                .then(response => response.blob())
                .then(blob => {
                let blobUrl = window.URL.createObjectURL(blob);
                forceDownload(blobUrl, filename);
                })
                .catch(e => console.error(e));
            }

            // Preparing image file
            function imageUploadOnChange() { 
                const ticketId = activeTicketId;
                const files = $("#upload-image").get(0).files;

                let isFileValid = validateFile(2, files[0]);
                if (!isFileValid) {
                    return;
                }

                let data = new FormData();
                let newFileName = generateNewFileName();
                
                // Prepare data
                if (files.length > 0)
                    data.append("image", files[0], newFileName);
                data.append("ticketId", ticketId);
                data.append("profileImage", userSession["profile_image"]);

                activeFile = data;
                isUploadingImage = true;
                isUploadingFile = false;
                showFileLabel(files[0]["name"]);

                return;
            }

            async function imageUpload() {
                const imageData = activeFile.get("image")
                const downloadURL = await window.firebaseUpload(imageData);
                activeFile.append("imagePath", downloadURL);
                
                console.log("saving image ...")
                $.ajax({
                    url: '<?php echo base_url();?>index.php/admin/Support/imageSendUpload',
                    type: "POST",
                    processData: false,
                    contentType: false,
                    data: activeFile,
                    success: function (response) {
                        $('#upload-image').val('');
                        ticketMessagesHistoryContainer.append(response);
                        scrollToLatest(ticketMessagesHistoryContainer);
                        toggleClassOnSend(2);
                        resetMessageArea();
                    },
                    error: function (er) {
                        let errMsg = "";

                        if (er.status == 415)
                            errMsg = "Invalid image file. Please upload JPG, PNG or GIF.";
                        else if (er.status == 413)
                            errMsg = "File size must not exceed 6MB.";
                        else
                            errMsg = "Something went wrong. Please try again later.";
                        
                        Swal.fire({
                            icon: "error",
                            title: "Oops!",
                            text: errMsg,
                            confirmButtonColor: "#f33636",
                            confirmButtonText: "Close"
                        });

                        toggleClassOnSend(2);
                    }
                });
            }

            // Preparing file
            function fileUploadOnChange() {
                const ticketId = activeTicketId;
                const files = $("#fileUploaded").get(0).files;

                let isFileValid = validateFile(1, files[0]);
                if (!isFileValid) {
                    return;
                }

                let data = new FormData();
                let oldFileName = files[0]["name"];
                let extension = oldFileName.split(".").pop();
                let newFileName = generateNewFileName() + "." + extension;

                // Prepare data
                if (files.length > 0)
                    data.append("file", files[0], newFileName);
                data.append("ticketId", ticketId);
                data.append("profileImage", userSession["profile_image"]);
                data.append("fileType", extension.toUpperCase());

                activeFile = data;
                isUploadingImage = false;
                isUploadingFile = true;
                showFileLabel(files[0]["name"]);

                return;
            }

            async function fileUpload() {
                const fileData = activeFile.get("file")
                const downloadURL = await window.firebaseUpload(fileData);
                activeFile.append("filePath", downloadURL);

                console.log("saving file ...")
                $.ajax({
                    url: '<?php echo base_url();?>index.php/admin/Support/fileSendUpload',
                    type: "POST",
                    processData: false,
                    contentType: false,
                    data: activeFile,
                    success: function (response) {
                        $('#fileUploaded').val('');
                        ticketMessagesHistoryContainer.append(response);
                        scrollToLatest(ticketMessagesHistoryContainer);
                        toggleClassOnSend(2);
                        resetMessageArea();
                    },
                    error: function (er) {
                        let errMsg = "";

                        if (er.status == 415)
                            errMsg = "Invalid file type. Please upload DOC, TXT or MP4.";
                        else if (er.status == 413)
                            errMsg = "File size must not exceed 6MB.";
                        else
                            errMsg = "Something went wrong. Please try again later.";
                        
                        Swal.fire({
                            icon: "error",
                            title: "Oops!",
                            text: errMsg,
                            confirmButtonColor: "#f33636",
                            confirmButtonText: "Close"
                        });

                        toggleClassOnSend(2);
                    }
                });
            }

            function generateNewFileName() {
                return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
                    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
                );
            }

            function scrollToLatest(container) {
                setTimeout(() => {
                    container.scrollTop(container.prop("scrollHeight") + 1000);
                }, 60);
            }

            function showFileLabel(fileName) {
                textMessageContainer.hide();
                textMessageContainer.val("");
                fileLabelContainer.show();
                fileLabelTextContainer.text(fileName);
            }

            function resetMessageArea() {
                activeFile = "";
                isUploadingImage = false;
                isUploadingFile = false;
                $("#upload-image").val("");
                $("#fileUploaded").val("");
                fileLabelContainer.hide();
                fileLabelTextContainer.text("");
                textMessageContainer.val("");
                textMessageContainer.show();
                textMessageContainer.focus();
            }

            function validateFile(type, file) {
                let errorTitle = "", errorMsg = "";
                let validFileSize = 6000000, validFileExtensions = [];
                let fileExtension = file.name.split('.').pop();
                fileExtension = fileExtension.toLowerCase();

                if (file.size > validFileSize) {
                    errorTitle = "Invalid file!";
                    errorMsg = "File size must not exceed 6MB.";
                }

                if (type == 1) {
                    // for file upload
                    validFileExtensions = ["pdf", "doc", "csv", "ppt", "docx", "txt", "avi", "mkv", "mp4"];
                    if (!validFileExtensions.includes(fileExtension)) {
                        errorTitle = "Invalid file!";
                        errorMsg = "Please upload PDF, DOC, CSV, PPT, TXT, AVI, MKV or MP4.";
                    }
                } else if (type == 2) {
                    // for image upload
                    validFileExtensions = ["jpg", "jpeg", "gif", "tiff", "tif", "png"];
                    if (!validFileExtensions.includes(fileExtension)) {
                        errorTitle = "Invalid image!";
                        errorMsg = "Please upload JPG, PNG, GIF or TIF.";
                    }
                }

                if (errorMsg) {
                    Swal.fire({
                        icon: "error",
                        title: errorTitle,
                        text: errorMsg,
                        confirmButtonColor: "#f33636",
                        confirmButtonText: "Close"
                    });
                    return false;
                }

                return true;
            }

            function toggleClassOnSend(type) {
                const btnSend = $("#btn-send");
                const btnFileClose = $(".file-close");

                if (type == 1) {
                    // for text message
                    textMessageContainer.prop("disabled", function(i, v) { return !v; });
                } else if (type == 2) {
                    // for image and file upload
                    fileLabelContainer.toggleClass("sending");
                    btnFileClose.toggleClass("d-none");
                }

                btnSend.toggleClass("sending");
            }
        </script>
    </body>
</html>