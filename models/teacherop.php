<?php
class Teacherop extends CI_Model {
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
	function step1()
  {
	 $stud=$_POST;
	 //echo count($stud);
	 //print_r($stud);
	 
	 $config=$this->loadDatabase("skill");
	 $db1=$this->load->database($config,TRUE);
	 $db1->insert("teacher_details",$stud);
	 $db1->where("id",$_POST['teacher_id']);
	 $data['register']=1;
	 $db1->update("teacher",$data);
	 redirect("teacher/step2");
  }
  	function step2()
  {
	  $stud=$this->session->userdata("userdetails");
	$e=$this->encrypt->encode($_POST['password']); //Encrypt the Password
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$data['password']=$e;
	$db1->where("id",$_POST['teacher_id']);
	$db1->update("teacher",$data);
	  $data1['userid']=$_POST['teacher_id'];
	  $data1['profiletype']="teacher";
	  $data1['profile_id']=random_string('numeric', 6);
	  $data1['email']=$stud->email;
	  $db1->insert("profile",$data1);
  }
	function getStudyRequest()
	{
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		return $db1->get("studyrequest");
	}
	function getmypreviousstudyresponse($request_id,$reply_user_id)
	{
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("request_id",$request_id);
		$db1->where("reply_user_id",$reply_user_id);
		$db1->where("reply_user_type","teacher");
		return $db1->get("studyresponse");
	}
	function getStudyRequestof($id)
	{
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("id",$id);
		return $db1->get("studyrequest");
	}
	function savestudydata()
	{
		$data['reply']=$_POST['reply'];
		$data['link']=$_FILES['userfile']['name'];
		$data['request_id']=$_POST['request_id'];
		$data['reply_user_id']=$_POST['reply_user_id'];
		$data['reply_user_type']=$_POST['reply_user_type'];
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->insert("studyresponse",$data);
	}
	function updateDetails()
	{
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$stud=$this->session->userdata("userdetails");
		$id=$stud->id;
		$db1->where("id",$id);
		$q=$db1->get("teacher_details");
		foreach($q->result() as $row):
			$this->session->set_userdata("userdetails",$row);
		endforeach;
	}
	function mychats(){
		$this->load->model("administrator");
		$obj=$this->session->userdata("userdetails");
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("msgto",$obj->email);
		//$db1->where("msgfrom",$obj->email);
		$query=$db1->get("messages");
		$data=array();
		$i=0;
		$unique=array();
		foreach($query->result() as $row):
			if($row->fromusertype=="professional" || $row->tousertype=="professional") {
				if($obj->email!=$row->msgto) {
					 $pname = $this->administrator->getprofessionalname($row->msgto);

				}
				else {
					$pname = $this->administrator->getprofessionalname($row->msgfrom);

				}
				
				$data[$i++]=array(
					'name'=>$pname,
					'email'=>$row->msgto,
					'ptype'=>"Professional"
				);
			}
			if($row->fromusertype=="student" || $row->tousertype=="student") {
				if($obj->email!=$row->msgto) {
					 $pname = $this->administrator->getStudentname($row->msgto);
				}
				else {
				 $pname = $this->administrator->getStudentname($row->msgfrom);
				}
				
				$data[$i++]=array(
					'name'=>$pname,
					'email'=>$row->msgfrom,
					'ptype'=>"Student"
				);
			}
		endforeach;
		return $data;
	}
	function sendmsg(){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$data['msgfrom']=$_POST['msgfrom'];
		$data['msgto']=$_POST['msgto'];
		$data['tousertype']=$_POST['tousertype'];
		$data['fromusertype']=$_POST['fromusertype'];
		$data['msg']=$_POST['msg'];
		$db1->insert("messages",$data);
		$msg="<div class='alert alert-success'>Message Sent</div>";
		$this->session->set_flashdata("msg",$msg);
		redirect("teacher/guide");
	}
	function updatereadmsg($msgto){
		$obj=$this->session->userdata("userdetails");
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$msgfrom=$obj->email;
		$db1->where("msgfrom",$msgto);
		$db1->where("msgto",$msgfrom);
		$data['recd']=1;
		$q=$db1->update("messages",$data);
	}
}	