<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
	/*
	 * Constructor Function
	 */
	public function Admin(){
		parent::__construct();
	}
	/*
	 * Load student registration form.
	 */
	public function student_register()
	{
		$this->load->view('registration/student/header');
		$this->load->view('registration/student/index');
		$this->load->view('registration/student/footer');
	}
	/*
	 * Load Teacher Registration Form
	 */
	public function teacher_register()
	{
		$this->load->model("teacher");
		$this->load->view('registration/teacher/header');
		$this->load->view('registration/teacher/index');
		$this->load->view('registration/teacher/footer');
	}
	/*
	 * Load Professional Registration Form
	 */
	public function professional_register()
	{
		$this->load->model("professional");
		$this->load->view('registration/professional/header');
		$this->load->view('registration/professional/index');
		$this->load->view('registration/professional/footer');
	}
	/*
	 * Administrator Dashboard
	 */
	public function super()
	{
			$this->load->model("administrator");
			$data['data']=$this->administrator->stats();
			$this->load->view('admin/super/header');
			$this->load->view('admin/super/index',$data);
			$this->load->view('admin/super/footer');

	}
	/*
	 * Administrator account setup
	 */
	public function subadminsetup()
	{
		$this->load->view('admin/super/header');
		$this->load->view('admin/super/subadmin');
		$this->load->view('admin/super/footer');
	}
	/*
	 * Load New College registration Form
	 */
    public function collegesetup()
	{
		$this->load->view('admin/super/header');
		$this->load->view('admin/super/newcollege');
		$this->load->view('admin/super/footer');
	}
	/*
	 * Show list of colleges
	 */
	public function showcollege()
	{
		$this->load->model("administrator");
		$this->load->view('admin/super/header');
		$this->load->view('admin/super/listcollege');
		$this->load->view('admin/super/footer');
	}
	/*
	 * List the professional requests
	 */
	public function showrequest()
	{
		$this->load->model("administrator");
		$this->load->view('admin/super/header');
		$this->load->view('admin/super/listrequest');
		$this->load->view('admin/super/footer');
	}
	/*
	 * List subadministrator form
	 */
	public function showsubadmin()
	{
		$this->load->view('admin/super/header');
		$this->load->view('admin/super/showsubadmin');
		$this->load->view('admin/super/footer');
	}
	/*
	 * add new college to database
	 */
	public function addcollege()
	{
		$this->load->model("administrator");
		$this->administrator->addcollege();	
	}
	/*
	 * Load on upload is successfull
	 */
	public function uploadsuccess()
	{
		$this->load->view('admin/super/header');
		$this->load->view('admin/super/uploadsuccess');
		$this->load->view('admin/super/footer');	
	}
	/*
	 * delete College from list
	 */
	public function deletecolleges()
	{
	   $this->load->model("administrator");
       $ids = ( explode( ',', $this->input->get_post('ids') ));
       $this->administrator->deletecollege($ids);
    }
	/*
	 * delete professional request if not valid
	 */
	public function deleteprofessionalrequest()
	{
	   $this->load->model("administrator");
       $ids = ( explode( ',', $this->input->get_post('ids') ));
       $this->administrator->deleteprequest($ids);
    }
	/*
	 * Show perticular college based on id
	 */
	public function college()
	{
		$id=$_GET['college'];
		$this->load->model("excel");
		$this->load->model("administrator");
		$this->load->view('admin/super/header');
		$data=array('id'=>$id);
		$this->load->view('admin/super/showcollege',$data);
		$this->load->view('admin/super/footer');
	}
	/*
	 * Download Excel file of student
	 */
	public function downloadstudent()
	{
		$this->load->helper("file");
		$this->load->helper("download");
		$this->load->model("administrator");
		$file_location=$this->administrator->downloadstudent();
		$data = file_get_contents($file_location); 
		$filename=$this->session->userdata('college_id')."_Student.csv";
		force_download($filename, $data);
		
	}
	/*
	 * Download Excel File of Teacher
	 */
	public function downloadteacher()
	{
		$this->load->helper("file");
		$this->load->helper("download");
		$this->load->model("administrator");
		$file_location=$this->administrator->downloadteacher();
		$data = file_get_contents($file_location); 
		$filename=$this->session->userdata('college_id')."_Teacher.csv";
		force_download($filename, $data);
		
	}
	/*
	 * Download Excel File of TPO
	 */
	public function downloadtpo()
	{
		$this->load->helper("file");
		$this->load->helper("download");
		$this->load->model("administrator");
		$file_location=$this->administrator->downloadtpo();
		$data = file_get_contents($file_location); 
		$filename=$this->session->userdata('college_id')."_TPO.csv";
		force_download($filename, $data);
		
	}
	/*
	 * Send userid created files, to college email address
	 */
	public function sendfiles()
	{	$this->load->library('zip');
		$this->load->helper("file");
		$this->load->helper("download");
		$this->load->model("administrator");
		
		$this->load->view('admin/super/header');
		$this->load->view('admin/super/sendfiles');
		$this->load->view('admin/super/footer');
	}
	/*
	 * Peerform signout.
	 */
	public function signout()
	 {
	 	$this->load->model("securelogin");
		$this->securelogin->logout();
	 }
	/*
	 * Accept Professional Request
	 */
	public function acceptrequest($id)
	{
		$this->load->model("generalop");
		$this->generalop->acceptprofessional($id);
		redirect("admin/showrequest");	
	}
	/*
	 * Load Notification Page
	 */
	public function notification()
	{
		$this->load->model("administrator");
		$this->load->view("admin/super/header");
		$this->load->view("admin/super/notification");
		$this->load->view("admin/super/footer");
	}
	/*
	 * Send User credentials to user email address
	 */
	public function sendcredential()
	{
		$this->load->model("administrator");
		$this->load->view("admin/super/header");
		$this->load->view("admin/super/sendcredential");
		$this->load->view("admin/super/footer");
	}
	/*
	 * List new examination module requests
	 */
	public function examrequest()
	{
		$this->load->model("administrator");
		$this->load->view("admin/super/header");
		$this->load->view("admin/super/examrequest");
		$this->load->view("admin/super/footer");
	}
	/*
	 * Accept professional's exam module request.
	 */
	public function acceptexamrequest($name,$email,$college_id,$mobile){
		$this->load->model("exam");
		$this->load->model("administrator");
		if($this->exam->saveCust($name,$email,$college_id,$mobile)){
			$this->administrator->delexamrequest($email);
			redirect("admin/examrequest/?success");
		}
		else{
			redirect("admin/examrequest/?error");
		}
	}
	/*
	 * New student rregistration form
	 */
	public function newstudent()
	{
		$this->load->model("administrator");
		$this->load->view('admin/super/header');
		$this->load->view('admin/super/newstudent');
		$this->load->view('admin/super/footer');
	}
	/*
	 * New TPO
	 */
	public function newtpo()
	{
		$this->load->model("administrator");
		$this->load->view('admin/super/header');
		$this->load->view('admin/super/newtpo');
		$this->load->view('admin/super/footer');
	}
	/*
	 * New Teacher form
	 */
	public function teacher()
	{
		$this->load->model("administrator");
		$this->load->view('admin/super/header');
		$this->load->view('admin/super/newteacher');
		$this->load->view('admin/super/footer');
	}
	/*
	 * Save newly created student
	 */
	public function savenewstudent(){
		$this->load->model("administrator");
		$this->administrator->savenewstudent();
	}
	/*
	 * Show login modal
	 */
	public function login(){
		$this->load->view("admin/super/header");
		$this->load->view("admin/super/modallogin");
		$this->load->view("admin/super/footer");
	}
	/*
	 * Destroy the session
	 */
	public function so(){
		$this->session->sess_destroy();
		redirect(base_url());
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */