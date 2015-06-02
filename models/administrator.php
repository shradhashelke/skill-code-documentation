<?php
/*
 * All database operations i.e. business operations are done in model
 */
class Administrator extends CI_Model {
	/*
	 * Global Variable
	 */
	var $cname;
	var $registerid;
	var $email;
	var $mobile;
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
	/*
	 * add new college
	 */
   	function addcollege()
   {
	   $this->cname=$_POST['cname'];
	   $this->registerid=$_POST['registerid'];
	   $this->email=$_POST['email'];
	   $this->mobile=$_POST['mobile'];
	   $this->saveexcelfiles();
   }
	/*
	 * save uploaded excel files
	 */
   	function saveexcelfiles()
   {
		$this->load->helper('form');// no use
		 //Configure upload.
		 $file1=rand(1,99999999)."_student";
		 $file2=rand(1,99999999)."_teacher";
		 $file3=rand(1,99999999)."_tpo";
		 $this->session->set_userdata('student_file', $file1);
		 $this->session->set_userdata('teacher_file', $file2);
		 $this->session->set_userdata('tpo_file', $file3);
		 $this->load->library("upload");
    	$this->upload->initialize(array(
        "file_name"     => array($file1, $file2, $file3),
        "upload_path"   => "./files/userfiles/",
		"allowed_types"=>"xls"
    	));
		if ( ! $this->upload->do_multi_upload("files"))
		{
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('admin/super/header');
			$this->load->view('admin/super/newcollege',$error);
			$this->load->view('admin/super/footer');
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());			// ?
			$this->savefilesanddata();
			$this->load->view('admin/super/header');
			$this->load->view('admin/super/uploadsuccess',$data);
			$this->load->view('admin/super/footer');
		}
   }
	/*
	 * send zip file to college
	 */
	function savefilesanddata()
   {
	   $config=$this->loadDatabase("skill");
	   $db1=$this->load->database($config,TRUE);
	   $data=array(
	   	'college_name'=>$this->cname,
		'reg_no'=>$this->registerid,
		'email'=>$this->email,
		'phone'=>$this->mobile,
		'file_student'=>$this->session->userdata("student_file"),
		'file_teacher'=>$this->session->userdata("teacher_file"),
		'file_tpo'=>$this->session->userdata("tpo_file")
	   );

	   $db1->insert("newcollege",$data);
	   $this->session->set_userdata('lastcollegedata', $data);
   }
	/*
	 * delete college request
	 */
    function deletecollege($ids)
   {
        $config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$ids = $ids;
        $count = 0;
        foreach ($ids as $id)
		{
            $did = intval($id).'<br>';
            $db1->where('id', $did);
            $db1->delete('newcollege');
            $count = $count+1;
        }

        echo '<div class="alert alert-success" style="margin-top:-17px;font-weight:bold">
             '.$count.' Items deleted successfully</div>';
        $count = 0;
  }
	/*
	 * Get files from college account
	 */
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
	/*
	 * Download student excel files
	 */
  	function downloadstudent()
  {
	$cdata=$this->getCollege();
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$criteria=array("college_id"=>$this->session->userdata("college_id"));
	$query = $db1->get_where('student',$criteria);
	$i=1;
	$filename=$this->session->userdata("college_id")."_Student_".rand(1,99999).".csv";
	$data="List of Students\n\nSr.No.,Candidate ID,Candidate Name,Email,Username,Password\n";
	$file_location="files/userfiles/".$filename;
	write_file($file_location,$data,"a+");
	foreach($query->result() as $row):
		$data="";
		$data=$i++.",".$row->gr.",".$row->sname.",".$row->email.",".$row->username.",".$row->password."\n";
		//     1,124,vc,c
		write_file($file_location,$data,"a+");
	endforeach;
	return $file_location;
  }
  	function downloadteacher()
  {
	$cdata=$this->getCollege();
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$criteria=array("college_id"=>$this->session->userdata("college_id"));
	$query = $db1->get_where('teacher',$criteria);
	$i=1;
	$filename=$this->session->userdata("college_id")."_Teacher_".rand(1,99999).".csv";
	$data="List of Teachers\n\nSr.No.,Employee ID,Teacher Name,Email,Username,Password\n";
	$file_location="files/userfiles/".$filename;
	write_file($file_location,$data,"a+");
	foreach($query->result() as $row):
		$data="";
		$data=$i++.",".$row->gr.",".$row->sname.",".$row->email.",".$row->username.",".$row->password."\n";
		write_file($file_location,$data,"a+");
	endforeach;

	return $file_location;
  }
  	function downloadtpo()
  {
	$cdata=$this->getCollege();
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$criteria=array("college_id"=>$this->session->userdata("college_id"));
	$query = $db1->get_where('tpo',$criteria);
	$i=1;
	$filename=$this->session->userdata("college_id")."_TPO_".rand(1,99999).".csv";
	$data="List of College Representetives\n\nSr.No.,Employee ID,TPO Representetive Name,Email,Username,Password\n";
	$file_location="files/userfiles/".$filename;
	write_file($file_location,$data,"a+");
	foreach($query->result() as $row):
		$data="";
		$data=$i++.",".$row->gr.",".$row->sname.",".$row->email.",".$row->username.",".$row->password."\n";
		write_file($file_location,$data,"a+");
	endforeach;
	return $file_location;
  }
	/*
	 * Return college name depend on id
	 */
	function getCollegeName($id){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("id",$id);
		$query=$db1->get("newcollege");
		$row = $query->row_array();
		return $row['college_name'];
	}
	function getProfessionalName($email){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("email",$email);
		$query=$db1->get("professional_details");
		$row = $query->row_array();
		return $row['fname']." ".$row['lname'];
	}
	function getTeacherName($email){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("email",$email);
		$query=$db1->get("teacher_details");
		$row = $query->row_array();
		return $row['fname']." ".$row['lname'];
	}
	function getStudentName($email){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("email",$email);
		$query=$db1->get("stud_details");
		$row = $query->row_array();
		return $row['fname']." ".$row['lname'];
	}
	function getProfessionalFname($email){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("email",$email);
		$query=$db1->get("professional_details");
		$row = $query->row_array();
		return $row['fname'];
	}
	function getProfessionalMobile($email){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("email",$email);
		$query=$db1->get("professional_details");
		$row = $query->row_array();
		return $row['mobile'];
	}
	function getTPOName($college_id){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("college_id",$college_id);
		$query=$db1->get("tpo");
		$row = $query->row_array();
		return $row['sname'];
	}
	function delexamrequest($email)
	{
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("email",$email);
		$data['exam_module']=1;
		$db1->update("requestexam",$data);
	}
	function savenewstudent(){
		$this->validate($_POST['email']);
		$this->load->helper('string');
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$questions['gr']=$_POST['gr'];
		$questions['sname']=$_POST['fname']." ".$_POST['mname']." ".$_POST['lname'];
		$questions['email']=$_POST['email'];
		$questions['mobile']=$_POST['mobile'];
		$questions['college_id']=$_POST['college'];
		$questions['username']=$_POST['email'];
		$questions['password']=random_string('alnum', 6);
		$db1->insert('student',$questions);
		redirect("admin/newstudent");
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
		if($cnt>=1){
			$msg="<div class='alert alert-danger'>Sorry! <b>$email</b> belongs to other user..";
			$this->session->set_flashdata("error",$msg);
			redirect("admin/newstudent");
		}
		/*
		 * Check in teacher's table
		 */
		$db1->where("email",$email);
		$db1->from("teacher");
		$cnt=$db1->count_all_results();
		if($cnt>=1){
			$msg="<div class='alert alert-danger'>Sorry! <b>$email</b> belongs to other user..";
			$this->session->set_flashdata("error",$msg);
			redirect("admin/newstudent");
		}
		/*
		 * Check in TPO Table
		 */
		$db1->where("email",$email);
		$db1->from("tpo");
		$cnt=$db1->count_all_results();
		if($cnt>=1){
			$msg="<div class='alert alert-danger'>Sorry! <b>$email</b> belongs to other user..";
			$this->session->set_flashdata("error",$msg);
			redirect("admin/newstudent");
		}
		/*
		 * Check in professionals table..
		 */
		$db1->where("email",$email);
		$db1->from("professional_details");
		$cnt=$db1->count_all_results();
		if($cnt>=1){
			$msg="<div class='alert alert-danger'>Sorry! <b>$email</b> belongs to other user..";
			$this->session->set_flashdata("error",$msg);
			redirect("admin/newstudent");
		}
	}
	function stats(){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$data['totalclg']=$db1->get("newcollege")->num_rows(); //Get Total clg
		$data['totalstud']=$db1->get("student")->num_rows(); //get total students
		$data['totalstudregistered']=$db1->get_where("student",array('register'=>1))->num_rows();//get total registered students
		$data['totalteachers']=$db1->get("teacher")->num_rows(); //total tchrs
		$data['totalteachersregistered']=$db1->get_where("teacher",array('register'=>1))->num_rows(); //registered teacheres
		$data['totalprof']=$db1->get("professional_details")->num_rows();//total professionals
		$data['pendingprof']=$db1->get("professionalrequest")->num_rows(); //pending prof request
		$data['mailqueue']=$db1->get("mailqueue")->num_rows(); //queued mails
		$data['examrequest']=$db1->get_where("requestexam",array('exam_module'=>0))->num_rows(); //pending exam request
		$data['sms']=$db1->get_where("sms",array('status'=>"A"))->num_rows(); //sms avtive ornot
		$data['studyrequest']=$db1->get("studyrequest")->num_rows(); //open requests
		return $data;
	}
}
