<?php
/**
 * @author Cyrus Muchiri
 * @mail cmuchiri8429@gmail.com
 * v. 1.0.0
 * Data model
 */

class Data_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/** Creates a new order.
	 * Updates new balance of a donation
	 * @param $order
	 * @return bool
	 */
	public function createOrder($order){

		/*Create donation*/
		$data = array(
			"clientId"=>$order["client_id"],
			"grantName"=>$order["grant_name"],
			"amountAwarded"=>$order["amount"],
			"balance" =>$order["amount"],
			"dateAwarded" => date("Y-m-d H:i:s")
		);
		$this->db->insert("client_donations",$data);
		$donation_id = $this->db->insert_id();
		/*Prepare data for order table*/
		$order_table = array(
			"deliveryStatusId" => 2,
			"dateCreated" => date("Y-m-d H:i:s"),
			"dateDispatched"=>"",
			"dateDelivered"=>"",
			"amount"=>$order["amount"],
			"lastUpdated"=>"",

		);
		/*Insert into orders table*/
		$this->db->insert("orders",$order_table);
		$order_id = $this->db->insert_id();
		/*Prepare order_locations look up table*/
		$orders_locations = array(
			"locationId"=>$order["locationId"],
			"orderId"=>$order_id
		);
		/*Insert into orders_beneficiaries */
		$this->db->insert("orders_locations",$orders_locations);
		/*Prepare orders_donations look up table*/
		$orders_donations = array(
			"clientDonationId"=>$donation_id,
			"orderId"=>$order_id
		);
		/*Insert into order donations*/
		return $this->db->insert("orders_donations",$orders_donations);

		/*Calculate and update balance in donations table*/
		/*$this->db->select("balance");
		$this->db->from("client_donations");
		$this->db->where("id",$order["donationId"]);
		$balance_result = $this->db->get()->result("array");
		$balance = $balance_result[0]["balance"];
		$new_balance = $balance-$order["amount"];
		$data= array(
			"balance"=>$new_balance
		);*/
		/*Update donations with new balance*/
	/*	$this->db->set("balance",$new_balance);
		$this->db->where("id",$order["donationId"]);
		return $this->db->update("client_donations",$data);*/
	}


	/**
	 * @param array $data
	 * @return bool
	 */
	public function addClient(array $data)
	{
		$userId = $this->addUser($data);
		if($userId == false){
			return false;
		}
		$clients_table = array(
			"userId" =>$userId,
			"name" => $data["name"],
			"repEmail" => $data["repEmail"],
			"repMobile" => $data["repMobile"],
			"groupCode"=>$data["groupCode"],
			"addressLocation" => $data["addressLocation"],
			"descriptions" => $data["descriptions"],
			"dateCreated" => date("Y-m-d H:i:s"),

		);

		 return $this->db->insert("clients",$clients_table);

	}

	/**
	 * @param array $data
	 * @return bool
	 */
	public function addAgent(array $data)
	{

		$userId = $this->addUser($data);
		if($userId == false){
			return false;
		}
		$agents_table = array(
			"userId"=>$userId,
			"name" => $data["name"],
			"email" =>$data["email"],
			"mobile" => $data["mobile"],
			"addressLocation" => $data["addressLocation"],
			"gender" => $data["gender"],
			"stateIdentificationType" => $data["stateIdentificationType"],
			"identificationNumber" =>$data["identificationNumber"],
			"groupCode" => $data["groupCode"],
			"dateCreated" => date("Y-m-d H:i:s"),
			"dateModified" => date("Y-m-d H:i:s"),


		);
		return $this->db->insert("agents",$agents_table);
	}

	public function addStaff(array $data)
	{
		$userId = $this->addUser($data);
		if($userId == false){
			return false;
		}
		$staff_table = array(
			"userId"=>$userId,
			"name" => $data["name"],
			"email" =>$data["email"],
			"mobile" => $data["mobile"],
			"addressLocation" => $data["addressLocation"],
			"gender" => $data["gender"],
			"stateIdentificationType" => $data["stateIdentificationType"],
			"identificationNumber" =>$data["identificationNumber"],
			"groupCode" => $data["groupCode"],
			"responsibilities" => $data["groupCode"],
			"dateCreated" => date("Y-m-d H:i:s"),
			"lastModified" => date("Y-m-d H:i:s"),


		);
		return $this->db->insert("staff",$staff_table);
	}

	public function addAdmin(array $data)
	{
		$userId = $this->addUser($data);
		if($userId == false){
			return false;
		}

		$admin_table = array(
			"userId"=>$userId,
			"name" => $data["name"],
			"email" =>$data["email"],
			"mobile" => $data["mobile"],
			"addressLocation" => $data["addressLocation"],
			"gender" => $data["gender"],
			"stateIdentificationType" => $data["stateIdentificationType"],
			"identificationNumber" =>$data["identificationNumber"],
			"groupCode" => $data["groupCode"],
			"dateCreated" => date("Y-m-d H:i:s"),
			"dateModified" => date("Y-m-d H:i:s"),


		);
		return $this->db->insert("administrators",$admin_table);
	}

	/**
	 * @param $data
	 * @return int
	 */
	private function addUser($data){
		$users_table = array(
			"groupCode"=>$data["groupCode"],
			"userName" => $data["userName"],
			"passCode" => $data["password"],
		);
		$this->db->insert("users",$users_table);
		return $this->db->insert_id();
	}

	/**
	 * @param $data
	 * @return bool
	 */
	public function addDonation($data){
		return $this->db->insert("client_donations",$data);
	}

	/**
	 * @param $data
	 * @return bool
	 */
	public function insertTrail($data){
		return $this->db->insert("audit_trail",$data);

	}

	/**
	 * @param $data
	 * @return bool
	 */
	public function linkOrder($data)
	{

		$this->db->select("*");
		$this->db->from("orders_agents");
		$this->db->where("orderId",$data["order_id"]);
		$num_rows = $this->db->get()->num_rows();
		if($num_rows >0){
			return false;
		}
		$order_agents = array(
			"orderId" =>$data["order_id"],
			"agentId" =>$data["agent_id"]

		);
		$this->db->insert("orders_agents",$order_agents);
		//update dispatch data
		$data_orders = array(
			"dateDispatched" =>date("Y-m-d H:i:s")
		);
		$this->db->set("dateDispatched",$data_orders["dateDispatched"]);
		$this->db->where("orderId",$data["order_id"]);
		return $this->db->update("orders",$data_orders);
	}

	public function updateOrder(array $params,$orderId)
	{
		$update_array = array();
		foreach ($params as $key => $value) {
			if ($value != null) {
				$update_array[$key]=$value;
				$this->db->set("$key", $value);
			}
		}
		$this->db->where("orderId",$orderId);
		$status = $this->db->update("orders",$update_array);
		return $status;
	}

	public function addLocation($data)
	{
		return $this->db->insert("locations",$data);
	}

	public function addBeneficiaryGroup($data)
	{	
		return $this->db->insert("beneficiary_groups",$data);
	}
	public function deleteBeneficiaryGroup($id)
	{	
		$this->db->where("id",$id);
		return $this->db->delete("beneficiary_groups");
	}
}
