<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class machines extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {
        $this->db->from('machine');
        return $this->db->get()->result_array();
    }

    function get($mid)
    {
        $query = $this->db->get_where('machine', array('id' => $mid));

        if($query->num_rows() == 1) {
            return $query->row();
        }
        return false;
    }

    function create($param)
    {
        if(is_array($param) && array_key_exists('hwaddr', $param) && array_key_exists('memo', $param))
        {
            foreach ($param as $key => $value) {
                $param[$key] = trim($value);
            }

            $param['id'] = null;

            if($this->db->insert('machine', $param))
                return true;
        }
        return false;
        
    }

    function remove($mid)
    {
        if($this->db->delete('machine', array('id' => $mid)))
            return true;
        else
            return false;
    }

}

?>