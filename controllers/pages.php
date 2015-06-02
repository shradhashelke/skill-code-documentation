<?php
/*
 * Not used but dont delete
 */
class Pages extends CI_controller
    {
		public function view($page='home')
		 {
			

			$data['title'] = ucfirst($page); // Capitalize the first letter

			$this->load->view('templates/header', $data);
			$this->load->view('pages/'.$page, $data);
			$this->load->view('templates/footer', $data);	 
			 
		 }
	
	}

?>