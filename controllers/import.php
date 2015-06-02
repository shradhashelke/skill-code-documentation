<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/*
	 * Class is used to reterive user the user profile from linkedIn
	 */
class Import extends CI_Controller {
	public function Imports(){
		echo "<a href='https://www.linkedin.com/uas/oauth2/authorization?response_type=code&client_id=77x90we8n9xx2x&redirect_uri=http://localhost/research/raj/import/linkedin&state=9960796998&scope=r_basicprofile'>Click</a>";
	}
	/*
	 * Make a RESTful request to linkedin using api to access userprofile
	 */
	public function linkedin($action="")
	{
		if (isset($_GET['code'])) {
			$postdata = "grant_type=authorization_code&code=" . $_GET['code'] . "&redirect_uri=http://localhost/research/raj/import/linkedin&client_id=77x90we8n9xx2x&client_secret=T4lsYcVnnfz1abYD";
			$url = "https://www.linkedin.com/uas/oauth2/accessToken";
			$ch = curl_init();
			if (defined("CURL_CA_BUNDLE_PATH")) curl_setopt($ch, CURLOPT_CAINFO, CURL_CA_BUNDLE_PATH);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			if (isset($postdata)) {
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			}
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($ch);
			curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			$in = json_decode($response);
			if (isset($in->access_token)) {
				$obj=$this->getProfile($in->access_token);
				$this->session->set_userdata("inprofile",$obj);
				$who=$this->session->userdata("loggedinas");
				if($who=="student"){
					redirect("admin/student_register");
				}
				if($who=="teacher"){
					redirect("teacher/step1");
				}
				if($who=="professional"){
					redirect("professional/step1");
				}
			}
			//header("location:https://api.linkedin.com/v1/people/~:(id,first-name,skills,educations,languages,twitter-accounts)?format=json");
		}
	}
	/*
	 * After Authentication reterive the profile
	 */
	public function getProfile($token){
			//$postdata="grant_type=authorization_code&code=".$_GET['code']."&redirect_uri=http://localhost/research/raj/import/linkedin&client_id=77x90we8n9xx2x&client_secret=T4lsYcVnnfz1abYD";
			$url="https://api.linkedin.com/v1/people/~:(firstName,lastName,maidenName,headline,summary,pictureUrl,specialties,public-profile-url,positions,skills,location,date-of-birth,phone-numbers)?format=json&oauth2_access_token=$token";
			$ch = curl_init();
			if (defined("CURL_CA_BUNDLE_PATH")) curl_setopt($ch, CURLOPT_CAINFO, CURL_CA_BUNDLE_PATH);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			 $r=json_decode(curl_exec($ch));
			curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close ($ch);
			return $r;
		/*	$k=0;
			for( $i="A"; $i!="ZZZ"; $i++) {
				$k++;
				echo $k . '. ' . $i . '<hr/>';
				$query = strtolower($i);
				$json_data = json_decode(file_get_contents('https://www.linkedin.com/ta/skill?query=' . $query));
				print_r($json_data);
				}
		*/

			}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */