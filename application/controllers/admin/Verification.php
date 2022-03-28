<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verification extends CI_Controller {

    function __construct(){
		parent :: __construct();
	}

    public function index($page = 'verify')
    {
        if ( ! file_exists(APPPATH.'views/verification/'.$page.'.php'))
        {
            show_404();
        }

        $data['title'] = ucfirst($page); // Capitalize the first letter
        $this->load->view('verification/'.$page, $data);
    }
}