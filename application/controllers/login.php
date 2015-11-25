<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller 
{
        var $parent_page = "login";
	function __construct()
	{
            parent::__construct(); 
	}
        
        private function viewpage($page='v_login', $data=array())
        {
            echo $this->load->view('v_header', $data, true);
            echo $this->load->view('v_menu', $data, true);
            echo $this->load->view($this->parent_page.'/'.$page, $data, true);
            echo $this->load->view('v_footer', $data, true);
        }


        public function index()
	{
            $bol_del_before_date = $this->m_conndb->dateBeforeDelete();
            
            try {
                
                $crud = new grocery_CRUD();

                $crud->set_theme('datatables');
                
                $crud->set_table('tempahan');
                
                $crud->set_subject('Booking Room');
                $crud->set_relation('m_id', 'masa', 'm_desc');
                $crud->set_relation('bm_id', 'bilik_meeting', 'bm_desc');
                $crud->display_as('t_datereg', 'Date Reg.')
                        ->display_as('t_date', 'Book Date')
                        ->display_as('t_day', 'Book Day')
                        ->display_as('m_id', 'Time')
                        ->display_as('t_perkara', 'Club / Item')
                        ->display_as('bm_id', 'Meeting Room')
                        ->display_as('t_status', 'Status');
                $crud->columns('t_date', 't_day', 'm_id', 't_perkara', 'bm_id');
                $crud->fields('t_date', 'm_id', 't_perkara', 'bm_id', 't_datereg', 't_day');
                $crud->field_type('t_datereg', 'hidden');
                $crud->field_type('t_day', 'hidden');
                $crud->required_fields('t_date', 'm_id', 't_perkara', 'bm_id');

                $umaq = $this->uri->segment(3);
                if (!($this->uri->segment(3) && $umaq == 'umaqkacak')) { 
                    $crud->unset_delete();
                    $crud->unset_edit();
                }
                
                $crud->callback_before_insert(array($this, 'setAutoTempahan'));

                $output = $crud->render();

                $this->viewpage('v_bookroom', $output);
                
            } catch (Exception $e) {
                show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
            }
	}
        
        function setAutoTempahan($post_array) 
        {
            $post_array['t_datereg'] = date('Y-m-d H:i:s');
            
            $t_date = $post_array['t_date'];
            $sql_date = explode("/", $t_date);
            $new_date = $sql_date[2] . "-" . $sql_date[1] . "-" . $sql_date[0];
            $timestamp = strtotime($new_date);
            $day = date('l', $timestamp);
            $post_array['t_day'] = $day;
            
            return $post_array;
        }

    public function book_room()
        {
            
        }
        
        function checklogin()
        {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            
            $bol_login = $this->simpleloginsecure->login($username, $password);
            
            if ($bol_login) {
                redirect(site_url('users'));
            } else {
                redirect(site_url('login'));
            }
        }
        
        function logout()
        {
            $this->simpleloginsecure->logout();
            redirect(site_url('login'));
        }
}
