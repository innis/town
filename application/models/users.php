<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class users extends CI_Model {

    function __construct()
    {

        parent::__construct();
        $this->load->database();
        
    }

    function chk_id_pw($param)
    {

        $this->db->select('id');
        $query = $this->db->get_where('user', array('id'=>trim($param['id']), 'password'=>trim($param['password'])),1);

        if($query->num_rows() == 1) {
            return true;
        }
        return false;
        
    }

    function update_lastlogin($userid)
    {

        $now = date('Y-m-d H:i:s');

        $this->db->where('id', $userid);
        $this->db->update('user', array('lastlogin' => $now));
    }

}

?>