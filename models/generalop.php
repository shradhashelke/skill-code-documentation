<?php
class Generalop extends CI_Model {
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
	function saveprofessional()
	{
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->insert("professionalrequest",$_POST);
	}
	function acceptprofessional($id)
	{
		$this->load->helper('string');
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("id",$id);
		$query=$db1->get("professionalrequest");
		foreach ($query->result() as $row):
			$email=$row->email;
		endforeach;
		$token=random_string('alnum', 12);
		$enc_email=$this->encrypt->encode($email);
		$data['email']=$email;
		$data['token']=$token;
		$db1->insert("tokens",$data);
		$dataq['msg']=base_url()."secure/verifyprofessional/$token/$email";
		$dataq['mailto']=$email;
		$dataq['type']="professionalrequest";
		$db1->insert("mailqueue",$dataq);
	}
	function validverify($token,$email)
	{
		$email=rawurldecode($email);	
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("token",$token);
		$db1->where("email",$email);
		$db1->from('tokens');
		$cnt=$db1->count_all_results();
		if($cnt==1) {
			$db1->where("token",$token);
			$db1->where("email",$email);
			$db1->delete("tokens");
			$db1->where("email", $email);
			$db1->delete("professionalrequest");
			$data['email'] = $email;
			$data['loggedinas']="professional";
			$data['userdetails']="professional";
			$this->session->set_userdata($data);
			redirect("professional/step1");
		}
		else{
				show_error("Invalid/ Expired Token");
		}
	}
	function getupdates($email)
	{
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);		
		$db1->where("username",$email);
		$db1->from('examnotifyprofessional');
		return $cnt=$db1->count_all_results();
	}
	function getupdatesstudent($cid)
	{
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);		
		$db1->where("college_id",$cid);
		$db1->from('examnotifystudent');
		return $cnt=$db1->count_all_results();
	}
	function getupdatestpo($cid)
	{
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);		
		$stud=$this->session->userdata("userdetails");
		$cid=$stud->email;
		$db1->where("msg_to",$cid);
		$db1->from('comments');
		return $cnt=$db1->count_all_results();
	}
	function updatereadmsg(){
		$obj=$this->session->userdata("userdetails");
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$me=$obj->email;
		$db1->where("msgto",$me);
		$db1->where("recd",0);
		$db1->from("messages");
		return $db1->count_all_results();
	}
}