<?php
/**
 * All GET requests
 * Retrieve Data for Reports and Dashboards
 * @author Cyrus Muchiri
 * @date 17th February 2020
 * @for Juba Express by SILKTECH
 * */
include_once 'Base.php';

use App\Models\Reports_model;
use Restserver\Libraries\REST_Controller;

class Reports extends Base
{
	public function __construct($config = 'rest')
	{
		parent::__construct($config);

		$this->load->model("Reports_model", "reports");
	}

	/**
	 *
	 */
	public function clients_get()
	{
		/*get attributes*/
		$client_id = $this->input->get('client_id', TRUE);
		$data = array(
			"clientId" => $client_id,
		);
		/*fetch data from model*/

		$result = $this->reports->getClients($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);

	}

	/**
	 *
	 */
	public function orders_get()
	{
		/*get attributes*/
		$approval_status = $this->input->get('approval_status', TRUE);
		$date_created = $this->input->get('date_created', TRUE);
		$date_created_range = $this->input->get('date_created_range', TRUE);
		$date_approved = $this->input->get('date_approved', TRUE);
		$date_approved_range = $this->input->get('date_approved_range', TRUE);
		$agent = $this->input->get('agent_id', TRUE);
		$order_id = $this->input->get('order_id', TRUE);
		$client_id = $this->input->get('client_id', TRUE);
		$location = $this->input->get('location_id', TRUE);
		$date_created_from = null;
		$date_created_to = null;
		$date_approved_from = null;
		$date_approved_to = null;
		if (!empty($date_created_range)) {
			$separate_dates = $this->splitDateRange($date_created_range);
			$date_created_from = $separate_dates[0];
			$date_created_to = $separate_dates[1];
		}
		if (!empty($date_approved_range)) {
			$separate_dates = $this->splitDateRange($date_approved_range);
			$date_approved_from = $separate_dates[0];
			$date_approved_to = $separate_dates[1];
		}
		$data = array(
			"approvalStatus" => $approval_status,
			"dateCreated" => $date_created,
			"lastUpdated" => $date_approved,
			"dateCreated >" => $date_created_from,
			"dateCreated <" => $date_created_to,
			"lastUpdated >" => $date_approved_from,
			"lastUpdated <" => $date_approved_to,
			"orders.orderId" => $order_id,
			"orders_agents.agentId" => $agent,
			"client_donations.clientId" => $client_id,
			"locations.locationId" => $location,


		);
		/*fetch data from model*/
		$result = $this->reports->getOrders($data);
		foreach ($result as $result_){
			$beneficiary_group_amounts =$this->reports->getBeneficiaryGroupAmounts($result_->orderId);
			$result_->beneficiary_group_amounts = $beneficiary_group_amounts;
		}
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}

