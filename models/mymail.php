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
		$data['msg']=$row->msg;
		$data['subject']=$row->subject;
		if($row->type=="professionalrequest")
		{
			$this->sendmail1($data);
		}
		else if($row->type=="notification"){
			$this->sendmail2($data);
		}
		else if($row->type=="accountcreated"){
			$this->sendmail3($data);
		}
		else if($row->type=="studentresult"){
			$this->sendmail4($data);
		}
		else if($row->type=="userdata") {
			$this->sendmail5($data);
			echo "in";
		}
		else
		{
			$this->sendmail($data);	
		}
	endforeach;
}
	//Connect database
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
	//Normal Mails
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
		if($data['attach']!=NULL)
			$this->email->attach($data['attach']);
		echo $this->email->message($this->load->view("mail_templates/sendid",'',true));
		//$f=fopen("temp.txt","a+");
		if($this->email->send())
			{
				//fwrite($f,"Y");
				$config=$this->loadDatabase('skill');
				$db1=$this->load->database($config,TRUE);
				$db1->where('id', $data['id']);
				$db1->delete('mailqueue'); 
				echo "<script>alert('sent');</script>";
			}
		else
			{
				//fwrite($f,"N");;
				echo "<script>alert('Not sentqqq');</script>";
				echo "<script>alert('Message Not Sent. Please Check Internet Connection');</script>";
				echo "<center><a href='".base_url()."administrator/accounts' class='btn btn-info'>Back</a></center><hr>";
				//echo $this->email->print_debugger();
			}
	}
	//Mails of professional request
	function sendmail1($data)
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
		$this->email->subject('Request Accepted : Skill+');
		if($data['attach']!=NULL)
			$this->email->attach($data['attach']);	
			$this->load->view("mail_templates/sendtoken",$data);
		$this->email->message($this->load->view("mail_templates/sendtoken",$data,true));

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
	//mails of notifications
	function sendmail2($data)
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
		$this->email->subject($data['subject']);
		if($data['attach']!=NULL) {
			$attach="./files/notification/".$data['attach'];
			$this->email->attach($attach);
		}
		$this->load->view("mail_templates/notify",$data);
		$this->email->message($this->load->view("mail_templates/notify",$data,true));

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
	//for online exam acc activation
	function sendmail3($data)
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
		if($data['attach']!=NULL)
			$this->email->attach($data['attach']);
		echo $this->email->message($this->load->view("mail_templates/sendolexamfile",$data,true));
		//$f=fopen("temp.txt","a+");
		if($this->email->send())
		{
			//fwrite($f,"Y");
			$config=$this->loadDatabase('skill');
			$db1=$this->load->database($config,TRUE);
			$db1->where('id', $data['id']);
			$db1->delete('mailqueue');
			echo "<script>alert('sent');</script>";
		}
		else
		{
			//fwrite($f,"N");;
			echo "<script>alert('Not sentqqq');</script>";
			echo "<script>alert('Message Not Sent. Please Check Internet Connection');</script>";
			echo "<center><a href='".base_url()."administrator/accounts' class='btn btn-info'>Back</a></center><hr>";
			//echo $this->email->print_debugger();
		}
	}
	//Send result to student
	function sendmail4($data)
	{
		//error_reporting(-E_ALL); //Disable the errors,notices,warnings
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
		if($data['attach']!=NULL)
			$this->email->attach($data['attach']);
		$this->email->message($this->load->view("mail_templates/sendresulttostudent",$data,true));
		//$f=fopen("temp.txt","a+");
		if($this->email->send())
		{
			//fwrite($f,"Y");
			$config=$this->loadDatabase('skill');
			$db1=$this->load->database($config,TRUE);
			$db1->where('id', $data['id']);
			$db1->delete('mailqueue');
			echo "<script>alert('sent');</script>";
		}
		else
		{
			//fwrite($f,"N");;
			echo "<script>alert('Not sentqqq');</script>";
			echo "<script>alert('Message Not Sent. Please Check Internet Connection');</script>";
			echo "<center><a href='".base_url()."administrator/accounts' class='btn btn-info'>Back</a></center><hr>";
			echo $this->email->print_debugger();
		}
	}
	//send usercredentials to users
	function sendmail5($data)
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
		if($data['attach']!=NULL)
			$this->email->attach($data['attach']);
		echo $this->email->message($this->load->view("mail_templates/userdata",$data,true));
		//$f=fopen("temp.txt","a+");
		if($this->email->send())
		{
			//fwrite($f,"Y");
			$config=$this->loadDatabase('skill');
			$db1=$this->load->database($config,TRUE);
			$db1->where('id', $data['id']);
			$db1->delete('mailqueue');
			echo "<script>alert('sent');</script>";
		}
		else
		{
			//fwrite($f,"N");;
			echo "<script>alert('Not sentqqq');</script>";
			echo "<script>alert('Message Not Sent. Please Check Internet Connection');</script>";
			echo "<center><a href='".base_url()."administrator/accounts' class='btn btn-info'>Back</a></center><hr>";
			//echo $this->email->print_debugger();
		}
	}

}