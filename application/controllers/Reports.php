<?php
/**
 * All GET requests
 * Retrieve Data for Reports and Dashboards
 * @author Cyrus Muchiri
 * @date 17th February 2020
 * @for Juba Express by SILKTECH
 * */
include_once "Base.php";
use Restserver\Libraries\REST_Controller;
class Reports extends Base
{
	public function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->load->model("Reports_model","model");
	}

	/**
	 *
	 */
	public function clients_get(){
		/*get attributes*/
		$client_id = $this->input->get('client_id', TRUE);
		$data= array(
			"clientId"=>$client_id,
		);
		/*fetch data from model*/

		$result = $this->model->getClients($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);

	}
	/**
	 *
	 */
	public function orders_get(){
		/*get attributes*/
		$delivery_status = $this->input->get('delivery_status', TRUE);
		$date_created = $this->input->get('date_created', TRUE);
		$date_created_range = $this->input->get('date_created_range', TRUE);
		$date_delivered = $this->input->get('date_delivered', TRUE);
		$date_delivered_range = $this->input->get('date_delivered_range', TRUE);
		$agent = $this->input->get('agent_id', TRUE);
		$beneficiary = $this->input->get('beneficiary_id', TRUE);
		$order_id = $this->input->get('order_id', TRUE);
		$donation_id = $this->input->get('donation_id', TRUE);
		$client_id =$this->input->get('client_id', TRUE);
		$date_created_from=null;
		$date_created_to =null;
		$date_delivered_from=null;
		$date_delivered_to =null;
		if(!empty($date_created_range)){
			$separate_dates = $this->splitDateRange($date_created_range);
			$date_created_from = $separate_dates[0];
			$date_created_to =  $separate_dates[1];
		}
		if(!empty($date_delivered_range)){
			$separate_dates = $this->splitDateRange($date_delivered_range);
			$date_delivered_from = $separate_dates[0];
			$date_delivered_to =  $separate_dates[1];
		}
		$data= array(
			"statusCode"=>$delivery_status,
			"dateCreated"=>$date_created,
			"dateDelivered"=>$date_delivered,
			"dateCreated >"=>$date_created_from,
			"dateCreated <"=>$date_created_to,
			"dateDelivered >"=>$date_delivered_from,
			"dateDelivered <"=>$date_delivered_to,
			"orderId"=>$order_id,
			"orders_agents.agentId"=>$agent,
			"orders_beneficiaries.beneficiaryId"=>$beneficiary,
			"clientDonationId"=>$donation_id,
			"client_donations.clientId"=>$client_id,


		);
		/*fetch data from model*/
		$result = $result = $this->model->getOrders($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}
	/**
	 *
	 */
	public function beneficiaries_get(){
		/*get attributes*/
		$beneficiary_id = $this->input->get('beneficiary_id', TRUE);
		$gender = $this->input->get('gender', TRUE);
		$date_registered = $this->input->get('date_registered', TRUE);
		$date_registered_range = $this->input->get('date_registered_range', TRUE);
		$date_registered_from = null;
		$date_registered_to = null;
		if(!empty($date_registered_range)){
			$separate_dates = $this->splitDateRange($date_registered_range);
			$date_registered_from = $separate_dates[0];
			$date_registered_to =  $separate_dates[1];
		}
		$data = array(
			"beneficiaryId" => $beneficiary_id,
			"gender"=>$gender,
			"dateRegistered"=>$date_registered,
			"dateRegistered>"=>$date_registered_from,
			"dateRegistered<"=>$date_registered_to,
			""
		);

		/*fetch data from model
		*/
		/*fetch data from model*/
		$result = $result = $this->model->getBeneficiaries($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}
	/**
	 *
	 */
	public function staff_get(){
		/*get attributes*/
		$staff_id = $this->input->get('staff_id', TRUE);
		$identification_number = $this->input->get('identification_number', TRUE);
		$date_created = $this->input->get('date_registered', TRUE);
		$gender =$this->input->get('gender', TRUE);
		$date_created_range = $this->input->get('date_registered_range', TRUE);
		$date_registered_from = null;
		$date_registered_to = null;
		if(!empty($date_created_range)){
			$separate_dates = $this->splitDateRange($date_created_range);
			$date_registered_from = $separate_dates[0];
			$date_registered_to =  $separate_dates[1];
		}
		$data = array(
			"staff_id"=>$staff_id,
			"gender"=>$gender,
			"identificationNumber"=>$identification_number,
			"dateCreated"=>$date_created,
			"dateCreated>"=>$date_registered_from,
			"dateCreated<"=>$date_registered_to,
		);

		/*fetch data from model
		*/
		$result = $result = $this->model->getStaff($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}

	/**
	 *
	 */
	public function auditTrail_get(){
		/*get attributes*/
		$action = $this->input->get('action', TRUE);
		$ip=$this->input->get('ip', TRUE);;
		$userId=$this->input->get('user_id', TRUE);
		$dateRange=$this->input->get('dateRange', TRUE);
		$start_date = null;
		$end_date = null;
		if(!empty($dateRange)){
			$separate_dates = $this->splitDateRange($dateRange);
			$start_date = $separate_dates[0];
			$end_date =  $separate_dates[1];
		}

		$data = array(
			"action"=>$action,
			"ipAdress"=>$ip,
			"userId"=>$userId,
			"actionTime>"=>$start_date,
			"actionTime<"=>$end_date,
		);

		/*fetch data from model
		*/
		$result = $result = $this->model->getAuditTrail($data);
		$this->response([
			"result" => $result
		], REST_Controller::HTTP_OK);
	}

}
