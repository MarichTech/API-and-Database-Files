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
		$client_id = $this->input->post('client_id', true);
		$location_id = $this->input->post('location_id', true);
		$amount = $this->input->post('amount', true);
		$grant_name = $this->input->post('grant_name', true);
		$beneficiary_group_amounts = $this->input->post('beneficiary_group_amounts');
		$decoded_ben_amounts = (array)json_decode($beneficiary_group_amounts);

		$order = array(
			"client_id" => $client_id,
			"amount" => $amount,
			"locationId" => $location_id,
			"grant_name" => $grant_name,
		);

		$status = $this->operations->createOrder($order, $decoded_ben_amounts);
		if ($status == true) {
			$action = "Create Order";
			$status = "Success";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"status" => "true",
			], REST_Controller::HTTP_CREATED);

		} else {
			$action = "Create Order";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "false",
			], REST_Controller::HTTP_BAD_REQUEST);

		}

	}

	public function updateOrder_post()
	{
		$delivery_status = $this->input->post('delivery_status', true);
		$locationDelivered = $this->input->post('location_delivered', true);
		$dateDelivered = $this->input->post('date_delivered', true);
		$locationExpected = $this->input->post('location_expected', true);
		$amount = $this->input->post('amount', true);
		$orderId = $this->input->post('order_id', true);
		$order = array(

			"deliveryStatusId" => $delivery_status,
			"amount" => $amount,
			"locationExpected" => $locationExpected,
			"locationDelivered" => $locationDelivered,
			"dateDelivered" => $dateDelivered,
			"lastUpdated" => date("Y-m-d H:i:s"),
		);
		$status = $this->operations->updateOrder($order, $orderId);

		if ($status == true) {
			$action = "Update Order";
			$status = "Success";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"status" => "true",
			], REST_Controller::HTTP_CREATED);

		} else {
			$action = "Update Order";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "false",
			], REST_Controller::HTTP_BAD_REQUEST);

		}

	}

	function approveOrder_post()
	{
		//change status
		$order_id = $this->input->post('order_id', true);
		$data = array(
			"order_id" => $order_id,
		);
		$status = $this->operations->approveOrder($data);


		if ($status == true) {
			//send mail
			$action = "Approve Order";
			$status = "Success";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"status" => "true",
			], REST_Controller::HTTP_CREATED);

		} else {
			$action = "Approve Order";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "false",
			], REST_Controller::HTTP_BAD_REQUEST);

		}

	}

	public function assignOrder_post()
	{
		$agents = json_decode($this->input->post('agents', true));
		$order_id = $this->input->post('order_id', true);
		$algorithm = $this->input->post('algorithm', true);
		$status = false;
		if ($algorithm == "alphabetic") {
			/*1. Get the order details*/
			$orderDetails = $this->reports->getOrders($data = array("orders.orderId" => $order_id));

			/*2. Check Dispatch*/
			if (!empty($orderDetails[0]->dateDispatched)) {
				$status = false;
			} else {

				/*3. Get all beneficiaries  eligible for the order (Location & group)*/
				/*Beneficiary groups where clause*/
				$beneficiary_group_amounts = $this->reports->getBeneficiaryGroupAmounts($orderDetails[0]->orderId);
				$where_clause = "";

				for ($i = 0; $i < sizeof($beneficiary_group_amounts); $i++) {
					$beneficiary_group = (array)$beneficiary_group_amounts[$i];
					if ($i == (sizeof($beneficiary_group_amounts) - 1)) {
						$where_clause .= "beneficiary_groups.id =" . $beneficiary_group["ben_group_id"] . " ";
					} else {
						$where_clause .= "beneficiary_groups.id =" . $beneficiary_group["ben_group_id"] . " OR ";
					}
				}

				$beneficiaries_filters = $data = array(
					"beneficiary.locationId" => $orderDetails[0]->locationId,
					"$where_clause " => null,
				);
				$beneficiaries = $this->reports->getBeneficiaries($beneficiaries_filters);
				/*Divide beneficiary array equally to the agents and add relationship*/
				$ben_count = sizeof($beneficiaries);
				$agent_count = sizeof($agents);
				$ben_per_agent = round($ben_count / $agent_count, 0);
				$start = 0;
				$end = $ben_per_agent;
				$status = $this->operations->updateDispatchOrder($order_id);
				for ($j = 0; $j < $agent_count; $j++) {
					/*
										echo " Debug <br>";
										echo "  <br>";
										echo " Agent Id : $agents[$j] <br>";
										echo " Start : $start <br>";
										echo " End : $end <br>";
										echo " Ben Count : $ben_count <br>";
										echo " Agent Count : $agent_count <br>";
										echo " Ben per Agent : $ben_per_agent <br>";
										echo "  <br>";*/

					for ($i = $start; $i < $end; $i++) {
						/*echo " Debug Inner <br>";
						echo "  <br>";
						echo " Start : $start <br>";
						echo " End : $end <br>";
						echo " i : $i <br>";

						echo "  <br>";*/


						$data = array(
							"order_id" => $order_id,
							"beneficiary_id" => $beneficiaries[$i]["beneficiaryId"],
							"agent_id" => $agents[$j]
						);
						//	print_r($data);
						$this->operations->assignOrder($data);
					}
					if ($j == ($agent_count - 2)) {
						$start += $ben_per_agent;
						$end = sizeof($beneficiaries);;
					} else {
						$start += $ben_per_agent;
						$end += $ben_per_agent;
					}
				}
			}
		}


		if ($status == true) {
			$action = "Assign Order";
			$status = "Success";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"status" => "true",
			], REST_Controller::HTTP_CREATED);

		} else {
			$action = "Assign Order";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"status" => "false",
				"message" => "Order is probably already assigned",
			], REST_Controller::HTTP_BAD_REQUEST);

		}
	}

	/*Users*/
	/*Password is */

	//todo check duplicates of usernames
	public function createUser_post()
	{
		$group_code = $this->input->post('groupCode', true);
		if (empty($group_code)) {
			$this->response([
				"status" => "false",
				"message" => "Group code invalid or missing",
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$status = false;
			switch ($group_code) {

				case "003":
					$data = array(
						"groupCode" => $group_code,
						"name" => $this->input->post('name', true),
						"repEmail" => $this->input->post('repEmail', true),
						"repMobile" => $this->input->post('repMobile', true),
						"addressLocation" => $this->input->post('addressLocation', true),
						"descriptions" => $this->input->post('descriptions', true),
						"userName" => $this->input->post('userName', true),
						"password" => $this->bcrypt->hash($this->input->post('password', true)),
					);
					$status = $this->operations->addClient($data);
					break;
				default:
					$data = array(
						"groupCode" => $group_code,
						"name" => $this->input->post('name', true),
						"email" => $this->input->post('email', true),
						"mobile" => $this->input->post('mobile', true),
						"addressLocation" => $this->input->post('addressLocation', true),
						"gender" => $this->input->post('gender', true),
						"stateIdentificationType" => $this->input->post('stateIdentificationType', true),
						"identificationNumber" => $this->input->post('identificationNumber', true),
						"responsibilities" => $this->input->post('responsibilities', true),
						"userName" => $this->input->post('userName', true),
						"password" => $this->bcrypt->hash($this->input->post('password', true)),
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
					"message" => "User created successfully",
				], REST_Controller::HTTP_CREATED);

			} else {
				$action = "Create User";
				$status = "Fail";
				$user_name = $_SERVER['PHP_AUTH_USER'];
				$this->createTrail($action, $user_name, $status);
				$this->response([
					"result" => "false",
					"message" => "Existing username or broken input",
				], REST_Controller::HTTP_BAD_REQUEST);

			}

		}
	}

	public function updateUser_post()
	{
		$group_code = $this->input->post('groupCode', true);
		$id = $this->input->post('id', true);

		if (empty($group_code)) {
			$this->response([
				"status" => "false",
				"message" => "Group code invalid or missing",
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$status = false;
			switch ($group_code) {

				case "003":
					$data = array(
						"groupCode" => $group_code,
						"id" => $id,
						"name" => $this->input->post('name', true),
						"repEmail" => $this->input->post('repEmail', true),
						"repMobile" => $this->input->post('repMobile', true),
						"addressLocation" => $this->input->post('addressLocation', true),
						"descriptions" => $this->input->post('descriptions', true),
					);
					$status = $this->operations->updateClient($data);
					break;
				default:
					$data = array(
						"groupCode" => $group_code,
						"id" => $id,
						"name" => $this->input->post('name', true),
						"email" => $this->input->post('email', true),
						"mobile" => $this->input->post('mobile', true),
						"addressLocation" => $this->input->post('addressLocation', true),
						"gender" => $this->input->post('gender', true),
						"stateIdentificationType" => $this->input->post('stateIdentificationType', true),
						"identificationNumber" => $this->input->post('identificationNumber', true),
						"responsibilities" => $this->input->post('responsibilities', true),
					);
					switch ($group_code) {
						case "001":
							$status = $this->operations->updateStaff($data);
							break;
						case "002":
							$status = $this->operations->updateAgent($data);
							break;
						case "004":
							$status = $this->operations->updateAdmin($data);
					}

			}
			if ($status == true) {
				$action = "Update User";
				$status = "Success";
				$user_name = $_SERVER['PHP_AUTH_USER'];
				$this->createTrail($action, $user_name, $status);
				$this->response([
					"status" => "true",
					"message" => "User Updated successfully",
				], REST_Controller::HTTP_CREATED);

			} else {
				$action = "Update User";
				$status = "Fail";
				$user_name = $_SERVER['PHP_AUTH_USER'];
				$this->createTrail($action, $user_name, $status);
				$this->response([
					"result" => "false",
					"message" => "Failed Updating User",
				], REST_Controller::HTTP_BAD_REQUEST);

			}

		}
	}

	public function updateClients_post()
	{

	}

	public function deleteUser_delete()
	{
		$usertype = $this->input->get("user_type");
		$id = $this->input->get("id");
		switch ($usertype) {
			case "agent":
				$status = $this->operations->deleteAgent($id);
				break;
			case "client":
				$status = $this->operations->deleteClient($id);
				break;
			case "staff" :
				$status = $this->operations->deleteStaff($id);
				break;
			case "admin":
				$status = $this->operations->deleteAdmin($id);
				break;
		}
		if ($status) {
			$action = "Delete" . $usertype;
			$status = "Success";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "true",
				"message" => "Success",
			], REST_Controller::HTTP_BAD_REQUEST);


		} else {
			$action = "Delete" . $usertype;
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "false",
				"message" => "Failed Deleting User",
			], REST_Controller::HTTP_BAD_REQUEST);

		}
	}


	/*Donations*/
	public function createDonation_post()
	{
		$client_id = $this->input->post('clientId', true);
		$amount = $this->input->post('amount', true);
		$date_awarded = $this->input->post('dateAwarded', true);

		$data = array(
			"clientId" => $client_id,
			"amountAwarded" => $amount,
			"balance" => $amount,
			"dateAwarded" => $date_awarded,
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
				"message" => "Donation created successfully",
			], REST_Controller::HTTP_CREATED);

		} else {
			$action = "Create Donation";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "false",
				"message" => "An error occured",
			], REST_Controller::HTTP_BAD_REQUEST);

		}

	}

	public function updateDonation_post()
	{

	}

	public function syncBeneficiaries_post()
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

		$status = $this->operations->checkExistence($beneficiary_id);
		if ($status == true) {
			$this->response([
				"result" => "false",
				"message" => "Already Uploaded",
			], REST_Controller::HTTP_OK);
		} else {
			//compress image
			$image = $_FILES["picture"]["tmp_name"];
			/*$destination = $picture;
			$destination = $this->compress($image,"$target_dir/$picture",40);
			var_dump($destination);
			var_dump($picture);*/
			if ($this->compress($image, "$target_dir/$picture", 40)) {
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
						"dob" => date("Y-m-d", strtotime($dob)),
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
						"message" => "Beneficiary Uploaded",
					], REST_Controller::HTTP_CREATED);

				} else {
					$this->response([
						"result" => "false",
						"message" => "Could not upload fingerprint",
					], REST_Controller::HTTP_BAD_REQUEST);

				}
			} else {
				$this->response([
					"result" => "false",
					"message" => "Could not upload Picture",
				], REST_Controller::HTTP_BAD_REQUEST);

			}
		}

	}

	public function syncKin_post()
	{
		$kin_id = $this->input->post("kin_id");
		$beneficiary_id = $this->input->post("beneficiary_id");
		$name = $this->input->post("name");
		$dob = $this->input->post("dob");
		$gender = $this->input->post("gender");
		$locationId = $this->input->post("locationId");
		$identificationNumber = $this->input->post("identification_number");
		$contactInfo = $this->input->post("contact_info");
		$printId = $this->input->post("printId");
		$kin_picture = $_FILES['picture'];
		$target_dir = PICTURES;
		$picture = $kin_picture["name"];
		$relatonship = $this->input->post("relationship");

		$status = $this->operations->checkExistenceKin($kin_id);
		if ($status == true) {
			$this->response([
				"result" => "false",
				"message" => "Already Uploaded",
			], REST_Controller::HTTP_OK);
		} else {

			if ($this->compress($_FILES["picture"]["tmp_name"], "$target_dir/$picture", 40)) {
				/*Upload Fingerprint*/
				$kin_fingerprint = $_FILES['fingerprint'];
				$target_dir = FINGERPRINTS;
				$fingerprint = $kin_fingerprint["name"];
				if (move_uploaded_file($_FILES["fingerprint"]["tmp_name"], "$target_dir/$fingerprint")) {
					//prepare data to insert to db

					$data = array(
						"kinId" => $kin_id,
						"beneficiaryId" => $beneficiary_id,
						"kinName" => $name,
						"dob" => date("Y-m-d", strtotime($dob)),
						"gender" => $gender,
						"locationId" => $locationId,
						"identificationNo" => $identificationNumber,
						"mobile" => $contactInfo,
						"pictureName" => $picture,
						"fingerprint" => $fingerprint,
						"relationship" => $relatonship,
						"printId" => $printId,
						"dateUploaded" => date("Y-m-d H:i:s"),

					);
					$status = $this->operations->newKin($data);
					$this->response([
						"result" => "true",
						"message" => "Kin Uploaded",
					], REST_Controller::HTTP_CREATED);

				} else {
					$this->response([
						"result" => "false",
						"message" => "Could not upload fingerprint",
					], REST_Controller::HTTP_BAD_REQUEST);

				}
			} else {
				$this->response([
					"result" => "false",
					"message" => "Could not upload Picture",
				], REST_Controller::HTTP_BAD_REQUEST);

			}
		}
	}

	public function uploadTransactions_post()
	{
		/*Upload Transactions*/
		$beneficiary_id = $this->input->post("beneficiary_id");
		$order_id = $this->input->post("order_id");
		$verified_person = $this->input->post("verified_person");
		$kin_id = $this->input->post("kin_id");
		$longitude = $this->input->post("longitude");
		$latitude = $this->input->post("latitude");
		$amount = $this->input->post("amount");
		$time_of_transaction = $this->input->post("time_of_transaction");
		$agent_id = $this->input->post("agent_id");
		$data = array(

			"beneficiary_id" => $beneficiary_id,
			"order_id" => $order_id,
			"verified_person" => $verified_person,
			"kin_id" => $kin_id,
			"longitude" => $longitude,
			"latitude" => $latitude,
			"amount" => $amount,
			"time_of_transaction" => $time_of_transaction,
			"date_uploaded" => date("Y-m-d H:i:s"),
			"agent_id" => $agent_id

		);
		$status = $this->operations->newTransaction($data);
		if ($status == false) {
			$this->response([
				"result" => "true",
				"message" => "Transaction Was already updated",
			], REST_Controller::HTTP_ALREADY_REPORTED);
		} else {
			$this->response([
				"result" => "true",
				"message" => "Transaction Uploaded",
			], REST_Controller::HTTP_CREATED);
		}
	}

	public function createLocation_post()
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
				"message" => "Lonation created successfully",
			], REST_Controller::HTTP_CREATED);

		} else {
			$action = "Create Location";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "false",
				"message" => "An error occured",
			], REST_Controller::HTTP_BAD_REQUEST);

		}
	}

	public function createBeneficiaryGroup_post()
	{
		# code...
		$name = $this->input->post("name");
		$description = $this->input->post("description");
		$data = array(
			"name" => $name,
			"description" => $description,
		);
		$status = $this->operations->addBeneficiaryGroup($data);
		if ($status == true) {
			$action = "Create Beneficiary Group";
			$status = "Success";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"status" => "true",
				"message" => "Beneficiary group created successfully",
			], REST_Controller::HTTP_CREATED);

		} else {
			$action = "Create Beneficiary Group";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "false",
				"message" => "An error occured",
			], REST_Controller::HTTP_BAD_REQUEST);

		}
	}

	public function deleteBeneficiaryGroup_delete()
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
				"message" => "Beneficiary group deleted successfully",
			], REST_Controller::HTTP_OK);

		} else {
			$action = "Delete Beneficiary Group";
			$status = "Fail";
			$user_name = $_SERVER['PHP_AUTH_USER'];
			$this->createTrail($action, $user_name, $status);
			$this->response([
				"result" => "false",
				"message" => "An error occured",
			], REST_Controller::HTTP_NO_CONTENT);

		}
	}


}
