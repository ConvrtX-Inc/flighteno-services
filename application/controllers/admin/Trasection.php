<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Trasection extends CI_Controller {
	function __construct(){
		parent :: __construct();
        
        // ini_set("display_errors", 1);
        // error_reporting(1);
        $this->load->model('Mod_login');

	}
  public function index(){

    $this->Mod_login->is_user_login();
    $db  =  $this->mongo_db->customQuery();
    if( $this->input->post() ){

      //$filterData['buyerTransactionsFilter'] = $this->input->post();
      //$this->session->set_userdata($filterData);
      if (isset($_POST['per_page'])) {
          $this->session->set_userdata('paginationData', $_POST);
      } else {
          $this->session->set_userdata('buyerTransactionsFilter', $_POST);
      }
    }
    $filterDataBuyer = $this->session->userdata('buyerTransactionsFilter');
    $paginationData = $this->session->userdata('paginationData');

    $findArray['price'] = ['$gt'=> 0 ];
    if(!is_null($filterDataBuyer)){
      if($filterDataBuyer['start_date'] != "" && $filterDataBuyer['end_date'] != ""){
        $startDate =  $this->mongo_db->converToMongodttime($filterDataBuyer['start_date']);
        //$endDate   =  $this->mongo_db->converToMongodttime($filterDataBuyer['end_date']);
        $endDate   =  $this->mongo_db->converToMongodttime(date('Y-m-d', strtotime($filterDataBuyer['end_date']. ' + 1 days')));

        $findArray['created_date'] = ['$gte' => $startDate, '$lte' => $endDate];
      }
      if(!empty($filterDataBuyer['price'])){
        /* FLIGHT-31 fix */
        $findArray['price'] = intval($filterDataBuyer['price']);
      }
      if(!empty($filterDataBuyer['search'])){
        $findArray['full_name'] = $filterDataBuyer['search'];
      }
    }

    $countData = [
      [
        '$match' => $findArray
      ],
      [
        '$project' => [
          '_id'            =>  ['$toString' => '$_id'],
          'created_date'  =>  '$created_date',
          'buyer_id'      =>  '$buyer_id',
          'order_id'      =>  '$order_id',
          'price'         =>  '$price'
        ]
      ],
      [
        '$lookup' => [
          'from' => 'users',
          'let' => [
            'admin_id' =>  ['$toObjectId' => '$buyer_id'],
          ],
          'pipeline' => [
            [
              '$match' => [
                '$expr' => [
                  '$eq' => ['$_id','$$admin_id']
                ],
              ],
            ],
            [
              '$project' => [
                '_id'             =>  ['$toString' => '$_id'],
                'profile_status'       =>  '$profile_status'
              ]
            ],
          ],
          'as' => 'profileData'
        ]
      ],
      [
        '$match'=>['profileData.profile_status'=>'buyer']
      ]
    ];

    //$buyer_trasections    =  $db->payment_details->find($findArray);
    $buyer_trasections    =  $db->payment_details->aggregate($countData);
    $buyerRes_trasections =  iterator_to_array($buyer_trasections);
    $total_rows = count($buyerRes_trasections);

    $config['base_url'] = SURL . 'index.php/admin/Trasection/index';
    //$config['total_rows'] = count($buyerRes_trasections);
    $config['total_rows'] = $total_rows;
    //$config['per_page'] = 3;
    $config['per_page'] = !empty($paginationData['per_page'])? intval($paginationData['per_page']) : 3;
    /*$config['num_links'] = 4;
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
    $config['num_tag_close'] = '</li>';*/
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
    
    $getData = [
      [
        '$match' => $findArray
      ],
      [
        '$project' => [
          '_id'            =>  ['$toString' => '$_id'],
          'type'          =>  '$type',
          'created_date'  =>  '$created_date',
          'offer_id'      =>  '$offer_id',
          'buyer_id'      =>  '$buyer_id',
          'traveler_id'   =>  '$raveler_id',
          'order_id'      =>  '$order_id',
          'price'         =>  '$price'
        ]
      ],
      [
        '$lookup' => [
          'from' => 'users',
          'let' => [
            'admin_id' =>  ['$toObjectId' => '$buyer_id'],
          ],
          'pipeline' => [
            [
              '$match' => [
                '$expr' => [
                  '$eq' => ['$_id','$$admin_id']
                ],
              ],
            ],
            [
              '$project' => [
                '_id'             =>  ['$toString' => '$_id'],
                'profile_image'   =>  '$profile_image',
                'full_name'       =>  '$full_name',
                'profile_status'       =>  '$profile_status'
              ]
            ],
          ],
          'as' => 'profileData'
        ]
      ],
      [
        '$match'=>['profileData.profile_status'=>'buyer']
      ],
      ['$sort' => [ 'created_date'=> -1]],
      ['$skip' =>  $page],
      ['$limit' =>  $config['per_page'], ],
    ];
    $buyer_trasec     =  $db->payment_details->aggregate($getData);
    $buyerRes_trasec  =  iterator_to_array($buyer_trasec);
    //echo "<pre>";var_dump($buyerRes_trasec);echo "</pre>";exit;
    
    $data['buyers_payment']    =  $buyerRes_trasec;
    $data['total_rows']=$total_rows;
    $data['index'] = $page;
    $data['per_page'] = $config['per_page'];

    $this->load->view('trasection/buyer', $data);
  }

  ///Update Query logic for FLIGHT - 32 Admin | Transaction
  public function trasectionTraveler(){
    $this->Mod_login->is_user_login();
    $db  =  $this->mongo_db->customQuery();
    
    if( $this->input->post() ){
      if (isset($_POST['per_page'])) {
          $this->session->set_userdata('paginationData', $_POST);
      } else {
          $this->session->set_userdata('travelerTransactionsFilter', $_POST);
      }
    }
    $filterData = $this->session->userdata('travelerTransactionsFilter');
    $paginationData = $this->session->userdata('paginationData');
    
    $findArray['price'] = ['$gt'=> 0 ];

    if(!is_null($filterData)) {
      if($filterData['start_date'] !="" && $filterData['end_date'] != ""){
          $startDate = $this->mongo_db->converToMongodttime($filterData['start_date']);
          //$endDate   = $this->mongo_db->converToMongodttime($filterData['end_date']);
          $endDate   =  $this->mongo_db->converToMongodttime(date('Y-m-d', strtotime($filterData['end_date']. ' + 1 days')));
          $findArray['created_date'] = ['$gte' => $startDate,  '$lte' => $endDate];
      }   
      if(!empty($filterData['price'])){
        $findArray['price'] = intval($filterData['price']);
      }
    }

    $countData = [
      [
        '$match' => $findArray
      ],
      [
        '$project' => [
          '_id'            =>  ['$toString' => '$_id'],
          'type'          =>  '$type',
          'created_date'  =>  '$created_date',
          'traveler_id'   =>  '$traveler_id',
          'price'         =>  '$price',
        ]
      ],
      [
        '$lookup' => [
          'from' => 'users',
          'let' => [
            'admin_id' =>  ['$toObjectId' => '$traveler_id'],
          ],
          'pipeline' => [
            [
              '$match' => [
                '$expr' => ['$eq' => ['$_id', '$$admin_id']],
              ],
            ],
            [
              '$project' => [
                '_id'             =>  ['$toString' => '$_id'],
                'profile_status'       =>  '$profile_status'
              ]
            ],
          ],
          'as' => 'profileData'
        ]
      ],
      [
        '$match'=>['profileData.profile_status'=>'traveler']
      ]
    ];

    //$buyer    =  $db->payment_details->find($findArray);
    $buyer    =  $db->payment_details->aggregate($countData);
    $buyerRes =  iterator_to_array($buyer);
    //echo '<pre>';var_dump($buyerRes);echo '</pre>';exit;
    $total_rows = count($buyerRes);
    
    $config['base_url'] = SURL . 'index.php/admin/Trasection/trasectionTraveler';
    $config['total_rows'] = $total_rows;
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

    $getData = [
      [
        '$match' => $findArray
      ],
      [
        '$project' => [
          '_id'            =>  ['$toString' => '$_id'],
          'type'          =>  '$type',
          'created_date'  =>  '$created_date',
          'offer_id'      =>  '$offer_id',
          'buyer_id'      =>  '$buyer_id',
          'traveler_id'   =>  '$traveler_id',
          'order_id'      =>  '$order_id',
          'price'         =>  '$price',
        ]
      ],
      [
        '$lookup' => [
          'from' => 'users',
          'let' => [
            'admin_id' =>  ['$toObjectId' => '$traveler_id'],
          ],
          'pipeline' => [
            [
              '$match' => [
                '$expr' => [
                  '$eq' => [ '$_id','$$admin_id']
                ],
              ],
            ],
            [
              '$project' => [
                '_id'             =>  ['$toString' => '$_id'],
                'profile_image'   =>  '$profile_image',
                'full_name'       =>  '$full_name',
                'profile_status'       =>  '$profile_status'
              ]
            ],

          ],
          'as' => 'profileData'
        ]
      ],
      [
        '$match'=>['profileData.profile_status'=>'traveler']
      ],
      ['$sort' => [ 'created_date'=> -1]],
      ['$skip' =>  $page],
      ['$limit' =>  $config['per_page'], ],
    ];

    $traveler     =  $db->payment_details->aggregate($getData);
    $travelerRes  =  iterator_to_array($traveler);
    //echo "<pre>";var_dump($travelerRes);echo "</pre>";exit;
    
    $data['traveler_res']    =  $travelerRes;
    $data['total_rows']=$total_rows;
    $data['index'] = $page;
    $data['per_page'] = $config['per_page'];

    $this->load->view('trasection/traveler', $data);
  }

  public function resetFilterTravelers(){ 

    $this->session->unset_userdata('travelerTransactionsFilter');
    $this->trasectionTraveler();
  }

  public function resetFilterBuyers(){
      
    $this->session->unset_userdata('buyerTransactionsFilter');
    $this->index();
  }//end
}
