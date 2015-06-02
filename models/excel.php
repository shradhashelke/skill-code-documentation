<?php
class Excel extends CI_Model {
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
	function isidgenerated()
 {
	 	$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("id",$this->session->userdata("college_id"));
		$query=$db1->get('newcollege');
		foreach($query->result() as $row):
			$status=$row->idgenereted;
		endforeach;
		return $status;
 }
	function getCollege()
 {
	 	$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("id",$this->session->userdata("college_id"));
		$query=$db1->get('newcollege');
		foreach($query->result() as $row):
			$data['student_file']=$row->file_student;
			$data['teacher_file']=$row->file_teacher;
			$data['tpo_file']=$row->file_tpo;
		endforeach;
		return $data;
 }
	function savestudent()
{
	//echo "<blockquote><p class='alert alert-success'>Please Wait!!<br>User-Credentials are getting Generated..</p></blockquote>";
	$cdata=$this->getCollege();
	$this->load->helper('string');
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	//$db1->truncate('quebank');
	error_reporting(-E_ALL); //Disable the errors,notices,warnings
	$this->load->library("Spreadsheet_Excel_Reader");
	$file="./files/userfiles/".$cdata['student_file'].".xls";
	$data= new Spreadsheet_Excel_Reader($file,false);
	/*
		Single Row Consist the following....
		1,1 : # [SR.No]
		1,2 : GR/PNR/UNIQUE NO
		1,3 : NAME
		1,4 : EMAIL
		1,5 : MOBILE
		1,6 : Department
	*/
	//Start from 2,2 ie. First Question...
	$questions=array();
	$k=0;
	$error=array();
	for($i=2;$i<=$data->rowcount(0);$i++)
		{
			if($data->val($i,2)=="")
				continue;
			$questions['gr']=$data->val($i,2);
			$questions['sname']=$data->val($i,3);			
			$questions['email']=$data->val($i,4);				
			$questions['mobile']=$data->val($i,5);	
			$questions['college_id']=$this->session->userdata("college_id");	
			$questions['username']=$data->val($i,4);
			$stud['password']=$questions['password']=random_string('alnum', 6);		
			$stud['username']=$data->val($i,4);
			$questions['dept']=$data->val($i,6);
			$stud['usertype']="student";
			//$db1->insert('login',$stud);
			if($this->validate($data->val($i,4))) {
				$db1->insert('student', $questions);
			}
			else{
				array_push($error,$data->val($i,4));
			}
		}
		if(count($error)<1){
			echo "<blockquote><p class='alert alert-success'>Credentials for Students are generated.</p></blockquote>";
		}
	else{
		echo "<blockquote><p class='alert alert-error'><b>".count($error)." Duplicate Records found while uploading student list..</b><br>";
		foreach($error as $e):
			echo $e." ";
		endforeach;
		echo "<br>Try to add manually.</p></blockquote>";
	}

	}
	function saveteacher()
{
	$cdata=$this->getCollege();
	$this->load->helper('string');
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	//$db1->truncate('quebank');
	error_reporting(-E_ALL); //Disable the errors,notices,warnings
	$this->load->library("Spreadsheet_Excel_Reader");
	$file="./files/userfiles/".$cdata['teacher_file'].".xls";
	$data= new Spreadsheet_Excel_Reader($file,false);
	/*
		Single Row Consist the following....
		1,1 : # [SR.No]
		1,2 : EMPID
		1,3 : NAME
		1,4 : EMAIL
		1,5 : MOBILE
		1,6 : Department
	*/
	//Start from 2,2 ie. First Question...
	$questions=array();
	
	$temparray=array("que","a","b","c","d","ans","sec","mark");
	$k=0;
	$error=array();
	for($i=2;$i<=$data->rowcount(0);$i++)
		{
			if($data->val($i,2)=="")
				continue;
			$questions['gr']=$data->val($i,2);
			$questions['sname']=$data->val($i,3);			
			$questions['email']=$data->val($i,4);				
			$questions['mobile']=$data->val($i,5);	
			$questions['college_id']=$this->session->userdata("college_id");	
			$questions['username']=$data->val($i,4);
			$questions['dept']=$data->val($i,6);
			$questions['password']=random_string('alnum', 6);
			if($this->validate($data->val($i,4))) {
				$db1->insert('teacher',$questions);
			}
			else{
				array_push($error,$data->val($i,4));
			}
		}
		if(count($error)<1){
			echo "<blockquote><p class='alert alert-success'>Credentials for Teachers are generated.</p></blockquote>";
		}
		else{
			echo "<blockquote><p class='alert alert-error'><b>".count($error)." Duplicate Records found while uploading teacher list..</b><br>";
			foreach($error as $e):
				echo $e." ";
			endforeach;
			echo "<br>Try to add manually.</p></blockquote>";
		}
	}
	function savetpo()
{
	$cdata=$this->getCollege();
	$this->load->helper('string');
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	//$db1->truncate('quebank');
	error_reporting(-E_ALL); //Disable the errors,notices,warnings
	$this->load->library("Spreadsheet_Excel_Reader");
	$error=array();
	$file="./files/userfiles/".$cdata['tpo_file'].".xls";
	$data= new Spreadsheet_Excel_Reader($file,false);
	/*
		Single Row Consist the following....
		1,1 : # [SR.No]
		1,2 : EMPID
		1,3 : NAME
		1,4 : EMAIL
		1,5 : MOBILE
	*/
	//Start from 2,2 ie. First Question...
	$questions=array();
	
	$temparray=array("que","a","b","c","d","ans","sec","mark");
	$k=0;
	for($i=2;$i<=$data->rowcount(0);$i++)
		{
			if($data->val($i,2)=="")
				continue;
			$questions['gr']=$data->val($i,2);
			$questions['sname']=$data->val($i,3);			
			$questions['email']=$data->val($i,4);				
			$questions['mobile']=$data->val($i,5);	
			$questions['college_id']=$this->session->userdata("college_id");	
			$questions['username']=$data->val($i,4);
			$questions['password']=random_string('alnum', 6);		
			//print_r($questions);		
			if($this->validate($data->val($i,4))) {
				$db1->insert('tpo',$questions);
			}
			else{
				array_push($error,$data->val($i,4));
			}
		}
	if(count($error)<1){
		echo "<blockquote><p class='alert alert-success'>Credentials for Teachers are generated.</p></blockquote>";
	}
	else{
		echo "<blockquote><p class='alert alert-error'><b>".count($error)." Duplicate Records found while uploading while tpo list..</b><br>";
		foreach($error as $e):
			echo $e." ";
		endforeach;
		echo "<br>Try to add manually.</p></blockquote>";
	}
		$update['idgenereted']="YES";
		$db1->update('newcollege', $update, array('id' => $this->session->userdata("college_id")));
	}
	function validate($email){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		/*
		 * Check in student table
		 */
		$db1->where("email",$email);
		$db1->from("student");
		$cnt=$db1->count_all_results();
		if($cnt>=1)
			return 0;
		/*
		 * Check in teacher's table
		 */
		$db1->where("email",$email);
		$db1->from("teacher");
		$cnt=$db1->count_all_results();
		if($cnt>=1)
			return 0;

		/*
		 * Check in TPO Table
		 */
		$db1->where("email",$email);
		$db1->from("tpo");
		$cnt=$db1->count_all_results();
		if($cnt>=1)
			return 0;
		/*
		 * Check in professionals table..
		 */
		$db1->where("email",$email);
		$db1->from("professional_details");
		$cnt=$db1->count_all_results();
		if($cnt>=1)
			return 0;

		return 1;
	}
}
