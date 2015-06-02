<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {
	/* default function controller
	*/
	public function index()
	{
		parent::__construct();
		if($this->session->userdata("userdetails")!=NULL){
			$obj=$this->session->userdata("loggedinas");
			if($obj=="professional"){
				redirect("professional/home");
			}elseif($obj=="teacher"){
				redirect("teacher/home");
			}elseif($obj=="tpo"){
				redirect("tpo/home");
			}elseif($obj=="student"){
				redirect("student/home");
			}elseif($obj=="admin"){
				redirect("secure/adminstep2");
			}
		}
		$this->load->view('home/header');
		$this->load->view('home/index');
		$this->load->view('home/footer');
	}
	public function sendmail()
	{
		echo "<script>alert('sentnnnn');</script>";
		$this->load->model("mymail");
		$this->mymail->checkStatus();	
	}
	/*
	 * Below methods are for testing
	 */
	public function ex()
	{
		$this->load->library('aes');
		echo $a=$this->aes->encrypt("raj");
		echo $this->aes->decrypt($a);	
	}
	public  function temp(){
		$this->load->library("aes");
		$key = '134567891234567';
		$secret_string = 'VaibhavraJ';
		$old_key_size = $this->aes->size();
		$this->aes->size(256);    // Also 192, 128
		echo $encrypted_secret_string = $this->aes->enc($secret_string, $key);
		echo $decrypted_secret_string = $this->aes->dec("0zEKRoCaD4P9oA8GqznsdMxJf2ydBFcfGGJ0h1UhW3a3o+HoYkk7gRP7ZpOIFM4S+pG/rg9aBGxOkP94kQvCJQ==", $key);
		$this->aes->size($old_key_size);  // Restore the old key size.
		$this->load->library("encrypt");
		echo $this->encrypt->decode("0zEKRoCaD4P9oA8GqznsdMxJf2ydBFcfGGJ0h1UhW3a3o+HoYkk7gRP7ZpOIFM4S+pG/rg9aBGxOkP94kQvCJQ==");
	}
	public function pdf()
	{
		$this->load->library('pdf');
	$this->pdf->load_view('welcome');
	$this->pdf->render();
	$this->pdf->stream("welcome.pdf");	
	}
	public function prof(){
		$this->load->view("student/home/temp");
	}
	public function decode(){
		echo $this->encrypt->decode("JscZpqNTMpo32PLuu1mJI~qyDEEB9KfbIE.eL3JQhQ5iQ3wJFqjTYbhFwXZ9e0WNuzvsQycGMJm0ndPySt6YuA--");
	}
	public function encode(){
		echo $this->encrypt->encode("admin");
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */