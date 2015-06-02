<?php
class Exam extends CI_Model {
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
	function saveCust($cname,$cemail,$college_id,$mobile)
	{
	$cemail=rawurldecode($cemail);
	$cname=rawurldecode($cname);
		//$this->getstudentfile($college_id,$cname,$cemail);
		//exit;
	$config=$this->loadDatabase("vr_examadmin");
	$db1=$this->load->database($config,TRUE) or die("Database Error");
	$this->load->helper('string');	
	up:
	$rand= random_string('alnum', 5);
	$db1->like('uid', $rand);
	$db1->from('customer');
	$cnt=$db1->count_all_results();
	if($cnt>=1)
	   goto up;
	$password=random_string('alnum', 6);  
	$data=array(
				'cname' => $cname,
				'uid' => $rand,
				'email' =>$cemail,
				'username'=>$cemail,
				'password'=>$password,
				'contact' => $mobile
				);			
	$db1->insert('customer',$data);
	$this->smsprofessional($cemail,$college_id,$mobile);
	$this->notifyprofessional($cemail,$college_id,$cemail,$password,$rand);
	$this->notifystudent($college_id,$cname,$cemail);
	$this->notifytpo($college_id,$cname,$cemail);
	$filename=$this->getstudentfile($college_id,$cname,$cemail);
	$mail=array(
				'subject'=>"Skill+ : Account Created for Online Examination",
				'mailto'=>$cemail,
				'msg'=>"Administrator Account for Online Examination @ <b>**".$cname."</b> has been created. You will be acknowledged on account activation.<br><b>Note : </b> Please use only attached file to upload students.",
				'attach'=>$filename,
				'type'=>"accountcreated",
				);
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE) or die("Database Error");
	$db1->insert('mailqueue',$mail);

