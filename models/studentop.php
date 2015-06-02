<?php
class StudentOP extends CI_Model {
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
   	function updateDetails()
   {
	 $config=$this->loadDatabase("skill");
	 $db1=$this->load->database($config,TRUE);
   	 $stud=$this->session->userdata("userdetails");  
	 $id=$stud->id;
	 $db1->where("id",$id);
	 $q=$db1->get("stud_details");
	 foreach($q->result() as $row):
		 $this->session->set_userdata("userdetails",$row);
	 endforeach;
   }
  	function registerstud()
  {
	 $stud=$_POST;
	 $config=$this->loadDatabase("skill");
	 $db1=$this->load->database($config,TRUE);
	 $db1->insert("stud_details",$stud);
	 $db1->where("id",$_POST['stud_id']);
	 $data['register']=1;
	 $db1->update("student",$data);
	 redirect("student/step2");
  }
  	function step2()
  {
	  $stud=$this->session->userdata("userdetails");
	$e=$this->encrypt->encode($_POST['password']); //Encrypt the Password
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$data['password']=$e;
	$db1->where("id",$_POST['stud_id']);
	$db1->update("student",$data);
	  $data1['userid']=$_POST['stud_id'];
	  $data1['profiletype']="student";
	  $data1['profile_id']=random_string('numeric', 6);
	  $data1['email']=$stud->email;
	  $db1->insert("profile",$data1);
  }
	function getupdates()
{
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$stud=$this->session->userdata("userdetails");
	$username=$stud->college_id;
	$db1->where("college_id",$username);
	$data= array();
	$q=$db1->get("examnotifystudent");
	foreach($q->result() as $row)
	{
		array_push($data,$row);	
	}
	return $data;
}
	function savestudyrequest()
{
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$data['quote']=$_POST['quote'];
	$data['keywords']=$_POST['keywords'];
	$stud=$this->session->userdata("userdetails");
	$data['stud_id']=$stud->id;
	$db1->insert("studyrequest",$data);
}
	function studyresponse()
{
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$stud=$this->session->userdata("userdetails");
	$db1->where("stud_id",$stud->id);
	return $db1->get("studyrequest");
}
	function mystudyresponse($id)
{
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$db1->where("request_id",$id);
	return $db1->get("studyresponse");
}
	function deletestudyrequest()
{
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$db1->where("id",$_GET['id']);
	$db1->delete("studyrequest");
	$db1->where("request_id",$_GET['id']);
	$db1->delete("studyresponse");
}
	function suggest(){
		$this->load->model("profile");
		$skills=$this->getSkills();
		return $this->profile->rankprofile($skills);
	}
	function suggestonquery($q){
		$this->load->model("profile");
		$query=strtolower($q);
		$search=explode(" ",str_replace(","," ",urldecode($query)));
		return $this->profile->rankprofile($search);
	}
	function getSkills(){
		$row=$this->session->userdata('userdetails');
		$skills=$row->topskill1.",".$row->topskill2.",".$row->topskill3.",".$row->topskill4.",".$row->topskill5.",";
		$skills.=$row->skills;
		$finalskills=strtolower($skills);
		$q=explode(" ",$finalskills);
		$newquery="";
		foreach($q as $single)
			$newquery.=trim($single).",";
		return explode(",",rtrim($newquery,","));
	}
	function getProfileData($pids){
		$pidarr=$pids['rank'];
		$profiles=array();
		foreach($pidarr as $pid => $rank):
			array_push($profiles,$this->getProfile($pid));
		endforeach;
	return $profiles;
	}
	function getProfile($pid){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("profile_id",$pid);
		$q=$db1->get("profile");
		$row=$q->row();
		$email=$row->email;
		$ptype=$row->profiletype;
		if($ptype=="professional"){
			$db1->where("email",$email);
			$q1=$db1->get("professional_details");
			$details=$q1->row();
			$data['pid']=$pid;
			$data['name']=$details->fname." ".$details->lname;
			$data['ptype']="Professional";
			$data['topskills']=$details->topskill1.",".$details->topskill2.",".$details->topskill3.",".$details->topskill4.",".$details->topskill5;
			$data['skills']=$details->skills;
			return $data;
		}
		if($ptype=="teacher"){
			$db1->where("email",$email);
			$q1=$db1->get("teacher_details");
			$details=$q1->row();
			$data['pid']=$pid;
			$data['name']=$details->fname." ".$details->lname;
			$data['ptype']="Teacher";
			$data['topskills']=$details->topskill1.",".$details->topskill2.",".$details->topskill3.",".$details->topskill4.",".$details->topskill5;
			$data['skills']=$details->skills;
			return $data;
		}
	}
	function newchat($pid){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("profile_id",$pid);
		$q=$db1->get("profile");
		$row=$q->row();
		$email=$row->email;
		$ptype=$row->profiletype;
		if($ptype=="professional"){
			$db1->where("email",$email);
			$q1=$db1->get("professional_details");
			$details=$q1->row();
			$data['pid']=$pid;
			$data['name']=$details->fname." ".$details->lname;
			$data['ptype']="Professional";
			$data['email']=$email;
			return $data;
		}
		if($ptype=="teacher"){
			$db1->where("email",$email);
			$q1=$db1->get("teacher_details");
			$details=$q1->row();
			$data['pid']=$pid;
			$data['name']=$details->fname." ".$details->lname;
			$data['ptype']="Teacher";
			$data['email']=$email;
			return $data;
		}
	}
	function sendnewchat(){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$obj=$this->session->userdata("userdetails");
		$data['msgfrom']=$obj->email;;
		$data['msgto']=$_POST['email'];
		$data['msg']=$_POST['message'];
		$data['tousertype']=strtolower($_POST['ptype']);
		$data['fromusertype']="student";
		$db1->insert("messages",$data);
		redirect("student/guide");
	}
	function mychats(){
		$this->load->model("administrator");
		$obj=$this->session->userdata("userdetails");
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		//$db1->where("msgto",$obj->email);
		$db1->where("msgfrom",$obj->email);
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
			if($row->fromusertype=="teacher" || $row->tousertype=="teacher") {
				if($obj->email!=$row->msgto) {
					$pname = $this->administrator->getTeachername($row->msgto);

				}
				else {
					$pname = $this->administrator->getTeachername($row->msgfrom);

				}
				$data[$i++]=array(
					'name'=>$pname,
					'email'=>$row->msgto,
					'ptype'=>"Teacher"
				);
			}
		endforeach;
		return $data;
	}
	function showchat($msgto,$name){
		$obj=$this->session->userdata("userdetails");
		$from=$obj->email;
		$fromptype=$this->session->userdata("loggedinas");
		$val=$this->thischat($msgto);
		echo "<form action='sendmsg' method='post'>";
		foreach($val as $r):
			if($r['thismsgfrom']==$from) {
				echo "<div class='row' style='margin-left: 10px;'><div class='alert alert-success col-md-9'>" . "<h6><i class='icon  icon-user'></i> Me</h6><p align='left'><i class='icon icon-flash'></i> " . $r['msg'] . "</p>" . "<br><label class='label label-warning pull-right'><i class='icon icon-calendar'></i> ".$r['timestamps']."</label>" . "</div></div> ";
			}else {
				echo "<div class='alert alert-warning col-md-offset-3 col-md-9'>" . "<h6><i class='icon icon-user'></i> ".urldecode($name)."</h6><p align='left'><i class='icon icon-flash'></i> ".$r['msg']."</p>" . "<br><label class='label label-success pull-right'> <i class='icon icon-calendar'></i> ".$r['timestamps']."</label>" . "</div>";
			}
		endforeach;
			   echo  "<br><textarea name='msg' class='form-control' id='txt' placeholder='Enter Here' autofocus='autofocus' required='required'></textarea><br>".
				"<input type='hidden' name='msgto' value='".$msgto."'>".
				"<input type='hidden' name='msgfrom' value='".$from."'>".
				"<input type='hidden' name='tousertype' value='".$val[0]['ptype']."'>".
				"<input type='hidden' name='fromusertype' value='".$fromptype."'>".
				"<input type='submit' id='submitbtn' class='btn btn-info pull-left' value='Send Message'>".
			   "<input type='button' class='btn btn-warning pull-right' value='Clear Message'>".
			"</form><br><hr>";
	}
	function thischat($msgto){
		$obj=$this->session->userdata("userdetails");
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$msgfrom=$obj->email;
		$db1->where("msgto",$msgto);
		$db1->where("msgfrom",$msgfrom);
		$db1->or_where("msgfrom",$msgto);
		$db1->where("msgto",$msgfrom);
		$db1->order_by("timestamps","ASC");
		$q=$db1->get("messages");
		$msg=array();
		$i=0;
		foreach($q->result() as $row):
			$msg[$i++]=array(
				'msg'=>$row->msg,
				'timestamps' => $row->timestamps,
				'thismsgfrom'=>$row->msgfrom,
				'msgfrom'=>$msgfrom,
				'ptype'=>$row->tousertype
			);
		endforeach;
		$this->updatereadmsg($msgto);
		return $msg;
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
		redirect("student/guide");
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