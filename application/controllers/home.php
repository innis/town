<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$method = $this->uri->segment(2);
		if(is_string($method) && !preg_match("/^(index|login)/", $method))
		{
			if(!$this->chk_logined())
			{
				redirect('home/index');
			}
			
		}
		
	}

	private function chk_logined()
	{
		$userinfo = $this->session->userdata('userinfo');
		return (is_array($userinfo) && array_key_exists('userid', $userinfo));
	}

	public function index()
	{
		$this->load->helper('form');
		//$this->output->set_profiler_sections(array('config'  => TRUE, 'queries' => TRUE));
		//$this->output->enable_profiler(TRUE);
		//print_r($this->users->get_all());

		$data = array();
		$data['flash_msg'] = $this->session->flashdata('flash_msg');

		$this->load->view('welcome_message', $data);
		
	}

	public function login()
	{

		$data = $this->input->post();

		$msg = "failed to login";

		if($data && array_key_exists('id', $data) && array_key_exists('password', $data))
		{
			$this->load->model('users');

			if($this->users->chk_id_pw($data))
			{
				$this->session->set_userdata('userinfo', array('userid'=>$data['id']));
				$this->users->update_lastlogin($data['id']);
				redirect('home/machines');
			} else 
				$msg = "invailed id or password";
		}

		$this->session->set_flashdata('flash_msg', $msg);
		redirect('home/index');

	}

	public function logout()
	{

		$this->session->unset_userdata('userinfo');
		redirect('home/index');

	}

	public function machines()
	{

		$this->load->helper('form');
		$this->load->model('machines');

		$machines = $this->machines->get_all();

		$data = array('machines' => $machines);

		$data['flash_msg'] = $this->session->flashdata('flash_msg');


		$this->load->view('machines', $data);

	}

	public function new_machine()
	{
		if($this->input->post())
		{
			$this->load->model('machines');
			$data = $this->input->post();

			$this->machines->create($data);
		}

		redirect('home/machines');
	}

	public function wake_machine($mid)
	{
		$msg = "failed wake on lan";
		if(is_numeric($mid))
		{
			$this->load->model('machines');
			$m = $this->machines->get($mid);


			$hwaddr = $m->hwaddr;
			$socket_number = "32446";
			$brd_arrd = "255.255.255.255";
			//$brd_arrd = "10.0.1.255";

			$msg = $this->wol($brd_arrd, $hwaddr, $socket_number);

		}

		$this->session->set_flashdata('flash_msg', $msg);
		redirect('home/machines');

	}

	private function wol($addr, $mac, $socket_number)
	{
		//flush();
		$ret_msg = "";
		$addr_byte = explode(':', $mac);
		$hw_addr = '';

		for ($a=0; $a < 6; $a++)
			$hw_addr .= chr(hexdec($addr_byte[$a]));

		$msg = chr(255).chr(255).chr(255).chr(255).chr(255).chr(255);

		for ($a = 1; $a <= 16; $a++)
			$msg .= $hw_addr;

		$s = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		if ($s == false)
		{
			$ret_msg .= "Error creating socket!\n";
			$ret_msg .= "Error code is '".socket_last_error($s)."' - " . socket_strerror(socket_last_error($s));
		}
		else
		{
			$opt_ret = socket_set_option($s, 1, 6, TRUE);
			if($opt_ret < 0)
				$ret_msg .= "setsockopt() failed, error: " . strerror($opt_ret) . "\n";

			$e = socket_sendto($s, $msg, strlen($msg), 0, $addr, $socket_number);
			socket_close($s);
			$ret_msg .= "Magic Packet sent (".$e.") to ".$addr;
		}

		return $ret_msg;
	}

	public function del_machine($mid)
	{
		$msg = "remove failed";

		if(is_numeric($mid))
		{
			$this->load->model('machines');
			if($this->machines->remove($mid))
			{
				$msg = 'remove successful';
			}
		}

		$this->session->set_flashdata('flash_msg', $msg);
		redirect('home/machines');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */