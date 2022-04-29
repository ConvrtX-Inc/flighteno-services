
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	function __construct(){
		parent :: __construct();

        ini_set("display_errors", 1);
        error_reporting(1); 
        
        //model
        $this->load->model('Mod_login');
        $this->load->model('Mod_users');
        $this->load->model('Mod_order');
	}

    public function index(){
        $this->Mod_login->is_user_login();
        $db  = $this->mongo_db->customQuery();
        $condition    =  ['sort' => ['created_date' => 1] ];
        $dataPayment  =  $db->payment_details->find([], $condition);
        $trasections  =  iterator_to_array($dataPayment);
        $data['trasections']  =  $trasections;

        //find all users Only 
        $usersCount     =  $db->users->find([ 'status' => 'user', 'user_role' => 2, '$or' => array(['profile_status' => 'buyer'], ['profile_status' => 'traveler'])]);
        $usersAll       =  iterator_to_array($usersCount);
        $data['users']  =  count($usersAll);

        // count active/inactive users
        $activeUsers =  $this->Mod_users->countActiveUsers();
        $data['active_users'] = $activeUsers;  
        $data['inActive_users'] = count($usersAll) - $activeUsers;

        // calculate active/inactive users percentage
        $activeUserPercentage = ($activeUsers / $data['users'] ) * 100;
        $data['active_user_percentage']  = $activeUserPercentage; 
        $data['inactiveUserPercentage']  = 100 - $activeUserPercentage;

        //find total signed up users
        $getSignedUpUsersDetails = $this->Mod_users->findSignedUpUsers();
        $data['percentageSignedUp']  = $getSignedUpUsersDetails['percentageSignedUp'];
        $data['totalEarnedCost']     = $this->Mod_order->calculateCost();
        //end total signed up users
        
        $activities      =  $db->activities->find([]);
        $activitiesCount =  iterator_to_array($activities);

        $config['base_url'] = SURL . 'index.php/admin/Dashboard/index';
        $config['total_rows'] = count($activitiesCount);
        $config['per_page'] = 12;
        $config['num_links'] = 2;
        $config['use_page_numbers'] = TRUE;
        $config['uri_segment'] = 4;
        $config['reuse_query_string'] = TRUE;
        $config["first_tag_open"] = '<li>';
        $config["first_tag_close"] = '</li>';
        $config["last_tag_open"] = '<li>';
        $config["last_tag_close"] = '</li>';
        $config['next_link'] = 'Next<i class="fa fa-long-arrow-right"></i>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '<i class="fa fa-long-arrow-left"></i>Previous';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
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

        $queryActivity = [
            [
                '$project' => [
                    '_id'      =>  ['$toString' => '$_id'],
                    'message'  =>   '$message',
                    'admin_id' =>   '$admin_id'
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
                            '_id'       =>  ['$toString' => '$_id'],
                            'full_name'     =>  [ '$split' => [ '$full_name', " " ]],
                            'profile_image' =>  '$profile_image',
                        ]
                    ],
                    // [
                    //     '$unwind'  => '$full_name' 
                    // ]
    
                ],
                'as' => 'profileData'
                ]
            ],
            [
                '$skip'  => $page
            ],
            [
                '$limit'   =>   $config['per_page']
            ],
            [
                '$sort'   =>  ['created_date' => -1]
            ]
        ];
        $activities    =  $db->activities->aggregate($queryActivity);
        $activitiesRes =  iterator_to_array($activities);
        $data['recentActivity']  = $this->Mod_users->getRecentUserActivities();
        // var_dump($data['recentActivity']);exit;

        $this->load->view('admin/admin_dashboard', $data);
    }//end

    public function markAllReadss(){
        $db = $this->mongo_db->customQuery();
        $db->admin_notification->updateMany(['status' => 'pending'], ['$set' => ['status' => 'read']]);
        return true;
    }//end 
}