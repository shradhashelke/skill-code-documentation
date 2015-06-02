<?php
class Validatesession extends CI_Model {

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
   
   function Validatesession()
   {
	   $sess=$this->session->all_userdata();
	   print_r($sess);
	   if(isset($sess['userdetails'])){
		   $loggedinas=$sess['loggedinas'];
		   if($loggedinas=="student"){
			   //redirect("student/home");
		   }

	   }else{
		   redirect(site_url());
	   }
   }

}