<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Class is used to handle various ajax request used as background api's.
 */
class Api extends CI_Controller {
	/*
	 * Return List of All students
	 */
	public function getstudent()
	{
		$this->load->model("apis");
		$this->apis->getStudents();
	}
	/*
	 * Show department based on id
	 */
	public function getDept()
	{
		$id=$_POST['id'];
		$this->load->model("apis");
		$this->apis->getDeptList($id);
	}
	/*
	 * Get teacher list
	 */
	public function getteacher()
	{
		$this->load->model("apis");
		$this->apis->getTeacher();
	}
	/*
	 * Get tpo list
	 */
	public function gettpo()
	{
		$this->load->model("apis");
		$this->apis->getTpo();
	}
	/*
	 * perform administrator login based on magnetic swipe card
	 */
	public function adminlogin()
	{
		$id=str_replace("q","71",$_POST['id']);
		$id=str_replace("?","",$id);
		$id=trim($id);
		$this->load->library("session");
		$this->load->model("securelogin");
		$cnt=$this->securelogin->cardlogin($id);
		/*if($cnt==1){
			$this->session->set_userdata("validcard","1");
		}
		else{
			$this->session->set_userdata("validcard","0");
		}*/
		$this->session->set_userdata("validcard","1");
		echo $cnt;
	}
	/*
	 * Send periodic updates to user dashboard using ajax and json functions
	 * [id]
	 */
	public function updates($user)
	{
		if($user=="student")
		{
		 $stud=$this->session->userdata("userdetails");
		 $cid=$stud->college_id;
		 $this->load->model("generalop");
		 $cnt=$this->generalop->getupdatesstudent($cid);
		 $unread=$this->generalop->updatereadmsg();
	 	 $response = array('cnt' => $cnt,'messages' => 'hi','unread'=>$unread);
         echo json_encode( $response );
		}
		else if($user=="tpo"){
		 $stud=$this->session->userdata("userdetails");
		 $cid=$stud->college_id;
		 $this->load->model("generalop");
		 $cnt=$this->generalop->getupdatestpo($cid); 
	 	 $response = array('cnt' => $cnt,'messages' => 'hi');
         echo json_encode( $response );
		}
		else if($user=="teacher"){
			$this->load->model("generalop");
			$unread=$this->generalop->updatereadmsg();
			$response = array('cnt'=>'0','messages' => 'hi','unread' => $unread);
			echo json_encode( $response);
		}
		else{
			$prof=$this->session->userdata("userdetails");
			$email=$prof->email;
			$this->load->model("generalop");
			$cnt=$this->generalop->getupdates($email);
			$unread=$this->generalop->updatereadmsg();
			$response = array('cnt' => $cnt,'messages' => 'hi','unread'=>$unread);
			echo json_encode( $response );
		}
	}
	/*
	 * Load chat using ajax
	 */
	public function chat($username)
	{
		$this->load->model("administrator");
		$data['username']=$username;
		$data['tbl']="examnotifyprofessional";
		$this->load->view("chat/tpo/header");
		$this->load->view("chat/tpo/index",$data);
		$this->load->view("chat/tpo/footer");
	}
	/*
	 * Update exam time and date
	 */
	public function updateexamschedule()
	{
		$this->load->model("apis");
		$this->apis->updateschedule();
		redirect("tpo/exam");	
	}
	/*
	* Edit the profile
	*/
	public function inlineedit()
	{
		$this->load->model("inline");
		if(!empty($_POST))
		{
			//database settings
			 $loggedinas=$this->session->userdata("loggedinas");
			foreach($_POST as $field_name => $val)
			{
				//clean post values
				$field_userid = strip_tags(trim($field_name));
				$val = trim($val);
	
				//from the fieldname:user_id we need to get user_id
				$split_data = explode(':', $field_userid);
				$user_id = $split_data[1];
				$field_name = $split_data[0];
				if(!empty($user_id) && !empty($field_name) && !empty($val))
				{
					//update the values
					//mysql_query("UPDATE user_details SET $field_name = '$val' WHERE user_id = $user_id") or mysql_error();
					if($loggedinas=="student") {
						echo $this->inline->updateStudent($field_name, $val);
					}
					if($loggedinas=="professional") {
						echo $this->inline->updateProfessional($field_name, $val);
					}
					 if($loggedinas=="teacher"){
							echo $this->inline->updateTeacher($field_name,$val);
						}
				}
				else{
					echo "Error";
				}
			}
		} else {
			echo "Invalid Requests";
		}
	}
	/*
	 * search student profile
	 */
	public function searchprofile(){
		$this->load->model("apis");
		echo $this->apis->getprofile();
	}
	/*
	 * Get chat history
	 */
	public function getchathistory($pid,$name){
		$this->load->model("apis");
		$this->apis->loadchat($pid,$name);
	}
	/*
	 * Algorithm to suggest the guide
	 */
	public function suggestguide($q=""){
		$this->load->model("studentop");
		$this->load->model("profile");
		$data['rank']=$this->studentop->suggestonquery($q);
		if(count($data['rank'])==0){
			$data['rank']=$this->studentop->suggest();
		}
		$profiles=$this->studentop->getProfiledata($data);
		$this->profile->showprofiles($profiles);
	}
	/*
	 * Rank the suggested guides based on skills
	 */
	public function filter($grade,$type,$skill,$dept,$download){
		$this->load->model("tpoop");
		$dept=explode(",",urldecode($dept));
		if($type=="onlygrades") {
			$this->tpoop->filtergrade($grade,$dept,$download);
		}
		if($type=="gradeandskill") {
			$this->load->model("tpoop");
			$emailrank=$this->tpoop->suggest($skill);
			$this->tpoop->filtergradeandskill($grade,urldecode($skill),$emailrank,$dept,$download);
		}
	}
}

/* End of file api.php */
/* Location: ./application/controllers/api.php */
