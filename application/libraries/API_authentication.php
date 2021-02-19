<?php

class API_authentication
{


	/**
	 * API Login
	 * @param $username
	 * @param $password
	 * @return bool
	 */
	public function login($username,$password){


		return true;
	}

	function test_get(){
		echo $this->response;
	}

}
