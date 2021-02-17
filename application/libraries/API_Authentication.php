<?php

class API_Authentication
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
