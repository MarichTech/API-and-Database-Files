<?php

/**
 * All POST/PUT/PATCH requests
 * Add, update, delete
 * @author Cyrus Muchiri
 * @date 17th February 2020
 * @for Juba Express by SILKTECH
 * */


use Restserver\Libraries\REST_Controller;

include_once "Base.php";

class Data_operations extends Base
{
	public function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->load->model("data_model", "operations");
		$this->load->library("bcrypt");
	}

	/*Orders*/
	public function createOrder_post()
	{
		$donation_id = $this->input->post('donation_id', TRUE);
		$beneficiary_id = $this->input->post('beneficiary_id', TRUE);
		$amount = $this->input->post('amount', TRUE);
		$locationExpected = $this->input->post('location_expected', TRUE);

		$order = array(
			"locationExpected" => $locationExpected,
			"amount" => $amount,
			"beneficiaryId" => $beneficiary_id,
			"donationId" => $donation_id
		);


		$status = $this->operations->createOrder($order);
		if ($status == true) {
			$action = "Create Order";
			$status = "Success";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action,$user_name,$status);
			$this->response([
				"status" => "true"
			], REST_Controller::HTTP_CREATED);

		} else {
			$action = "Create Order";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action,$user_name,$status);
			$this->response([
				"result" => "false"
			], REST_Controller::HTTP_BAD_REQUEST);

		}


	}

	function updateOrder_post()
	{

	}


	/*Users*/
	/*Password is */
	function createUser_post()
	{
		$group_code = $this->input->post('groupCode', TRUE);
		if (empty($group_code)) {
			$this->response([
				"status" => "false",
				"message" => "Group code invalid or missing"
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$status = false;
			switch ($group_code) {

				case "003":
					$data = array(
						"groupCode" => $group_code,
						"name" => $this->input->post('name', TRUE),
						"repEmail" => $this->input->post('repEmail', TRUE),
						"repMobile" => $this->input->post('repMobile', TRUE),
						"addressLocation" => $this->input->post('addressLocation', TRUE),
						"descriptions" => $this->input->post('descriptions', TRUE),
						"userName" => $this->input->post('userName', TRUE),
						"password" => $this->bcrypt->hash($this->input->post('password', TRUE)),
					);
					$status = $this->operations->addClient($data);
					break;
				default:
					$data = array(
						"groupCode" => $group_code,
						"name" => $this->input->post('name', TRUE),
						"email" => $this->input->post('email', TRUE),
						"mobile" => $this->input->post('mobile', TRUE),
						"addressLocation" => $this->input->post('addressLocation', TRUE),
						"gender" => $this->input->post('gender', TRUE),
						"stateIdentificationType" => $this->input->post('stateIdentificationType', TRUE),
						"identificationNumber" => $this->input->post('identificationNumber', TRUE),
						"responsibilities" => $this->input->post('responsibilities', TRUE),
						"userName" => $this->input->post('userName', TRUE),
						"password" => $this->bcrypt->hash($this->input->post('password', TRUE)),
					);
					switch ($group_code) {
						case "001":
							$status = $this->operations->addStaff($data);
							break;
						case "002":
							$status = $this->operations->addAgent($data);
							break;
						case "004":
							$status = $this->operations->addAdmin($data);
					}


			}
			if ($status == true) {
				$action = "Create User";
				$status = "Success";
				$user_name = $_SERVER['PHP_AUTH_USER'];
				$this->createTrail($action,$user_name,$status);
				$this->response([
					"status" => "true",
					"message" => "User created successfully"
				], REST_Controller::HTTP_CREATED);

			} else {
				$action = "Create User";
				$status = "Fail";
				$user_name = $_SERVER['PHP_AUTH_USER'];
				$this->createTrail($action,$user_name,$status);
				$this->response([
					"result" => "false",
					"Message" => "Existing username or broken input"
				], REST_Controller::HTTP_BAD_REQUEST);

			}

		}
	}


	function updateUser_post()
	{

	}





	/*Donations*/
	function createDonation_post()
	{
		$client_id = $this->input->post('clientId', TRUE);
		$amount = $this->input->post('amount', TRUE);
		$date_awarded = $this->input->post('dateAwarded', TRUE);;

		$data = array(
			"clientId"=>$client_id,
			"amountAwarded"=>$amount,
			"balance" =>$amount,
			"dateAwarded" => $date_awarded
		);
		$status = true;
		$status = $this->operations->addDonation($data);
		if ($status == true) {
			$action = "Create Donation";
			$status = "Success";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action,$user_name,$status);
			$this->response([
				"status" => "true",
				"message" => "Donation created successfully"
			], REST_Controller::HTTP_CREATED);

		} else {
			$action = "Create Donation";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action,$user_name,$status);
			$this->response([
				"result" => "false",
				"Message" => "An error occured"
			], REST_Controller::HTTP_BAD_REQUEST);

		}


	}

	function updateDonation_post()
	{

	}
}