	return 1;	
}
	function notifyprofessional($email,$colleg_id,$username,$password,$userid)
{
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE) or die("Database Error");
	$data['username']=$email;
	$data['password']=$password;
	$data['message']="Your account has been created for online examination. Following are the details for the same. Currently your account is <strong>INACTIVE</strong>, On activating your account you will be notified by an email. Be tuned with you email address.";
	$data['type']="requestaccepted";
	$data['college_id']=$colleg_id;
	$data['userid']=$userid;
	$db1->insert("examnotifyprofessional",$data);
}
	function notifystudent($college_id,$cname,$p_email)
{
	$config=$this->loadDatabase("skill");
	$this->load->model("administrator");
	$db1=$this->load->database($config,TRUE) or die("Database Error");
	$data['college_id']=$college_id;
	$data['type']="justnotify";
	$data['message']="Online Test has been setup for you by <b>".$this->administrator->getProfessionalName($p_email)."</b> (Professional). Soon you will recive the Credentials for the test. Stay tuned with <b>".$this->administrator->getTPOName($college_id)."</b> (Representetive of ".$cname.").";
	$db1->insert("examnotifystudent",$data);
}
	function smsprofessional($email,$cid,$mobile)
{
	$config=$this->loadDatabase("skill");
	$this->load->model("administrator");
	$this->load->model("sms");
	$db1=$this->load->database($config,TRUE) or die("Database Error");
	$data="Dear ".$this->administrator->getProfessionalFname($email).",\nOnline Test Acc. for ".$this->administrator->getCollegeName($cid)." has been created. Soon you will be notified on account activation.\nThank You\n--\nSkill+ Team";
	$this->sms->sendsms($mobile,$data);
}
	function notifytpo($college_id,$cname,$p_email)
{
	$config=$this->loadDatabase("skill");
	$this->load->model("administrator");
	$db1=$this->load->database($config,TRUE) or die("Database Error");
	$db1->where('id',$college_id);
	$query=$db1->get("tpo");
	foreach($query->result() as $row):
		$data['msg_to']=$row->email;
		$data['type']="comment";
		$data['msg_from']=$p_email;
		$data['message']="Hi <b>".$this->administrator->getTPOName($college_id)."</b>, <br>I am <b>".$this->administrator->getProfessionalName($p_email)." (Professional)</b>. I appriciate the knowledge and activities the students are doing. For further analysis and other activities I want to Analyze your Students. So please kindly acknowledge me with the details when I can schedule the online test for the students.<br><br> <h5>To Acknowledge <a href='exam'><i class='icon icon-link'></i> CLICK HERE</a>";
		$db1->insert("comments",$data);
	endforeach;	
}
	function getstudentfile($cid,$cname,$cemail){
			$this->loadRequirements($cid,$cemail);
			//load our new PHPExcel library
			$this->load->library('excel');
			$this->load->helper("file");
			$config=$this->loadDatabase("skill");
			$db1=$this->load->database($config,TRUE) or die("Database Error");
			$query=$this->loadRequirements($cid,$cemail);
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
			/*
			 * Format Excel Sheet
			 */
		$style = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		);
		$borderThin = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
		$borderThick = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
		$this->excel->getActiveSheet()->getStyle('A1:F1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$this->excel->getActiveSheet()->getStyle('A1:F1')->getFill()->getStartColor()->setARGB('FFA0A0A0');
		$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$this->excel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($style);
		$this->excel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($borderThick);
		$this->excel->getActiveSheet()->getDefaultStyle()->applyFromArray($style);
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(3);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$color = "FFFFFF";
		$this->excel->getActiveSheet()->getStyle("A1:F1")->getFont()->getColor()->applyFromArray(array("rgb" => $color));
		$this->excel->getActiveSheet()->getStyle("A1:F1")->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($borderThick);

			//name the worksheet
			$this->excel->getActiveSheet()->setTitle('Account Info');
			//set cell A1 content with some text
			$this->excel->getActiveSheet()->setCellValue('A1', '#');
			$this->excel->getActiveSheet()->setCellValue('B1', 'UID');
			$this->excel->getActiveSheet()->setCellValue('C1', 'Name');
			$this->excel->getActiveSheet()->setCellValue('D1', 'Department');
			$this->excel->getActiveSheet()->setCellValue('E1', 'Email');
			$this->excel->getActiveSheet()->setCellValue('F1', 'Mobile');
			$i=1;
			$j=2;
			foreach($query->result() as $row):
				$this->excel->getActiveSheet()->setCellValue("A".$j, $i++);
				$this->excel->getActiveSheet()->setCellValue("B".$j, $row->gr);
				$this->excel->getActiveSheet()->setCellValue("C".$j, $row->fname." ".$row->mname." ".$row->lname);
				$this->excel->getActiveSheet()->setCellValue("D".$j, $row->dept);
				$this->excel->getActiveSheet()->setCellValue("E".$j, $row->email);
				$this->excel->getActiveSheet()->setCellValue("F".$j, $row->mobile);
				$this->excel->getActiveSheet()->getStyle("A{$j}:F{$j}")->applyFromArray($borderThin);
			$j++;
			endforeach;
			$filename=rand(0,9876543)."studentolexam.xls"; //save our workbook as this file name
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			//force user to download the Excel file without writing it to server's HD
			$path="files/userfiles/".$filename;
			$objWriter->save($path);
		   //echo "<a href='".base_url().$path."'>File</a>";
		return $path;
	}
	function loadRequirements($cid,$pemail){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE) or die("Database Error");
		$db1->where("email",$pemail);
		$db1->where("college_id",$cid);
		$query=$db1->get("requestexam");
		$row=$query->row_array();
		$gto=$row['gto'];
		$gfrom=$row['gfrom'];
		$dept=explode(",",$row['message']);
		$db1->where("college_id",$cid);
		$db1->where('be >=',$gfrom);
		$db1->where('be <=',$gto);
		$query=$db1->get("stud_details");
		return $query;
	}
}