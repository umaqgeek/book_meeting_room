<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('phpass-0.3/PasswordHash.php');

define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', false);

/**
 * SimpleLoginSecure Class
 *
 * Makes authentication simple and secure.
 *
 * Simplelogin expects the following database setup. If you are not using 
 * this setup you may need to do some tweaking.
 *   
 * 
 *   CREATE TABLE `users` (
 *     `user_id` int(10) unsigned NOT NULL auto_increment,
 *     `user_email` varchar(255) NOT NULL default '',
 *     `user_pass` varchar(60) NOT NULL default '',
 *     `user_date` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Creation date',
 *     `user_modified` datetime NOT NULL default '0000-00-00 00:00:00',
 *     `user_last_login` datetime NULL default NULL,
 *     PRIMARY KEY  (`user_id`),
 *     UNIQUE KEY `user_email` (`user_email`),
 *   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 * 
 * @package   SimpleLoginSecure
 * @version   2.1.1
 * @author    Stéphane Bourzeix, Pixelmio <stephane[at]bourzeix.com>
 * @copyright Copyright (c) 2012-2013, Stéphane Bourzeix
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 * @link      https://github.com/DaBourz/SimpleLoginSecure
 */
class SimpleLoginSecure
{
	var $CI;
	var $user_table = 'users';
	
	public function encrypt_password($pwd)
	{
		//Hash user_pass using phpass
		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		return $hasher->HashPassword($pwd);
	}

	/**
	 * Create a user account
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function create($user_email = '', $user_pass = '', $auto_login = false) 
	{
		$this->CI =& get_instance();
		
		//Make sure account info was sent
		if($user_email == '' OR $user_pass == '') {
			return false;
		}
		
		//Check against user table
		$this->CI->db->where('user_email', $user_email); 
		$query = $this->CI->db->get_where($this->user_table);
		
		if ($query->num_rows() > 0) //user_email already exists
			return false;

		//Hash user_pass using phpass
		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		$user_pass_hashed = $hasher->HashPassword($user_pass);

		//Insert account into the database
		$data = array(
					'user_email' => $user_email,
					'user_pass' => $user_pass_hashed,
					'magicnum'=>md5($user_email),
					'user_date' => date('c'),
					'user_modified' => date('c'),
				);

		$this->CI->db->set($data); 

		if(!$this->CI->db->insert($this->user_table)){ //There was a problem! 
			return false;
		}
		else{
			//send activation email
			$this->send_email_activation($user_email);
		}

				
		if($auto_login)
			$this->login($user_email, $user_pass);
		
		return true;
	}

	function createhashedPassword($password)
	{
		//Hash user_pass using phpass
		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		$user_pass_hashed = $hasher->HashPassword($password);
		return $user_pass_hashed;
	}
	
	public function send_email($to, $subject, $msg)
	{
		$this->CI =& get_instance();
		/*
		$config = Array(
		    'protocol' => 'smtp',
		    'smtp_host' => 'ssl://smtp.googlemail.com',
		    'smtp_port' => 465,
		    'smtp_user' => 'umar@tuffah.info',
		    'smtp_pass' => 'kalimas123',
		    'mailtype'  => 'html', 
		    'charset'   => 'iso-8859-1'
		);
		//*/
		$config = Array(
		    'protocol' => 'smtp',
		    'smtp_host' => 'mail.dinarpal.com',
		    'smtp_port' => 25,
		    'smtp_user' => 'umaq@dinarpal.com',
		    'smtp_pass' => '123',
		    'mailtype'  => 'html', 
		    'charset'   => 'iso-8859-1'
		);

		$this->CI->load->library('email', $config);
		$this->CI->email->set_newline("\r\n");
		$this->CI->email->from('admin@dinarpal.com', 'DinarPal');
        $this->CI->email->to($to); 

        $this->CI->email->subject($subject);
        $this->CI->email->message($msg);

		// Set to, from, message, etc
		
