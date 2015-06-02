<?php
class SMS extends CI_Model {
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

function sendsms($mobile,$msg)
 {
	$config=$this->loadDatabase("skill");
	$db1=$this->load->database($config,TRUE) or die("Database Error");
    $query=$db1->get("sms");
	$row=$query->row_array(); 	
	$user = $row['user'];
	$apikey = $row['apikey'];
	$senderid  =  $row['senderid'];
	$mobile  =  $mobile; 
	$message   =  $msg; 
	$message = urlencode($message);
	$type   =  $row['type'];
	$status=$row['status'];
	if($status!="NA"){
		$ch = curl_init("http://smshorizon.co.in/api/sendsms.php?user=".$user."&apikey=".$apikey."&mobile=".$mobile."&senderid=".$senderid."&message=".$message."&type=".$type.""); 
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);      
		curl_close($ch); 
		echo $output;
	}
 }

}