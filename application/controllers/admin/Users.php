<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
	function __construct()
	{
		parent :: __construct();

        // ini_set("display_errors", 1);
        // error_reporting(1);
        $this->load->model('Mod_login');
        $this->load->library('parser');
	}

    public function index(){
        $this->Mod_login->is_user_login();
        $db  =  $this->mongo_db->customQuery();

        if ($this->input->post()) {
            if (isset($_POST['per_page'])) {
                $this->session->set_userdata('paginationData', $_POST);
            } else {
                $this->session->set_userdata('buyerUsersFilter', $_POST);
            }
        }

        $searchData = $this->session->userdata('buyerUsersFilter');
        $paginationData = $this->session->userdata('paginationData');
        if(!is_null($searchData)){
            if (!empty($searchData['filter_type'])) {
                if ($searchData['filter_type'] == 'country') {
                    // search by country
                    $findArray['country'] = $searchData['country'];
                } elseif ($searchData['filter_type'] == 'location') {
                    // search by location/area
                    $findArray['location']  = [ '$regex' => $searchData['filter_search'], '$options' => 'si'];
                } else {
                    // search by full name
                    $findArray['full_name']  = [ '$regex' => $searchData['filter_search'], '$options' => 'si'];
                }
            } elseif (!empty($searchData['filter_search'])) {
                $findArray['location'] = [ '$regex' => $searchData['filter_search'], '$options' => 'si'];
                $findArray['full_name'] = [ '$regex' => $searchData['filter_search'], '$options' => 'si'];
            }
        }

        $findArray['profile_status'] = 'buyer';
       
        $buyer      =  $db->users->find($findArray);
        $buyerCount =  iterator_to_array($buyer);

        $config['base_url'] = SURL . 'index.php/admin/users/index';
        $config['total_rows'] = count($buyerCount);
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

        $condition = array('sort'=>array('created_date'=> -1));
        $condition = array('limit' => $config['per_page'], 'skip' =>  $page); 
        
        $buyer     =  $db->users->find($findArray, $condition);
        $buyerRes  =  iterator_to_array($buyer);

        $data['buyers']    =  $buyerRes;
        $data['total']     =  count($buyerCount);

        $data['getAllCountries'] = getCountry();

        // to be used in loadMore function
        $data['index'] = $page;
        $data['per_page'] = $config['per_page'];
        $data['findArray'] = $findArray;

        $this->load->view('users/buyer', $data);
    }
    public function traveler(){
        $this->Mod_login->is_user_login();
        $db  =  $this->mongo_db->customQuery();

        if( $this->input->post() ){
            $postData['travelerUsersFilter'] = $this->input->post(); 
            $this->session->set_userdata($postData);
        }

        $searchData = $this->session->userdata('travelerUsersFilter');
        if(!is_null($searchData)){
            if (!empty($searchData['filter_type'])) {
                if ($searchData['filter_type'] == 'country') {
                    // search by country
                    $findArray['country'] = $searchData['country'];
                } elseif ($searchData['filter_type'] == 'location') {
                    // search by location/area
                    $findArray['location']  = [ '$regex' => $searchData['filter_search'], '$options' => 'si'];
                } else {
                    // search by full name
                    $findArray['full_name']  = [ '$regex' => $searchData['filter_search'], '$options' => 'si'];
                }
            } else {
                $findArray['location'] = [ '$regex' => $searchData['filter_search'], '$options' => 'si'];
                $findArray['full_name'] = [ '$regex' => $searchData['filter_search'], '$options' => 'si'];
            }
        }

        $findArray['profile_status'] = 'traveler';

        $traveler      =  $db->users->find($findArray);
        $travelerCount =  iterator_to_array($traveler);

        $config['base_url'] = SURL . 'index.php/admin/users/traveler';
        $config['total_rows'] = count($travelerCount);
        $config['per_page'] = 10;
        $config['num_links'] = 5;
        $config['use_page_numbers'] = TRUE;
        $config['uri_segment'] = 4;
        $config['reuse_query_string'] = TRUE;
        $config["first_tag_open"] = '<li>';
        $config["first_tag_close"] = '</li>';
        $config["last_tag_open"] = '<li>';
        $config["last_tag_close"] = '</li>';
        $config['next_link'] = '<i class="fas fa-angle-right"></i>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '<i class="fas fa-angle-left"></i>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['first_link'] = '<i class="fas fa-angle-double-left"></i>';
        $config['last_link'] = '<i class="fas fa-angle-double-right"></i>';
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
        $data["links"] = $this->pagination->create_links($config);

        $condition = array('sort'=>array('created_date'=> -1));
        $condition = array('limit' => $config['per_page'], 'skip' =>  $page); 
        
        $traveler     =  $db->users->find($findArray, $condition);
        $travelerRes  =  iterator_to_array($traveler);

        $data['traveler']    =  $travelerRes;
        $data['total']       =  count($travelerCount);
        $data['getAllCountries'] = getCountry();

        // to be used in loadMore function
        $data['index'] = $page;
        $data['per_page'] = $config['per_page'];
        $data['findArray'] = $findArray;
        
        $this->load->view('users/traveler', $data);
    }

    public function resetFilterTravelers(){ 

        $this->session->unset_userdata('travelerUsersFilter');
        $this->traveler();
    }

    public function resetFilterBuyers(){
        
        $this->session->unset_userdata('buyerUsersFilter');
        $this->index();
    }//end

    public function getFullNames(){

        $db = $this->mongo_db->customQuery();
        $name = $db->users->find([]);
        $nameRes = iterator_to_array($name);
        $user_name_array = array_column($nameRes, 'full_name');
        unset($nameRes, $name );
        echo json_encode($user_name_array);
        exit;
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

        $more_data =  $db->users->find($findArray, $condition);
        $more_data_res  =  iterator_to_array($more_data);

        $temp = '';

        // loop data and create string template
        foreach ($more_data_res as $res) {
            // fix conditional data for template usage
            if (empty($res['profile_image']) || $res['profile_image'] == ''|| is_null($res['profile_image'])) {                               
                $res['profile_image'] = SURL.'assets/images/male.png';
            }

            if (empty($res['location']) || is_null($res['location'])) {
                $res['location'] = 'N/A';
            }

            $temp .= $this->parser->parse('users/template', $res, TRUE);
        }

        echo $temp;
        exit;
    }

    public static function findCountryByCode($code) {
        $countries = getCountry();
        foreach ($countries as $country) {
            if ( $country["code"] == $code ) {
                return $country["name"];
            }
        }
    }
}
