<?php
class Securelogin extends CI_Model {

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
	function login()
{
	$config=$this->loadDatabase('skill');
	$db1=$this->load->database($config,TRUE);
	$username=$_REQUEST['username'];
	$password=$_REQUEST['password'];
	$db1->where('username',$username);
	$db1->where('password',$password);
	$db1->from('admin');
	$cnt=$db1->count_all_results();	
	if($cnt==1)
	{
		$this->session->set_userdata('username',$username);
		//$dbname="vr_".$uid;
		//$this->session->set_userdata('dbname',$dbname);	
	}
	return $cnt;
}
	function logout()
{
	$this->session->sess_destroy();
	redirect(base_url());
}
	function studlogin()
{
	$config = $this->loadDatabase('skill');
	$db1 = $this->load->database($config, TRUE);
	$username = $_REQUEST['username'];
	$password = $_REQUEST['password'];
	$db1->where('username', $username);
	$db1->where('password', $password);
	$db1->from('student');
	echo $cnt = $db1->count_all_results();
	if ($cnt == 1) {
		$db1->where('username', $username);
		$db1->where('password', $password);
		$query = $db1->get('student');
		foreach ($query->result() as $row) {
			$this->session->set_userdata("userdetails", $row);
			$this->session->set_userdata("loggedinas", "student");
		}
		$this->session->set_userdata('username', $username);
	} else {
		$this->load->library("encrypt");
		$db1->where('username', $username);
		$query = $db1->get('student');
		foreach ($query->result() as $row) {
			if ($this->encrypt->decode($row->password) == $password AND $row->register == "1") {
				$db1->where('stud_id', $row->id);
				$query = $db1->get('stud_details ');
				foreach ($query->result() as $row) {
					$this->session->set_userdata("userdetails", $row);
					$this->session->set_userdata("loggedinas", "student");
				}
				redirect("student/home");
			}
		}
	}
	return $cnt;
}
	function teacherlogin(){
		$config=$this->loadDatabase('skill');
		$db1=$this->load->database($config,TRUE);
		$username=$_REQUEST['username'];
		$password=$_REQUEST['password'];
		$db1->where('username',$username);
		$db1->where('password',$password);
		$db1->from('teacher');
		$cnt=$db1->count_all_results();
		if($cnt==1)
		{
			$db1->where('username',$username);
			$db1->where('password',$password);
			$query = $db1->get('teacher');
			foreach ($query->result() as $row)
			{
				$this->session->set_userdata("userdetails",$row);
				$this->session->set_userdata("loggedinas","teacher");
			}
			$this->session->set_userdata('username',$username);
		}else {
			$this->load->library("encrypt");
			$db1->where('username', $username);
			$query = $db1->get('teacher');
			foreach ($query->result() as $row) {
				if ($this->encrypt->decode($row->password) == $password AND $row->register == "1") {
					$db1->where('teacher_id', $row->id);
					$query = $db1->get('teacher_details ');
					foreach ($query->result() as $row) {
						$this->session->set_userdata("userdetails", $row);
						$this->session->set_userdata("loggedinas", "teacher");
					}
					redirect("teacher/home");
				}
			}
		}

		return $cnt;
	}
	function proflogin()
	{
		$config=$this->loadDatabase('skill');
		$db1=$this->load->database($config,TRUE);
		$username=$_REQUEST['username'];
		$password=$_REQUEST['password'];
			$this->load->library("encrypt");
			$db1->where('username', $username);
			$query = $db1->get('professional_details');
			foreach ($query->result() as $row) {
				if ($this->encrypt->decode($row->password) == $password) {
					$this->session->set_userdata("userdetails", $row);
					$this->session->set_userdata("loggedinas", "professional");
					redirect("professional/home");
				}
			}

		return 0;
	}
	function tpologin()
{
	$config=$this->loadDatabase('skill');
	$db1=$this->load->database($config,TRUE);
	$username=$_REQUEST['username'];
	$password=$_REQUEST['password'];
	$db1->where('username',$username);
	$db1->where('password',$password);
	$db1->from('tpo');
	$cnt=$db1->count_all_results();	
	if($cnt==1)
	{
		$db1->where('username',$username);
		$db1->where('password',$password);
		$query = $db1->get('tpo');
		foreach ($query->result() as $row)
		{
    		$this->session->set_userdata("userdetails",$row);
			$this->session->set_userdata("loggedinas","tpo");
		}
		$this->session->set_userdata('username',$username);
	}else {
		$this->load->library("encrypt");
		$db1->where('username', $username);
		$query = $db1->get('tpo');
		foreach ($query->result() as $row) {
			if ($this->encrypt->decode($row->password) == $password AND $row->register == "1") {
				$db1->where('tpo_id', $row->id);
				$query = $db1->get('tpo_details ');
				foreach ($query->result() as $row) {
					$this->session->set_userdata("userdetails", $row);
					$this->session->set_userdata("loggedinas", "tpo");
				}
				redirect("tpo/home");
			}
		}
	}
	//exit;
	return $cnt;
}
   	function delete($ids)
   {
        $config=$this->loadDatabase($this->session->userdata('dbname'));
		$db1=$this->load->database($config,TRUE);
		$ids = $ids;
        $count = 0;
        foreach ($ids as $id)
		{
            $did = intval($id).'<br>';
            $db1->where('id', $did);
            $db1->delete('quebank');  
            $count = $count+1;
        }
       
        echo '<div class="alert alert-success" style="margin-top:-17px;font-weight:bold">
             '.$count.' Item deleted successfully</div>';
        $count = 0;
  }
  	function deleteStudent($ids)
   {
        $config=$this->loadDatabase($this->session->userdata('dbname'));
		$db1=$this->load->database($config,TRUE);
		$ids = $ids;
        $count = 0;
        foreach ($ids as $id)
		{
            $did = intval($id).'<br>';
            $db1->where('id', $did);
            $db1->delete('studrec');  
            $count = $count+1;
        }
       
        //echo '<div class="alert alert-success" style="margin-top:-17px;font-weight:bold">
          //   '.$count.' Item deleted successfully</div>';
        $count = 0;
     }
	function cardlogin($id)
	{
		$config = $this->loadDatabase('skill');
		$db1 = $this->load->database($config, TRUE);
		$db1->where('card',$id);
		$db1->from('admin');
		echo $db1->count_all_results();
	}
	function nextstep()
	{
		$config = $this->loadDatabase('skill');
		$db1 = $this->load->database($config, TRUE);
		$username = $_REQUEST['username'];
		$password = $_REQUEST['password'];
		$db1->where('username', $username);
		$query=$db1->get('admin');
		$row=$query->row_array();
		print_r($row);
		$newunmae=$this->encrypt->decode($row['password']);
		if($newunmae==$password){
			$this->session->set_userdata("loggedinas","admin");
			$this->session->set_userdata("userdetails",$row);
			$db1->where("username",$username);

			//$db1->update("admin",array('lastlogin'=>$d1));
			redirect("admin/super");
		}

	}
}

