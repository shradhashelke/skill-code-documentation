<?php
class Professionalop extends CI_Model {
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
  	function step2()
  {
	  $this->load->helper('string');
	$e=$this->encrypt->encode($_POST['password']); //Encrypt the Password
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$data['password']=$e;
	$data['username']=$this->session->userdata("email");
	$db1->where("email",$this->session->userdata("email"));
	$db1->update("professional_details",$data);
	  $data1['email']=$this->session->userdata("email");
	  $data1['profiletype']="professional";
	  $data1['profile_id']=random_string('numeric', 6);
	  $db1->insert("profile",$data1);

  }
	function acceptexamrequest(){
		$userdata=$this->session->userdata("userdetails");
		$data['email']=$userdata->email;
		$data['college_id']=$_POST['college_id'];
		$data['gfrom']=$_POST['from'];
		$data['gto']=$_POST['to'];
		$data['message']=implode(",",$_POST['dept']);
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->insert("requestexam",$data);
	}
	function getupdates()
{
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$stud=$this->session->userdata("userdetails");
	$username=$stud->email;
	$db1->where("username",$username);
	$db1->order_by("timestamps", "DESC"); 
	$data= array();
	$q=$db1->get("examnotifyprofessional");
	foreach($q->result() as $row)
	{
		array_push($data,$row);	
	}
	return $data;
}
	function getStudyRequest()
{
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	return $db1->get("studyrequest");	
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
	function getmypreviousstudyresponse($request_id,$reply_user_id)
{
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$db1->where("request_id",$request_id);
	$db1->where("reply_user_id",$reply_user_id);
	$db1->where("reply_user_type","professional");
	return $db1->get("studyresponse");	
}
	function updateDetails()
	{
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$stud=$this->session->userdata("userdetails");
		$id=$stud->id;
		$db1->where("id",$id);
		$q=$db1->get("professional_details");
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
			if($row->fromusertype=="student" || $row->tousertype=="student") {
				if($obj->email!=$row->msgto) {
					$pname = $this->administrator->getStudentname($row->msgto);
					$to=$row->msgto;
				}
				else {
					$pname = $this->administrator->getStudentname($row->msgfrom);
					$to=$row->msgfrom;
				}
				$data[$i++]=array(
					'name'=>$pname,
					'email'=>$to,
					'ptype'=>"student"
				);
			}
			if($row->fromusertype=="teacher" || $row->tousertype=="teacher") {
				if($obj->email!=$row->msgto) {
					$pname = $this->administrator->getTeachername($row->msgto);
					$to=$row->msgto;
				}
				else {
					$pname = $this->administrator->getTeachername($row->msgfrom);
					$to=$row->msgfrom;
				}
				$data[$i++]=array(
					'name'=>$pname,
					'email'=>$to,
					'ptype'=>"Teacher"
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
		redirect("professional/guide");
	}
}	