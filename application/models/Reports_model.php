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
		delivery_status.statusCode,delivery_status.statusDescription as deliveryStatusDescription,
		orders.dateCreated,orders.dateDispatched,orders.dateDelivered,orders.lastUpdated,agents.name as agentName,
		 agents.agentId, 
		clients.name as clientName, clients.clientId as clientId");
		$this->db->from("orders");
		$this->db->join("delivery_status", "orders.deliveryStatusId = delivery_status.statusCode");
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
		return $this->db->get()->result("array");
	}

	/**
	 * @param $params
	 * @return array|array[]|object|object[]
	 */
	public function getBeneficiaries($params)
	{
		$this->db->select("beneficiary.beneficiaryId,beneficiaryName,locationAddress,gender,email,mobile,printId,
		fsName as fingerPrintFileSystemName,dateTaken as fingerPrintDateCreated,beneficiary_location.locationId,locations.name
		,dob,pictureName,national_id");
		$this->db->from("beneficiary");
		$this->db->join("fingerprints","fingerprints.printId =beneficiary.fingerPrintId","LEFT OUTER");
		$this->db->join("beneficiary_location","beneficiary_location.beneficiaryId =beneficiary.beneficiaryId","LEFT OUTER");
		$this->db->join("locations","beneficiary_location.locationId =locations.id","LEFT OUTER");
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
				$this->db->where("$key", $value);
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

		$this->db->select("count(orders.orderId) as count");
		$this->db->from("orders");
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


}
