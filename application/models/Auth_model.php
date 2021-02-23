<?php
/**
 * @author Cyrus Muchiri
 *
*/

class Auth_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/**Authenticate a user
	 * @param $username
	 * @param $password
	 * @return array|array[]|false|object|object[]
	 */
	function auth($username, $password){
		$this->db->select("userId,username, passCode, groupCode");
		$this->db->from(array("users"));
		$this->db->where("userName",$username);
		$user = $this->db->get();
		if($user==false) {
			return false;
		}else{
			$user = $user->row();

			$user_details = array();
			if ($this->bcrypt->compare($password, $user->passCode)) {
				if($user->groupCode =="004"){
					$this->db->select("administrators.*,groupDescription,description as stateIdentificationTypeDesc");
					$this->db->from(array("administrators"));
					$this->db->join("user_groups","user_groups.groupCode = administrators.groupCode");
					$this->db->join("state_identification_type","state_identification_type.id = administrators.stateIdentificationType");
					$this->db->where("userId",$user->userId);
					$user_details = $this->db->get()->row();
				}elseif ($user->groupCode=="002"){
					$this->db->select("agents.*,groupDescription,description as stateIdentificationTypeDesc");
					$this->db->from(array("agents"));
					$this->db->join("user_groups","user_groups.groupCode = agents.groupCode");
					$this->db->join("state_identification_type","state_identification_type.id = agents.stateIdentificationType");
					$this->db->where("userId",$user->userId);
					$user_details = $this->db->get()->row();
				}elseif ($user->groupCode=="003"){
					$this->db->select("clients.*,groupDescription");
					$this->db->from(array("clients"));
					$this->db->join("user_groups","user_groups.groupCode = clients.groupCode");
					$this->db->where("userId",$user->userId);
					$user_details = $this->db->get()->row();
				}elseif ($user->groupCode=="001"){
					$this->db->select("staff.*,groupDescription,description as stateIdentificationTypeDesc");
					$this->db->from(array("staff"));
					$this->db->join("user_groups","user_groups.groupCode = staff.groupCode");
					$this->db->join("state_identification_type","state_identification_type.id = staff.stateIdentificationType");
					$this->db->where("userId",$user->userId);
					$user_details = $this->db->get()->row();
				}
				return $user_details;
			} else {
				return false;
			}
		}

	}
}
