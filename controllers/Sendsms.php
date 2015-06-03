<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
*/
class Sendsms extends CI_Controller {
	/*
	 * Sending the SMS to users
	 */
	public function index()
	{
		$this->load->model("sms");
		$this->sms->sendsms("mobile","msg");
	}
}
