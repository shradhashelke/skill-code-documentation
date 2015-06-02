<?php
class Inline extends CI_Model {

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
   
  function updateStudent($field,$value)
  {
	 $stud=$this->session->userdata("userdetails");
	 $id=$stud->id;
	 $config=$this->loadDatabase("skill");
	 $db1=$this->load->database($config,TRUE);
	 $data[$field]=$value;
	 $db1->where("id",$id);
	 $db1->update("stud_details",$data);
	 return "Updated";
  }
	function updateProfessional($field,$value)
	{
		$stud=$this->session->userdata("userdetails");
		$id=$stud->id;
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$data[$field]=$value;
		$db1->where("id",$id);
		$db1->update("professional_details",$data);
		return "Updated";
	}
	function updateTeacher($field,$value)
	{
		$stud=$this->session->userdata("userdetails");
		$id=$stud->id;
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$data[$field]=$value;
		$db1->where("id",$id);
		$db1->update("teacher_details",$data);
		return "Updated";
	}
}	