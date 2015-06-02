<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Users extends CI_Controller {

	//Global variable
    public $outputData;		//Holds the output data for each view
	public $loggedInUser;
	   
	/**
	 * Constructor 
	 *
	 * Loads language files and models needed for this controller
	 */
	
	public function index()
    {
        
		parent::__construct(); 
	
		//Load the session liberary
		$this->load->library('session');	
		
		//Load Models - for this function
		$this->load->model('users_model');
		
		$this->load->model('auth_model');
		
		//Load Form Helper
		$this->load->helper('form');
		
		$loggedInUser = $this->users_model->getLoggedInUser();
		$this->outputData['loggedInUser'] 	= $loggedInUser; 
		 
		 
		 if($this->input->post('usersLogin'))
		 {
				
			$conditions =  array('user_name'=>$this->input->post('username'),'user_password' => $this->input->post('pwd'),'ci_users.status' => '1');

			$query	= $this->users_model->getUsers($conditions);
			
			if($query->num_rows() > 0)
			{
					
					$row =  $query->row();

					// update the last activity in the users table
					$updateData = array();
					//date("Y-m-d H:i:s")
               		 $updateData['online'] = "1";
                                          
					// update process for users table
					$this->users_model->updateUser(array('user_id'=>$row->user_id),$updateData);


					 //Set Session For User
					$this->auth_model->setUserSession($row);
					$this->session->userdata('user_id', $row->user_id);
                    $this->session->userdata('user_name', $row->user_name);
						
					//Notification message
					$this->session->set_flashdata('flash_message', $this->users_model->flash_message('success','Logged In Successfull'));
					redirect('chat');	
							 
				
				} 
				else {
				
					 //Notification message
					 $this->session->set_flashdata('flash_message', $this->users_model->flash_message('error','Login failed! Incorrect username or password'));
					 echo "error";
					 exit;
				 	 //redirect('users');
				} //If username exists	

			//}//If End - Check For Validation				
		}

		$this->load->view('users/users',$this->outputData);
		
	} //Controller End 
	// --------------------------------------------------------------------
	
	
		// --------------------------------------------------------------------
	
	/**
	 * Loads logout .
	 *
	 * @access	public
	 * @param	nil
	 * @return	void
	 */	
	function logout()
	{	
		parent::__construct(); 
		
		//Load Models - for this function
		$this->load->model('users_model');
		$this->load->model('auth_model');
		
        // update the last activity in the users table
		$updateData = array();
		$updateData['online'] = "0";
       
	    // update process for users table
		$this->users_model->updateUser(array('user_id'=>$this->session->userdata('user_id')),$updateData);
		
		$this->auth_model->clearUserSession();
		
		
		$this->session->set_flashdata('flash_message', $this->users_model->flash_message('success',$this->lang->line('logout_success')));
		$this->auth_model->clearUserCookie(array('username','password'));
		redirect('welcome');
				
	} //Function logout End
	
	
	
	
} // end class