<?php
/*
 * Class Apis: Provides all operations requested via ajax requests.
 */
class Apis extends CI_Model {
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
   	function getStudents()
   {
	   $config=$this->loadDatabase("skill");
	   $db1=$this->load->database($config,TRUE);
	   $db1->where('college_id',$_REQUEST['id']);
	   $query=$db1->get_where('student');
	   foreach($query->result() as $row):
	   	echo "<option value='".$row->email."'>".$row->sname." [".$row->email."] </option>";
	   endforeach;
   }
  	function getTeacher()
   {
	   $config=$this->loadDatabase("skill");
	   $db1=$this->load->database($config,TRUE);
	   $db1->where('college_id',$_REQUEST['id']);
	   $query=$db1->get_where('teacher');
	   foreach($query->result() as $row):
	   	echo "<option value='".$row->email."'>".$row->sname." [".$row->email."] </option>";
	   endforeach;
   }
  	function getTpo()
   {
	   $config=$this->loadDatabase("skill");
	   $db1=$this->load->database($config,TRUE);
	   $db1->where('college_id',$_REQUEST['id']);
	   $query=$db1->get_where('tpo');
	   foreach($query->result() as $row):
	   	echo "<option value='".$row->email."'>".$row->sname." [".$row->email."] </option>";
	   endforeach;
   }
   	function updateschedule()
   {
	   $config=$this->loadDatabase("skill");
	   $db1=$this->load->database($config,TRUE);
	   $db1->where('email',$_REQUEST['email']);
	   $data['schedule']=$_REQUEST['datetime'];
	   $db1->update("requestexam",$data);
   }
	function  getprofile(){

		if($_GET)
		{
			$q=$_GET['searchword'];
			$items = array();
			$config=$this->loadDatabase("skill");
			$db1=$this->load->database($config,TRUE);
			/*
			 * Generate JSON variables of professionals..
			 */
			$db1->like('fname', $q);
			$db1->or_like('mname', $q);
			$db1->or_like('lname', $q);
			$db1->order_by('id','ASC');
			$db1->limit(3);
			$query=$db1->get("professional_details");
			foreach($query->result() as $row):
				$uid = $row->id;
				$username=$row->fname." ".$row->lname;
				$email=$row->email;
				$media="ann";
				$pid=$this->getProfileId($row->id,$row->email);
				if($pid!=NULL)
					$country="Professional";
				else{
					$country="Professional";
					$pid=00;
				}
				$b_username='<b>'.$q.'</b>';
				$b_email='<b>'.$q.'</b>';
				$final_username = str_replace($q, $b_username, $username);
				$final_email = str_replace($q, $b_email, $email);
				$items[] = array('uid' => $uid, 'username' => $final_username, 'email' => $final_email, 'country' => $country, 'media' => $media,'pid'=>$pid);
			endforeach;
			/*
			 * Repeat JSON variables of Teachers..
			 */
			$db1->like('fname', $q);
			$db1->or_like('mname', $q);
			$db1->or_like('lname', $q);
			$db1->order_by('id','ASC');
			$db1->limit(3);
			$query=$db1->get("teacher_details");
			foreach($query->result() as $row):
				$uid = $row->id;
				$username=$row->fname." ".$row->lname;
				$email=$row->email;
				$media="ann";
				$pid=$this->getProfileId($row->id,$row->email);
				if($pid!=NULL)
					$country="Teacher at ".$this->getCollegeName($row->college_id);
				else{
					$country="Teacher at ".$this->getCollegeName($row->college_id);
					$pid=00;
				}
				$b_username='<b>'.$q.'</b>';
				$b_email='<b>'.$q.'</b>';
				$b_country='<b>'.$q.'</b>';
				$final_username = str_replace($q, $b_username, $username);
				$final_email = str_replace($q, $b_email, $email);
				$items[] = array('uid' => $uid, 'username' => $final_username, 'email' => $final_email, 'country' => $country, 'media' => $media,'pid'=>$pid);
			endforeach;
			/*
			 * Repeat JSON variables of students..
			 */
			$db1->like('fname', $q);
			$db1->or_like('mname', $q);
			$db1->or_like('lname', $q);
			$db1->order_by('id','ASC');
			$db1->limit(3);
			$query=$db1->get("stud_details");
			foreach($query->result() as $row):
				$uid = $row->id;
				$username=$row->fname." ".$row->lname;
				$email=$row->email;
				$media="ann";
				$pid=$this->getProfileId($row->id,$row->email);
				if($pid!=NULL)
					$country="Student at ".$this->getCollegeName($row->college_id);
				else{
					$country="Student at ".$this->getCollegeName($row->college_id);
					$pid=00;
				}
				$b_username='<b>'.$q.'</b>';
				$b_email='<b>'.$q.'</b>';
				$b_country='<b>'.$q.'</b>';
				$final_username = str_replace($q, $b_username, $username);
				$final_email = str_replace($q, $b_email, $email);
				$items[] = array('uid' => $uid, 'username' => $final_username, 'email' => $final_email, 'country' => $country, 'media' => $media,'pid'=>$pid);
			endforeach;
			header('Content-Type:text/json');
			echo json_encode($items);
		}
		else{
			echo json_encode('No search string found');
		}
}
	function getCollegeName($id){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("id",$id);
		$query=$db1->get("newcollege");
		$row = $query->row_array();
		return $row['college_name'];
	}
	function getProfileId($id,$email){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("email",$email);
		$db1->from("profile");
		$cnt=$db1->count_all_results();
		if($cnt<1)
			return 0;
		$db1->where("email",$email);
		$query=$db1->get("profile");
		$row = $query->row_array();
		return $row['profile_id'];

	}
	function loadchat($toemail,$name){
		$this->load->model("studentop");
		$this->studentop->showchat($toemail,$name);
	}
	function getDeptList($id){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$cid=$id;
		$db1->select('dept');//Select only departments
		$db1->where('college_id',$cid); //Of your college
		$query=$db1->get("student");
		$list=array();
		foreach($query->result() as $row):
			if($row->dept!="ALL")
				array_push($list,$row->dept);
		endforeach;
		$dept=array_unique($list);
		foreach($dept as $row):
			echo "<option value='".$row."'>".$row."</option>";
		endforeach;

	}
}