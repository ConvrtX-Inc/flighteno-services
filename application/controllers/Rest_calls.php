<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
// use Twilio\Rest\Client;
use Stripe\Stripe;
require 'vendor/autoload.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Rest_calls extends REST_Controller
{
    private $stripe = null;
    function __construct()
    {
        parent::__construct();
        $this->load->model('Mod_isValidUser');
        $this->load->model('Mod_users');
        $this->load->model('Mod_chat');
        $this->load->model('Mod_order');
        $this->load->model('Mod_trip');
        $this->load->model('Mod_rating');
        $this->load->model('Mod_activity');
        $this->load->model('Mod_ticket');
        $this->load->model('Mod_card');
        ini_set("display_errors", 1);
        error_reporting(1);
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load();
        $this->stripe  = new \Stripe\StripeClient([
            'api_key' => $_ENV['STRIPE_SECRET_KEY'],
            'stripe_version' => '2020-08-27',
        ]);
    }

    public function createOrder_post()
    {

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                $received_Token = $received_Token_Array['Authorization'];

            }

            $token = trim(str_replace("Token: ", "", $received_Token));
            $token = trim(str_replace("Bearer ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $replyChecking = isUserExistsUsingAdminId((string)$tokenArray->admin_id);

                if ($replyChecking == true || $replyChecking == 1) {

                    if ($this->post()) { 

                        $totalCharges = (float)((float)$this->post('product_price')) + (float)$this->post('estimated_dilivery_fee') + 
                                            ((float)$this->post('vip_service_fee')) + ((float)$this->post('flighteno_cost')) + ((float)$this->post('tax'));

                        $raw_start = $this->post('preferred_dilivery_date')." ".$this->post('preferred_dilivery_start_time').":00";
                        $raw_end = $this->post('preferred_dilivery_date')." ".$this->post('preferred_dilivery_end_time').":00";

                    
                        $pref_date = $this->mongo_db->converToMongodttime(date($this->post('preferred_dilivery_date')));
                        $pref_delivery_start = $this->mongo_db->converToMongodttime(date("m/d/Y H:i:s", strtotime($raw_start)));
                        $pref_delivery_end = $this->mongo_db->converToMongodttime(date("m/d/Y H:i:s", strtotime($raw_end)));
                        $prod_delivery_date = $this->mongo_db->converToMongodttime(date($this->post('product_dilivery_date')));

                        //error_log("preferred date: ".$pref_date);
                        //error_log("start time: ".$pref_delivery_start);
                        //error_log("end time: ".$pref_delivery_end);
                        //error_log("delivery date: ".$prod_delivery_date);

                        $insertData = [
                            'url' => $this->post('prodect_url'),
                            'order_type' => $this->post('order_type'), // manual/url
                            'product_image' => $this->post('product_image'),
                            'name' => $this->post('prodect_name'),
                            'admin_id' => (string)$this->post('admin_id'),
                            'preferred_date' => $pref_date,
                            'preferred_dilivery_start_time' => $pref_delivery_start,
                            'preferred_dilivery_end_time' => $pref_delivery_end,
                            'quantity' => (float)$this->post('quantity'),
                            'box_status' => $this->post('box_status'),
                            'vip_service_status' => $this->post('vip_service_status'),
                            'vip_service_fee' => (float)$this->post('vip_service_fee'),
                            'product_price' => (float)$this->post('product_price'),
                            'product_discription' => $this->post('product_discription'),
                            'product_weight' => (float)$this->post('product_weight'),
                            'product_buy_country_name' => $this->post('product_buy_country_name'),
                            'product_buy_city_name' => $this->post('product_buy_city_name'),
                            'product_dilivery_country_name' => $this->post('product_dilivery_country_name'),
                            'product_dilivery_city_name' => $this->post('product_dilivery_city_name'),
                            'product_dilivery_date' => $prod_delivery_date,
                            'flighteno_cost' => (float)$this->post('flighteno_cost'),
                            'status' => 'new',
                            'tax' => (float)$this->post('tax'),
                            'order_created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                            'use_item_for_testing' => $this->post('use_item_for_testing'),
                            'open_box_check_phisical_apperance' => $this->post('open_box_check_phisical_apperance'),
                            'product_type' => (string)$this->post('product_type'),
                            'payment_status' => 'pending',
                            'estimated_dilivery_fee' => (float)$this->post('estimated_dilivery_fee'),
                            'offer_sender_account_ids' => [],
                            'Total' => (float)$totalCharges,

                        ];
                        $db = $this->mongo_db->customQuery();
                        $order_status = $db->orders->insertOne($insertData);
                        $activityData = [
                            'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                            'message' => 'created order',
                            'admin_id' => (string)$this->post('admin_id')
                        ];
                        $this->Mod_activity->saveActivity($activityData);//lock the activity                        
                        
                        $order_id = $order_status->getInsertedId();                  
                        $this->Mod_order->insertOrderDetailsHistory($order_id, $this->post('admin_id'), 'new');

                        $totalCharges = (float)((float)$this->post('product_price')) + (float)$this->post('estimated_dilivery_fee') + ((float)$this->post('vip_service_fee')) + ((float)$this->post('flighteno_cost')) + ((float)$this->post('tax'));
                        $responseArray = [

                            'order_number' => (string)$order_status->getInsertedId(),
                            'order_price' => (float)$this->post('product_price'),
                            'estimated_dilivery_fee' => $this->post('estimated_dilivery_fee'),
                            'vip_service_fee' => (float)$this->post('vip_service_fee'),
                            'flighteno_cost' => (float)$this->post('flighteno_cost'),
                            'tax' => (float)$this->post('tax'),
                            'Total' => $totalCharges,
                            'status' => 'Your Orders Is successfully Created'
                        ];

                        $this->set_response($responseArray, REST_Controller::HTTP_CREATED);

                    } else {

                        $response_array['status'] = 'Payload is Missing!!!!!!!!!!!';
                        $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                    }

                } else {

                    $response_array['status'] = 'Authorization Failed !!!!!!!!!!!';
                    $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                }
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }

        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
        

    }//end function


    public function getOrderDetailsUsingURL_post()
    {

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                $received_Token = $received_Token_Array['Authorization'];

            }

            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {
                if ($this->post()) {

                    $url = $this->post('url');
                    file_put_contents("php://stderr", "URL: ".$url."\n");

                    $result = strpos($url, 'https://www.ebay.com');
                    if ($result !== false) {
                        $html = file_get_html($url);

                        $price = $html->find("span#convbidPrice", 0)->plaintext;
                        $price_data_souce = 0;
                        if (empty($price)) {  //notranslate

                            $price = $html->find("span#convbinPrice", 0)->plaintext;
                            $price_data_souce = 1;
                        }
                        if (empty($price)) {

                            $price = $html->find("span.notranslate", 0)->plaintext;
                            $price_data_souce = 2;
                        }

                        if (empty($price)) {

                            $price = $html->find("div.display-price", 0)->plaintext;
                            $price_data_souce = 3;
                        }

                        $name = $html->find("span#vi-lkhdr-itmTitl", 0)->plaintext;
                        $name_data_source = 0;

                        //  Get product name for 2nd view template
                        if (empty($name)) {
                            $name = $html->find(".product-title", 0)->plaintext;
                            $name_data_source = 1;
                        }


                       /* $data = $html->find("div.fs_imgc", 0);  //get all images
                        preg_match('@src="([^"]+)"@', $data, $match);
                        $img_src = array_pop($match);
                        */

                        //Fix to get true image
                        $img_true_url = $html->find("img[id=icImg]", 0);
                        preg_match('@src="([^"]+)"@', $img_true_url, $match_img_url);
                        $img_src_highres = array_pop($match_img_url);
                        $img_src_highres = str_replace('s-l300', 's-l1600', $img_src_highres);
                        $img_data_source = 0;

                        // Get image for 2nd view template
                        if (empty($img_src_highres)) {
                            $data = $html->find(".vi-image-gallery__image", 0);
                            preg_match( '@src=([^"]+ )@' , $data, $match );
                            $img_src_highres = trim(array_pop($match));
                            $img_data_source = 1;
                        }
                      
                        /*
                        file_put_contents("php://stderr", "SCRAPED IMAGE #2x\n");
                        file_put_contents("php://stderr", "img tag:".$img_true_url."\n");
                        file_put_contents("php://stderr", "img source:".$img_src_highres."\n");
                        */

                        // Get store name for 1st template
                        $store_name = $html->find("div.ux-seller-section__item--seller>a>span", 0)->plaintext;
                        $store_name_source = 0;

                        // Get store name for 2nd template
                        if (empty($store_name)) {
                            $store_name = $html->find("div.seller-persona>span>a", 0)->plaintext;
                            $store_name_source = 1;
                        }

                        $price = str_replace("US $", "", $price);
                        $price = str_replace("(including shipping)", "", $price);
                        $price = str_replace("$", "", $price);
                        $reponseArray = [
                            'url' => $this->post('url'),
                            'product_image' => $img_src_highres,
                            'price' => (float)$price,
                            'name' => $name,
                            'store_name' => $store_name,
                            // 'price_data_souce' => $price_data_souce,
                            // 'name_data_source' => $name_data_source,
                            // 'img_data_source' => $img_data_source,
                            // 'store_name_source' => $store_name_source,
                        ];
                        $this->set_response($reponseArray, REST_Controller::HTTP_CREATED);
                    } else {

                        $response_array['status'] = 'URL is not Valid!';
                        $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                    }
                } else {

                    $response_array['status'] = 'PayLoad is Missing!!!!!!!!!';
                    $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                }

            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }

    }//end function


    public function loginMobile_post()
    {

        $db = $this->mongo_db->customQuery();
        $usernameAuth = md5($this->input->server('PHP_AUTH_USER'));
        $passwordAuth = md5($this->input->server('PHP_AUTH_PW'));

        if ($this->post('email_address') && $this->post('password')) {

            $validateCredentials = varify_basic_auth($passwordAuth, $usernameAuth);
            //$validateCredentials=true;
            if ($validateCredentials == true || $validateCredentials == 1) {

                $email = strtolower(trim($this->post('email_address')));
                $password = trim($this->post('password'));


                $lookup = [

                    [
                        '$match' => [

                            'email_address' => $email,
                            'user_role' => 2,
                            'flag_reported' => ['$exists' => false],
                        ]
                    ],

                    [
                        '$project' => [

                            '_id' => ['$toString' => '$_id'],
                            'first_name' => '$first_name',
                            'last_name' => '$last_name',
                            'username' => '$username',
                            'email_address' => '$email_address',
                            'stripe_customer_id' => '$stripe_customer_id',
                            'stripe_account_id' => '$stripe_account_id',
                            'profile_image' => '$profile_image',
                            'profile_status' => '$profile_status',
                            'password' => '$password',
                            'conected_account_id' => '$conected_account_id',
                            'customer_id' => '$customer_id',
                            'Geometry' => '$Geometry',
                            ' Postal Code ' => '$postal_code',
                            'country' => '$country',
                            'created_date' => '$created_date',
                            'full_name' => '$full_name',
                            'last_login_time' => '$last_login_time',
                            'location' => '$location',
                            'login_status' => '$login_status',                            
                            'kyc_status_verified' => ['$ifNull' => ['$kyc_status_verified', false] ],
                            'phone_number' => '$phone_number',
                            'rating' => '$rating',
                            'signup_source' => '$signup_source',
                            'status' => '$status',
                            'user_role' => '$user_role'
                        ]
                    ]

                ];

                $getUser = $db->users->aggregate($lookup);
                $userData = iterator_to_array($getUser);

                $userData = $userData[0];
                $getRatting = $this->Mod_rating->getUserAvgRatting($userData['_id']);
                $userData['rating'] = $getRatting;

                if (count($userData) > 0) {

                    //removed temporarily for testing
                    //if (md5($password) == $userData['password']) {
                    if (count($userData) > 0) {
                        if(!isset($userData['stripe_customer_id'])) {
                            $customer = $this->stripe->customers->create(['name' => $userData['full_name'],'email' => $userData['email_address']]);
                            $stripe_customer_id = $customer->id;
                            $userData['stripe_customer_id'] = $stripe_customer_id;
                            $db->users->updateOne(['email_address' => $userData['email_address']], ['$set' => ['stripe_customer_id' => $stripe_customer_id]]);
                        }
                        if(!isset($userData['stripe_account_id'])) {
                            $userData['stripe_account_id'] = '';
                        }
                        makeLoginStatusTrue($email);
                        $token = $this->Mod_isValidUser->GenerateJWT((string)$userData['_id']);
                        $response_array = [

                            'data' => $userData,
                            'status' => 'successfully Login!!!!!!!!!!',
                            'token' => $token,
                        ];

                        $this->set_response($response_array, REST_Controller::HTTP_CREATED);
                    } else {

                        $response_array['status'] = 'Incorrect Pasword!!!!!!!';
                        $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                    }
                } else {

                    $response_array['status'] = 'Wrong Credentials or User Mark as Flag!';
                    $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                }
            } else {

                $response_array['status'] = 'Authorization Failed !!!!!!!!!!!';
                $response_array['validateCredentials'] = $validateCredentials;

                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'PayLoad is Missing!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }

    }//end controller

    //user signup
    public function RegisterUser_post()
    {

        $db = $this->mongo_db->customQuery();
        $checkEmailStatus = isEmailExists($this->post('email'));
        $checkPhoneStatus = isPhoneExists((string)$this->post('phone_number'));

        if ($checkEmailStatus == true || $checkEmailStatus == 1 || $checkPhoneStatus == true || $checkPhoneStatus == 1) {

            $response_array = [
                'status' => 'Email or Phone Number Already Exists Please Try With Another Email or Phone Number!',
                'type' => 400
            ];

            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        } else {
            //user information check
            $userLocationData = $this->Mod_users->getUserLocation();
            $customer = $this->stripe->customers->create(['name' => (string)$this->post('full_name'),
                'email' => strtolower(trim((string)$this->post('email')))]);
            $stripe_customer_id = $customer->id;
            $signupData = [

                'first_name' => (string)$this->post('first_name'),
                'full_name' => (string)$this->post('full_name'),
                'last_name' => (string)$this->post('last_name'),
                'email_address' => strtolower(trim((string)$this->post('email'))),
                'phone_number' => (string)$this->post('phone_number'),
                'password' => md5((string)$this->post('password')),
                'user_role' => 2,
                'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                'login_status' => true,
                'kyc_status_verified' => false,
                'profile_image' => (string)$this->post('profile_image'),
                'last_login_time' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                'status' => 'user',
                'status_update_time' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                'profile_status' => '',  //buyer / traveler
                'location' => $userLocationData['city'] . ',' . $userLocationData['region'] . ', ' . $userLocationData['country'],
                'Geometry' => $userLocationData['loc'],
                'country' => $userLocationData['country'],
                'Postal Code' => $userLocationData['postal'],
                'country_code' => (string)$this->post('country_code'),
                'stripe_customer_id' => $stripe_customer_id,
            ];

            $checkStatus = $db->users->insertOne($signupData);

            if ($checkStatus->getInsertedId()) {

                $token = $this->Mod_isValidUser->GenerateJWT((string)$checkStatus->getInsertedId());

                $userData = [

                    '_id' => (string)$checkStatus->getInsertedId(),
                    'first_name' => (string)$this->post('first_name'),
                    'last_name' => (string)$this->post('last_name'),
                    'full_name' => (string)$this->post('full_name'),
                    'email_address' => (string)$this->post('email'),
                    'phone_number' => (string)$this->post('phone_number'),
                    'password' => md5((string)$this->post('password')),
                    'user_role' => 2,
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'login_status' => true,
                    'kyc_status_verified' => false,
                    'profile_image' => (string)$this->post('profile_image'),
                    'last_login_time' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'status' => 'user',
                    'status_update_time' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'location' => $userLocationData['city'] . ',' . $userLocationData['region'] . ', ' . $userLocationData['country'],
                    'Geometry' => $userLocationData['loc'],
                    'country' => $userLocationData['country'],
                    'Postal Code' => $userLocationData['postal'],
                    'country_code' => (string)$this->post('country_code'),
                    'stripe_customer_id' => $stripe_customer_id,
                ];

                $getRatting = $this->Mod_rating->getUserAvgRatting((string)$checkStatus->getInsertedId());
                $userData['rating'] = $getRatting;

                //lock the activity
                $activityData = [
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'message' => 'Sign Up',
                    'admin_id' => (string)$checkStatus->getInsertedId()
                ];
                $this->Mod_activity->saveActivity($activityData);
                //end lock the activity

                $response_array = [

                    'data' => $userData,
                    'status' => 'Your Account is Successfully Created',
                    'type' => 200,
                    'token' => $token,

                ];
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array = [
                    'status' => 'SomeThing Wrong With your DB',
                    'type' => 400
                ];

                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }//end signup


    public function logoutMobile_post()
    {

        $db = $this->mongo_db->customQuery();
        $admin_id = $this->post('admin_id');

        if (!empty($admin_id)) {

            $db->users->updateOne(['_id' => $this->mongo_db->mongoId($admin_id)], ['$set' => ['login_status' => false]]);

            $response_array = [
                'status' => 'SucessFully Logout',
                'type' => 200
            ];
            $this->set_response($response_array, REST_Controller::HTTP_CREATED);


        } else {

            $response_array = [
                'status' => 'admin_id is messing in your payload',
                'type' => 400
            ];

            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }

    }//end


    public function forgetPassword_post()
    {

        $db = $this->mongo_db->customQuery();
        $usernameAuth = md5($this->input->server('PHP_AUTH_USER'));
        $passwordAuth = md5($this->input->server('PHP_AUTH_PW'));

        if ($this->post('phone_number') && $this->post('password') && $this->post('confirmed_password')) {

            $checkUser = isUserExists($this->post('phone_number'));

            if ($checkUser == true || $checkUser == 1) {

                $validateCredentials = varify_basic_auth($passwordAuth, $usernameAuth);
                if ($validateCredentials == true || $validateCredentials == 1) {

                    $phone_number = (string)$this->post('phone_number');
                    $password = md5($this->post('password'));
                    $confirmed_passwrd = md5($this->post('confirmed_password'));

                    if ($password == $confirmed_passwrd) {

                        $db->users->updateOne(['phone_number' => $phone_number], ['$set' => ['password' => $password]]);

                        $response_array = [
                            'status' => 'Your Password Is Successfully Updated!',
                            'type' => 200
                        ];
                        $this->set_response($response_array, REST_Controller::HTTP_CREATED);


                    } else {

                        $response_array = [
                            'status' => 'Password and Confirmed_Password are not Matched!',
                            'type' => 400
                        ];
                        $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                    }

                } else {

                    $response_array = [
                        'status' => 'Authorization Failed!',
                        'type' => 400,
                    ];
                    $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                }
            } else {

                $response_array = [
                    'status' => 'User Not Exists Against This Number',
                    'type' => 400
                ];
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }

        } else {

            $response_array = [
                'status' => 'Payload is Missing!',
                'type' => 400,
            ];
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }

    }//end


    public function updatePassword_post()
    {

        $db = $this->mongo_db->customQuery();
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                $received_Token = $received_Token_Array['Authorization'];

            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                if ($this->post('admin_id') && $this->post('password') && $this->post('confirmed_password')) {

                    $checkUser = isUserExistsUsingAdminId((string)$this->post('admin_id'));

                    if ($checkUser == true || $checkUser == 1) {

                        $admin_id = (string)$this->post('admin_id');
                        $password = md5($this->post('password'));
                        $confirmed_passwrd = md5($this->post('confirmed_password'));
                        $oldPassword = md5($this->post('oldPassword'));

                        $checking = checkOldPasswordIsValid($admin_id, $oldPassword);

                        if ($checking == true || $checking == 1) {
                            if ($password == $confirmed_passwrd) {

                                $db->users->updateOne(['_id' => $this->mongo_db->mongoId($admin_id)], ['$set' => ['password' => $password]]);
                                //lock the activity
                                $activityData = [
                                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                                    'message' => 'Update Password',
                                    'admin_id' => $admin_id
                                ];
                                $this->Mod_activity->saveActivity($activityData);
                                //end lock the activity

                                $response_array = [
                                    'status' => 'Your Password Is Successfully Updated!!!!!!!!!!!',
                                    'type' => 200
                                ];
                                $this->set_response($response_array, REST_Controller::HTTP_CREATED);

                            } else {

                                $response_array = [
                                    'status' => 'Password and Confirmed_Password are not Matched!!!!!!!!!!!',
                                    'type' => 400
                                ];
                                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                            }
                        } else {

                            $response_array = [
                                'status' => 'Your Old Password is Wrong!!!!!!!!!!',
                                'type' => 400
                            ];
                            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                        }
                    } else {

                        $response_array = [
                            'status' => 'User Not Exists Against This Number',
                            'type' => 400
                        ];
                        $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                    }
                } else {

                    $response_array = [
                        'status' => 'Payload is Missing!!!!!!!!!!!',
                        'type' => 400,
                    ];
                    $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                }
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end


    public function qrcode_get()
    {

        $data['img_url'] = "";

        $this->load->library('ciqrcode');
        $qr_image = rand() . '.png';


        $arrayForQR = [

            'order_number' => 111,
            'order_price' => (float)$this->post('product_price'),
            // 'estimice'               => (float)$this->post('product_price'),
        ];

        $params['data'] = $arrayForQR;
        $params['level'] = 'H';
        $params['size'] = 10;
        $params['savename'] = FCPATH . 'tes.png';

        $status = $this->ciqrcode->generate($arrayForQR);

        echo $status;

        if ($this->ciqrcode->generate($arrayForQR)) {
            $data['img_url'] = $qr_image;


            echo $qr_image;


        } else {

            echo '<br>QR code not creating Error';
        }

    }//end function


    public function profileStatusUpdate_post()
    {

        $db = $this->mongo_db->customQuery();
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $admin_id = (string)$this->post('admin_id');
                $profile_status = (string)$this->post('profile_status');
                //lock the activity
                $activityData = [
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'message' => 'Profile status update',
                    'admin_id' => $admin_id
                ];
                $this->Mod_activity->saveActivity($activityData);
                //end lock the activity
                $db->users->updateOne(['_id' => $this->mongo_db->mongoId($admin_id)], ['$set' => ['profile_status' => $profile_status]]);

                $response_array = [
                    'status' => 'SuccessFully Updated!!!!!!!!!!!',
                    'profile_status' => (string)$this->post('profile_status'),
                    'type' => 200
                ];
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);

            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }

        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }

    }//end function


    //social signup
    public function RegisterUserUsingSocial_post()
    {

        $usernameAuth = md5($this->input->server('PHP_AUTH_USER'));
        $passwordAuth = md5($this->input->server('PHP_AUTH_PW'));

        $validateCredentials = varify_basic_auth($passwordAuth, $usernameAuth);
        if ($validateCredentials == true || $validateCredentials == 1) {

            $db = $this->mongo_db->customQuery();
            $checkEmailStatus = socialExistsCheck((string)$this->post('email_address'), (string)$this->post('signup_source'));

            if ($checkEmailStatus['status'] == true) {

                $userData = $checkEmailStatus['data'];
                $userData['rating'] = $this->Mod_rating->getUserAvgRatting((string)$admin_id);

                $token = $this->Mod_isValidUser->GenerateJWT((string)$userData['_id']);

                $response_array = [
                    'status' => 'Login Success',
                    'data' => $checkEmailStatus['data'],
                    'type' => 200,
                    'token' => $token,
                ];

                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            } else {

                //user information check
                $userLocationData = $this->Mod_users->getUserLocation();

                $signupData = [
                    'full_name' => (string)$this->post('full_name'),
                    'first_name' => (string)$this->post('first_name'),
                    'email_address' => (string)$this->post('email_address'),
                    'user_role' => 2,
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'login_status' => true,                    
                    'password' => md5((string)$this->post('password')),
                    'profile_image' => (string)$this->post('profile_image'),
                    'last_login_time' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'status' => 'user',
                    'status_update_time' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'profile_status' => '',  //buyer / traveler
                    'signup_source' => (string)$this->post('signup_source'), //google / facebook
                    'location' => $userLocationData['city'] . ',' . $userLocationData['region'] . ', ' . $userLocationData['country'],
                    'Geometry' => $userLocationData['loc'],
                    'country' => $userLocationData['country'],
                    'Postal Code' => $userLocationData['postal'],
                    'country_code' => (string)$this->post('country_code'),
                ];

                $where['email_address'] = (string)$this->post('email_address');

                $checkStatus = $db->users->updateOne($where, ['$set' => $signupData], ['upsert' => true]);

                if ($checkStatus->getUpsertedId()) {

                    $admin_id = (string)$checkStatus->getUpsertedId();
                } else {
                    $admin_id = $checkEmailStatus['id'];
                }


                if (!empty($admin_id)) {

                    $token = $this->Mod_isValidUser->GenerateJWT((string)$admin_id);

                    $userData = [

                        '_id' => (string)$admin_id,
                        'full_name' => (string)$this->post('full_name'),
                        'first_name' => (string)$this->post('first_name'),
                        'email_address' => (string)$this->post('email_address'),
                        'phone_number' => $checkEmailStatus['phone_number'],
                        'password' => md5((string)$this->post('password')),
                        'user_role' => 2,
                        'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                        'login_status' => true,                        
                        'profile_image' => (string)$this->post('profile_image'),
                        'last_login_time' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                        'status' => 'user',
                        'status_update_time' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                        'signup_source' => $this->post('signup_source'),
                        'location' => $userLocationData['city'] . ',' . $userLocationData['region'] . ', ' . $userLocationData['country'],
                        'Geometry' => $userLocationData['loc'],
                        'country' => $userLocationData['country'],
                        'Postal Code' => $userLocationData['postal'],
                        'country_code' => (string)$this->post('country_code'),
                    ];

                    $getRatting = $this->Mod_rating->getUserAvgRatting((string)$admin_id);
                    $userData['rating'] = $getRatting;

                    //lock the activity
                    $activityData = [
                        'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                        'message' => 'Sign up',
                        'admin_id' => $admin_id
                    ];
                    $this->Mod_activity->saveActivity($activityData);
                    //end lock the activity

                    $response_array = [

                        'data' => $userData,
                        'status' => 'Your Account is Successfully Created',
                        'type' => 200,
                        'token' => $token,

                    ];
                    $this->set_response($response_array, REST_Controller::HTTP_CREATED);
                } else {

                    $response_array = [
                        'status' => 'SomeThing Wrong With your DB',
                        'type' => 400
                    ];

                    $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                }
            }

        } else {

            $response_array = [
                'status' => 'Authorization Failed!!',
                'type' => 400
            ];
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end signup 


    public function markUserAsFlag_post()
    {

        $db = $this->mongo_db->customQuery();
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                $received_Token = $received_Token_Array['Authorization'];

            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $admin_id = (string)$this->post('admin_id');

                $db->users->updateOne(['_id' => $this->mongo_db->mongoId($admin_id)], ['$set' => ['flag_reported' => 'reported']]);
                //lock the activity
                $activityData = [
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'message' => 'Mark flag',
                    'admin_id' => $admin_id
                ];
                $this->Mod_activity->saveActivity($activityData);
                //end lock the activity
                $response_array['status'] = 'User Successfully Reported as Flag User!';
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }

        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }

    }//end function


    public function addTrip_post()
    {

        $db = $this->mongo_db->customQuery();
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $admin_id = $this->post('admin_id');
                $TravelingFrom = $this->post('Traveling_from');
                $city = $this->post('city_from');
                $TravelingTo = $this->post('Traveling_to');
                $cityTo = $this->post('city_to');
                $departDate = $this->post('depart_date');
                $depart_time = $this->post('depart_time');

                $trip = [

                    'admin_id' => (string)$this->post('admin_id'),
                    'Traveling_from' => $this->post('Traveling_from'),
                    'city' => $this->post('city_from'),
                    'Traveling_to' => $this->post('Traveling_to'),
                    'cityTo' => $this->post('city_to'),
                    'depart_date' => $this->mongo_db->converToMongodttime(date($this->post('depart_date'))),
                    'return_date' => $this->mongo_db->converToMongodttime(date($this->post('return_date'))),
                    'status' => 'new',
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s'))
                ];

                $checkStatus = $this->Mod_users->checkTripAlreadyExists($admin_id, $TravelingFrom, $TravelingTo, $departDate);
                $dynamicStatus = '';
                if ($checkStatus == true || $checkStatus == 1) {

                    $dynamicStatus = 'Trip is Already added with same Details';
                    $type = '500';
                } else {

                    $db->user_trip->insertOne($trip);
                    $dynamicStatus = 'Successfully Added';
                    $type = '200';
                }

                $lookUp = [
                    [
                        '$match' => [

                            'admin_id' => (string)$this->post('admin_id')
                        ]
                    ],

                    [
                        '$project' => [
                            '_id' => ['$toString' => '$_id'],
                            'admin_id' => '$admin_id',
                            'Traveling_from' => '$Traveling_from',
                            'city' => '$city',
                            'Traveling_to' => '$Traveling_to',
                            'cityTo' => '$cityTo',
                            'depart_date' => '$depart_date',
                            'status' => '$status',
                            'created_date' => '$created_date',
                        ]
                    ],
                    ['$sort' => ['created_date' => -1]]
                ];

                $trip = $db->user_trip->aggregate($lookUp);
                $userData = iterator_to_array($trip);

                $response_array = [
                    'status' => $dynamicStatus,
                    'user_trip' => $userData,
                    'type' => $type
                ];

                //lock the activity
                $activityData = [
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'message' => 'Trip add',
                    'admin_id' => $admin_id
                ];
                $this->Mod_activity->saveActivity($activityData);
                //end lock the activity

                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }

        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end function


    public function getUserTrip_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                $received_Token = $received_Token_Array['Authorization'];

            }

            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $admin_id = (string)$this->post('admin_id');

                $userTrips = $this->Mod_trip->getUserTrips($admin_id);

                $response_array = [
                    'status' => 'SuccussFully Fetched all Trips',
                    'type' => 200,
                    'user_trip' => $userTrips,
                ];
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);

            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }

        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }// end functions


    public function getUserOrdersOnTheBasisOnCountry_post()
    {

        $db = $this->mongo_db->customQuery();
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                $received_Token = $received_Token_Array['Authorization'];

            }

            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {
                $traveling_from = '';
                $traveling_to = '';
                $depart_dateGet = '';


                $admin_id = (string)$this->post('admin_id');

                $getLocations = [
                    [
                        '$match' => [
                            'admin_id' => $admin_id,
                            'status' => 'new',
                            '$or' => [
                                ['accepted_buyer_admin_id' => ['$exists' => false]],
                                ['accepted_traveler_admin_id' => ['$exists' => false]]
                            ]
                        ]
                    ],

                    [
                        '$group' => [
                            '_id' => ['$toString' => '$_id'],
                            'traveling_from' => ['$first' => '$Traveling_from'],
                            'traveling_to' => ['$first' => '$Traveling_to'],
                            'depart_date' => ['$first' => '$depart_date']
                        ]
                    ],
                    [
                        '$sort' => ['created_date' => -1, '_id' => -1]
                    ],


                    [
                        '$limit' => 1
                    ],
                ];

                $trip1 = $db->user_trip->aggregate($getLocations);
                $tripLocation = iterator_to_array($trip1);

                if (count($tripLocation) > 0) {

                    $traveling_from = $tripLocation[0]['traveling_from'];
                    $traveling_to = $tripLocation[0]['traveling_to'];
                    $depart_dateGet = $tripLocation[0]['depart_date'];

                    $lookup = [
                        [
                            '$match' => [

                                'product_buy_country_name' => ['$regex' => $traveling_from, '$options' => 'si'],
                                'product_dilivery_country_name' => ['$regex' => $traveling_to, '$options' => 'si'],
                                'status' => 'new',
                                'preferred_date' => ['$gte' => $depart_dateGet]
                            ]
                        ],

                        [
                            '$project' => [

                                '_id' => ['$toString' => '$_id'],
                                'admin_id' => '$admin_id',
                                'url' => '$url',
                                'order_type' => '$order_type',
                                'product_image' => '$product_image',
                                'name' => '$name',
                                'preferred_date' => '$preferred_date',
                                'preferred_dilivery_start_time' => '$preferred_dilivery_start_time',
                                'preferred_dilivery_end_time' => '$preferred_dilivery_end_time',
                                'quantity' => '$quantity',
                                'box_status' => '$box_status',
                                'vip_service_status' => '$vip_service_status',
                                'vip_service_fee' => '$vip_service_fee',
                                'product_price' => '$product_price',
                                'product_discription' => '$product_discription',
                                'product_weight' => '$product_weight',
                                'product_buy_country_name' => '$product_buy_country_name',
                                'product_buy_city_name' => '$product_buy_city_name',
                                'product_dilivery_country_name' => '$product_dilivery_country_name',
                                'product_dilivery_city_name' => '$product_dilivery_city_name',
                                'product_dilivery_date' => '$product_dilivery_date', 
                                'flighteno_cost' => '$flighteno_cost',
                                'status' => '$status',
                                'tax' => '$tax',
                                'order_created_date' => '$order_created_date',
                                'use_item_for_testing' => '$use_item_for_testing',
                                'open_box_check_phisical_apperance' => '$open_box_check_phisical_apperance',
                                'product_type' => '$product_type',
                                'payment_status' => '$payment_status',
                                'estimated_dilivery_fee' => '$estimated_dilivery_fee',
                                'Total' => '$Total',
                                'offer_sender_account_ids' => '$offer_sender_account_ids',
                                'rated_admin_id' => '$rated_admin_id'

                            ]
                        ],

                        [
                            '$lookup' => [
                                "from" => "users",
                                "let" => [
                                    "admin_id" => ['$toObjectId' => '$admin_id']
                                ],
                                "pipeline" => [
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
                                            '_id' => ['$toString' => '$_id'],
                                            'profile_image' => '$profile_image',
                                            'full_name' => '$full_name'
                                        ]
                                    ]
                                ],
                                'as' => 'profile_data'
                            ]
                        ],
                        [
                            '$sort' => ['order_created_date' => -1]
                        ]

                    ];

                    $trip = $db->orders->aggregate($lookup);
                    $orderRes = iterator_to_array($trip);
                }
                $response_array = [
                    'status' => 'Successfully Fetched',
                    'type' => '200',
                    'orders' => $orderRes
                ];

                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end function 


    public function getUserOrdersOnTheBasisOnCountryFilter_post()
    {        
        $db = $this->mongo_db->customQuery();
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                $received_Token = $received_Token_Array['Authorization'];

            }

            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {
                $traveling_from = '';
                $traveling_to = '';
                $depart_dateGet = '';


                $admin_id = (string)$this->post('admin_id');

                $getLocations = [
                    [
                        '$match' => [
                            'admin_id' => $admin_id,
                            'status' => 'new',
                            '$or' => [
                                ['accepted_buyer_admin_id' => ['$exists' => false]],
                                ['accepted_traveler_admin_id' => ['$exists' => false]]
                            ]
                        ]
                    ],

                    [
                        '$group' => [
                            '_id' => ['$toString' => '$_id'],
                            'traveling_from' => ['$first' => '$Traveling_from'],
                            'traveling_to' => ['$first' => '$Traveling_to'],
                            'depart_date' => ['$first' => '$depart_date']
                        ]
                    ],
                    [
                        '$sort' => ['created_date' => -1, '_id' => -1]
                    ],


                    [
                        '$limit' => 1
                    ],
                ];

                $trip1 = $db->user_trip->aggregate($getLocations);
                $tripLocation = iterator_to_array($trip1);

                if (count($tripLocation) > 0) {

                    $traveling_from = $tripLocation[0]['traveling_from'];
                    $traveling_to = $tripLocation[0]['traveling_to'];
                    $depart_dateGet = $tripLocation[0]['depart_date'];
                    
                    $search['product_buy_country_name'] = ['$regex' => $traveling_from, '$options' => 'si'];
                    $search['product_dilivery_country_name'] = ['$regex' => $traveling_to, '$options' => 'si'];
                    $search['status'] = 'new';
                    $search['preferred_date'] = ['$gte' => $depart_dateGet];  
                    
                    if (!empty($this->post('product_type'))) {
                        $search['product_type'] = ['$regex' => trim($this->post('product_type')), '$options' => 'si'];
                    }

                    if (!empty($this->post('product_name'))) {
                        $search['name'] = ['$regex' => trim($this->post('product_name')), '$options' => 'si'];
                    }

                    if (!empty($this->post('store_name'))) {
                        $search['store_name'] = ['$regex' => trim($this->post('store_name')), '$options' => 'si'];
                    }
                    
                    if (((($this->post('starting_price') === '0')) or (!empty($this->post('starting_price')))) && ((($this->post('ending_price') === '0')) or (!empty($this->post('ending_price'))))){                    
                        $start_price = (float)($this->post('starting_price'));
                        $end_price = (float)($this->post('ending_price'));                    
                        $search['product_price'] = ['$gte' => $start_price, '$lte' => $end_price];                        
                    }

                    if (((($this->post('starting_estimated_dilivery_fee') === '0')) or (!empty($this->post('starting_estimated_dilivery_fee')))) && ((($this->post('ending_estimated_dilivery_fee') === '0')) or (!empty($this->post('ending_estimated_dilivery_fee'))))){                                        
                        $start_estimated_dilivery_fee = (float)$this->post('starting_estimated_dilivery_fee');
                        $end_estimated_dilivery_fee = (float)$this->post('ending_estimated_dilivery_fee');
                        $search['estimated_dilivery_fee'] = ['$gte' => $start_estimated_dilivery_fee, '$lte' => $end_estimated_dilivery_fee];
                    }

                    if (!empty($this->post('sorted_by'))) {
                        $sorted_by = trim($this->post('sorted_by'));
                    }
                    else {
                        $sorted_by = 'order_created_date';
                    }
                    if (!empty($this->post('sort'))) {
                        $sort = trim((float)$this->post('sort'));
                        $sort = (int)$sort;
                    }
                    else {
                        $sort = 1;
                    }                    

                    $lookup = [
                        [
                            '$match' => $search
                        ],

                        [
                            '$project' => [

                                '_id' => ['$toString' => '$_id'],
                                'admin_id' => '$admin_id',
                                'url' => '$url',
                                'order_type' => '$order_type',
                                'product_image' => '$product_image',
                                'name' => '$name',
                                'preferred_date' => '$preferred_date',
                                'preferred_dilivery_start_time' => '$preferred_dilivery_start_time',
                                'preferred_dilivery_end_time' => '$preferred_dilivery_end_time',
                                'quantity' => '$quantity',
                                'box_status' => '$box_status',
                                'vip_service_status' => '$vip_service_status',
                                'vip_service_fee' => '$vip_service_fee',
                                'product_price' => '$product_price',
                                'product_discription' => '$product_discription',
                                'product_weight' => '$product_weight',
                                'product_buy_country_name' => '$product_buy_country_name',
                                'product_buy_city_name' => '$product_buy_city_name',
                                'product_dilivery_country_name' => '$product_dilivery_country_name',
                                'product_dilivery_city_name' => '$product_dilivery_city_name',
                                'product_dilivery_date' => '$product_dilivery_date',
                                'flighteno_cost' => '$flighteno_cost',
                                'status' => '$status',
                                'tax' => '$tax',
                                'order_created_date' => '$order_created_date',
                                'use_item_for_testing' => '$use_item_for_testing',
                                'open_box_check_phisical_apperance' => '$open_box_check_phisical_apperance',
                                'product_type' => '$product_type',
                                'payment_status' => '$payment_status',
                                'estimated_dilivery_fee' => '$estimated_dilivery_fee',
                                'Total' => '$Total',
                                'offer_sender_account_ids' => '$offer_sender_account_ids',
                                'rated_admin_id' => '$rated_admin_id',                       
                                'store_name' => '$store_name'

                            ]
                        ],

                        [
                            '$lookup' => [
                                "from" => "users",
                                "let" => [
                                    "admin_id" => ['$toObjectId' => '$admin_id']
                                ],
                                "pipeline" => [
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
                                            '_id' => ['$toString' => '$_id'],
                                            'profile_image' => '$profile_image',
                                            'full_name' => '$full_name'
                                        ]
                                    ]
                                ],
                                'as' => 'profile_data'
                            ]
                        ],
                        [
                            //'$sort' => ['order_created_date' => -1]
                            '$sort' => [$sorted_by => $sort]
                        ]

                    ];

                    $trip = $db->orders->aggregate($lookup);
                    $orderRes = iterator_to_array($trip);
                }
                $response_array = [
                    'status' => 'Successfully Fetched',
                    'type' => '200',
                    'orders' => $orderRes
                ];

                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end function 



    public function filterApi_post()
    {

        $db = $this->mongo_db->customQuery();
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                $received_Token = $received_Token_Array['Authorization'];

            }

            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            $search = [];
            if (!empty($tokenArray->admin_id)) {

                if (!empty($this->post('product_type'))) {
                    $search['product_type'] = ['$regex' => trim($this->post('product_type')), '$options' => 'si'];
                }

                if (!empty($this->post('product_name'))) {

                    $search['name'] = ['$regex' => trim($this->post('product_name')), '$options' => 'si'];
                }
                if (!empty($this->post('store_name'))) {

                    $search['store_name'] = ['$regex' => trim($this->post('store_name')), '$options' => 'si'];
                }
                if (!empty($this->post('admin_id'))) {

                    $search['admin_id'] = ['$regex' => trim($this->post('admin_id')), '$options' => 'si'];
                }
                if (!empty($this->post('starting_price')) && !empty($this->post('ending_price'))) {                 
                    $start_price = (float)$this->post('starting_price');
                    $end_price = (float)$this->post('ending_price');
                    $search['product_price'] = ['$gte' => $start_price, '$lte' => $end_price];
                }
            
                if (!empty($this->post('starting_estimated_dilivery_fee')) && !empty($this->post('ending_estimated_dilivery_fee'))) {                 
                    $start_estimated_dilivery_fee = (float)$this->post('starting_estimated_dilivery_fee');
                    $end_estimated_dilivery_fee = (float)$this->post('ending_estimated_dilivery_fee');
                    $search['estimated_dilivery_fee'] = ['$gte' => $start_estimated_dilivery_fee, '$lte' => $end_estimated_dilivery_fee];
                }
                                
                if (!empty($this->post('sorted_by'))) {
                    $sorted_by = trim($this->post('sorted_by'));
                }
                else {
                    $sorted_by = 'order_created_date';
                }
                if (!empty($this->post('sort'))) {
                    $sort = trim((float)$this->post('sort'));
                    $sort = (int)$sort;
                }
                else {
                    $sort = 1;
                }

                $search['Total'] = ['$exists' => true];

                $lookUp = [
                    [
                        '$match' => $search
                    ],
                    [
                        '$project' => [

                            '_id' => ['$toString' => '$_id'],
                            'url' => '$url',
                            'order_type' => '$order_type',
                            'product_image' => '$product_image',
                            'name' => '$name',
                            'admin_id' => '$admin_id',
                            'preferred_date' => '$preferred_date',
                            'preferred_dilivery_start_time' => '$preferred_dilivery_start_time',
                            'preferred_dilivery_end_time' => '$preferred_dilivery_end_time',
                            'quantity' => '$quantity',
                            'box_status' => '$box_status',
                            'vip_service_status' => '$vip_service_status',
                            'vip_service_fee' => '$vip_service_fee',
                            'product_price' => '$product_price',
                            'product_discription' => '$product_discription',
                            'product_weight' => '$product_weight',
                            'product_buy_country_name' => '$product_buy_country_name',
                            'product_buy_city_name' => '$product_buy_city_name',
                            'product_dilivery_country_name' => '$product_dilivery_country_name',
                            'product_dilivery_city_name' => '$product_dilivery_city_name',
                            'product_dilivery_date' => '$product_dilivery_date',
                            'store_name' => '$store_name',
                            'flighteno_cost' => '$flighteno_cost',
                            'status' => '$status',
                            'tax' => '$tax',
                            'order_created_date' => '$order_created_date',
                            'use_item_for_testing' => '$use_item_for_testing',
                            'open_box_check_phisical_apperance' => '$open_box_check_phisical_apperance',
                            'product_type' => '$product_type',
                            'payment_status' => '$payment_status',
                            'estimated_dilivery_fee' => '$estimated_dilivery_fee',
                            'offer_sender_account_ids' => '$offer_sender_account_ids',
                            'Total' => '$Total',
                            'rated_admin_id' => '$rated_admin_id'
                        ]
                    ],

                    [
                        '$lookup' => [
                            "from" => "users",
                            "let" => [
                                "admin_id" => ['$toObjectId' => '$admin_id']
                            ],
                            "pipeline" => [
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
                                        '_id' => ['$toString' => '$_id'],
                                        'profile_image' => '$profile_image',
                                        'full_name' => '$full_name'
                                    ]
                                ]
                            ],
                            'as' => 'profile_data'
                        ]
                    ],                    

                    [
                        '$sort' => [$sorted_by => $sort]
                    ]

                ];
                
                $result = $db->orders->aggregate($lookUp);
                $orders = iterator_to_array($result);

                $response_array = [
                    'status' => 'Data Fetched!',
                    'orders' => $orders
                ];
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);

            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end 


    public function getChatMessages_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $sender_id = (string)$this->post('admin_id');
                $getChats = $this->Mod_chat->getUserChats($sender_id);

                $response_array['status'] = 'Fetched Messages';
                $response_array['messagesData'] = $getChats;

                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    } //end function


    public function getOrdersPendingCancelledCompleted_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $admin_id = (string)$this->post('admin_id');
                $getOrders = $this->Mod_order->getOrders($admin_id);
                $response_array['status'] = 'Fetched Successfully!';
                $response_array['order'] = $getOrders;
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end function


    public function getTravelerOrdersPendingCompleted_post()
    {

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $admin_id = (string)$this->post('admin_id');
                $getOrders = $this->Mod_order->getTravelersOrders($admin_id);
                $response_array['status'] = 'Fetched Successfully!';
                $response_array['traveler_order'] = $getOrders;
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function offerAccepted_post()
    {//payment trasection and order status changed
        $db = $this->mongo_db->customQuery();

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $offer_id = (string)$this->post('offer_id');
                $status = $this->post('status');
                $payment_method_id = $this->post('payment_method_id');
                $getOrder = $this->Mod_order->acceptTheOfferAndChangeTheOrderStatus($offer_id, $status, $payment_method_id);

                $offerData = $this->Mod_order->getOfferDetails($offer_id);
                $reciver_admin_id = (string)$offerData[0]['traveler_id'];
                $sender_admin_id = (string)$offerData[0]['buyer_id'];
                $order_id = (string)$offerData[0]['order_id'];

                $full_name = $this->Mod_users->getUserFullName($sender_admin_id);
                $orderFullName = $this->Mod_order->getOrderName($order_id);

                if ($status == 'accept') {
                    //lock the activity
                    $activityData = [
                        'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                        'message' => 'Accept offer',
                        'admin_id' => (string)$offerData[0]['buyer_id']
                    ];
                    $this->Mod_activity->saveActivity($activityData);
                    // print_r("=============");

                    //end lock the activity
                    $type = "accept";
                    $status = "Offer is Accepted!";
                    $message = $full_name . ' has accepted your offer for ' . $orderFullName;
                    // print_r("=============");

                    //admin notification logs
                    $activityData1 = [
                        'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                        'message' => $full_name . ' has paid ' . $offerData[0]['total'] . ' for ' . $order_id,
                        'status' => 'pending',
                        'name' => $full_name,
                        'admin_id' => $sender_admin_id,
                        'order_id' => $order_id
                    ];
                    // print_r('--------------');

                    $db->admin_notification->insertOne($activityData1);
                    // print_r("=============");

                } else {

                    //lock the activity
                    $activityData = [
                        'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                        'message' => 'Reject offer',
                        'admin_id' => (string)$offerData[0]['buyer_id']
                    ];
                    $this->Mod_activity->saveActivity($activityData);
                    //end lock the activity
                    $type = "reject";
                    $status = "Offer is Rejected!";
                    $message = $full_name . ' has rejected your offer for ' . $orderFullName;
                }
                $this->Mod_users->sendNotification($reciver_admin_id, $message, $type, $sender_admin_id, $order_id);

                $response_array['order'] = $getOrder;
                $response_array['status'] = $status;
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }

    }//end function


    public function saveOfferDetails_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $arrayInserted = [
                    'order_price' => (float)$this->post('order_price'),
                    'estimateDelivery' => (float)$this->post('estimateDelivery'),
                    'vipServiceFee' => (float)$this->post('vipServiceFee'),
                    'flighteno_cost' => (float)$this->post('flighteno_cost'),
                    'tax' => (float)$this->post('tax'),
                    'total' => (float)$this->post('total'),
                    'status' => 'new',
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'trip_id' => (string)$this->post('tirpId')
                ];
                $offerId = $this->Mod_order->offerSaveForPayment($arrayInserted, $this->post('order_id'), $this->post('buyer_id'), $this->post('traveler_id'), $this->post('tirpId'));
                $response_array['status'] = 'Saved!';
                $response_array['offerId'] = $offerId;

                //lock the activity
                $activityData = [
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'message' => 'Send Offer',
                    'admin_id' => $this->post('traveler_id')
                ];
                $this->Mod_activity->saveActivity($activityData);
                //end lock the activity

                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end


    public function getRecentOrders_post()
    {

        $db = $this->mongo_db->customQuery();

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $admin_id = (string)$this->post('admin_id');
                $orders = $this->Mod_order->getRecentOrders($admin_id);
                $userProfileData = $this->Mod_users->getUserProfileStatus($admin_id);
                $orderHistory = $this->Mod_order->getRecentOrderNotificationHistory($admin_id);

                $response_array['status'] = 'Data Fetched!';
                $response_array['orders'] = $orders;
                $response_array['profile'] = $userProfileData;
                $response_array['order_history'] = $orderHistory;

                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }


    public function markOrderAsCancelled_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $order_id = (string)$this->post('order_id');
                $admin_id = (string)$this->post('admin_id');

                $orders = $this->Mod_order->makeAsCancelled($order_id, $admin_id);
                $this->Mod_order->insertOrderDetailsHistory($order_id, $admin_id, 'cancelled');
                if ($orders == true || $orders == 1) {

                    //lock the activity
                    $activityData = [
                        'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                        'message' => 'order Cancelled',
                        'admin_id' => (string)$admin_id
                    ];
                    $this->Mod_activity->saveActivity($activityData);
                    //end lock the activity
                    $response_array['status'] = 'SuccessFully Cancelled!';
                    $this->set_response($response_array, REST_Controller::HTTP_CREATED);
                } else {

                    $response_array['status'] = 'Not Cancelled Due to Some Reason!';
                    $this->set_response($response_array, REST_Controller::HTTP_CREATED);
                }
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end


    public function support_post()
    {
        $db = $this->mongo_db->customQuery();
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $insertSupport = [
                    'order_number' => (string)$this->post('order_number'),
                    'subject' => $this->post('subject'),
                    'message' => $this->post('message'),
                    'imageUrl' => $this->post('imageUrl'),
                    'admin_id' => $this->post('admin_id'),
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'status' => 'new',
                    'videoUrl' => $this->post('videoUrl'),
                    'profile_status' => $this->post('profile_status'),
                ];

                //lock the activity
                $activityData = [
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'message' => 'Complaint registered',
                    'admin_id' => $this->post('admin_id')
                ];
                $this->Mod_activity->saveActivity($activityData);
                //end lock the activity

                $this->Mod_order->saveSupport($insertSupport);
                $response_array['status'] = 'Support is Successfully Submitted!!';
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end

    public function getUserDetailsAndProfile_post()
    {

        //$db = $this->mongo_db->customQuery();

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $token = trim(str_replace("Bearer ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $admin_id = (string)$this->post('admin_id');

                $userData = $this->Mod_users->getUserDetail($admin_id);
                $userProfileData = $this->Mod_users->getUserProfileStatus($admin_id);

                $getRatting = $this->Mod_rating->getUserAvgRatting($admin_id);
                $response_array['rating'] = $getRatting;

                $response_array['status'] = 'Successfully fetched!!';
                $response_array['user_data'] = $userData;
                $response_array['profile'] = $userProfileData;
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function getUserDetails_post()
    {

        //$db = $this->mongo_db->customQuery();

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $token = trim(str_replace("Bearer ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $admin_id = (string)$this->post('admin_id');

                $userData = $this->Mod_users->getUserDetail($admin_id);
                //$userProfileData = $this->Mod_users->getUserProfileStatus($admin_id);

                $getRatting = $this->Mod_rating->getUserAvgRatting($admin_id);
                $response_array['rating'] = $getRatting;

                $response_array['status'] = 'Successfully fetched!!';
                $response_array['user_data'] = $userData;
                $response_array['profile'] = $userProfileData;
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }


    public function uploadPicAndReciptAgainstTheOrder_post()
    {
        $db = $this->mongo_db->customQuery();

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $order_id = (string)$this->post('order_id');
                $image = (string)$this->post('image');
                $recipt = (string)$this->post('recipt');

                $this->Mod_order->uploadImageAndRecipt($order_id, $image, $recipt);
                $response_array['status'] = 'Successfully Uploaded!';
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);

            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end


    public function orderMarkAsComplete_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {
                $order_id = (string)$this->post('order_id');
                $getDetails = $this->Mod_order->getDetailsForNotification($order_id);
                $orderFullName = $this->Mod_order->getOrderName($order_id);

                $reciver_admin_id = $getDetails[0]['traveler_id'];
                $sender_admin_id = $getDetails[0]['buyer_id'];

                $full_name = $this->Mod_users->getUserFullName($sender_admin_id);

                $type = "complete";
                $message = $full_name . ' has received the order for ' . $orderFullName;

                $this->Mod_users->sendNotification($reciver_admin_id, $message, $type, $sender_admin_id, $order_id);
                $this->Mod_order->markCompete($order_id);
                $this->Mod_order->insertOrderDetailsHistory($order_id, $sender_admin_id, 'complete');

                //lock the activity
                $activityData = [
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'message' => 'Order received',
                    'admin_id' => $sender_admin_id
                ];
                $this->Mod_activity->saveActivity($activityData);

                $activityData = [
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'message' => $full_name . ' has received ' . $order_id,
                    'status' => 'pending',
                    'name' => $full_name,
                    'admin_id' => $sender_admin_id,
                    'order_id' => $order_id
                ];
                $this->admin_notification->insertOne($activityData);
                // end lock the activity

                $response_array['status'] = 'Order has been mark as completed!';
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end


    public function submitRating_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {
                $ratingArray = [

                    'buyer_admin_id' => (string)$this->post('buyer_admin_id'),
                    'traveler_admin_id' => (string)$this->post('traveler_admin_id'),
                    'order_id' => (string)$this->post('order_id'),
                    'rating' => (int)$this->post('rating'),
                    'description' => (string)$this->post('description'),
                    'image_url' => $this->post('image_url'),
                    'video_url' => $this->post('video_url'),
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s'))
                ];
                $order_id = (string)$this->post('order_id');
                $buyer_admin_id = (string)$this->post('buyer_admin_id');

                $check = $this->Mod_rating->submitRating($ratingArray, $order_id, $buyer_admin_id);
                if ($check == true) {

                    $full_name = $this->Mod_users->getUserFullName($buyer_admin_id);
                    $orderFullName = $this->Mod_order->getOrderName($order_id);

                    $reciver_admin_id = (string)$this->post('traveler_admin_id');
                    $sender_admin_id = $buyer_admin_id;
                    $type = "rate";
                    $message = $full_name . ' has reviewed your delivery for ' . $orderFullName;
                    $this->Mod_users->sendNotification($reciver_admin_id, $message, $type, $sender_admin_id);
                    $response_array['status'] = 'Rating Successfully Submitted!';

                    //lock the activity
                    $activityData = [
                        'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                        'message' => 'give review',
                        'admin_id' => $buyer_admin_id
                    ];
                    $this->Mod_activity->saveActivity($activityData);
                    //end lock the activity
                } else {

                    $response_array['status'] = 'Your Rating Is Already Exists!';
                }
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end


    public function getTravelerProfileWithReviews_post()
    {

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $traveler_admin_id = (string)$this->post('traveler_admin_id');
                $ratingData = $this->Mod_rating->getTravelerReviews($traveler_admin_id);


                $response_array['status'] = 'Ratting Fetched!';
                $response_array['Rating'] = $ratingData;
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }

    }//end


    public function saveDeviceToken_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $admin_id = (string)$this->post('admin_id');
                $deveice_token = (string)$this->post('deveice_token');
                $this->Mod_users->saveToken($admin_id, $deveice_token);

                $response_array['status'] = 'Device Token is saved Successfully!';
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end function


    public function chatMessageSendNotification_post()
    {

        $reciver_admin_id = (string)$this->post('traveler_admin_id');
        $sender_admin_id = (string)$this->post('sender_admin_id');
        $type = 'chat';
        $messageComing = $this->post('message');
        $status = $this->post('status');
        $order_id = $this->post('order_id');

        if ($status == 'message') {

            $type = 'chat';
            $message = $messageComing;
        } else {

            $full_name = $this->Mod_users->getUserFullName($sender_admin_id);
            $orderFullName = $this->Mod_order->getOrderName($order_id);

            $type = 'offer';
            $message = $full_name . ' has sent a new offer for ' . $orderFullName;
        }
        $this->Mod_users->sendNotification($reciver_admin_id, $message, $type, $sender_admin_id, $status, $order_id);
    }


    public function testingNotification_post()
    {
        $db = $this->mongo_db->customQuery();

        $sender_admin_id = (string)$this->post('sender_admin_id');
        $reciver_admin_id = (string)$this->post('reciver_admin_id');
        $message = $this->post('message');
        $title = $this->post('title');
        $type = $this->post('type');
        $checkStatus = $this->Mod_users->sendNotification($reciver_admin_id, $message, $title, $type, $sender_admin_id);

        if ($checkStatus == false) {

            $response_array['status'] = 'not sended';
            $this->set_response($response_array, REST_Controller::HTTP_CREATED);

        } else {

            $response_array['status'] = 'sended';
            $response_array['data'] = $checkStatus;
            $this->set_response($response_array, REST_Controller::HTTP_CREATED);
        }
    }

    public function getMyNotification_post()
    {
        //$db = $this->mongo_db->customQuery();

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $admin_id = (string)$this->post('admin_id');
                $data = $this->Mod_users->getNotification($admin_id);

                $response_array['status'] = 'Notification Fetched!';
                $response_array['notification'] = $data;
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end function


    public function notificationMarkAsRead_post()
    {
        $db = $this->mongo_db->customQuery();

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $admin_id = (string)$this->post('admin_id');
                $notificationId = (string)$this->post('notificationId');
                $this->Mod_users->notificationStatusMarkAsRead($notificationId, $admin_id);
                $response_array['status'] = 'Mark Read Done!';
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end function


    public function getUserDetailsForStripeConnectedAccountID_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $admin_id = (string)$this->post('admin_id');
                $user_data = $this->Mod_users->getUser($admin_id);

                $getRatting = $this->Mod_rating->getUserAvgRatting((string)$admin_id);
                $user_data['rating'] = $getRatting;

                $response_array['status'] = 'Data Fetched!!';
                $response_array['data'] = $user_data;
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);

            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end function


    public function editProfile_post()
    {
        $db = $this->mongo_db->customQuery();

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $checkUser = isUserExistsUsingAdminId((string)$this->post('admin_id'));
                if ($checkUser == true || $checkUser == 1) {

                    $admin_id = (string)$this->post('admin_id');
                    $full_name = (string)$this->post('full_name');
                    $profile_image = (string)$this->post('profile_image');
                    $phone_number = (string)$this->post('phone_number');

                    $this->Mod_users->updateProfile($admin_id, $full_name, $profile_image, $phone_number);

                    //lock the activity
                    $activityData = [
                        'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                        'message' => 'Update profile',
                        'admin_id' => $admin_id
                    ];
                    $this->Mod_activity->saveActivity($activityData);
                    //end lock the activity
                    $lookup = [

                        [
                            '$match' => [

                                '_id' => $this->mongo_db->mongoId($admin_id),
                            ]
                        ],

                        [
                            '$project' => [

                                '_id' => ['$toString' => '$_id'],
                                'first_name' => '$first_name',
                                'last_name' => '$last_name',
                                'username' => '$username',
                                'email_address' => '$email_address',
                                'phone_number' => '$phone_number',
                                'profile_image' => '$profile_image',
                                'country' => '$country',
                                'full_name' => '$full_name',
                                'profile_status' => '$profile_status',
                                'password' => '$password',
                                'conected_account_id' => '$conected_account_id',
                                'Geometry' => '$Geometry',
                                ' Postal Code ' => '$postal_code',
                                'country' => '$country',
                                'created_date' => '$created_date',
                                'full_name' => '$full_name',
                                'last_login_time' => '$last_login_time',
                                'location' => '$location',
                                'login_status' => '$login_status',
                                'kyc_status_verified' => '$kyc_status_verified',
                                'phone_number' => '$phone_number',
                                'rating' => '$rating',
                                'signup_source' => '$signup_source',
                                'status' => '$status',
                                'user_role' => '$user_role'
                            ]
                        ]

                    ];
                    $getUser = $db->users->aggregate($lookup);
                    $userData = iterator_to_array($getUser);
                    $userData = $userData[0];
                    $getRatting = $this->Mod_rating->getUserAvgRatting($userData['_id']);
                    $userData['rating'] = $getRatting;

                    $token = $this->Mod_isValidUser->GenerateJWT((string)$userData['_id']);
                    $response_array = [

                        'data' => $userData,
                        'status' => 'Profile Update Successfully!',
                        'token' => $token,
                    ];
                    $this->set_response($response_array, REST_Controller::HTTP_CREATED);
                } else {

                    $response_array['status'] = 'Admin id is not valid!!';
                    $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                }
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end


    public function getOrderDetails_post()
    {        
        $db = $this->mongo_db->customQuery();

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) { 

                $order_id = (string)$this->post('order_id');
                $orderData = $this->Mod_order->getOrderDetails($order_id);
                $userProfileData = $this->Mod_users->getUserProfileStatus($orderData['admin_id']);
                $orderHistory = $this->Mod_order->getOrderDetailsHistory($order_id);

                $response_array = [
                    'data' => $orderData,
                    'profile' => $userProfileData,
                    'order_history' => $orderHistory,
                    'status' => 'Successfully Fetched!',
                ];
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);                
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }        
    }//end


    public function getStoreName_post()
    {
        $db = $this->mongo_db->customQuery();

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $order_id = (string)$this->post('order_id');
                $store_name = $this->Mod_order->getStoreName($order_id);

                $response_array = [
                    'store_name' => $store_name,
                    'status' => 'Successfully Fetched!',
                ];
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }


    public function getStoreNames_get()
    {
        if (!empty($this->input->request_headers('Authorization'))) {
            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];

            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                $received_Token = $received_Token_Array['Authorization'];
            }

            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {
                $store_names = $this->Mod_order->getStoreNames();
                $response_array = [
                    'store_names' => $store_names,
                    'status' => 'Successfully Fetched!',
                ];
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {
                $response_array['status'] = 'Authorization Failed!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
            
        } else {
            $response_array['status'] = 'Headers Are Missing!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }


    public function trendingOrders_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $tresndingOrders = $this->Mod_order->getTreandingOrders();
                $response_array = [
                    'data' => $tresndingOrders,
                    'status' => 'Successfully Fetched!',
                ];
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end


    public function getChatUsingId_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $buy_admin_id = (string)$this->post('buy_admin_id');
                $traveler_admin_id = (string)$this->post('traveler_admin_id');
                $order_id = (string)$this->post('order_id');

                $getChats = $this->Mod_chat->getUserChatsUsingId($buy_admin_id, $traveler_admin_id, $order_id);

                $response_array['status'] = 'Fetched Messages';
                $response_array['messagesData'] = $getChats;

                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }


    public function createTicket_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $admin_id = (string)$this->post('admin_id');
                $insertTicket = [

                    'admin_id' => $admin_id,
                    'message' => $this->post('message'),
                    'subject' => $this->post('subject'),
                    'image' => $this->post('image'),
                    'video' => $this->post('video'),
                    'order_number' => $this->post('order_number'),
                    'status' => 'pending',
                    'profile_status' => $this->post('profile_status'),
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                ];

                $ticketResponseData = $this->Mod_ticket->create($insertTicket, $admin_id);

                $response_array['status'] = 'Ticket is Submitted';
                $response_array['data'] = $ticketResponseData;
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end

    public function createCard_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) { 
                $admin_id = (string)$this->post('admin_id');
                $insertCard = [
                    'admin_id' => $admin_id,
                    'card_number' => $this->post('card_number'),
                    'card_type' => $this->post('card_type'),
                    'expiry_date' => $this->post('expiry_date'),
                    'cvv' => $this->post('cvv'),
                    'card_name' => $this->post('card_name'),    
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),                
                ];                

                $db = $this->mongo_db->customQuery();

                $db->card->insertOne($insertCard);
                
                $response_array['status'] = 'Card is Submitted';
                $response_array['data'] = $cardResponseData;
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
                
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        } 
    }//end    

    public function getCard_post()
    {     
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {      
                $card_id = (string)$this->post('card_id');                                
                $tData = $this->Mod_card->getCardInfo($card_id);

                $response_array['data'] = $tData;                
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
                
            } else {
                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        } 
    }

    public function editCard_post()
    {
        $db = $this->mongo_db->customQuery();
        if (!empty($this->input->request_headers('Authorization'))) {
            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {
                $card_id = (string)$this->post('card_id');
                $admin_id = (string)$this->post('admin_id');
                $card_number = (string)$this->post('card_number');
                $card_type = (string)$this->post('card_type');                
                $expiry_date = (string)$this->post('expiry_date');
                $cvv = (string)$this->post('cvv');
                $card_name = (string)$this->post('card_name');
                                    
                $this->Mod_card->updateCard($card_id, $admin_id, $card_number, $card_type, $expiry_date, $cvv, $card_name);
                                    
                $response_array = [                        
                    'status' => 'Card Update Successfully!',                        
                ];
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);                
            } else {
                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }        
    }//end

    public function getAllCards_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {
            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {
                $getCards = $this->Mod_card->getAllCards();

                $response_array['status'] = 'Fetched Cards';
                $response_array['cardsData'] = $getCards;

                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    } //end function

    public function getAllTickets_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {
                $admin_id = $this->post('admin_id');
                $tData = $this->Mod_ticket->getTickts($admin_id);

                $response_array['data'] = $tData;
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end


    public function ticketReply_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $tickerReply = [

                    'ticket_id' => $this->post('ticket_id'),
                    'message' => $this->post('message'),
                    'admin_id' => $this->post('admin_id'),
                    'status' => 'new',
                    'created_date' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                ];
                $this->Mod_ticket->sendMessage($tickerReply);

                $response_array['status'] = 'Reply Send';
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end


    public function changeTicketStatus_post()
    {

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $ticket_id = (string)$this->post('ticket_id');
                $status = (string)$this->post('status');
                $this->Mod_ticket->changeTicketStatus($ticket_id, $status);

                $response_array['status'] = 'Updated Successfully!';
                $response_array['type'] = '200';
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end


    public function varifyOrderId_post()
    {
        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $order_id = (string)$this->post('order_id');
                $admin_id = (string)$this->post('admin_id');

                $statusCheck = $this->Mod_order->checkStatus($order_id, $admin_id);

                $response_array['profileStatus'] = $statusCheck;
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }//end

    /*
     * this is for KYC
     */
    public function knowYourCustomer_post()
    {             
        $data = $this->post();        
        try {
            if (!empty($this->input->request_headers('Authorization'))) {
                $receivedTokenArray = $this->input->request_headers('Authorization');
                $receivedToken      = $receivedTokenArray['authorization'];
                if ($receivedToken == '' || $receivedToken == null || empty($receivedToken)) {
                    $receivedToken  = $receivedTokenArray['Authorization'];
                }
                $token = trim(str_replace(["Token: ", 'Bearer'], ["",''], $receivedToken));
                $tokenArray = $this->Mod_isValidUser->jwtDecode($token);
                if (!empty($tokenArray->admin_id)) {
                    if (!$data['user_id']) {
                        $this->set_response([
                            'error'         => true,
                            'sent_data'     => $data,
                            'response'      => [
                                'message'   => 'user_id is required on this request!',
                            ],
                            'status_code'   => REST_Controller::HTTP_BAD_REQUEST

                        ], REST_Controller::HTTP_BAD_REQUEST);
                        return;
                    }

                    $updateData = [
                        'id_type'        => (string)$data['id_type'],
                        'id_number'      => (string)$data['id_number'],
                        'id_front'       => (string)$data['id_front'],
                        'id_back'        => (string)$data['id_back'],
                        'profile_image'  => (string)$data['profile_image'],
                        'first_name'     => (string)$data['first_name'],
                        'middle_name'    => (string)$data['middle_name'],
                        'last_name'      => (string)$data['last_name'],
                        'suffix'         => (string)$data['suffix'],
                        'address_line_1' => (string)$data['address_line_1'],
                        'location'       => (string)$data['address_line_2'],
                        'birth_date'     => (string)$data['birth_date'],
                        'phone_number'   => (string)$data['phone_number'],
                        'kyc_status_verified' => true,                        
                    ];
                    $updateData = array_filter($updateData, fn($value) => !is_null($value) && $value !== '');
                    $this->Mod_users->kycUpdate($data['user_id'], $updateData); 
                    $activityData = [
                        'created_date'  => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                        'message'       => 'KYC Update',
                        'admin_id'      => (string)$data['user_id']
                    ];
                    $this->Mod_activity->saveActivity($activityData);

                    $response_array = [
                        'error'         => false,
                        'sent_data'     => $data,
                        'response'      => [
                            'details'   => $this->Mod_users->getUser($data['user_id']),
                            'message'   => 'Successfully updated',
                        ],
                        'status_code'   => REST_Controller::HTTP_OK

                    ];
                    $this->set_response($response_array, REST_Controller::HTTP_OK);
                } else {
                    $this->set_response([
                        'error'         => true,
                        'sent_data'     => $data,
                        'response'      => [
                            'message'   => 'Authorization Failed!!',
                        ],
                        'status_code'   => REST_Controller::HTTP_UNAUTHORIZED

                    ], REST_Controller::HTTP_UNAUTHORIZED);
                }
            } else {
                $this->set_response([
                    'error'         => true,
                    'sent_data'     => $data,
                    'response'      => [
                        'message'   => 'Headers Are Missing!!!!!!!!!!!',
                    ],
                    'status_code'   => REST_Controller::HTTP_BAD_REQUEST

                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $exception) {
            $this->set_response([
                'error'         => true,
                'sent_data'     => $data,
                'response'      => [
                    'message'   => 'Something went wrong!!',
                ],
                'status_code'   => REST_Controller::HTTP_INTERNAL_SERVER_ERROR

            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }        
    }//end signup

    public function addChat_post()
    {
        $db = $this->mongo_db->customQuery();
        //if (!empty($this->input->request_headers('Authorization'))) {

           // $received_Token_Array = $this->input->request_headers('Authorization');
            //$received_Token = '';
            //$received_Token = $received_Token_Array['authorization'];
            //if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
            //    $received_Token = $received_Token_Array['Authorization'];
           // }
           // $token = trim(str_replace("Token: ", "", $received_Token));
            //$tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            //if (!empty($tokenArray->admin_id)) {

                $order_id = $this->post('order_id');
                $sender_id = $this->post('sender_id');
                $reciver_id = $this->post('reciver_id');               
                $chat = [
                    'order_id' => $this->post('order_id'), 
                    'sender_id' => $this->post('sender_id'),                    
                    'reciver_id' => $this->post('reciver_id'),                    
                    'time' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s'))
                ];

                $checkStatus = $this->Mod_chat->checkChatAlreadyExists($order_id, $sender_id, $reciver_id);
                $dynamicStatus = '';
                if ($checkStatus == true || $checkStatus == 1) {
                    $dynamicStatus = 'Chat is Already added with same Details';
                    $type = '500';
                } else {

                    $db->chat->insertOne($chat);
                    $dynamicStatus = 'Successfully Added';
                    $type = '200';
                }

                $chatID = $this->Mod_chat->getChatID($order_id, $sender_id, $reciver_id); 

                $response_array = [
                    'status' => $dynamicStatus,
                    'chat_id' => $chatID,                    
                ];
                
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            //} else {

              //  $response_array['status'] = 'Authorization Failed!!';
              //  $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
           // }

       // } else {

         //   $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
          //  $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        //}
    }//end function

    public function addChatMessages_post()
    {
        $db = $this->mongo_db->customQuery();
        //if (!empty($this->input->request_headers('Authorization'))) {

            //$received_Token_Array = $this->input->request_headers('Authorization');
            //$received_Token = '';
            //$received_Token = $received_Token_Array['authorization'];
            //if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
            //    $received_Token = $received_Token_Array['Authorization'];
           // }
            ///$token = trim(str_replace("Token: ", "", $received_Token));
            //$tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            //if (!empty($tokenArray->admin_id)) {

                $sender = $this->post('sender');
                $chat_id = $this->post('chat_id');
                $sender_id = $this->post('sender_id');             
                $findArray = json_decode(stripslashes($_POST['currentMessage']));    
                            
                $chat = [

                    'sender_id' => $this->post('sender_id'),                                        
                    'currentMessage' =>  $findArray,       
                    'time' => $this->mongo_db->converToMongodttime(date('Y-m-d H:i:s')),
                    'chat_id' => $this->post('chat_id'),                    
                    'is_read' => false,
                    'sender' => $this->post('sender'),                                                                                                                     
                ];

                $db->chat_messages->insertOne($chat);
                $dynamicStatus = 'Successfully Added';
                $type = '200';

                $response_array = [
                    'status' => $dynamicStatus,                                         
                ];
                
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            //} else {

              //  $response_array['status'] = 'Authorization Failed!!';
               // $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            //}

        //} else {

          //  $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
           // $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
       // }
    }//end function    

    public function createVerificationSession_post(){
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->set_response([
                'error'         => true,
                'response'      => [
                    'message'   => 'Invalid request',
                ],
                'status_code'   => REST_Controller::HTTP_BAD_REQUEST

            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                $received_Token = $received_Token_Array['Authorization'];

            }

            $token = trim(str_replace("Token: ", "", $received_Token));
            $token = trim(str_replace("Bearer ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {
        
                // Load `.env` file from the server directory so that
                // environment variables are available in $_ENV or via
                // getenv().
                $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
                $dotenv->load();

                // Set your secret key. Remember to switch to your live secret key in production.
                // See your keys here: https://dashboard.stripe.com/apikeys
                $stripe = new \Stripe\StripeClient([
                    'api_key' => $_ENV['STRIPE_SECRET_KEY'],
                    'stripe_version' => '2020-08-27',
                ]);

                // Create the session
                $verification_session = $stripe->identity->verificationSessions->create([
                    'type' => 'document',
                    'options' => ['document' => ['require_matching_selfie' => true]],
                    'metadata' => [
                    //'user_id' => '{{USER_ID}}',
                    'user_id' => $tokenArray->admin_id
                    ]
                ]);

                // Return only the client secret to the frontend.
                $client_secret = $verification_session->client_secret;
                $url = $verification_session->url;

                $response_array = [
                    'error'         => false,
                    //'verification_session' => $verification_session,
                    'verification_session_id' => $verification_session->id,
                    'client_secret'     => $client_secret,
                    'url'     => $url,
                    'status_code'   => REST_Controller::HTTP_OK

                ];
                //$this->createverificationsession_post=TRUE;
                $this->set_response($response_array, REST_Controller::HTTP_OK);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function retrieveVerificationSession_post(){
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->set_response([
                'error'         => true,
                'response'      => [
                    'message'   => 'Invalid request',
                ],
                'status_code'   => REST_Controller::HTTP_BAD_REQUEST

            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        if (!empty($this->input->request_headers('Authorization'))) {

            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                $received_Token = $received_Token_Array['Authorization'];

            }

            $token = trim(str_replace("Token: ", "", $received_Token));
            $token = trim(str_replace("Bearer ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {
                $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
                $dotenv->load();

                // Set your secret key. Remember to switch to your live secret key in production.
                // See your keys here: https://dashboard.stripe.com/apikeys
                $stripe = new \Stripe\StripeClient([
                    'api_key' => $_ENV['STRIPE_SECRET_KEY'],
                    'stripe_version' => '2020-08-27',
                ]);

                $expandedSession = $stripe->identity->verificationSessions->retrieve(
                    $this->post('verification_session_id'),
                    //'vs_1Kd4bIGwymx5JE4T5mnRqa2T',
                    [
                    'expand' => [
                        'verified_outputs',
                    ],
                    ]
                );

                $response_array = [
                    'error'         => false,
                    'expanded_session' =>$expandedSession,
                    'is_verified' => $expandedSession->status,
                    'status_code'   => REST_Controller::HTTP_OK

                ];

                $updateData['is_verified']=$expandedSession->status;
                $updateData['verification_result_data']=$expandedSession->verified_outputs;
                $this->Mod_users->updateVerification($tokenArray->admin_id, $updateData);

                $this->set_response($response_array, REST_Controller::HTTP_OK);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function getUserLatestTransactions_post()
    {        
        if (!empty($this->input->request_headers('Authorization'))) {
            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {

                $received_Token = $received_Token_Array['Authorization'];
            }
            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {

                $admin_id = (string)$this->post('admin_id');
                $latestTransactions = $this->Mod_order->getUserTransactionHistory($admin_id);
                $response_array['status'] = 'Fetched Successfully!';
                $response_array['user_latest_transactions'] = $latestTransactions;
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {

                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {

            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    //This is create account first to enable bank transfer',
    public function createStripeAccount_post()
    {
        try {
            if (!empty($this->input->request_headers('Authorization'))) {
                $body = $this->post();
                $email = (string)$body['email'];
                $params = [
                    'required' => [ 'product_description','phone','first_name','company_name','country','email'],
                    'optional' => [ 'maiden_name','last_name','state','postal_code','line2','line1','city']
                ];
                $data = array_keys( $body);
                if(!in_array('product_description',$data) ||
                    !in_array('phone',$data) ||
                    !in_array('first_name',$data) ||
                    !in_array('company_name',$data) ||
                    !in_array('email',$data) ||
                    !in_array('country',$data)
                ) {
                    $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
                    $response_array['status'] = 'error';
                    $response_array['message'] = 'Missing required param! Please see the list for required params.';
                    $response_array['params'] = $params;
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
                    return json_encode($response_array);
                }
                if(strlen($body['country']) != 2) {
                    $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
                    $response_array['status'] = 'error';
                    $response_array['message'] = 'Two-letter country code (ISO 3166-1 alpha-2).';
                    $response_array['sent_data'] = $body['country'];
                    $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
                    return json_encode($response_array);
                }

                $checkEmailStatus = isEmailExists($email);
                if(!$checkEmailStatus) {
                    $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
                    $response_array['status'] = 'error';
                    $response_array['message'] = 'User not found with email:'.$email;
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
                    return json_encode($response_array);
                }
                $received_Token_Array = $this->input->request_headers('Authorization');
                $received_Token = $received_Token_Array['authorization'];
                if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                    $received_Token = $received_Token_Array['Authorization'];
                }
                $token = trim(str_replace("Token: ", "", $received_Token));
                $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

                if (!empty($tokenArray->admin_id)) {
                    $db = $this->mongo_db->customQuery();
                    $lookup = [ [ '$match' => [ 'email_address' => $email,  ]  ],  ];

                    $getUser = $db->users->aggregate($lookup);
                    $userData = iterator_to_array($getUser);
                    $userData = $userData[0];
                    if(!$userData['stripe_account_id']) {

                        $externalContent = file_get_contents('http://checkip.dyndns.com/');
                        preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
                        $externalIp = $m[1];
                        $account = $this->stripe->accounts->create([
                            'type' => 'custom',
                            'country' => $body['country'],
                            'email' => $body['email'],
                            'business_type' => 'individual',
                            'company' => [
                                'address' => [
                                    'city' => $body['city'],
                                    'line1' => $body['line1'],
                                    'line2' => $body['line2'],
                                    'postal_code' => $body['postal_code'],
                                    'state' => $body['state'],
                                    'country' => $body['country'],
                                ],
                                'name' => $body['company_name'],
                                'owners_provided' => true,
                                'phone' => $body['phone'],
                            ],
                            'individual' => [
                                'address' => [
                                    'city' => $body['city'],
                                    'line1' => $body['line1'],
                                    'line2' => $body['line2'],
                                    'postal_code' => $body['postal_code'],
                                    'state' => $body['state'],
                                    'country' => $body['country'],
                                ],
                            ],
                            'tos_acceptance' => [
                                'date' => time(),
                                'ip' => $externalIp,
                            ],
                            'business_profile' => [
                                'name' => $body['company_name'],
                                'product_description' => $body['product_description'],
                                'support_address' => [
                                    'city' => $body['city'],
                                    'line1' => $body['line1'],
                                    'line2' => $body['line2'],
                                    'postal_code' => $body['postal_code'],
                                    'state' => $body['state'],
                                    'country' => $body['country'],
                                ],
                                'support_email' => $body['email'],
                                'support_phone' => $body['phone'],
                            ],
                            'capabilities' => [
                                'card_payments' => ['requested' => true],
                                'transfers' => ['requested' => true],
                            ],
                        ]);
                        $stripe_account_id = $account->id;
                        $db->users->updateOne(['email_address' => $email], ['$set' => ['stripe_account_id' => $stripe_account_id]]);
                        $response_array['code'] = REST_Controller::HTTP_CREATED;
                        $response_array['status'] = 'success';
                        $response_array['message'] = 'Account created successfully, with account id:'.$stripe_account_id;
                        $response_array['stripe_account_id'] = $stripe_account_id;
                        $response_array['sent_data'] = $body;
                        $this->set_response($response_array, REST_Controller::HTTP_CREATED);
                        return json_encode($response_array);
                    }

                    $response_array['code'] = REST_Controller::HTTP_OK;
                    $response_array['status'] = 'success';
                    $response_array['message'] = 'Account already created, account id:'.$userData['stripe_account_id'];
                    $response_array['stripe_account_id'] = $userData['stripe_account_id'];
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_OK);
                    return json_encode($response_array);
                } else {
                    $response_array['code'] = REST_Controller::HTTP_NOT_FOUND;
                    $response_array['status'] = 'error';
                    $response_array['message'] = 'Authorization Failed!!';
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                    return json_encode($response_array);
                }
            } else {
                $response_array['code'] = REST_Controller::HTTP_NOT_FOUND;
                $response_array['status'] = 'error';
                $response_array['message'] = 'Headers Are Missing!!!!!!!!!!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                return json_encode($response_array);
            }
        } catch (Exception $exception) {
            $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
            $response_array['status'] = 'error';
            $response_array['message'] = $exception->getMessage();
            $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
            return json_encode($response_array);
        }
    }

    // 'This is to onboard the account so that the user can transfer money to account',
    public function onBoardAccount_post()
    {
        try {
            if (!empty($this->input->request_headers('Authorization'))) {
                $body = $this->post();
                $data = array_keys( $body);
                if(!in_array('stripe_account_id',$data)
                ) {
                    $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
                    $response_array['status'] = 'error';
                    $response_array['message'] = 'Missing required param! stripe_account_id';
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
                    return json_encode($response_array);
                }

                $received_Token_Array = $this->input->request_headers('Authorization');
                $received_Token = $received_Token_Array['authorization'];
                if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                    $received_Token = $received_Token_Array['Authorization'];
                }
                $token = trim(str_replace("Token: ", "", $received_Token));
                $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

                if (!empty($tokenArray->admin_id)) {
                    $account = $this->stripe->accountLinks->create([
                        'account' => $body['stripe_account_id'],
                        'refresh_url' => 'https://flighteno-dev.herokuapp.com/signin',
                        'return_url' => 'https://flighteno-dev.herokuapp.com/success',
                        'type' => 'account_onboarding',
                    ]);
                    $response_array['code'] = REST_Controller::HTTP_CREATED;
                    $response_array['status'] = 'success';
                    $response_array['message'] = 'Successfully generated 1 time use onboarding URL';
                    $response_array['response'] = $account;
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_CREATED);
                    return json_encode($response_array);
                } else {
                    $response_array['code'] = REST_Controller::HTTP_NOT_FOUND;
                    $response_array['status'] = 'error';
                    $response_array['message'] = 'Authorization Failed!!';
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                    return json_encode($response_array);
                }
            } else {
                $response_array['code'] = REST_Controller::HTTP_NOT_FOUND;
                $response_array['status'] = 'error';
                $response_array['message'] = 'Headers Are Missing!!!!!!!!!!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                return json_encode($response_array);
            }
        } catch (Exception $exception) {
            $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
            $response_array['status'] = 'error';
            $response_array['message'] = $exception->getMessage();
            $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
            return json_encode($response_array);
        }
    }

    //'This is to add external bank account for connect Please see https://jsfiddle.net/ywain/L2cefvtp/ for the external_account',
    public function connectBankToAccount_post()
    {
        try {
            if (!empty($this->input->request_headers('Authorization'))) {
                $body = $this->post();
                $data = array_keys( $body);
                if(!in_array('stripe_account_id',$data) || !in_array('external_account',$data)
                ) {
                    $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
                    $response_array['status'] = 'error';
                    $response_array['message'] = 'Missing required param! stripe_account_id or external_account.Please check https://jsfiddle.net/ywain/L2cefvtp/ for the external_account';
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
                    return json_encode($response_array);
                }

                $received_Token_Array = $this->input->request_headers('Authorization');
                $received_Token = $received_Token_Array['authorization'];
                if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                    $received_Token = $received_Token_Array['Authorization'];
                }
                $token = trim(str_replace("Token: ", "", $received_Token));
                $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

                if (!empty($tokenArray->admin_id)) {
                    $account = $this->stripe->accounts->createExternalAccount($body['stripe_account_id'],[
                        'external_account' => $body['external_account'],
                    ]);
                    $response_array['code'] = REST_Controller::HTTP_CREATED;
                    $response_array['status'] = 'success';
                    $response_array['message'] = 'Successfully connected bank account';
                    $response_array['response'] = $account;
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_CREATED);
                    return json_encode($response_array);
                } else {
                    $response_array['code'] = REST_Controller::HTTP_NOT_FOUND;
                    $response_array['status'] = 'error';
                    $response_array['message'] = 'Authorization Failed!!';
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                    return json_encode($response_array);
                }
            } else {
                $response_array['code'] = REST_Controller::HTTP_NOT_FOUND;
                $response_array['status'] = 'error';
                $response_array['message'] = 'Headers Are Missing!!!!!!!!!!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                return json_encode($response_array);
            }
        } catch (Exception $exception) {
            $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
            $response_array['status'] = 'error';
            $response_array['message'] = $exception->getMessage();
            $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
            return json_encode($response_array);
        }
    }

    //This is to transfer money to bank account
    public function transferToAccount_post()
    {
        try {
            if (!empty($this->input->request_headers('Authorization'))) {
                $body = $this->post();
                $data = array_keys( $body);
                $requiredParam = [
                    'stripe_account_id',
                    'total_service_amount',
                    'transfer_money',
                    'payee_email',
                    'currency'
                ];

                if(!in_array('stripe_account_id',$data) ||
                    !in_array('total_service_amount',$data) ||
                    !in_array('transfer_money',$data) ||
                    !in_array('payee_email',$data)||
                    !in_array('currency',$data)
                ) {
                    $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
                    $response_array['status'] = 'error';
                    $response_array['message'] = 'Missing required param! ';
                    $response_array['param'] = $requiredParam;
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
                    return json_encode($response_array);
                }

                $received_Token_Array = $this->input->request_headers('Authorization');
                $received_Token = $received_Token_Array['authorization'];
                if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                    $received_Token = $received_Token_Array['Authorization'];
                }
                $token = trim(str_replace("Token: ", "", $received_Token));
                $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

                if (!empty($tokenArray->admin_id)) {
                    $db = $this->mongo_db->customQuery();
                    $lookup = [ [ '$match' => [ 'email_address' => $body['payee_email'],  ]  ],  ];
                    $getUser = $db->users->aggregate($lookup);
                    $userData = iterator_to_array($getUser);
                    $userData = $userData[0];
                    if (!$userData) {
                        $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
                        $response_array['status'] = 'error';
                        $response_array['message'] = 'User not found with email:' . $body['payee_email'];
                        $response_array['sent_data'] = $body;
                        $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
                        return json_encode($response_array);
                    }

                    if (!$userData['stripe_customer_id']) {
                        $customer = $this->stripe->customers->create(['name' => $userData['full_name'],'email' => $userData['email_address']]);
                        $stripe_customer_id = $customer->id;
                        $db->users->updateOne(['email_address' => $userData['email_address']], ['$set' => ['stripe_customer_id' => $stripe_customer_id]]);
                    } else {
                        $stripe_customer_id = $userData['stripe_customer_id'];
                    }

                    $ephemeralKey = $this->stripe->ephemeralKeys->create([
                        'customer' => $stripe_customer_id,
                        'apiVersion' => '2020-08-27'
                    ]);

                    $paymentIntent = $this->stripe->paymentIntents->create([
                        'amount' => $body['total_service_amount'] * 100,
                        'currency' => $body['currency'],
                        'customer' => $stripe_customer_id,
                        'automatic_payment_methods' => [
                            'enabled' => true
                        ],
                        'application_fee_amount' => $body['transfer_money'] * 100,
                        'transfer_data' => [
                            'destination' => $body['stripe_account_id']
                        ],
                    ]);

                    $response_array['code'] = REST_Controller::HTTP_CREATED;
                    $response_array['status'] = 'success';
                    $response_array['message'] = 'Successfully created payment intent. Please confirm this payment to proceed';
                    $response_array['response'] = [
                        'payment_intent_id' => $paymentIntent->id,
                        'ephemeral_key' => $ephemeralKey->secret,
                        'stripe_customer_id' => $stripe_customer_id,
                    ];
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_CREATED);
                    return json_encode($response_array);
                } else {
                    $response_array['code'] = REST_Controller::HTTP_NOT_FOUND;
                    $response_array['status'] = 'error';
                    $response_array['message'] = 'Authorization Failed!!';
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                    return json_encode($response_array);
                }
            } else {
                $response_array['code'] = REST_Controller::HTTP_NOT_FOUND;
                $response_array['status'] = 'error';
                $response_array['message'] = 'Headers Are Missing!!!!!!!!!!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                return json_encode($response_array);
            }
        } catch (Exception $exception) {
            $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
            $response_array['status'] = 'error';
            $response_array['message'] = $exception->getMessage();
            $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
            return json_encode($response_array);
        }
    }


    //This is to generate stripe customer id
    public function createStripeCustomer_post() {
        try {
            if (!empty($this->input->request_headers('Authorization'))) {
                $body = $this->post();
                $data = array_keys( $body);
                if( !in_array('email_address',$data)  ) {
                    $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
                    $response_array['status'] = 'error';
                    $response_array['message'] = 'Missing required param!';
                    $response_array['param'] = [
                        'email_address'
                    ];
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
                    return json_encode($response_array);
                }

                $received_Token_Array = $this->input->request_headers('Authorization');
                $received_Token = $received_Token_Array['authorization'];
                if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                    $received_Token = $received_Token_Array['Authorization'];
                }
                $token = trim(str_replace("Token: ", "", $received_Token));
                $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

                if (!empty($tokenArray->admin_id)) {
                    $db = $this->mongo_db->customQuery();
                    $lookup = [ [ '$match' => [ 'email_address' => $body['email_address'],  ]  ],  ];
                    $getUser = $db->users->aggregate($lookup);
                    $userData = iterator_to_array($getUser);
                    $userData = $userData[0];
                    if (!$userData) {
                        $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
                        $response_array['status'] = 'error';
                        $response_array['message'] = 'User not found with email:' . $body['email_address'];
                        $response_array['sent_data'] = $body;
                        $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
                        return json_encode($response_array);
                    }
                    if (!$userData['stripe_customer_id']) {
                        $customer = $this->stripe->customers->create(['name' => $userData['full_name'],'email' => $userData['email_address']]);
                        $stripe_customer_id = $customer->id;
                        $userData['stripe_customer_id'] = $stripe_customer_id;
                        $db->users->updateOne(['email_address' => $userData['email_address']], ['$set' => ['stripe_customer_id' => $stripe_customer_id]]);
                    }
                    $response_array['code'] = REST_Controller::HTTP_OK;
                    $response_array['status'] = 'success';
                    $response_array['message'] = 'Successfully attached payment method';
                    $response_array['response'] = $userData;
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_OK);
                    return json_encode($response_array);
                } else {
                    $response_array['code'] = REST_Controller::HTTP_NOT_FOUND;
                    $response_array['status'] = 'error';
                    $response_array['message'] = 'Authorization Failed!!';
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                    return json_encode($response_array);
                }
            } else {
                $response_array['code'] = REST_Controller::HTTP_NOT_FOUND;
                $response_array['status'] = 'error';
                $response_array['message'] = 'Headers Are Missing!!!!!!!!!!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                return json_encode($response_array);
            }
        } catch (Exception $exception) {
            $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
            $response_array['status'] = 'error';
            $response_array['message'] = $exception->getMessage();
            $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
            return json_encode($response_array);
        }
    }

    //This is to confirm the transfer
    public function createPaymentMethod_post()
    {
         try {
             if (!empty($this->input->request_headers('Authorization'))) {
                 $body = $this->post();
                 $data = array_keys( $body);
                 if(!in_array('expiry_date',$data) ||
                     !in_array('card_no',$data) ||
                     !in_array('cvc',$data)||
                     !in_array('user_email',$data)
                 ) {
                     $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
                     $response_array['status'] = 'error';
                     $response_array['message'] = 'Missing required param!';
                     $response_array['param'] = [
                         'expiry_date','card_no','cvc','user_email'
                     ];
                     $response_array['sent_data'] = $body;
                     $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
                     return json_encode($response_array);
                 }

                 $received_Token_Array = $this->input->request_headers('Authorization');
                 $received_Token = $received_Token_Array['authorization'];
                 if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                     $received_Token = $received_Token_Array['Authorization'];
                 }
                 $token = trim(str_replace("Token: ", "", $received_Token));
                 $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

                 if (!empty($tokenArray->admin_id)) {
                     $db = $this->mongo_db->customQuery();
                     $lookup = [ [ '$match' => [ 'email_address' => $body['payee_email'],  ]  ],  ];
                     $getUser = $db->users->aggregate($lookup);
                     $userData = iterator_to_array($getUser);
                     $userData = $userData[0];
                     if (!$userData) {
                         $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
                         $response_array['status'] = 'error';
                         $response_array['message'] = 'User not found with email:' . $body['payee_email'];
                         $response_array['sent_data'] = $body;
                         $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
                         return json_encode($response_array);
                     }
                     if (!$userData['stripe_customer_id']) {
                         $customer = $this->stripe->customers->create(['name' => $userData['full_name'],'email' => $userData['email_address']]);
                         $stripe_customer_id = $customer->id;
                         $db->users->updateOne(['email_address' => $userData['email_address']], ['$set' => ['stripe_customer_id' => $stripe_customer_id]]);
                     } else {
                         $stripe_customer_id = $userData['stripe_customer_id'];
                     }
                     $account = $this->stripe->paymentMethods->create([
                         'type' => 'card_type',
                         'card' => [
                             'number' => $body['card_no'],
                             'exp_month' => date("m",strtotime($body['expiry_date'])),
                             'exp_year' => date("Y",strtotime($body['expiry_date'])),
                             'cvc' => $body['cvc'],
                         ]
                     ]);
                     $this->stripe->paymentMethods->attach($account->id, [
                         'customer' => $stripe_customer_id,
                     ]);
                     $this->stripe->customers->update($stripe_customer_id, [
                         'invoice_settings' =>[
                             'default_payment_method' => $account->id
                         ]
                     ]);

                     $response_array['code'] = REST_Controller::HTTP_OK;
                     $response_array['status'] = 'success';
                     $response_array['message'] = 'Successfully attached payment method';
                     $response_array['response'] = $account;
                     $response_array['sent_data'] = $body;
                     $this->set_response($response_array, REST_Controller::HTTP_OK);
                     return json_encode($response_array);
                 } else {
                     $response_array['code'] = REST_Controller::HTTP_NOT_FOUND;
                     $response_array['status'] = 'error';
                     $response_array['message'] = 'Authorization Failed!!';
                     $response_array['sent_data'] = $body;
                     $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                     return json_encode($response_array);
                 }
             } else {
                 $response_array['code'] = REST_Controller::HTTP_NOT_FOUND;
                 $response_array['status'] = 'error';
                 $response_array['message'] = 'Headers Are Missing!!!!!!!!!!!';
                 $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                 return json_encode($response_array);
             }
         } catch (Exception $exception) {
             $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
             $response_array['status'] = 'error';
             $response_array['message'] = $exception->getMessage();
             $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
             return json_encode($response_array);
         }
    }

    //This is to confirm the transfer
    public function confirmTransferToAccount_post()
    {
        try {
            if (!empty($this->input->request_headers('Authorization'))) {
                $body = $this->post();
                $data = array_keys( $body);
                if(!in_array('payment_intent_id',$data) || !in_array('payment_method_id',$data)
                ) {
                    $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
                    $response_array['status'] = 'error';
                    $response_array['message'] = 'Missing required param! payment_intent_id or payment_method_id.';
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
                    return json_encode($response_array);
                }

                $received_Token_Array = $this->input->request_headers('Authorization');
                $received_Token = $received_Token_Array['authorization'];
                if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                    $received_Token = $received_Token_Array['Authorization'];
                }
                $token = trim(str_replace("Token: ", "", $received_Token));
                $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

                if (!empty($tokenArray->admin_id)) {
                    $account = $this->stripe->paymentIntents->confirm($body['payment_intent_id'],[
                        'payment_method' => $body['payment_method_id'],
                        'return_url' => 'https://flighteno-dev.herokuapp.com'
                    ]);

                    $response_array['code'] = REST_Controller::HTTP_CREATED;
                    $response_array['status'] = 'success';
                    $response_array['message'] = 'Successfully confirmed payment';
                    $response_array['response'] = $account;
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_CREATED);
                    return json_encode($response_array);
                } else {
                    $response_array['code'] = REST_Controller::HTTP_NOT_FOUND;
                    $response_array['status'] = 'error';
                    $response_array['message'] = 'Authorization Failed!!';
                    $response_array['sent_data'] = $body;
                    $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                    return json_encode($response_array);
                }
            } else {
                $response_array['code'] = REST_Controller::HTTP_NOT_FOUND;
                $response_array['status'] = 'error';
                $response_array['message'] = 'Headers Are Missing!!!!!!!!!!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
                return json_encode($response_array);
            }
        } catch (Exception $exception) {
            $response_array['code'] = REST_Controller::HTTP_BAD_REQUEST;
            $response_array['status'] = 'error';
            $response_array['message'] = $exception->getMessage();
            $this->set_response($response_array, REST_Controller::HTTP_BAD_REQUEST);
            return json_encode($response_array);
        }
    }


    // This is to get the payment_method from the order_id
    public function getPaymentMethodByOrderId_post() {
        $db = $this->mongo_db->customQuery();

        if (!empty($this->input->request_headers('Authorization'))) {
            $received_Token_Array = $this->input->request_headers('Authorization');
            $received_Token = '';
            $received_Token = $received_Token_Array['authorization'];
            
            if ($received_Token == '' || $received_Token == null || empty($received_Token)) {
                $received_Token = $received_Token_Array['Authorization'];
            }

            $token = trim(str_replace("Token: ", "", $received_Token));
            $tokenArray = $this->Mod_isValidUser->jwtDecode($token);

            if (!empty($tokenArray->admin_id)) {
                $order_id = (string)$this->post('order_id');
                $payment_method = $this->Mod_order->getPaymentMethodFromAcceptedOffers($order_id);

                $response_array['payment_method_id'] = $payment_method;
                $response_array['status'] = 'Successfully Fetched';
                $this->set_response($response_array, REST_Controller::HTTP_CREATED);
            } else {
                $response_array['status'] = 'Authorization Failed!!';
                $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $response_array['status'] = 'Headers Are Missing!!!!!!!!!!!';
            $this->set_response($response_array, REST_Controller::HTTP_NOT_FOUND);
        }
    }

}//end controller                                








