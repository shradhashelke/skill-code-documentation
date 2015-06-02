<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Professional extends CI_Controller {
	/*
	 * Verify user type and redirect them
	 */
	public function Professional(){
		parent::__construct();
		if($this->session->userdata("userdetails")==NULL){
			redirect(base_url());
		}
		else{
			$obj=$this->session->userdata("loggedinas");
			if($obj=="student"){
				redirect("student/home");
			}elseif($obj=="teacher"){
				redirect("teacher/home");
			}elseif($obj=="tpo"){
				redirect("tpo/home");
			}
		}
	}
	/*
	 * Load database
	 */
	function loadDatabase($dbname)
	{
		$config['hostname'] = "localhost";
		$config['username'] = "root";
		$config['password'] = "";
		$config['database'] = $dbname;
		$config['dbdriver'] = "mysql";
		$config['dbprefix'] = "";
		$config['pconnect'] = FALSE;
		$config['db_debug'] = TRUE;
		$config['cache_on'] = FALSE;
		$config['cachedir'] = "";
		$config['char_set'] = "utf8";
		$config['dbcollat'] = "utf8_general_ci";
		return $config;
	}
	/*
	 * Load professional dashboard
	 */
	public function index()
	{
		//$this->load->view('home/index');
		$this->load->view('home/header');
		$this->load->view('home/index');
		$this->load->view('home/footer');
	}
	/*
	 * Professional registraation step1
	 */
	function step1()
	{
		$this->load->view("registration/professional/header");
		$this->load->view("registration/professional/index");
		$this->load->view("registration/professional/footer");
	}
	/*
	 * Save professional
	 */
	function step2()
	{
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->insert("professional_details",$_POST);
		redirect("professional/nextstep");
	}
	/*
	 * Professional saved
	 */
	function nextstep(){
		$this->load->library('form_validation');
		$this->load->view("registration/professional/header");
		$this->load->view("registration/professional/step2");
		$this->load->view("registration/professional/footer");
	}
	/*
	 * password saved of professional and account is created
	 */
	public function savenextstep()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|matches[passconf]');
		$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required');
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
		if ($this->form_validation->run() == FALSE)
		{
			//$this->load->view('myform');
			$this->nextstep();
		}
		else
		{
			//$this->load->view('formsuccess');
			$this->load->model("professionalop");
			$this->professionalop->step2();
			$this->step3();
		}
	}
	public function step3()
	{
		$this->load->view('registration/professional/header');
		$this->load->view('registration/professional/step3');
		$this->load->view('registration/professional/footer');
	}
	/*
	 * Dashboard
	 */
	public  function home(){
		$this->load->view('professional/home/header');
		$this->load->view('professional/home/index');
		$this->load->view('professional/home/footer');
	}
	/*
	 * List requests
	 */
	public  function request(){
		$this->load->model("administrator");
		$this->load->model("professionalop");		
		$this->load->view('professional/home/header');
		$this->load->view('professional/home/request');
		$this->load->view('professional/home/footer');
	}
	/*
	 * Show Profile
	 */
	public function profile(){
		$this->load->model("professionalop");
		$this->professionalop->updateDetails();
		$this->load->library('ciqrcode');
		$this->load->view("professional/profile/header");
		$this->load->view("professional/profile/index");
		$this->load->view("professional/profile/footer");
	}
	/*
	 * Make a request for nexamination module
	 */
	public function requestexam(){
		$this->load->model("professionalop");
		$this->professionalop->acceptexamrequest();
		$this->session->set_flashdata("msg","Your Request has been submitted");
		redirect("professional/request");
	}
	/*
	 * Send updates to dashboard via ajax requests
	 */
	public function updates()
	{
		$this->load->model("professionalop");
		$row=$this->professionalop->getupdates();
		$data['row']=$row;
		$this->load->view('professional/home/header');
		$this->load->view('professional/home/updates',$data);
		$this->load->view('professional/home/footer');		
	}
	/*
	 * Guide the students via thier requests
	 */
	public function studyresponse()
	{
		$this->load->model("professionalop");
		$this->load->view('professional/home/header');
		$this->load->view('professional/home/studyresponse');
		$this->load->view('professional/home/footer');		
	}
	/*
	 * Reply to study request of student
	 */
	public function sendstudyreply()
	{
		$config['upload_path'] = './files/studymaterial/';
		$config['allowed_types'] = 'gif|jpg|png|doc|docx|xls|xlsx|csv|txt|c|c|java|php|py|js|pdf';
		$config['remove_spaces']=true;
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload()){
			$error="<div class='alert alert-danger'>".$this->upload->display_errors()."</div>";
			$this->session->set_flashdata("error",$error);
			$id=$_POST['request_id'];
			redirect("professional/studyresponse?request_id=$id");
		}
		else
		{
			$error="<div class='alert alert-success'>Response Submitted</div>";
			$this->session->set_flashdata("success",$error);
			$this->load->model("professionalop");
			$this->professionalop->savestudydata();
			redirect("professional/request");
		}

	}
	/*
	 * Signout
	 */
	public function so()
	{
		$this->load->library("session");
		$this->session->sess_destroy();
		redirect(base_url());
	}
	/*
	 * List guidence requests
	 */
	public function guide(){
		$this->load->model("professionalop");
		$data['chat']=$this->professionalop->mychats();
		$this->load->view("professional/home/header");
		$this->load->view("professional/home/guide",$data);
		$this->load->view("professional/home/footer");
	}
	/*
	 * Send message/mail
	 */
	public function sendmsg(){
		$this->load->model("professionalop");
		$this->professionalop->sendmsg();
	}
}

/* End of file secure.php */
/* Location: ./application/controllers/secure.php */