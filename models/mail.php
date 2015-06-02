<?php

class Mymail extends CI_Model {

function checkStatus()
{
	echo "<script>alert('senti');</script>";
	$config=$this->loadDatabase('skill');
	$db1=$this->load->database($config,TRUE);
	$query = $db1->get_where('mailqueue', array('status' => 'queue'));
	foreach($query->result() as $row):
		$data['id']=$row->id;
		$data['to']=$row->mailto;
		$data['attach']=$row->attach;
		$this->sendmail($data);
	endforeach;
}

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

function sendmail($data)
	{
		error_reporting(-E_ALL); //Disable the errors,notices,warnings	
		$config=array(
						'protocol'=>'smtp',
						'smtp_host'=>'ssl://smtp.googlemail.com',
						'smtp_port'=>'465',
						'smtp_user'=>'vaibhavraj.developer@gmail.com',
						'smtp_pass'=>'rajvaibhav1234',
						'mailtype'=>'html',
						'wordwrap'=>FALSE,
						'charset'=>'iso-8859-1',
						'newline'=>'\r\n'
						);
		$this->load->library('email',$config);
		$this->email->set_newline("\r\n");
		$this->email->set_crlf( "\r\n" );
		$this->email->from("vaibhavraj.roham@gmail.com");
		$this->email->to($data['to']);
		$this->email->subject('User-Credentials : Skill+');
		$this->email->attach($data['attach']);
		$this->email->message($this->load->view("mail_templates/sendid",'',true));
		if($this->email->send())
			{
				$config=$this->loadDatabase('skill');
				$db1=$this->load->database($config,TRUE);
				$db1->where('id', $data['id']);
				$db1->delete('mailqueue'); 
				
				echo "<script>alert('sent');</script>";
			}
		else
			{
				echo "<script>alert('Not sent');</script>";
				echo "<script>alert('Message Not Sent. Please Check Internet Connection');</script>";
				echo "<center><a href='".base_url()."administrator/accounts' class='btn btn-info'>Back</a></center><hr>";
				//echo $this->email->print_debugger();
			}
	}
	
}