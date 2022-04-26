<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Support extends CI_Controller {
	
	function __construct() {

		parent::__construct();
		ini_set("display_errors", 1);
        error_reporting(1);
		$this->load->model('Mod_isValidUser');
		$this->load->model('Mod_login');
        $this->load->library('parser');
	}


    public function index(){
        $this->Mod_login->is_user_login();
        $db  =  $this->mongo_db->customQuery();

        if ($this->input->post()) {
            if (isset($_POST['per_page'])) {
                $this->session->set_userdata('paginationData', $_POST);
            }
        }

        $paginationData = $this->session->userdata('paginationData');
        $findArray['profile_status'] = 'buyer';
        $buyerComplains    =  $db->ticket->find($findArray);
        $buyerComplainsRes =  iterator_to_array($buyerComplains);
  
        $config['base_url'] = SURL . 'index.php/admin/Support/index';
        $config['total_rows'] = count($buyerComplainsRes);
        $config['per_page'] = !empty($paginationData['per_page'])? intval($paginationData['per_page']) : 3;
        $config['num_links'] = 0;
        $config['use_page_numbers'] = TRUE;
        $config['uri_segment'] = 4;
        $config['reuse_query_string'] = TRUE;
        $config["first_tag_open"] = '<li class="d-none">';
        $config["first_tag_close"] = '</li>';
        $config["last_tag_open"] = '<li class="d-none">';
        $config["last_tag_close"] = '</li>';
        $config['next_link'] = '<i class="fas fa-chevron-right"></i>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '<i class="fas fa-chevron-left"></i>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['first_link'] = '';
        $config['last_link'] = '';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['cur_tag_open'] = '<li class="active"><a href="#"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
  
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
  
        if($page !=0) 
        {
          $page = ($page-1) * $config['per_page'];
        }
        $data["links"] = $this->pagination->create_links();

        $aggregateQuery = [
            [
                '$match' => $findArray
            ],

            [
                '$project' => [

                    '_id'           =>   ['$toString' => '$_id'],
                    'order_number'  =>  '$order_number',
                    'subject'       =>  '$subject',
                    'message'       =>  '$message',
                    'image'         =>  '$image',
                    'admin_id'      =>  '$admin_id',
                    'created_date'  =>  '$created_date',
                    'status'        =>  '$status',
                    'video'         =>  '$video',
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'users',
                    'let' => [
                        'admin_id' =>  ['$toObjectId' => '$admin_id'],
                    ],
                    'pipeline' => [
                        [
                            '$match' => [
                            '$expr' => [
                                '$eq' => [
                                '$_id',
                                '$$admin_id'
                                ]
                            ],
                        ],
                    ],
                    [
                        '$project' => [
                        '_id'             =>  ['$toString' => '$_id'],
                        'profile_image'   =>  '$profile_image',
                        'full_name'       =>  '$full_name'
                        ]
                    ],
        
                    ],
                    'as' => 'profileData'
                ]
            ],
            [ 
                '$sort'=> [ 'created_date' => -1]
            ],
            [ 
                '$skip' =>  intval($page)
            ],
            [
                '$limit' => intval($config['per_page']) 
            ]
        ];
        $buyerData        =  $db->ticket->aggregate($aggregateQuery);
        $supportBuyerRes  =  iterator_to_array($buyerData);
        
        $data['buyer_res']    =  $supportBuyerRes;
        $data['total']        =  count($buyerComplainsRes);

        // to be used in loadMore function
        $data['index'] = $page;
        $data['per_page'] = $config['per_page'];
        $data['findArray'] = $findArray;
        
        $this->load->view('support/buyer', $data);
    }//end

    public function traveler(){
        $this->Mod_login->is_user_login();
        $db  =  $this->mongo_db->customQuery();

        if ($this->input->post()) {
            if (isset($_POST['per_page'])) {
                $this->session->set_userdata('paginationData', $_POST);
            }
        }

        $paginationData = $this->session->userdata('paginationData');
        $findArray['profile_status'] = 'traveler';
        $travelerComplains    =  $db->ticket->find($findArray);
        $travelerComplainsRes =  iterator_to_array($travelerComplains);
    
        $config['base_url'] = SURL . 'index.php/admin/Support/traveler';
        $config['total_rows'] = count($travelerComplainsRes);
        $config['per_page'] = !empty($paginationData['per_page'])? intval($paginationData['per_page']) : 3;
        $config['num_links'] = 0;
        $config['use_page_numbers'] = TRUE;
        $config['uri_segment'] = 4;
        $config['reuse_query_string'] = TRUE;
        $config["first_tag_open"] = '<li class="d-none">';
        $config["first_tag_close"] = '</li>';
        $config["last_tag_open"] = '<li class="d-none">';
        $config["last_tag_close"] = '</li>';
        $config['next_link'] = '<i class="fas fa-chevron-right"></i>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '<i class="fas fa-chevron-left"></i>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['first_link'] = '';
        $config['last_link'] = '';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['cur_tag_open'] = '<li class="active"><a href="#"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
    
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
    
        if($page !=0) 
        {
          $page = ($page-1) * $config['per_page'];
        }
        $data["links"] = $this->pagination->create_links();
    
        $aggregateQuery = [
            [
                '$match' => $findArray
            ],
    
            [
                '$project' => [
    
                    '_id'           =>   ['$toString' => '$_id'],
                    'order_number'  =>  '$order_number',
                    'subject'       =>  '$subject',
                    'message'       =>  '$message',
                    'image'         =>  '$image',
                    'admin_id'      =>  '$admin_id',
                    'created_date'  =>  '$created_date',
                    'status'        =>  '$status',
                    'video'         =>  '$video',
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'users',
                    'let' => [
                        'admin_id' =>  ['$toObjectId' => '$admin_id'],
                    ],
                    'pipeline' => [
                        [
                            '$match' => [
                            '$expr' => [
                                '$eq' => [
                                '$_id',
                                '$$admin_id'
                                ]
                            ],
                        ],
                    ],
                    [
                        '$project' => [
                        '_id'             =>  ['$toString' => '$_id'],
                        'profile_image'   =>  '$profile_image',
                        'full_name'       =>  '$full_name'
                        ]
                    ],
        
                    ],
                    'as' => 'profileData'
                ]
            ],
            [ 
                '$sort'=> [ 'created_date' => -1]
            ],
            [ 
                '$skip' =>  intval($page)
            ],
            [
                '$limit' => intval($config['per_page']) 
            ]
        ];
        $travelerData        =  $db->ticket->aggregate($aggregateQuery);
        $supporttravelerRes  =  iterator_to_array($travelerData);
        
        $data['traveler_res']    =  $supporttravelerRes;
        $data['total']        =  count($travelerComplainsRes);
    
        // to be used in loadMore function
        $data['index'] = $page;
        $data['per_page'] = $config['per_page'];
        $data['findArray'] = $findArray;
        
        $this->load->view('support/traveler', $data);
    }//end

    public function tickets($profile_status, $id_user = 0, $id_ticket = 0){
        $this->Mod_login->is_user_login();
        $db  =  $this->mongo_db->customQuery();

        if ($profile_status == 'traveler') {
            $search['profile_status'] = 'traveler';
            $this->session->set_userdata('tab', 'traveler');
        } elseif ($profile_status == 'buyer') {
            $this->session->set_userdata('tab', 'buyer');
            $search['profile_status'] = 'buyer';
        } else {
            show_404();
        }

        $searchValue = '';
        $searchMongoIdObject = '';
        if(!empty($_POST['searchValue'])) {
            $searchValue = $_POST['searchValue'];
            if ($this->mongo_db->isValid2($searchValue)) {
                $searchMongoIdObject = $this->mongo_db->mongoId($searchValue);
                $search['_id'] = $searchMongoIdObject;
                $searchValue = ''; // to skip searching for full_name
            }
        }

        $config['per_page'] = 99;
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        if($page !=0) {
            $page = ($page-1) * $config['per_page'];
        }

        $getTickets = [
            ['$match' => $search],
            [
                '$project' => [
                    '_id'           =>  ['$toString' => '$_id'],
                    'admin_id'      =>  '$admin_id',
                    'message'       =>  '$message',
                    'subject'       =>  '$subject',
                    'status'        =>  '$status',
                    'image'         =>  '$image',
                    'video'         =>  '$video',
                    'order_number'  =>  '$order_number',
                    'created_date'  =>  '$created_date'
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'ticket_reply',
                    'let' => ['ticketId' => '$_id'],
                    'pipeline' => [
                        [
                            '$match' => [
                                '$expr' => ['$eq' => ['$ticket_id', '$$ticketId']],
                                'status' => 'new'
                            ],
                        ],
                        [
                            '$group' => [
                                '_id'    =>  '$ticket_id',
                                'count'  =>  ['$sum' => 1]
                            ]
                        ]
                    ],
                    'as' => 'unreadMessageCount'
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'users',
                    'let' => ['admin_id' => ['$toObjectId' => '$admin_id']],
                    'pipeline' => [
                        [
                            '$match' => [
                                '$expr' => ['$eq' => ['$_id', '$$admin_id']],
                                'full_name' => [
                                    '$regex' => $searchValue,
                                    '$options' => 'i'
                                ]
                            ],
                        ],
                        [
                            '$project' => [
                                '_id'           =>  1,
                                'full_name'     => '$full_name',
                                'profile_image' => '$profile_image'
                            ]
                        ]
                    ],
                    'as' => 'ticketUserData'
                ]
            ],
            ['$match' => ['ticketUserData' => ['$ne' => []]]],
            ['$sort' => ['created_date' => -1 ]],
            ['$skip' => intval($page)],
            ['$limit'  => intval($config['per_page'])],
        ];

        $tickets    = $db->ticket->aggregate($getTickets);
        $ticketData = iterator_to_array($tickets);

        $totalTickets  =  $db->ticket->count($search);

        $data['profile_status'] = $profile_status;
        $data['id_user'] = $id_user;
        $data['id_ticket'] = $id_ticket;

        $config['base_url']         =   SURL . 'index.php/admin/Support/tickets';
        $config['total_rows']       =   $totalTickets;
        $config['num_links']        =   2;
        $config['use_page_numbers'] =   TRUE;
        $config['uri_segment']      =   4;
        $config['reuse_query_string'] = TRUE;
        $config["first_tag_open"]   =   '<li>';
        $config["first_tag_close"]  =   '</li>';
        $config["last_tag_open"]    =   '<li>';
        $config["last_tag_close"]   =   '</li>';
        $config['next_link']        =   '<i class="fas fa-angle-right"></i>';
        $config['next_tag_open']    =   '<li>';
        $config['next_tag_close']   =   '</li>';
        $config['prev_link']        =   '<i class="fas fa-angle-left"></i>';
        $config['prev_tag_open']    =   '<li>';
        $config['prev_tag_close']   =   '</li>';
        $config['first_link']       =   '<i class="fas fa-angle-double-left"></i>';
        $config['last_link']        =   '<i class="fas fa-angle-double-right"></i>';
        $config['full_tag_open']    =   '<ul class="pagination">';
        $config['full_tag_close']   =   '</ul>';
        $config['cur_tag_open']     =   '<li class="active"><a href="#"><b>';
        $config['cur_tag_close']    =   '</b></a></li>';
        $config['num_tag_open']     =   '<li>';
        $config['num_tag_close']    =   '</li>';

        $this->pagination->initialize($config);
        
        $data["links"] = $this->pagination->create_links();
        $data['tickets'] = $ticketData;
        $this->load->view('support/tickets', $data);
    }//end
    
    public function getMessages(){
        $this->Mod_login->is_user_login();
        $db = $this->mongo_db->customQuery();
        $ticketId  =  (string)$this->input->post('ticketId');
        // $ticketId = "61f5306f8da54f6b6833a17a";
        // $db->ticket_reply->updateMany(['ticket_id' => $ticketId, 'status' => "new" ], ['$set' => ['status' => 'read']]);
        $getMessages = [
            [
                '$match' => [

                    '_id' => $this->mongo_db->mongoId($ticketId)
                ]
            ],
    
            [
                '$project' => [
                    '_id'           =>  ['$toString' => '$_id'],
                    'admin_id'      =>  '$admin_id',
                    'image'         =>  '$image',
                    'video'         =>  '$video',
                    'order_number'  =>  '$order_number',
                    'subject'       =>  '$subject',
                    'message'       =>  '$message',
                    // 'created_date'  =>  [ '$dateToString' => [ 'format' => "%Y:%m:%d:%H:%M:%S:%L%z", 'date' => '$created_date', 'timezone' => "America/New_York"] ],
                    'created_date'  =>  [ '$dateToString' => [ 'format' => "%Y/%m/%d %H:%M:%S", 'date' => '$created_date', 'timezone' => "America/New_York"] ],
                    'status'        =>  '$status',
                ]
            ],

            [
                '$lookup' => [
                    'from' => 'users',
                    'let' => [
                        'admin_id' =>    ['$toObjectId' => '$admin_id'],
                    ],
                    'pipeline' => [
                        [
                        '$match' => [
                            '$expr' => [
                                '$eq' => [
                                        '$_id',
                                        '$$admin_id'
                                    ]
                                ],
                            ],
                        ],
                        
                        [
                        '$project' => [
                                '_id'           =>  1,
                                'full_name'     => '$full_name',
                                'profile_image' => '$profile_image',
                                'email_address' =>  '$email_address'
                            ]
                        ]
                    ],
                    'as' => 'profileData'
                ]
            ],

            [
            '$lookup' => [
                'from' => 'ticket_reply',
                'let' => [
                    'ticket_id' => '$_id',
                ],
                'pipeline' => [
                    [
                        '$match' => [
                            '$expr' => [
                                '$eq' => [
                                    '$ticket_id',
                                    '$$ticket_id'
                                ]
                            ],
                        ],
                    ],
                    
                    [
                        '$project' => [
                            '_id'           =>  ['$toString' => '$_id'],
                            'ticket_id'     =>  '$ticket_id',
                            'admin_id'      =>  '$admin_id',
                            'message'       =>  '$message',
                            // 'created_date'  =>  [ '$dateToString' => [ 'format' => "%Y:%m:%d:%H:%M:%S:%L%z", 'date' => '$created_date', 'timezone' => "America/New_York"] ],
                            'created_date'  =>  [ '$dateToString' => [ 'format' => "%Y/%m/%d %H:%M:%S", 'date' => '$created_date', 'timezone' => "America/New_York"] ],
                            'status'        =>  '$status',
                            'file'          =>  '$file',
                            'file_type'     =>  '$file_type',
                            'image'         =>  '$image',
                        ]
                    ],
                    [
                        '$lookup' => [
                            'from' => 'users',
                            'let' => [
                                'admin_id' =>    ['$toObjectId' => '$admin_id'],
                            ],
                            'pipeline' => [
                                [
                                '$match' => [
                                    '$expr' => [
                                        '$eq' => [
                                                '$_id',
                                                '$$admin_id'
                                            ]
                                        ],
                                    ],
                                ],
                                
                                [
                                '$project' => [
                                        '_id'           =>  1,
                                        'full_name'     => '$full_name',
                                        'profile_image' => '$profile_image',
                                        'email_address' =>  '$email_address'
                                    ]
                                ]
                            ],
                            'as' => 'userData'
                        ]
                    ],
                    [
                        '$sort' => ['created_date' => 1]
                    ],
                ],
                'as' => 'messages'
                ]
            ],

            [
                '$sort' => ['created_date' => 1 ]
            ],
        ];

        $messages     = $db->ticket->aggregate($getMessages);
        $messagesData = iterator_to_array($messages);
        // $messagesData = json_encode((array)$messagesData);
        // echo $messagesData;exit;
        // print_r($messagesData);
        // var_dump($messagesData[0]['messages']);

        $messagesHTML = '';
        $ticketMainData = array();
        $ticketMainData['div_class1'] = 'msg msg-incoming w-75';
        $ticketMainData['div_class2'] = 'this-top d-flex';

        $ticketCreatorImage = $messagesData[0]['profileData'][0]['profile_image'];
        if (empty($ticketCreatorImage)) {
            $ticketMainData['profile_image'] = "https://ptetutorials.com/images/user-profile.png";
        } else {
            $ticketMainData['profile_image'] = $ticketCreatorImage;
        }

        $time_zone = date_default_timezone_get();
        $date = date('Y/m/d h:i:s', strtotime($messagesData[0]['created_date']));
        $last_time_ago = time_elapsed_string($date, $time_zone);
        $ticketMainData['time_lapsed'] = $last_time_ago;

        $first_image = $messagesData[0]['image'][0];
        $first_video = $messagesData[0]['video'][0];
        $first_message = $messagesData[0]['message'];

        if (!empty($first_image)) {
            $ticketMainData['message'] = '<img src="'. $first_image . '"><a class="link-download" href="'. $first_image .'" target="_blank"><img src="'.SURL.'assets/images/arrow-bottom-right-r.png"></a>';
            $messagesHTML .= $this->parser->parse('support/template-ticket', $ticketMainData, TRUE);
        }

        if (!empty($first_video)) {
            $ticketMainData['message'] = '<video controls><source src="'. $first_video . '" ></video>';
            $messagesHTML .= $this->parser->parse('support/template-ticket', $ticketMainData, TRUE);
        }

        $ticketMainData['message'] = $first_message;
        $messagesHTML .= $this->parser->parse('support/template-ticket', $ticketMainData, TRUE);

        $ticket_creator_id = $messagesData[0]['admin_id'];

        // loop data and create string template of messages
        foreach ($messagesData[0]['messages'] as $res) {
            $template_data = array();
            $user_id = $res['userData'][0]['_id'];
            $profile_image = $res['userData'][0]['profile_image'];
            $message_main = $res['message'];

            if (empty($profile_image) || $profile_image == ''|| is_null($profile_image)) {                               
                // $template_data['profile_image'] = SURL.'assets/images/male.png';
                $template_data['profile_image'] = 'https://png.pngtree.com/png-clipart/20190924/original/pngtree-user-vector-avatar-png-image_4830521.jpg';
            } else {
                $template_data['profile_image'] = $profile_image;
            }

            if ($ticket_creator_id == $user_id) {
                // incoming classes
                $template_data['div_class1'] = 'msg msg-incoming w-75';
                $template_data['div_class2'] = 'this-top d-flex';
            } else {
                // outgoing classes
                $template_data['div_class1'] = 'msg msg-outgoing w-75 ml-auto';
                $template_data['div_class2'] = 'this-top d-flex justify-content-end';
            }

            if (empty($message_main) || $message_main == ''|| is_null($message_main)) {   
                $url_image = $res['image'];
                $url_file = $res['file'];

                if (empty($url_file) || $url_file == ''|| is_null($url_file)) {
                    $template_data['message'] = '<img src="'. $url_image . '"><a class="link-download" href="'. $url_image .'" download><img src="'.SURL.'assets/images/arrow-bottom-right-r.png"></a>';
                    // $template_data['message'] = '<img src="'.SURL.'assets/uploads/'. $res['image'] . '">';
                } else {
                    $file_extension = $res['file_type'];
                    $template_data['message'] = '<a href="' . $url_file . '" download>Download ' . $file_extension . ' file.</a>';
                }
            } else {
                $template_data['message'] = nl2br($res['message']);
            }

            $time_zone = date_default_timezone_get();
            $date = date('Y/m/d h:i:s', strtotime($res['created_date']));
            $last_time_ago = time_elapsed_string($date, $time_zone);
            $template_data['time_lapsed'] = $last_time_ago;

            // var_dump($res);
            // echo "<br/><br/>";

            $messagesHTML .= $this->parser->parse('support/template-ticket', $template_data, TRUE);
        }

        // echo $messagesHTML;

        $messagesData[0]['messages'] = $messagesHTML;
        $messagesData = json_encode((array)$messagesData);
        echo $messagesData;exit;
        // print_r($messagesData);
        // var_dump($messagesData[0]['messages']);

        exit;
    }//end

    public function sendMessage(){
        $this->Mod_login->is_user_login();
        $db = $this->mongo_db->customQuery();

        $ticket_id = (string)$this->input->post('ticketId');
        $message = (string)$this->input->post('sendMessage');
        $created_date = $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s'));

        $time_zone = date_default_timezone_get();
        $date = date('Y/m/d h:i:s', $created_date);
        $last_time_ago = time_elapsed_string($date, $time_zone);

        // Save new reply
        $tickerReply = [
            'ticket_id'    =>  $ticket_id,
            'message'      =>  $message,
            'admin_id'     =>  $this->session->userdata('admin_id'),
            'status'       =>  'new',
            'created_date' =>  $created_date,
        ];
        $db->ticket_reply->insertOne($tickerReply);

        // Generate html template
        $ticketMainData = array();
        $ticketMainData['profile_image'] = $this->input->post('profileImage');
        $ticketMainData['message'] = nl2br($message);
        $ticketMainData['div_class1'] = 'msg msg-outgoing w-75 ml-auto';
        $ticketMainData['div_class2'] = 'this-top d-flex justify-content-end';
        $ticketMainData['time_lapsed'] = $last_time_ago;
        $messagesHTML = $this->parser->parse('support/template-ticket', $ticketMainData, TRUE);

        echo $messagesHTML;
        exit;
    }//end message

    public function imageSendUpload(){
        /* 
        *   Old upload integration via PHP upload library
        */

        // if($_FILES['image']['name'] != '' && $this->input->post('ticketId') ){
		// 	$uploadImagePath = FCPATH.'assets/uploads/';

		// 	$orignal_file_name = $_FILES['image']['name'];

		// 	$config['upload_path']   = $uploadImagePath;
		// 	$config['allowed_types'] = 'jpg|jpeg|gif|tiff|tif|png';
		// 	$config['max_size']	     = '6000';
		// 	$config['overwrite']     = true;
        //     $config['encrypt_name']  =  TRUE;
		// 	$config['file_name']     = $orignal_file_name;
		// 	$this->load->library('upload', $config);
        //     $this->upload->initialize($config);

		// 	if (!$this->upload->do_upload('image')) {
		// 		$error_file_arr = array('error' => $this->upload->display_errors());
		// 		// echo json_encode($error_file_arr);
        //         echo http_response_code(415);
        //         exit;
		// 	} else {
        //         $data = array('upload_data' => $this->upload->data());
		// 	    // $imagePath = $data['upload_data']['full_path'];
        //         // $imagePath = str_replace("/var/www/html/","http://3.120.159.133/", $imagePath);
        //         $image_file_name = $data['upload_data']['file_name'];
        //         $imagePath = SURL.'assets/uploads/'. $image_file_name;
        //         $created_date = $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s'));
        
        //         $time_zone = date_default_timezone_get();
        //         $date = date('Y/m/d h:i:s', $created_date);
        //         $last_time_ago = time_elapsed_string($date, $time_zone);

        //         $tickerReply = [
        //             'image'        =>  $imagePath,
        //             'ticket_id'    =>  (string)$this->input->post('ticketId'),
        //             'admin_id'     =>  $this->session->userdata('admin_id'),
        //             'status'       =>  'new',
        //             'created_date' =>  $created_date,
        //         ];
        //         $db = $this->mongo_db->customQuery();
        //         $db->ticket_reply->insertOne($tickerReply); 

        //         // Generate html template
        //         $ticketMainData = array();
        //         $ticketMainData['profile_image'] = $this->input->post('profileImage');
        //         $ticketMainData['message'] = '<img src="'.$imagePath.'"><a class="link-download" href="'. $imagePath .'" download><img src="'.SURL.'assets/images/arrow-bottom-right-r.png"></a>';
        //         $ticketMainData['div_class1'] = 'msg msg-outgoing w-75 ml-auto';
        //         $ticketMainData['div_class2'] = 'this-top d-flex justify-content-end';
        //         $ticketMainData['time_lapsed'] = $last_time_ago;
        //         $messagesHTML = $this->parser->parse('support/template-ticket', $ticketMainData, TRUE);

        //         echo $messagesHTML;
		//         exit;
        //     }
        // }

        /* 
        *   New upload integration via Firebase Storage
        */
        $imagePath = (string)$this->input->post('imagePath');
        $ticketId = (string)$this->input->post('ticketId');
        $adminId = $this->session->userdata('admin_id');
        
        if ($imagePath && $ticketId && $adminId) {
            $created_date = $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s'));
            $time_zone = date_default_timezone_get();
            $date = date('Y/m/d h:i:s', $created_date);
            $last_time_ago = time_elapsed_string($date, $time_zone);

            $tickerReply = [
                'image'        =>  $imagePath,
                'ticket_id'    =>  $ticketId,
                'admin_id'     =>  $adminId,
                'status'       =>  'new',
                'created_date' =>  $created_date,
            ];
            $db = $this->mongo_db->customQuery();
            $db->ticket_reply->insertOne($tickerReply); 

            // Generate html template
            $ticketMainData = array();
            $ticketMainData['profile_image'] = $this->input->post('profileImage');
            $ticketMainData['message'] = '<img src="'.$imagePath.'"><a class="link-download" href="'. $imagePath .'" download><img src="'.SURL.'assets/images/arrow-bottom-right-r.png"></a>';
            $ticketMainData['div_class1'] = 'msg msg-outgoing w-75 ml-auto';
            $ticketMainData['div_class2'] = 'this-top d-flex justify-content-end';
            $ticketMainData['time_lapsed'] = $last_time_ago;
            $messagesHTML = $this->parser->parse('support/template-ticket', $ticketMainData, TRUE);

            echo $messagesHTML;
            exit;
        }
    }//end
    
    public function fileSendUpload(){
        /* 
        *   Old upload integration via PHP upload library
        */

        // if($_FILES['file']['name'] != '' && $this->input->post('ticketId') ){
        //     $fileUploadPath = FCPATH.'assets/uploads/';
		// 	$orignal_file_name = $_FILES['file']['name'];

		// 	$config['upload_path']   = $fileUploadPath;
		// 	$config['allowed_types'] = 'pdf|doc|csv|ppt|docx|txt';
		// 	$config['max_size']	     = '6000';
		// 	$config['overwrite']     = true;
        //     $config['encrypt_name']  =  TRUE;
		// 	$config['file_name']     = $orignal_file_name;
		// 	$this->load->library('upload', $config);
        //     $this->upload->initialize($config);

		// 	if (!$this->upload->do_upload('file')) {
        //         $error_file_arr = array('error' => $this->upload->display_errors());
		// 		// echo json_encode($error_file_arr);
        //         echo http_response_code(415);
        //         exit;
		// 	} else {
        //         $data = array('upload_data' => $this->upload->data());
		// 	    // $filePathFinal = $data['upload_data']['full_path'];
        //         // $filePathFinal = str_replace("/var/www/html/", "http://3.120.159.133/", $filePathFinal);
        //         $file_name = $data['upload_data']['file_name'];
        //         $filePath = SURL.'assets/uploads/'. $file_name;
        //         $created_date = $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s'));
        
        //         $time_zone = date_default_timezone_get();
        //         $date = date('Y/m/d h:i:s', $created_date);
        //         $last_time_ago = time_elapsed_string($date, $time_zone);

        //         $tickerReply = [
        //             'file'         =>  $filePath,
        //             'ticket_id'    =>  (string)$this->input->post('ticketId'),
        //             'admin_id'     =>  $this->session->userdata('admin_id'),
        //             'status'       =>  'new',
        //             'created_date' =>  $created_date,
        //         ];
        //         $db = $this->mongo_db->customQuery();
        //         $db->ticket_reply->insertOne($tickerReply); 

        //         // Generate html template
        //         $file_extension = strtoupper(pathinfo($filePath, PATHINFO_EXTENSION));

        //         $ticketMainData = array();
        //         $ticketMainData['profile_image'] = $this->input->post('profileImage');
        //         $ticketMainData['message'] = '<a href="' . $filePath . '" download>Download ' . $file_extension . ' file.</a>';
        //         $ticketMainData['div_class1'] = 'msg msg-outgoing w-75 ml-auto';
        //         $ticketMainData['div_class2'] = 'this-top d-flex justify-content-end';
        //         $ticketMainData['time_lapsed'] = $last_time_ago;
        //         $messagesHTML = $this->parser->parse('support/template-ticket', $ticketMainData, TRUE);

        //         echo $messagesHTML;
		//         exit;
        //     }
        // }
        
        /* 
        *   New upload integration via Firebase Storage
        */
        $filePath = (string)$this->input->post('filePath');
        $fileType = (string)$this->input->post('fileType');
        $ticketId = (string)$this->input->post('ticketId');
        $adminId = $this->session->userdata('admin_id');

        if ($filePath && $fileType && $ticketId && $adminId) {
            $created_date = $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s'));
            $time_zone = date_default_timezone_get();
            $date = date('Y/m/d h:i:s', $created_date);
            $last_time_ago = time_elapsed_string($date, $time_zone);

            $tickerReply = [
                'file'         =>  $filePath,
                'file_type'    =>  $fileType,
                'ticket_id'    =>  $ticketId,
                'admin_id'     =>  $adminId,
                'status'       =>  'new',
                'created_date' =>  $created_date,
            ];
            $db = $this->mongo_db->customQuery();
            $db->ticket_reply->insertOne($tickerReply); 

            // Generate html template
            $file_extension = $fileType;

            $ticketMainData = array();
            $ticketMainData['profile_image'] = $this->input->post('profileImage');
            $ticketMainData['message'] = '<a href="' . $filePath . '" download>Download ' . $file_extension . ' file.</a>';
            $ticketMainData['div_class1'] = 'msg msg-outgoing w-75 ml-auto';
            $ticketMainData['div_class2'] = 'this-top d-flex justify-content-end';
            $ticketMainData['time_lapsed'] = $last_time_ago;
            $messagesHTML = $this->parser->parse('support/template-ticket', $ticketMainData, TRUE);

            echo $messagesHTML;
            exit;
        }
    }//end

    public function loadMore() {
        // prepare data
        $index = intval($_GET['index']);
        $per_page = intval($_GET['per_page']);
        $total = $_GET['total'];
        $findArray = json_decode(stripslashes($_GET['findArray']));

        $db = $this->mongo_db->customQuery();

        $condition = array('sort'=>array('created_date'=> -1));
        $condition = array('limit' => $per_page, 'skip' =>  $index + $per_page);

        $aggregateQuery = [
            [
                '$match' => $findArray
            ],

            [
                '$project' => [

                    '_id'           =>   ['$toString' => '$_id'],
                    'order_number'  =>  '$order_number',
                    'subject'       =>  '$subject',
                    'message'       =>  '$message',
                    'image'         =>  '$image',
                    'admin_id'      =>  '$admin_id',
                    'created_date'  =>  '$created_date',
                    'status'        =>  '$status',
                    'video'         =>  '$video',
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'users',
                    'let' => [
                        'admin_id' =>  ['$toObjectId' => '$admin_id'],
                    ],
                    'pipeline' => [
                        [
                            '$match' => [
                            '$expr' => [
                                '$eq' => [
                                '$_id',
                                '$$admin_id'
                                ]
                            ],
                        ],
                    ],
                    [
                        '$project' => [
                        '_id'             =>  ['$toString' => '$_id'],
                        'profile_image'   =>  '$profile_image',
                        'full_name'       =>  '$full_name'
                        ]
                    ],
        
                    ],
                    'as' => 'profileData'
                ]
            ],
            [ 
                '$sort'=> [ 'created_date' => -1]
            ],
            [ 
                '$skip' =>  $index + $per_page
            ],
            [
                '$limit' => $per_page
            ]
        ];

        $more_data =  $db->ticket->aggregate($aggregateQuery);
        $more_data_res = iterator_to_array($more_data);

        $temp = '';

        // loop data and create string template
        foreach ($more_data_res as $res) {
            $template_data = array();
            $profile_image = json_decode(json_encode($res["profileData"]))[0]->profile_image;

            // fix conditional data for template usage
            if (empty($profile_image) || $profile_image == ''|| is_null($profile_image)) {                               
                $template_data['profile_image'] = SURL.'assets/images/male.png';
            } else {
                $template_data['profile_image'] = $profile_image;
            }

            $template_data['_id'] = $res['_id'];
            $template_data['full_name'] = json_decode(json_encode($res["profileData"]))[0]->full_name;
            $template_data['subject'] = $res['subject'];
            $template_data['order_number'] = $res['order_number'];

            $temp .= $this->parser->parse('support/template', $template_data, TRUE);
        }

        echo $temp;
        exit;
    }
}