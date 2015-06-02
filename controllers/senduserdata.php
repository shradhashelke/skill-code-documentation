<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Senduserdata extends CI_Controller {
	//Connection to database....
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
	//Modules to send notification to individual user...
	public function student()
	{
		$data['subject']=$_POST['subject'];
		$data['msg']=$_POST['message'];
		$data['mailto']=$_POST['student'];
		$data['attach']=$_FILES['userfile']['name'];
		$data['type']="notification";
		$config=$this->loadDatabase("skill");
	    $db1=$this->load->database($config,TRUE);

		$config['upload_path'] = './files/notification/';
		$config['allowed_types'] = 'gif|jpg|png|bmp|docx|doc|xls|xlsx|csv|pdf|ppt|pptx';
		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$msg['error'] = $this->upload->display_errors();

			$msg="<div class='alert alert-warning'>Error Occured</div>";
			$this->session->set_flashdata("msg",$msg);
			redirect("admin/sendcredential");
		}
		else
		{
			$msg="<div class='alert alert-warning'>Notifications Sent!</div>";
			$this->session->set_flashdata("msg",$msg);
			$db1->insert("mailqueue",$data);
			redirect("admin/sendcredential");
		}
	}
	public function teacher()
	{
		$data['subject']=$_POST['subject'];
		$data['msg']=$_POST['message'];
		$data['mailto']=$_POST['teacher'];
		$data['attach']=$_FILES['userfile']['name'];
		$data['type']="notification";
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);

		$config['upload_path'] = './files/notification/';
		$config['allowed_types'] = 'gif|jpg|png|bmp|docx|doc|xls|xlsx|csv|pdf|ppt|pptx';
		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$msg['error'] = $this->upload->display_errors();

			$msg="<div class='alert alert-warning'>Error Occured</div>";
			$this->session->set_flashdata("msg",$msg);
			redirect("admin/sendcredential");
		}
		else
		{
			$msg="<div class='alert alert-warning'>Notifications Sent!</div>";
			$this->session->set_flashdata("msg",$msg);
			$db1->insert("mailqueue",$data);
			redirect("admin/sendcredential");
		}
	}
	public function tpo()
	{
		$data['subject'] = $_POST['subject'];
		$data['msg'] = $_POST['message'];
		$data['mailto'] = $_POST['tpo'];
		$data['attach'] = $_FILES['userfile']['name'];
		$data['type'] = "notification";
		$config = $this->loadDatabase("skill");
		$db1 = $this->load->database($config, TRUE);

		$config['upload_path'] = './files/notification/';
		$config['allowed_types'] = 'gif|jpg|png|bmp|docx|doc|xls|xlsx|csv|pdf|ppt|pptx';
		$this->load->library('upload', $config);

		if (!$this->upload->do_upload()) {
			$msg['error'] = $this->upload->display_errors();

			$msg = "<div class='alert alert-warning'>Error Occured</div>";
			$this->session->set_flashdata("msg", $msg);
			redirect("admin/sendcredential");
		} else {
			$msg = "<div class='alert alert-warning'>Notifications Sent!</div>";
			$this->session->set_flashdata("msg", $msg);
			$db1->insert("mailqueue", $data);
			redirect("admin/sendcredential");
		}
	}
	public function prof()
	{
		$data['subject'] = $_POST['subject'];
		$data['msg'] = $_POST['message'];
		$data['mailto'] = $_POST['prof'];
		$data['attach'] = $_FILES['userfile']['name'];
		$data['type'] = "notification";
		$config = $this->loadDatabase("skill");
		$db1 = $this->load->database($config, TRUE);

		$config['upload_path'] = './files/notification/';
		$config['allowed_types'] = 'gif|jpg|png|bmp|docx|doc|xls|xlsx|csv|pdf|ppt|pptx';
		$this->load->library('upload', $config);

		if (!$this->upload->do_upload()) {
			$msg['error'] = $this->upload->display_errors();

			$msg = "<div class='alert alert-warning'>Error Occured</div>";
			$this->session->set_flashdata("msg", $msg);
			redirect("admin/sendcredential");
		} else {
			$msg = "<div class='alert alert-warning'>Notifications Sent!</div>";
			$this->session->set_flashdata("msg", $msg);
			$db1->insert("mailqueue", $data);
			redirect("admin/sendcredential");
		}
	}
	//Modules to send notification to all users...
	public function allstudent()
	{
		$config = $this->loadDatabase("skill");
		$db1 = $this->load->database($config, TRUE);
		$col_id=$_POST['college_id'];
		//Data to insert in db..
		$data['subject'] = $_POST['subject'];
		//$data['msg'] = $_POST['message'];
		//$data['attach'] = $_FILES['userfile']['name'];
		//---------------------------
			$msg = "<div class='alert alert-warning'>Notifications Sent!</div>";
			$this->session->set_flashdata("msg", $msg);
			if (isset($_POST['status'])) {
				$query = $db1->get("student");
			} else {
				$db1->where("college_id", $col_id);
				$query = $db1->get_where("student");
			}

			foreach ($query->result() as $row):
				$data['msg']=implode(",",array(
					'name'=>$row->sname,
					'username'=>$row->username,
					'password'=>$row->password
				));
				$data['mailto'] = $row->email;
				$data['type']="userdata";
				$db1->insert("mailqueue", $data);
			endforeach;
			redirect("admin/sendcredential");
	}
	public function allteacher()
	{
		$config = $this->loadDatabase("skill");
		$db1 = $this->load->database($config, TRUE);
		$col_id=$_POST['college_id'];
		//Data to insert in db..
		$data['subject'] = $_POST['subject'];
		$data['type'] = "userdata";
		//---------------------------

			$msg = "<div class='alert alert-warning'>Notifications Sent!</div>";
			$this->session->set_flashdata("msg", $msg);
			if (isset($_POST['status'])) {
				$query = $db1->get("teacher");
			} else {
				$db1->where("college_id", $col_id);
				$query = $db1->get_where("teacher");
			}
			foreach($query->result() as $row):
				$data['msg']=implode(",",array(
					'name'=>$row->sname,
					'username'=>$row->username,
					'password'=>$row->password
				));
				$data['mailto']=$row->email;
				$db1->insert("mailqueue", $data);
			endforeach;
			redirect("admin/sendcredential");
	}
	public function alltpo()
	{
		$config = $this->loadDatabase("skill");
		$db1 = $this->load->database($config, TRUE);
		$col_id=$_POST['college_id'];
		//Data to insert in db..
		$data['subject'] = $_POST['subject'];
		//$data['msg'] = $_POST['message'];
		$data['type'] = "userdata";
		//---------------------------

			$msg = "<div class='alert alert-warning'>Notifications Sent!</div>";
			$this->session->set_flashdata("msg", $msg);
			if (isset($_POST['status'])) {
				$query = $db1->get("tpo");
			} else {
				$db1->where("college_id", $col_id);
				$query = $db1->get_where("tpo");
			}

			foreach($query->result() as $row):
				$data['msg']=implode(",",array(
					'name'=>$row->sname,
					'username'=>$row->username,
					'password'=>$row->password
				));
				$data['mailto']=$row->email;
				$db1->insert("mailqueue", $data);
			endforeach;
			redirect("admin/sendcredential");
		}
}

/* End of file notify.php */
/* Location: ./application/controllers/notify.php */