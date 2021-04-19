<?php
/**
 * @author Cyrus Muchiri
 *
 * */

use Restserver\Libraries\REST_Controller;

include_once 'Base.php';
class Auth extends Base
{
	public function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->load->model("auth_model","auth");
	}

	function login_post(){
		$username = $this->input->post('username', TRUE);
		$password = $this->input->post('password', TRUE);

		$login_details = $this->auth->auth($username,$password);
		if($login_details == false){
			$this->createTrail("login",$username,"Fail");
			$this->response([
				"status" => "false",
				"message" => "invalid username or password"
			], REST_Controller::HTTP_OK);
		}else{
			$this->createTrail("login",$username,"Success");
			$this->response([
				"status" => "true",
				"result" => $login_details
			], REST_Controller::HTTP_OK);
		}

	}

}
