<?php
require APPPATH . '/libraries/REST_Controller.php';
use \Restserver\Libraries\REST_Controller;
class Base extends REST_Controller
{

public function __construct($config = 'rest')
{
	parent::__construct($config);
}

	/**
	 * @param $dateRange
	 * @return false|string[]
	 */
	protected function splitDateRange($dateRange){
		$separate_dates = explode(' to ', $dateRange);
		/*Check if delimiter used is hyphen*/
		if(empty($separate_dates)){
			$separate_dates =  explode(' - ', $dateRange);
		}
		return $separate_dates;


}

}
