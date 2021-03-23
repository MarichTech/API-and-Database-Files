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
		/*If not exists create the following folders*/
		if (!file_exists(FCPATH . 'assets/')) {
			mkdir(FCPATH . 'assets/');
		}
		DEFINE("PICTURES", FCPATH . 'assets/pictures');
		DEFINE("FINGERPRINTS", FCPATH . 'assets/fingerprints');
		/*FingerPrints FCPATH./assets/pictures*/
		if (!file_exists(FCPATH . 'assets/pictures')) {
			mkdir(FCPATH . 'assets/pictures');
		}
		/*Pictures FCPATH./assets/fingerprints*/
		if (!file_exists(FCPATH . 'assets/fingerprints')) {
			mkdir(FCPATH . 'assets/fingerprints');
		}
	}

	/*Orders*/
	public function createOrder_post()
	{
		$client_id = $this->input->post('client_id', TRUE);
		$location_id = $this->input->post('location_id', TRUE);
		$amount = $this->input->post('amount', TRUE);
		$grant_name = $this->input->post('grant_name', TRUE);
		$beneficiary_group_amounts = $this->input->post('beneficiary_group_amounts');
		$decoded_ben_amounts = (array)json_decode($beneficiary_group_amounts);

		$order = array(
			"client_id" => $client_id,
			"amount" => $amount,
			"locationId" => $location_id,
			"grant_name" => $grant_name
		);


		$status = $this->operations->createOrder($order, $decoded_ben_amounts);
		if ($status == true) {
			$action = "Create Order";
			$status = "Success";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"status" => "true"
			], REST_Controller::HTTP_CREATED);

		} else {
			$action = "Create Order";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "false"
			], REST_Controller::HTTP_BAD_REQUEST);

		}


	}

	function updateOrder_post()
	{
		$delivery_status = $this->input->post('delivery_status', TRUE);
		$locationDelivered = $this->input->post('location_delivered', TRUE);
		$dateDelivered = $this->input->post('date_delivered', TRUE);
		$locationExpected = $this->input->post('location_expected', TRUE);
		$amount = $this->input->post('amount', TRUE);
		$orderId = $this->input->post('order_id', TRUE);
		$order = array(

			"deliveryStatusId" => $delivery_status,
			"amount" => $amount,
			"locationExpected" => $locationExpected,
			"locationDelivered" => $locationDelivered,
			"dateDelivered" => $dateDelivered,
			"lastUpdated" => date("Y-m-d H:i:s")
		);
		$status = $this->operations->updateOrder($order, $orderId);

		if ($status == true) {
			$action = "Update Order";
			$status = "Success";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"status" => "true"
			], REST_Controller::HTTP_CREATED);

		} else {
			$action = "Update Order";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "false"
			], REST_Controller::HTTP_BAD_REQUEST);

		}

	}

	function linkOrder_post()
	{
		$agent_id = $this->input->post('agent_id', TRUE);
		$order_id = $this->input->post('order_id', TRUE);
		$data = array(
			"order_id" => $order_id,
			"agent_id" => $agent_id
		);
		$status = $this->operations->linkOrder($data);
		if ($status == true) {
			$action = "Assign Order";
			$status = "Success";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"status" => "true"
			], REST_Controller::HTTP_CREATED);

		} else {
			$action = "Assign Order";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "false",
				"message" => "Order is probably already assigned"
			], REST_Controller::HTTP_BAD_REQUEST);

		}
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
				$this->createTrail($action, $user_name, $status);
				$this->response([
					"status" => "true",
					"message" => "User created successfully"
				], REST_Controller::HTTP_CREATED);

			} else {
				$action = "Create User";
				$status = "Fail";
				$user_name = $_SERVER['PHP_AUTH_USER'];
				$this->createTrail($action, $user_name, $status);
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

	function updateClients_post()
	{

	}


	/*Donations*/
	function createDonation_post()
	{
		$client_id = $this->input->post('clientId', TRUE);
		$amount = $this->input->post('amount', TRUE);
		$date_awarded = $this->input->post('dateAwarded', TRUE);;

		$data = array(
			"clientId" => $client_id,
			"amountAwarded" => $amount,
			"balance" => $amount,
			"dateAwarded" => $date_awarded
		);
		$status = true;
		$status = $this->operations->addDonation($data);
		if ($status == true) {
			$action = "Create Donation";
			$status = "Success";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"status" => "true",
				"message" => "Donation created successfully"
			], REST_Controller::HTTP_CREATED);

		} else {
			$action = "Create Donation";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "false",
				"Message" => "An error occured"
			], REST_Controller::HTTP_BAD_REQUEST);

		}


	}

	function updateDonation_post()
	{

	}


	function syncBeneficiaries_post()
	{
		$beneficiary_id = $this->input->post("beneficiary_id");
		$name = $this->input->post("beneficiary_name");
		$dob = $this->input->post("beneficiary_dob");
		$gender = $this->input->post("gender");
		$locationId = $this->input->post("locationId");
		$identificationNumber = $this->input->post("identification_number");
		$contactInfo = $this->input->post("contact_info");
		$no_of_kin = $this->input->post("no_of_kin");
		$agent_id = $this->input->post("agent_id");
		$beneficiary_group = $this->input->post("beneficiaryGroupId");
		$date_registered = $this->input->post("dateRegistered");
		$printId = $this->input->post("printId");
		$beneficiary_picture = $_FILES['picture'];
		$target_dir = PICTURES;
		$picture = $beneficiary_picture["name"];

		if (move_uploaded_file($_FILES["picture"]["tmp_name"], "$target_dir/$picture")) {
			/*Upload Fingerprint*/
			$beneficiary_fingerprint = $_FILES['fingerprint'];
			$target_dir = FINGERPRINTS;
			$fingerprint = $beneficiary_fingerprint["name"];
			if (move_uploaded_file($_FILES["fingerprint"]["tmp_name"], "$target_dir/$fingerprint")) {
				//prepare data to insert to db

				$data = array(
					"beneficiaryId" => $beneficiary_id,
					"beneficiaryName" => $name,
					"mobile" => $contactInfo,
					"locationId" => $locationId,
					"email" => '',
					"dob" => $dob,
					"national_id" => $identificationNumber,
					"gender" => $gender,
					"pictureName" => $picture,
					"fingerprint" => $fingerprint,
					"no_of_kin" => $no_of_kin,
					"registeredBy" => $agent_id,
					"beneficiaryGroupId" => $beneficiary_group,
					"dateRegistered" => $date_registered,
					"printId" => $printId,
					"dateUploaded" => date("Y-m-d H:i:s"),

				);
				$status = $this->operations->newBeneficiary($data);
				$this->response([
					"result" => "true",
					"Message" => "Beneficiary Uploaded"
				], REST_Controller::HTTP_CREATED);

			} else {
				$this->response([
					"result" => "false",
					"Message" => "Could not upload fingerprint"
				], REST_Controller::HTTP_BAD_REQUEST);

			}
		} else {
			$this->response([
				"result" => "false",
				"Message" => "Could not upload Picture"
			], REST_Controller::HTTP_BAD_REQUEST);

		}


	}

	function uploadTransactions_post()
	{
		/*Upload Transactions*/
	}

	function createLocation_post()
	{
		$state_id = $this->input->post("state_id");
		$name = $this->input->post("name");
		$data = array(
			"stateId" => $state_id,
			"name" => $name,

		);
		$status = $this->operations->addLocation($data);
		if ($status == true) {
			$action = "Create Location";
			$status = "Success";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"status" => "true",
				"message" => "Lonation created successfully"
			], REST_Controller::HTTP_CREATED);

		} else {
			$action = "Create Location";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "false",
				"Message" => "An error occured"
			], REST_Controller::HTTP_BAD_REQUEST);

		}
	}

	public
	function createBeneficiaryGroup_post()
	{
		# code...
		$name = $this->input->post("name");
		$description = $this->input->post("description");
		$data = array(
			"name" => $name,
			"description" => $description
		);
		$status = $this->operations->addBeneficiaryGroup($data);
		if ($status == true) {
			$action = "Create Beneficiary Group";
			$status = "Success";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"status" => "true",
				"message" => "Beneficiary group created successfully"
			], REST_Controller::HTTP_CREATED);

		} else {
			$action = "Create Beneficiary Group";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "false",
				"Message" => "An error occured"
			], REST_Controller::HTTP_BAD_REQUEST);

		}
	}

	public
	function deleteBeneficiaryGroup_delete()
	{
		$group_id = $this->input->get("group_id");
		$status = $this->operations->deleteBeneficiaryGroup($group_id);
		if ($status == true) {
			$action = "Delete Beneficiary Group";
			$status = "Success";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"status" => "true",
				"message" => "Beneficiary group deleted successfully"
			], REST_Controller::HTTP_OK);

		} else {
			$action = "Delete Beneficiary Group";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "false",
				"Message" => "An error occured"
			], REST_Controller::HTTP_NO_CONTENT);

		}
	}
}