		if(!$this->CI->email->send()) {
			show_error($this->CI->email->print_debugger());
		}
	}
	
	function test_email()
	{
		$this->CI =& get_instance();
		//*
		$config = Array(
		    'protocol' => 'smtp',
		    'smtp_host' => 'webmail.dinarpal.com',
		    'smtp_port' => 25,
		    'smtp_user' => 'umaq@dinarpal.com',
		    'smtp_pass' => '123',
		    'mailtype'  => 'html', 
		    'charset'   => 'iso-8859-1'
		);
		//*/
		/*
		$config = Array(
		    'protocol' => 'smtp',
		    'smtp_host' => 'ssl://smtp.googlemail.com',
		    'smtp_port' => 465,
		    'smtp_user' => 'umar@tuffah.info',
		    'smtp_pass' => 'kalimas123',
		    'mailtype'  => 'html', 
		    'charset'   => 'iso-8859-1'
		);
		//*/
		
		$this->CI->load->library('email');
		$this->CI->email->set_newline("\r\n");
		    $this->CI->email->from('admin@dinarpal.com', 'DinarPal');
        $this->CI->email->to('umaq@dinarpal.com'); 

        $this->CI->email->subject('DinarPal Activation Link - TEST');
		$message = 'Test test '.date('Y-m-d H:i:s');
        $this->CI->email->message($message);

		// Set to, from, message, etc
		
		if(!$this->CI->email->send()) {
			show_error($this->CI->email->print_debugger());
		} else {
			echo "Send ..";
		}
	}

	//send email activation
	function send_email_activation($me_email, $me_username, $me_password)
	{
		$this->CI =& get_instance();
		/*
		$config = Array(
		    'protocol' => 'smtp',
		    'smtp_host' => 'ssl://smtp.googlemail.com',
		    'smtp_port' => 465,
		    'smtp_user' => 'umar@tuffah.info',
		    'smtp_pass' => 'kalimas123',
		    'mailtype'  => 'html', 
		    'charset'   => 'iso-8859-1'
		);
		//*/
		$config = Array(
		    'protocol' => 'smtp',
		    'smtp_host' => 'mail.dinarpal.com',
		    'smtp_port' => 25,
		    'smtp_user' => 'umaq@dinarpal.com',
		    'smtp_pass' => '123',
		    'mailtype'  => 'html', 
		    'charset'   => 'iso-8859-1'
		);

		$magicnum=md5($me_email);

		$this->CI->load->library('email', $config);
		$this->CI->email->set_newline("\r\n");
		    $this->CI->email->from('admin@dinarpal.com', 'DinarPal');
        $this->CI->email->to($me_email); 

        $this->CI->email->subject('DinarPal Activation Link');
		$message = 'Thank You for registering with us, click here to activate your account : <br/> '.site_url().'login/activate_account/'.$magicnum.'<br/><br/> Account:-<br/> Username: '.$me_username.'<br/> Password: '.$me_password;
        $this->CI->email->message($message);

		// Set to, from, message, etc
		
		if(!$this->CI->email->send()) {
			show_error($this->CI->email->print_debugger());
			return false;
		} else {
			return true;
		}
	}

	public function activate_account($magicnum=null)
	{

		$this->CI =& get_instance();

		$this->CI->db->where('me_magic_num', $magicnum); 
		$query = $this->CI->db->get_where('members');
		
		if ($query->num_rows() > 0){ //user_email already exists
			//update user status to active
			$data = array(
					'me_activation_status' => 2
				);
 
		$this->CI->db->where('me_magic_num', $magicnum);

		if(!$this->CI->db->update('members', $data)) //There was a problem! 
			return false;						
			else
				return true;
		}
			else{
				return false;
			}
		
	}

	/**
	 * Update a user account
	 *
	 * Only updates the email, just here for you can 
	 * extend / use it in your own class.
	 *
	 * @access	public
	 * @param integer
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function update($user_id = null, $user_email = '', $auto_login = true) 
	{
		$this->CI =& get_instance();

		//Make sure account info was sent
		if($user_id == null OR $user_email == '') {
			return false;
		}
		
		//Check against user table
		$this->CI->db->where('user_id', $user_id);
		$query = $this->CI->db->get_where($this->user_table);
		
		if ($query->num_rows() == 0){ // user don't exists
			return false;
		}
		
		//Update account into the database
		$data = array(
					'user_email' => $user_email,
					'user_modified' => date('c'),
				);
 
		$this->CI->db->where('user_id', $user_id);

		if(!$this->CI->db->update($this->user_table, $data)) //There was a problem! 
			return false;						
				
		if($auto_login){
			$user_data['user_email'] = $user_email;
			$user_data['user'] = $user_data['user_email']; // for compatibility with Simplelogin
			
			$this->CI->session->set_userdata($user_data);
			}
		return true;
	}
	
	function isValidPassword($me_username = '', $me_password = '')
	{
		$this->CI =& get_instance();

		if($me_username == '' OR $me_password == '')
			return false;
		
		
		//Check against user table
		$this->CI->db->where('me_username', $me_username); 
		$this->CI->db->where('me_activation_status', 2); //make sure the email is activated
		$query = $this->CI->db->get_where($this->user_table);
		
		if ($query->num_rows() > 0) 
		{			
			$user_data = $query->row_array(); 

			$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);

			if(!$hasher->CheckPassword($me_password, $user_data['me_password']))
			{
				//echo "h1|".$me_password."|".$user_data['me_password']."|"; die();
				return false;
			}
			
			//echo "h2"; die();
			return true;
		} 
		else 
		{
			//echo "h3"; die();
			return false;
		}	
	}

	/**
	 * Login and sets session variables
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function login($u_username = '', $u_password = '') 
	{
		$this->CI =& get_instance();

		if($u_username == '' OR $u_password == '')
			return false;


		//Check if already logged in
		if($this->CI->session->userdata('u_username') == $u_username)
			return true;
		
		
		//Check against user table
		$this->CI->db->where('u_username', $u_username); 
		$this->CI->db->where('u_password', $u_password); 
//		$this->CI->db->where('me_activation_status', 2); //make sure the email is activated
		$query = $this->CI->db->get_where($this->user_table);
		
		if ($query->num_rows() > 0) 
		{			
			$user_data = $query->row_array(); 

//			$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
//
//			if(!$hasher->CheckPassword($me_password, $user_data['me_password']))
//			{
//				//echo "hoho1"; die();
//				return false;
//			}
				
			//echo $me_password.'|'.$user_data['me_password']; die();

			//Destroy old session
			$this->CI->session->sess_destroy();
			
			//Create a fresh, brand new session
			$this->CI->session->sess_create();

//			$this->CI->db->simple_query('UPDATE ' . $this->user_table  . ' SET me_last_login = "' . date('c') . '" WHERE me_id = ' . $user_data['me_id']);

			//Set session data
			unset($user_data['u_password']);
			//$user_data['member'] = $user_data['me_username']; // for compatibility with Simplelogin
			$user_data['logged_in'] = true;
			$this->CI->session->set_userdata($user_data);
			
			return true;
		} 
		else 
		{
			return false;
		}	

	}

	/**
	 * Logout user
	 *
	 * @access	public
	 * @return	void
	 */
	function logout() {
		$this->CI =& get_instance();		

		$this->CI->session->sess_destroy();

		//clear store_transaction session
		/*$store_transaction=$this->CI->session->userdata('store_transaction');
		$this->CI->session->unset_userdata($store_transaction);
	*/
	}

	/**
	 * Delete user
	 *
	 * @access	public
	 * @param integer
	 * @return	bool
	 */
	function delete($user_id) 
	{
		$this->CI =& get_instance();
		
		if(!is_numeric($user_id))
			return false;			

		return $this->CI->db->delete($this->user_table, array('user_id' => $user_id));
	}
	
	
	/**
	* Edit a user password
	* @author    Stéphane Bourzeix, Pixelmio <stephane[at]bourzeix.com>
	* @author    Diego Castro <castroc.diego[at]gmail.com>
	*
	* @access  public
	* @param  string
	* @param  string
	* @param  string
	* @return  bool
	*/
	function edit_password($user_email = '', $old_pass = '', $new_pass = '')
	{
		$this->CI =& get_instance();
		// Check if the password is the same as the old one
		$this->CI->db->select('user_pass');
		$query = $this->CI->db->get_where($this->user_table, array('user_email' => $user_email));
		$user_data = $query->row_array();

		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);	
		if (!$hasher->CheckPassword($old_pass, $user_data['user_pass'])){ //old_pass is the same
			return FALSE;
		}
		
		// Hash new_pass using phpass
		$user_pass_hashed = $hasher->HashPassword($new_pass);
		// Insert new password into the database
		$data = array(
			'user_pass' => $user_pass_hashed,
			'user_modified' => date('c')
		);
		
		$this->CI->db->set($data);
		$this->CI->db->where('user_email', $user_email);
		if(!$this->CI->db->update($this->user_table, $data)){ // There was a problem!
			return FALSE;
		} else {
			return TRUE;
		}
	}

	//tambah ganti password!

	public function is_logged_in()
	{
		$this->CI =& get_instance();
		if($this->CI->session->userdata('logged_in')!=true)
			return false;
		else
			return true;
	}
	
}
?>
