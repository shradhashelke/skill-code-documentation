<?php
class Profile extends CI_Model {
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
	function getProfile($pid){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("profile_id",$pid);
		$db1->from("profile");
		$cnt=$db1->count_all_results();
		if($cnt<=0){

			show_error("Profile Not Found");
		}

		$db1->where("profile_id",$pid);
		$query=$db1->get("profile");
		$row = $query->row_array();
		$email=$row['email'];
		$userid=$row['userid'];
		$ptype=$row['profiletype'];
		return $profile=$this->generateProfile($email,$userid,$ptype);
	}
	function generateProfile($email,$userid,$ptype){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		if($ptype=="student") {
			$db1->where("stud_id",$userid);
			$db1->or_where("email",$email);
			$query = $db1->get("stud_details");
			return $query->result();
		}
		if($ptype=="professional") {
			$db1->where("email",$email);
			$query = $db1->get("professional_details");
			return $query->result();
		}
		if($ptype=="teacher") {
			$db1->where("id",$userid);
			$db1->or_where("email",$email);
			$query = $db1->get("teacher_details");
			return $query->result();
		}
	}
	function getProfileId($id,$email,$ptype){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$db1->where("profiletype",$ptype);
		$db1->where("email",$email);
		//$db1->or_where("userid",$id);
		$db1->from("profile");
		$cnt=$db1->count_all_results();
		if($cnt<1)
			return 0;
		$db1->where("profiletype",$ptype);
		$db1->where("email",$email);
		$query=$db1->get("profile");
		$row = $query->row_array();
		return $row['profile_id'];

	}
	function getSkills(){
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		/*
		 *
		 * Extract the skills and other information of professional..
		 *
		 */
		$q=$db1->get("professional_details");
		foreach($q->result() as $row):
			$pid=$this->getProfileId($row->id,$row->email,"professional");

			if($pid!=0){
				$skills=$row->topskill1.",".$row->topskill2.",".$row->topskill3.",".$row->topskill4.",".$row->topskill5.",";
				$skills.=$row->skills;
				$collection[$pid]=strtolower($skills);
			}
		endforeach;
		/*
		 *
		 * Extract the skills and other information of teacher..
		 * Teachers must be of same college only....
		 */
		$q=$db1->get("teacher_details");
		foreach($q->result() as $row):
			$stud=$this->session->userdata('userdetails');
			$pid=$this->getProfileId($row->id,$row->email,"teacher");
			if($row->college_id==$stud->college_id){
				if($pid!=0){
					$skills=$row->topskill1.",".$row->topskill2.",".$row->topskill3.",".$row->topskill4.",".$row->topskill5.",";
					$skills.=$row->skills;
					$collection[$pid]=strtolower($skills);
				}
			}
		endforeach;
		//	print_r($collection);
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
		$matchDocs = array();
		$docCount = count($index['docCount']);
		//print_r($query);
		foreach($query as $qterm) {
			$entry = $index['dictionary'][$qterm];
			foreach($entry['postings'] as $docID => $posting) {
				$matchDocs[$docID] += $posting['tf'] * log($docCount + 1 / $entry['df'] + 1, 2);
			}
		}
		// length normalise
		//print_r($matchDocs);
		/*foreach($matchDocs as $docID => $score) {
			$matchDocs[$docID] = $score/$index['docCount'][$docID];
		}*/

		arsort($matchDocs); // high to low
		//print_r($matchDocs);
		return $matchDocs;
	}
	function showprofiles($profiles){
                    foreach($profiles as $p):
						$pid=$p['pid'];
						$name=$p['name'];
						$topskills=explode(",",$p['topskills']);
						$ptype=$p['ptype'];
						$skills=explode(",",$p['skills']);
						echo "<div class='col-md-3'>".
							"<div class='featured-box featured-box-secondary'>".
								"<div class='box-content'>".
									"<i class='icon-featured icon icon-user'></i>".
									"<h4>$name</h4>".
									"<h5>$ptype</h5>".
									"<hr>".
									"<p>";
								foreach($topskills as $skill):
											echo "<label class='label label-danger'>".$skill."</label> ";
								endforeach;
										foreach($skills as $skill):
											echo "<label class='label label-danger'>".$skill."</label> ";
										endforeach;

									echo "</p>".
									"<a class='btn btn-success' href='newchat?pid=".$this->encrypt->encode($pid)."'><i class='icon icon-road'></i> Guide Me</a>&nbsp;".
									"<a class='btn btn-info' href='".base_url()."in/p/$pid'><i class='icon icon-eye'></i> Profile</a><br><br>".
								"</div>".
							"</div>".
						"</div>";
					 endforeach;
}
}