<?php


class Reports_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/**
	 * @param $params
	 * @return array|array[]|object|object[]
	 */
	public function getClients($params)
	{

		$this->db->select("clientId,userId,name,repEmail,repMobile,clients.groupCode,groupDescription,clients.dateCreated,
		addressLocation,descriptions");
		$this->db->from("clients");
		$this->db->join("user_groups", "user_groups.groupCode = clients.groupCode");
		foreach ($params as $key => $value) {
			if ($value != null) {
				$this->db->where("$key", $value);
			}
		}
		return $this->db->get()->result("array");

	}

	/**
	 * @param $params
	 * @return array|array[]|object|object[]
	 */
	public function getOrders($params)
	{
		$this->db->select("orders.orderId,client_donations.grantName,locations.id as locationId,locations.name as locationName,amount,
		order_activation_status.id as statusCode,order_activation_status.description as activationStatus,
		orders.dateCreated,orders.dateDispatched,orders.dateDelivered,orders.lastUpdated,agents.name as agentName,
		 agents.agentId, 
		clients.name as clientName, clients.clientId as clientId");
		$this->db->from("orders");
		$this->db->join("order_activation_status", "orders.approvalStatus = order_activation_status.id");
		$this->db->join("orders_locations", "orders.orderId = orders_locations.orderId","LEFT OUTER");
		$this->db->join("locations", "orders_locations.locationId = locations.id");
		$this->db->join("orders_agents", "orders_agents.orderId = orders.orderId", "LEFT OUTER");
		$this->db->join("agents", "orders_agents.agentId = agents.agentId","LEFT OUTER");
		$this->db->join("orders_donations", "orders.orderId = orders_donations.orderId");
		$this->db->join("client_donations", "client_donations.id = orders_donations.clientDonationId");
		$this->db->join("clients", "client_donations.clientId = clients.clientId");
		foreach ($params as $key => $value) {
			if ($value != null) {
				$this->db->where("$key", $value);
			}
		}
		return $this->db->get()->result();
	}

	/**
	 * @param $params
	 * @return array|array[]|object|object[]
	 */
	public function getBeneficiaries($params)
	{
		$this->db->select("beneficiary.beneficiaryId,beneficiaryName,locations.name as locationAddress,gender,email,mobile,printId,
		fsName as fingerPrintFileSystemName,beneficiary.locationId,locations.name
		,dob,pictureName,national_id,beneficiaryGroupId,beneficiary_groups.name as groupName");
		$this->db->from("beneficiary");
		$this->db->join("fingerprints","fingerprints.printId =beneficiary.fingerPrintId","LEFT");
		$this->db->join("locations","beneficiary.locationId =locations.id","LEFT");
		$this->db->join("beneficiary_groups","beneficiary.beneficiaryGroupId =beneficiary_groups.id","LEFT");
		foreach ($params as $key => $value) {
			if ($value != null) {
				$this->db->where("$key", $value);
			}
		}
		return $this->db->get()->result("array");
	}
	/**
	 * @param $params
	 * @return array|array[]|object|object[]
	 */
	public function getKin($params)
	{
		$this->db->select("kin.kinId,kin.beneficiaryId,kinName,locations.name as locationAddress,gender,mobile,kin.printId,relationship,
		fsName as fingerPrintFileSystemName,kin.locationId
		,dob,pictureName,identificationNo as national_id	");
		$this->db->from("kin");
		$this->db->join("fingerprints","fingerprints.printId =kin.printId","LEFT");
		$this->db->join("locations","kin.locationId =locations.id","LEFT");
		foreach ($params as $key => $value) {
			if ($value != null) {
				$this->db->where("$key", $value);
			}
		}
		return $this->db->get()->result("array");
	}

	/**
	 * @param $params
	 * @return array|array[]|object|object[]
	 */
	public function getStaff($params){
		$this->db->select("email,name,userName,mobile,addressLocation,gender,state_identification_type.description as idType,
		identificationNumber,responsibilities,staff.dateCreated 
		as dateRegistered, lastModified");
		$this->db->from("staff");
		$this->db->join("users","users.userId = staff.userId");
		$this->db->join("state_identification_type","staff.stateIdentificationType = state_identification_type.id");
		foreach ($params as $key => $value) {
			if ($value != null) {
				$this->db->where("$key", $value);
			}
		}
		return $this->db->get()->result("array");
	}

	/**
	 * @param $params
	 * @return array|array[]|object|object[]
	 */
	public function getAuditTrail($params){
		$this->db->select("action,ipAddress,status,actionTime,audit_trail.userName,groupDescription");
		$this->db->from("audit_trail");
		$this->db->join("users","users.userName = audit_trail.userName");
		$this->db->join("user_groups","users.groupCode = user_groups.groupCode");
		foreach ($params as $key => $value) {
			if ($value != null) {
				if($key == "action"){
					
					$this->db->like("$key", $value);
				}else{
				$this->db->where("$key", $value);
				}
			}
		}
		return $this->db->get()->result("array");
	}

	/**
	 * @param $params
	 * @return array|array[]|object|object[]
	 */
	public function getModules($params){
		$this->db->select("modules.moduleId,moduleName,icon,url");
		$this->db->from("modules");
		$this->db->join("access_levels","access_levels.moduleId=modules.moduleId");
		$this->db->join("user_groups","user_groups.groupCode=access_levels.groupCode");
		foreach ($params as $key => $value) {
			if ($value != null) {
				$this->db->where("$key", $value);
			}
		}
		return $this->db->get()->result();
	}

	/**
	 * @param $params
	 * @return array|array[]|object|object[]
	 */
	public function getAgents($params){
		$this->db->select("agentid,name,username,email,mobile,addressLocation,gender,identificationNumber,
		state_identification_type.description as idType,agents.dateCreated 
		as dateRegistered, dateModified,addressLocation,gender,users.username,users.passCode");
		$this->db->from("agents");
		$this->db->join("users","users.userId = agents.userId");
		$this->db->join("state_identification_type","agents.stateIdentificationType = state_identification_type.id");

		foreach ($params as $key => $value) {
			if ($value != null) {
				$this->db->where("$key", $value);
			}
		}
		return $this->db->get()->result("array");
	}

	/**
	 * @param $params
	 * @return array|array[]|object|object[]
	 */
	public function getAdmins($params){
		$this->db->select("adminId, name,username,email,mobile,addressLocation,gender,identificationNumber,state_identification_type.description as idType,
		dateCreated 
		as dateRegistered, dateModified,addressLocation,gender");
		$this->db->from("administrators");
		$this->db->join("users","users.userId = administrators.userId");
		$this->db->join("state_identification_type","administrators.stateIdentificationType = state_identification_type.id");

		foreach ($params as $key => $value) {
			if ($value != null) {
				$this->db->where("$key", $value);
			}
		}
		return $this->db->get()->result("array");
	}

	/**
	 * @param $params
	 * @return array|array[]|object|object[]
	 */
	public function getDonations($params){
		$this->db->select("id,name as ClientName,clients.clientId,dateAwarded,balance ,amountAwarded");
		$this->db->from("client_donations");
		$this->db->join("clients","clients.clientId = client_donations.clientId");
		foreach ($params as $key => $value) {
			if ($value != null) {
				$this->db->where("$key", $value);
			}
		}
		return $this->db->get()->result("array");
	}

	public function getUserGroups()
	{
		$this->db->select("*");
		$this->db->from("user_groups");
		return $this->db->get()->result();
	}

	public function getIdentificationTypes()
	{

		$this->db->select("*");
		$this->db->from("state_identification_type");
		return $this->db->get()->result();
	}

	public function orderComparison(array $data,$type,$instance_month)
	{

		$this->db->select("count(transactions.id) as count");
		$this->db->from("transactions");
		$this->db->join("orders_donations", "orders.orderId = orders_donations.orderId");
		$this->db->join("client_donations", "client_donations.id = orders_donations.clientDonationId");

		if($type =="delivered"){
			$this->db->where("deliveryStatusId",1);
			$this->db->like("dateDelivered", $instance_month);

		}elseif ($type=="undelivered"){
			$this->db->where("deliveryStatusId",2);
			$this->db->like("orders.dateCreated", $instance_month);
		}
		foreach ($data as $key => $value) {
			if ($value != null) {
				$this->db->where("$key", $value);
			}
		}
		//$this->db->group_by("MONTH(dateCreated)");
		$result = $this->db->get()->row();
		return $result->count;

	}

	public function getStates(array $data)
	{
		$this->db->select("*");
		$this->db->from("states");
		foreach ($data as $key => $value) {
			if ($value != null) {
				$this->db->where("$key", $value);
			}
		}
		$result = $this->db->get()->result();
		return $result;
	}

	public function getLocations(array $data)
	{
		$this->db->select("locations.*,states.name as state_name");
		$this->db->from("locations");
		$this->db->join("states","locations.stateId = states.id");
		foreach ($data as $key => $value) {
			if ($value != null) {
				$this->db->where("$key", $value);
			}
		}
		$result = $this->db->get()->result();
		return $result;
	}
	public function getBeneficiaryGroups(){
		$this->db->select("*");
		$this->db->from("beneficiary_groups");
		$result = $this->db->get()->result();
		return $result;

	}

	public function getBeneficiaryGroupAmounts($orderId)
	{
		$this->db->select("beneficiary_group_amounts.beneficiary_group_id as ben_group_id,name,amount");
		$this->db->from("beneficiary_group_amounts");
		$this->db->join("beneficiary_groups","beneficiary_group_id = beneficiary_groups.id");
		$this->db->where("order_id",$orderId);
		$result = $this->db->get()->result();
		return $result;
	}

	public function getTransactions($data){
		$this->db->select("t.id as transaction_id,t.beneficiary_id,t.order_id,t.agent_id,b.beneficiaryName as beneficiary_name,
		client_donations.grantName as grant_name,t.verified_person,t.kin_id,
		t.amount,t.time_of_transaction,agents.name as agent_name,t.longitude,t.latitude,locations.name as locationExpected");
		$this->db->from("transactions t");
		$this->db->join("beneficiary b","b.beneficiaryId = t.beneficiary_id");
		$this->db->join("agents","agents.agentId = t.agent_id","LEFT OUTER");
		$this->db->join("orders","orders.orderId = t.order_id");
		$this->db->join("orders_donations","orders.orderId = orders_donations.orderId");
		$this->db->join("client_donations","orders_donations.clientDonationId = client_donations.id");
		$this->db->join("orders_locations","orders.orderId = orders_locations.orderId");
		$this->db->join("locations","orders_locations.locationId = locations.id");
		foreach ($data as $key => $value) {
			if ($value != null) {
					$this->db->where("$key", $value);
			}
		}
		return $this->db->get()->result();
	}
	public function getTransactionForGraph($data){
		$this->db->select("count(t.id) as count");
		$this->db->from("transactions t");
		$this->db->join("beneficiary b","b.beneficiaryId = t.beneficiary_id");
		$this->db->join("agents","agents.agentId = t.agent_id","LEFT OUTER");
		$this->db->join("orders","orders.orderId = t.order_id");
		$this->db->join("orders_donations","orders.orderId = orders_donations.orderId");
		$this->db->join("client_donations","orders_donations.clientDonationId = client_donations.id");


		foreach ($data as $key => $value) {
			if ($value != null) {
				if($key == "time_of_transaction"){
					$this->db->like("$key", $value);
				}else {
					$this->db->where("$key", $value);
				}
			}
		}
		$result = $this->db->get()->row();
		return $result->count;
	}


}
