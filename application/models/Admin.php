<?php
    class Admin extends CI_Model{
        public function __construct()
        {
            $this->load->database();    
        }
        public function login($email, $password)
        {
            // extracting the staff with given email and password
            $staff = $this->db->get_where('staff', array(
                'email' => $email,
                'password' => $password
            ));
            //checking the number of rows of the checked credentials and returning the id to the controller
            if ($staff->num_rows() == 1) {
                return $staff->row_array(0);
            }
            else {
                return false;
            }
        }
    }