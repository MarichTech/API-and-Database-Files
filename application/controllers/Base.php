<?php
require APPPATH . '/libraries/REST_Controller.php';
use \Restserver\Libraries\REST_Controller;
class Base extends REST_Controller
{

public function __construct($config = 'rest')
{
	parent::__construct($config);
}

}
