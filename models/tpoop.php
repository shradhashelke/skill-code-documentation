<?php
class Tpoop extends CI_Model {
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
  	function step1()
  {
	 $stud=$_POST;
	 echo count($stud);
	 print_r($stud);	
	 $config=$this->loadDatabase("skill");
	 $db1=$this->load->database($config,TRUE);
	 $db1->insert("tpo_details",$stud);
	 $db1->where("id",$_POST['tpo_id']);
	 $data['register']=1;
	 $db1->update("tpo",$data);
	 redirect("tpo/step2");
  }
  	function step2()
  {
	$e=$this->encrypt->encode($_POST['password']); //Encrypt the Password
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$data['password']=$e;
	$db1->where("id",$_POST['tpo_id']);
	$db1->update("tpo",$data);
	  $data1['userid']=$_POST['tpo_id'];
	  $data1['profiletype']="tpo";
	  $data1['profile_id']=random_string('numeric', 6);
	  $db1->insert("profile",$data1);
  }
  	function getupdates()
  {
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$stud=$this->session->userdata("userdetails");
	$cid=$stud->email;
	$db1->where("msg_to",$cid);
	$data= array();
	$q=$db1->get("comments");
	foreach($q->result() as $row)
	{
		array_push($data,$row);	
	}
	return $data;
  }
  	function examrequests()
  {
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE);
	$stud=$this->session->userdata("userdetails");
	$cid=$stud->college_id;
	$db1->where("college_id",$cid);
	$db1->where("exam_module",1);
	$data= array();
	$q=$db1->get("requestexam");
	foreach($q->result() as $row)
	{
		array_push($data,$row);	
	}
	return $data;
  }
	function filtergrade($grade,$dept,$download){
		$depts=strtoupper(implode(",",$dept));
		if($grade!="all")
			$title="STUDENTS HAVING GREATER THAN {$grade} GRADES FROM {$depts} DEPARTMENT[S].";
		else
			$title="STUDENTS HAVING ANY GRADES FROM {$depts} DEPARTMENT[S].";
		$this->load->library('excel');
		$this->load->helper("file");
		if($download==1){
			$obj=$this->session->userdata("userdetails");
			$collegename=$this->getCollegeName($obj->college_id);
			$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader("&C&H{$collegename}");
			$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&BPLEASE TREAT THIS DOCUMENT AS CONFIDENTIAL!&RPAGE &P of &N');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
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
			$this->excel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($borderThick);
			$this->excel->getActiveSheet()->getDefaultStyle()->applyFromArray($style);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle('Account Info');
			//Set header........
			$color = "FFFFFF";
			$this->excel->getActiveSheet()->getStyle("A1")->getFont()->getColor()->applyFromArray(array("rgb" => $color));
			$this->excel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getStyle("A2:F2")->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($borderThick);
			//set cell A1 content with some text............
			$this->excel->getActiveSheet()->mergeCells("A1:F1");
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);
			$this->excel->getActiveSheet()->setCellValue('A1', $title);
			$this->excel->getActiveSheet()->setCellValue('A2', '#');
			$this->excel->getActiveSheet()->setCellValue('B2', 'Name');
			$this->excel->getActiveSheet()->setCellValue('C2', 'Department');
			$this->excel->getActiveSheet()->setCellValue('D2', 'Email');
			$this->excel->getActiveSheet()->setCellValue('E2', 'Mobile');
			$this->excel->getActiveSheet()->setCellValue('F2', 'Marks');
			$index=1;
			$j=3;
		}
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$obj=$this->session->userdata("userdetails");
		$cid=$obj->college_id;
		$db1->where("college_id",$cid);
		$db1->order_by("be","DESC");
		if($grade!="all")
			$db1->where("be >=",$grade);
		$q=$db1->get("stud_details");
		echo "<table class='table table-condensed table-striped table-hover'>".
			"<tr class='alert alert-danger'>".
			"<th>#</th>".
			"<th>Name</th>".
			"<th>Department</th>".
			"<th>Email</th>".
			"<th>Mobile</th>".
			"<th>Grade</th>".
			"</tr>";
		$i=1;
		foreach($q->result() as $row):
			if($dept['0']!="all") {
				if (in_array($row->dept, $dept)) {
					echo "<tr>" .
						"<td>" . $i++ . "</td>" .
						"<td>" . $row->fname . " " . $row->mname . " " . $row->lname . "</td>" .
						"<td>" . $row->dept . "</td>" .
						"<td>" . $row->email . "</td>" .
						"<td>" . $row->mobile . "</td>" .
						"<td>" . $row->be . "</td>" .
						"</tr>";
						if($download==1) {
							$this->excel->getActiveSheet()->setCellValue("A" . $j, $index++);
							$name = $row->fname . " " . $row->mname . " " . $row->lname;
							$this->excel->getActiveSheet()->setCellValue("B" . $j, $name);
							$this->excel->getActiveSheet()->setCellValue("C" . $j, $row->dept);
							$this->excel->getActiveSheet()->setCellValue("D" . $j, $row->email);
							$this->excel->getActiveSheet()->setCellValue("E" . $j, $row->mobile);
							$this->excel->getActiveSheet()->setCellValue("F" . $j, $row->be);
							$this->excel->getActiveSheet()->getStyle("A{$j}:F{$j}")->applyFromArray($borderThin);
							$j++;
						}
				}
			}else{
				echo "<tr>" .
					"<td>" . $i++ . "</td>" .
					"<td>" . $row->fname . " " . $row->mname . " " . $row->lname . "</td>" .
					"<td>" . $row->dept . "</td>" .
					"<td>" . $row->email . "</td>" .
					"<td>" . $row->mobile . "</td>" .
					"<td>" . $row->be . "</td>" .
					"</tr>";
					if($download==1) {
						$this->excel->getActiveSheet()->setCellValue("A" . $j, $index++);
						$name = $row->fname . " " . $row->mname . " " . $row->lname;
						$this->excel->getActiveSheet()->setCellValue("B" . $j, $name);
						$this->excel->getActiveSheet()->setCellValue("C" . $j, $row->dept);
						$this->excel->getActiveSheet()->setCellValue("D" . $j, $row->email);
						$this->excel->getActiveSheet()->setCellValue("E" . $j, $row->mobile);
						$this->excel->getActiveSheet()->setCellValue("F" . $j, $row->be);
						$this->excel->getActiveSheet()->getStyle("A{$j}:F{$j}")->applyFromArray($borderThin);
						$j++;
					}
			}
		if($download==1){

		}
		endforeach;
		if($download==1) {
			$index=$index+2;
			$total="Total = ".($index-3)." Students";
			$this->excel->getActiveSheet()->mergeCells("A{$index}:B{$index}");
			$this->excel->getActiveSheet()->getStyle("A{$index}:B{$index}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$this->excel->getActiveSheet()->getStyle("A{$index}:B{$index}")->getFill()->getStartColor()->setARGB('FFA0A0A0');
			$this->excel->getActiveSheet()->setCellValue("A" . $index, $total);
			$this->excel->getActiveSheet()->getStyle("A{$index}:B{$index}")->applyFromArray($borderThin);
			$filename = rand(0, 9876543) . "studentExport.xls"; //save our workbook as this file name
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			//force user to download the Excel file without writing it to server's HD
			$path = "files/userfiles/" . $filename;
			$objWriter->save($path);
		}
		if($download==1) {
			echo "<tr class='alert alert-danger'>" .
				"<th colspan='6'>Total " . ($i - 1) . " Student(s) <a class='btn btn-sm btn-primary pull-right' href='".base_url()."$path'><i class='icon icon-download'></i> Download File</a></th>" .
				"</tr>" .
				"</table>";
		}
		else{
			echo "<tr class='alert alert-danger'>" .
				"<th colspan='6'>Total " . ($i - 1) . " Student(s)</th>" .
				"</tr>" .
				"</table>";
		}

	}
	function filtergradeandskill($grade,$skill,$emailrank,$dept,$download){
		$depts=strtoupper(implode(",",$dept));
		$skills=strtoupper($skill);
		if($grade!="all")
			$title="STUDENTS HAVING {$skills} SKILLS FROM {$depts} DEPARTMENT[S].";
		else
			$title="STUDENTS HAVING ANY GRADES FROM {$depts} DEPARTMENT[S].";
		$this->load->library('excel');
		$this->load->helper("file");
		if($download==1){
			$obj=$this->session->userdata("userdetails");
			$collegename=$this->getCollegeName($obj->college_id);
			$this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader("&C&H{$collegename}");
			$this->excel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&BPLEASE TREAT THIS DOCUMENT AS CONFIDENTIAL!&RPAGE &P of &N');
			//activate worksheet number 1
			$this->excel->setActiveSheetIndex(0);
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
			$this->excel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($borderThick);
			$this->excel->getActiveSheet()->getDefaultStyle()->applyFromArray($style);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			//name the worksheet
			$this->excel->getActiveSheet()->setTitle('Account Info');
			//Set header........
			$color = "FFFFFF";
			$this->excel->getActiveSheet()->getStyle("A1")->getFont()->getColor()->applyFromArray(array("rgb" => $color));
			$this->excel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getStyle("A2:F2")->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($borderThick);
			//set cell A1 content with some text............
			$this->excel->getActiveSheet()->mergeCells("A1:F1");
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->applyFromArray(
				array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
			);
			$this->excel->getActiveSheet()->setCellValue('A1', $title);
			$this->excel->getActiveSheet()->setCellValue('A2', '#');
			$this->excel->getActiveSheet()->setCellValue('B2', 'Name');
			$this->excel->getActiveSheet()->setCellValue('C2', 'Department');
			$this->excel->getActiveSheet()->setCellValue('D2', 'Email');
			$this->excel->getActiveSheet()->setCellValue('E2', 'Mobile');
			$this->excel->getActiveSheet()->setCellValue('F2', 'Marks');
			$index=1;
			$j=3;
		}
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$obj=$this->session->userdata("userdetails");
		$cid=$obj->college_id;
		$db1->where("college_id",$cid);
		$db1->order_by("be","DESC");
		if($grade!="all")
			$db1->where("be >=",$grade);
		$q=$db1->get("stud_details");
		echo "<table class='table table-condensed table-striped table-hover'>".
			"<tr class='alert alert-danger'>".
			"<th>#</th>".
			"<th>Name</th>".
			"<th>Department</th>".
			"<th>Email</th>".
			"<th>Mobile</th>".
			"<th>Grade</th>".
			"</tr>";
		$i=1;
		//print_r($emailrank);
		foreach($q->result() as $row):
			if(in_array($row->email,$emailrank)) {
				if($dept['0']!="all") {
					if (in_array($row->dept, $dept)) {
						echo "<tr>" .
							"<td>" . $i++ . "</td>" .
							"<td>" . $row->fname . " " . $row->mname . " " . $row->lname . "</td>" .
							"<td>" . $row->dept . "</td>" .
							"<td>" . $row->email . "</td>" .
							"<td>" . $row->mobile . "</td>" .
							"<td>" . $row->be . "</td>" .
							"</tr>";
						if($download==1) {
							$this->excel->getActiveSheet()->setCellValue("A" . $j, $index++);
							$name = $row->fname . " " . $row->mname . " " . $row->lname;
							$this->excel->getActiveSheet()->setCellValue("B" . $j, $name);
							$this->excel->getActiveSheet()->setCellValue("C" . $j, $row->dept);
							$this->excel->getActiveSheet()->setCellValue("D" . $j, $row->email);
							$this->excel->getActiveSheet()->setCellValue("E" . $j, $row->mobile);
							$this->excel->getActiveSheet()->setCellValue("F" . $j, $row->be);
							$this->excel->getActiveSheet()->getStyle("A{$j}:F{$j}")->applyFromArray($borderThin);
							$j++;
						}
					}
				}else{
					echo "<tr>" .
						"<td>" . $i++ . "</td>" .
						"<td>" . $row->fname . " " . $row->mname . " " . $row->lname . "</td>" .
						"<td>" . $row->dept . "</td>" .
						"<td>" . $row->email . "</td>" .
						"<td>" . $row->mobile . "</td>" .
						"<td>" . $row->be . "</td>" .
						"</tr>";
					if($download==1) {
						$this->excel->getActiveSheet()->setCellValue("A" . $j, $index++);
						$name = $row->fname . " " . $row->mname . " " . $row->lname;
						$this->excel->getActiveSheet()->setCellValue("B" . $j, $name);
						$this->excel->getActiveSheet()->setCellValue("C" . $j, $row->dept);
						$this->excel->getActiveSheet()->setCellValue("D" . $j, $row->email);
						$this->excel->getActiveSheet()->setCellValue("E" . $j, $row->mobile);
						$this->excel->getActiveSheet()->setCellValue("F" . $j, $row->be);
						$this->excel->getActiveSheet()->getStyle("A{$j}:F{$j}")->applyFromArray($borderThin);
						$j++;
					}
				}
				if($download==1){

				}
			}
		endforeach;


		if($download==1) {
			$index=$index+2;
			$total="Total = ".($index-3)." Students";
			$this->excel->getActiveSheet()->mergeCells("A{$index}:B{$index}");
			$this->excel->getActiveSheet()->getStyle("A{$index}:B{$index}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$this->excel->getActiveSheet()->getStyle("A{$index}:B{$index}")->getFill()->getStartColor()->setARGB('FFA0A0A0');
			$this->excel->getActiveSheet()->setCellValue("A" . $index, $total);
			$this->excel->getActiveSheet()->getStyle("A{$index}:B{$index}")->applyFromArray($borderThin);
			$filename = rand(0, 9876543) . "studentExport.xls"; //save our workbook as this file name
			//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
			//if you want to save it as .XLSX Excel 2007 format
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			//force user to download the Excel file without writing it to server's HD
			$path = "files/userfiles/" . $filename;
			$objWriter->save($path);
		}
		if($download==1) {
			echo "<tr class='alert alert-danger'>" .
				"<th colspan='6'>Total " . ($i - 1) . " Student(s) <a class='btn btn-sm btn-primary pull-right' href='".base_url()."$path'><i class='icon icon-download'></i> Download File</a></th>" .
				"</tr>" .
				"</table>";
		}
		else{
			echo "<tr class='alert alert-danger'>" .
				"<th colspan='6'>Total " . ($i - 1) . " Student(s)</th>" .
				"</tr>" .
				"</table>";
		}

	}
	function suggest($q){
		//$skills=$this->getSkills();
		//print_r($q);
		$q=explode(",",urldecode($q));
		$str="";
		foreach($q as $current):
			$str.=$current." ";
			endforeach;
		$q=explode(" ",trim($str));
		$rank=$this->rankprofile($q);
		$rankemail=array();
		foreach($rank as $r => $s):
			array_push($rankemail,$r);
		endforeach;
		return $rankemail;
	}
	function getSkills(){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		/*
		 *
		 * Extract the skills and other information of student..
		 *
		 */
		$obj=$this->session->userdata("userdetails");
		$cid=$obj->college_id;
		$db1->where("college_id",$cid);
		$q=$db1->get("stud_details");
		$i=0;
		foreach($q->result() as $row):
				$skills=$row->topskill1.",".$row->topskill2.",".$row->topskill3.",".$row->topskill4.",".$row->topskill5.",";
				$skills.=$row->skills;
				$collection[$row->email]=strtolower($skills);
		endforeach;
		//print_r($collection);

		$dictionary = array();
		$docCount = array();

		foreach($collection as $docID => $doc) {
			$terms = explode(',', $doc);
			$str="";
			foreach($terms as $word):
				$str.=$word." ";
			endforeach;
			$terms = explode(' ', $str);
			$docCount[$docID] = count($terms);
			foreach($terms as $term) {
				if(!isset($dictionary[$term])) {
					$dictionary[$term] = array('df' => 0, 'postings' => array());
				}
				if(!isset($dictionary[$term]['postings'][$docID])) {
					$dictionary[$term]['df']++;
					$dictionary[$term]['postings'][$docID] = array('tf' => 0);
				}

				$dictionary[$term]['postings'][$docID]['tf']++;
			}
		}

		return array('docCount' => $docCount, 'dictionary' => $dictionary);
	}
	function rankprofile($query){
		error_reporting(E_ERROR);
		//$query = array('team', 'php', 'management');//case-sensitive always convert query to lowercase...
		$index =$this->getSkills();
		//print_r($query);
		$matchDocs = array();
		$docCount = count($index['docCount']);

		foreach($query as $qterm) {
			$entry = $index['dictionary'][$qterm];
			foreach($entry['postings'] as $docID => $posting) {
				$matchDocs[$docID] += $posting['tf'] * log($docCount + 1 / $entry['df'] + 1, 2);
			}
		}
		// length normalise
		foreach($matchDocs as $docID => $score) {
			$matchDocs[$docID] = $score/$index['docCount'][$docID];
		}
		arsort($matchDocs); // high to low
		return $matchDocs;
	}
	function getDeptList(){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$mydata=$this->session->userdata('userdetails');
		$cid=$mydata->college_id;
		$db1->select('dept');//Select only departments
		$db1->where('college_id',$cid); //Of your college
		$query=$db1->get("stud_details");
		$list=array();
		foreach($query->result() as $row):
			array_push($list,$row->dept); //make a list of depts. (There will be no. of duplicate department names)
		endforeach;
		$list=array_unique($list); //Remove duplicates and we will get all the unique dept list.
		return $list; //return list;
	}
	function getCollegeName($id){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("id",$id);
		$query=$db1->get("newcollege");
		$row = $query->row_array();
		return $row['college_name'];
	}
}	