	/**
	 *
	 */
	public function beneficiaries_get()
	{
		/*get attributes*/
		$beneficiary_id = $this->input->get('beneficiary_id', TRUE);
		$gender = $this->input->get('gender', TRUE);
		$location_id = $this->input->get('location_id', TRUE);
		$date_registered = $this->input->get('date_registered', TRUE);
		$date_registered_range = $this->input->get('date_registered_range', TRUE);
		$date_registered_from = null;
		$date_registered_to = null;
		if (!empty($date_registered_range)) {
			$separate_dates = $this->splitDateRange($date_registered_range);
			$date_registered_from = $separate_dates[0];
			$date_registered_to = $separate_dates[1];
		}
		$data = array(
			"beneficiaryId" => $beneficiary_id,
			"gender" => $gender,
			"dateRegistered" => $date_registered,
			"dateRegistered>" => $date_registered_from,
			"dateRegistered<" => $date_registered_to,
			"beneficiary_location.locationId" => $location_id,

		);

		/*fetch data from model
		*/
		/*fetch data from model*/
		$result = $result = $this->reports->getBeneficiaries($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}
/**
	 *
	 */
	public function kin_get()
	{
		/*get attributes*/
		$kin_id = $this->input->get('kin_id', TRUE);
		$beneficiary_id = $this->input->get('beneficiary_id', TRUE);
		$gender = $this->input->get('gender', TRUE);
		$location_id = $this->input->get('location_id', TRUE);
		$date_registered = $this->input->get('date_registered', TRUE);
		$date_registered_range = $this->input->get('date_registered_range', TRUE);
		$date_registered_from = null;
		$date_registered_to = null;
		if (!empty($date_registered_range)) {
			$separate_dates = $this->splitDateRange($date_registered_range);
			$date_registered_from = $separate_dates[0];
			$date_registered_to = $separate_dates[1];
		}
		$data = array(
			"kinId" => $kin_id,
			"kin.beneficiaryId" => $beneficiary_id,
			"gender" => $gender,
			"dateRegistered" => $date_registered,
			"dateRegistered>" => $date_registered_from,
			"dateRegistered<" => $date_registered_to,
			"kin_locations.locationId" => $location_id,

		);

		/*fetch data from model
		*/
		/*fetch data from model*/
		$result = $result = $this->reports->getKin($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}
	/**
	 *
	 */
	public function staff_get()
	{
		/*get attributes*/
		$staff_id = $this->input->get('staff_id', TRUE);
		$identification_number = $this->input->get('identification_number', TRUE);
		$date_created = $this->input->get('date_registered', TRUE);
		$gender = $this->input->get('gender', TRUE);
		$date_created_range = $this->input->get('date_registered_range', TRUE);
		$date_registered_from = null;
		$date_registered_to = null;
		if (!empty($date_created_range)) {
			$separate_dates = $this->splitDateRange($date_created_range);
			$date_registered_from = $separate_dates[0];
			$date_registered_to = $separate_dates[1];
		}
		$data = array(
			"staff_id" => $staff_id,
			"gender" => $gender,
			"identificationNumber" => $identification_number,
			"dateCreated" => $date_created,
			"dateCreated>" => $date_registered_from,
			"dateCreated<" => $date_registered_to,
		);

		/*fetch data from model
		*/
		$result = $result = $this->reports->getStaff($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}

	/**
	 *
	 */
	public function auditTrail_get()
	{
		/*get attributes*/
		$action = $this->input->get('action', TRUE);
		$ip = $this->input->get('ip', TRUE);;
		$userId = $this->input->get('user_id', TRUE);
		$dateRange = $this->input->get('dateRange', TRUE);
		$date = $this->input->get('date', TRUE);
		$start_date = null;
		$end_date = null;
		if (!empty($dateRange)) {
			$separate_dates = $this->splitDateRange($dateRange);
			$start_date = $separate_dates[0];
			$end_date = $separate_dates[1];
		}

		$data = array(
			"action" => $action,
			"ipAdress" => $ip,
			"userId" => $userId,
			'DATE(actionTime)'=>"$date",
			"actionTime>" => $start_date,
			"actionTime<" => $end_date,
		);

		/*fetch data from model
		*/
		$result = $result = $this->reports->getAuditTrail($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}

	public function admins_get()
	{
		$admin_id = $this->input->get('admin_id', TRUE);
		$identification_number = $this->input->get('identification_number', TRUE);
		$date_created = $this->input->get('date_registered', TRUE);
		$gender = $this->input->get('gender', TRUE);
		$date_created_range = $this->input->get('date_registered_range', TRUE);
		$date_registered_from = null;
		$date_registered_to = null;
		if (!empty($date_created_range)) {
			$separate_dates = $this->splitDateRange($date_created_range);
			$date_registered_from = $separate_dates[0];
			$date_registered_to = $separate_dates[1];
		}
		$data = array(
			"adminId" => $admin_id,
			"gender" => $gender,
			"identificationNumber" => $identification_number,
			"dateCreated" => $date_created,
			"dateCreated>" => $date_registered_from,
			"dateCreated<" => $date_registered_to,
		);

		/*fetch data from model
		*/
		$result = $result = $this->reports->getAdmins($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}

	public function agents_get()
	{
		$agent_id = $this->input->get('agent_id', TRUE);
		$identification_number = $this->input->get('identification_number', TRUE);
		$date_created = $this->input->get('date_registered', TRUE);
		$gender = $this->input->get('gender', TRUE);
		$date_created_range = $this->input->get('date_registered_range', TRUE);
		$date_registered_from = null;
		$date_registered_to = null;
		if (!empty($date_created_range)) {
			$separate_dates = $this->splitDateRange($date_created_range);
			$date_registered_from = $separate_dates[0];
			$date_registered_to = $separate_dates[1];
		}
		$data = array(
			"agentId" => $agent_id,
			"gender" => $gender,
			"identificationNumber" => $identification_number,
			"dateCreated" => $date_created,
			"dateCreated>" => $date_registered_from,
			"dateCreated<" => $date_registered_to,
		);

		/*fetch data from model
		*/
		$result = $result = $this->reports->getAgents($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}

	public function donations_get()
	{
		$client_id = $this->input->get('client_id', TRUE);
		$date_created = $this->input->get('date', TRUE);
		$date_created_range = $this->input->get('date_registered_range', TRUE);
		$amount_greater_than = $this->input->get('amount_awarded_greater_than', TRUE);;
		$amount_less_than = $this->input->get('amount_awarded_less_than', TRUE);;
		$amount_equal_to = $this->input->get('amount_awarded_equal_to', TRUE);;
		$balance_greater_than = $this->input->get('balance_greater_than', TRUE);;
		$balance_equal_to = $this->input->get('balance_equal_to', TRUE);;
		$balance_less_than = $this->input->get('balance_less_than', TRUE);;

		$date_registered_from = null;
		$date_registered_to = null;
		if (!empty($date_created_range)) {
			$separate_dates = $this->splitDateRange($date_created_range);
			$date_registered_from = $separate_dates[0];
			$date_registered_to = $separate_dates[1];
		}
		$data = array(
			"client_donations.clientId" => $client_id,
			"dateAwarded" => $date_created,
			"dateAwarded>" => $date_registered_from,
			"dateAwarded<" => $date_registered_to,
			"balance >" => $balance_greater_than,
			"balance <" => $balance_less_than,
			"balance " => $balance_equal_to,
			"amountAwarded >" => $amount_greater_than,
			"amountAwarded <" => $amount_less_than,
			"amountAwarded " => $amount_equal_to,
			);

		/*fetch data from model
		*/
		$result = $result = $this->reports->getDonations($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}

	public function modules_get()
	{

		$group_code = $this->input->get('group_code', TRUE);
		$level = $this->input->get('level_code', TRUE);
		$parentId = $this->input->get('parent_id', TRUE);


		$data = array(
			"user_groups.groupCode" => $group_code,
			"modules.level" => $level,
			"parentId" => $parentId

		);
		$result = $result = $this->reports->getModules($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}

	public function userGroups_get()
	{
		$result = $result = $this->reports->getUserGroups();
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}

	public function idTypes_get()
	{
		$result = $result = $this->reports->getIdentificationTypes();
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}

	public function orderComparison_get()
	{


		$dat = date('Y-m-d');
		$date = new DateTime($dat);
		$client_id = $this->input->get('client_id', TRUE);

		$result['delivered'] = array();
		$result['not_delivered'] = array();

		$period = $date->modify("-11 months");
		for ($i = 0; $i < 12; $i++) {
			$data = array(
				"client_donations.clientId" => $client_id,

			);
			$instance_month = $period->format("Y-m");
			array_push($result['delivered'], $this->reports->orderComparison($data, "delivered", $instance_month));
			array_push($result['not_delivered'], $this->reports->orderComparison($data, "undelivered", $instance_month));
			$period = $date->modify("+1 months");

		}

		$this->response([
			"status" => "true",
			"result" => $result
		], REST_Controller::HTTP_OK);
	}

	public function states_get()
	{
		$state_id = $this->input->get('state_id', TRUE);
		$data = array(
			"stateId" => $state_id,
		);
		$result = $result = $this->reports->getStates($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}
	public function locations_get(){
		$location_id =  $this->input->get('location_id', TRUE);
		$state_id =  $this->input->get('state_id', TRUE);
		$data = array(
			"stateId" => $state_id,
			"locations.id"=>$location_id
		);
		$result = $result = $this->reports->getLocations($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}
	public function beneficiaryGroups_get()
	{
		# code...
		$result  = $this->reports->getBeneficiaryGroups();
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}
	public function transactions_get(){
		$date = $this->input->get('date', TRUE);
		$date_range = $this->input->get('date_range', TRUE);
		$agent_id = $this->input->get('agent_id', TRUE);
		$beneficiary_id = $this->input->get('beneficiary_id', TRUE);
		$order_id = $this->input->get('order_id', TRUE);
		$client_id = $this->input->get('client_id', TRUE);
		$date_to = null;
		$date_from = null;
		if (!empty($date_range)) {
			$separate_dates = $this->splitDateRange($date_range);
			$date_from = $separate_dates[0];
			$date_to = $separate_dates[1];
		}
		$data = array(
			"time_of_transaction" => $date,
			"time_of_transaction >" => $date_from,
			"time_of_transaction <" => $date_to,
			"t.agent_id" => $agent_id,
			"t.beneficiary_id" => $beneficiary_id,
			"t.order_id" => $order_id,
			"client_donations.clientId" => $client_id,
		);
		$result = $this->reports->getTransactions($data);
		$location_name = null;
		foreach ($result as $item) {
			/*Get City, Town From Latitude and Longitude*/
				$geolocation = $item->latitude . ',' . $item->longitude;
				$request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $geolocation . '&sensor=false&key=AIzaSyCYj7_3RpBL7ozF-WIk_piDxo-BSFOt1rM';
				$file_contents = file_get_contents($request);
				$json_decode = json_decode($file_contents);
				if (isset($json_decode->results[0])) {
					$response = array();
					foreach ($json_decode->results[0]->address_components as $addressComponet) {
						if (in_array('political', $addressComponet->types)) {
							$response[] = $addressComponet->long_name;
						}
					}

					if (isset($response[0])) {
						$first = $response[0];
					} else {
						$first = 'null';
					}
					if (isset($response[1])) {
						$second = $response[1];
					} else {
						$second = 'null';
					}
					if (isset($response[2])) {
						$third = $response[2];
					} else {
						$third = 'null';
					}
					if (isset($response[3])) {
						$fourth = $response[3];
					} else {
						$fourth = 'null';
					}
					if (isset($response[4])) {
						$fifth = $response[4];
					} else {
						$fifth = 'null';
					}


					if ($first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth != 'null') {
						$location_name = $first .' ,'.$second.' ,'.$third.' ,'.$fourth.' ,'.$fifth;
					} else if ($first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth == 'null') {
						$location_name = $first .' ,'.$second.' ,'.$third.' ,'.$fourth;
					} else if ($first != 'null' && $second != 'null' && $third != 'null' && $fourth == 'null' && $fifth == 'null') {
						$location_name = $first .' ,'.$second.' ,'.$third;
					} else if ($first != 'null' && $second != 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null') {
						$location_name = $first .' ,'.$second;
					} else if ($first != 'null' && $second == 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null') {
						$location_name = $first .'';
					}
				}

			$item->locationDelivered = $location_name;
		}

		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}

	public function transactionsGraph_get(){

		$dat = date('Y-m-d');
		$date = new DateTime($dat);
		$client_id = $this->input->get('client_id', TRUE);

		$result['transactions'] = array();


		$period = $date->modify("-11 months");
		for ($i = 0; $i < 12; $i++) {
			$instance_month = $period->format("Y-m");
			$data = array(
				"client_donations.clientId" => $client_id,
				"time_of_transaction"=>$instance_month

			);

			array_push($result['transactions'], $this->reports->getTransactionForGraph($data));
			$period = $date->modify("+1 months");

		}

		$this->response([
			"status" => "true",
			"result" => $result
		], REST_Controller::HTTP_OK);
	}


}
