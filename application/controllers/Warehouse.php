<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse extends CI_Controller {

	public function index()
	{
		$this->load->view('warehouse/V_warehouse');
	}
	public function admin($id_warehouse){
		
		$this->load->model("m_warehouse");
		$this->m_warehouse->set_id_pk_warehouse($id_warehouse);
		$result = $this->m_warehouse->detail_by_id();
		$data["warehouse"] = $result->result_array();
		
		$this->load->view('warehouse_admin/v_master_warehouse_admin',$data);
	}
	public function daftar_akses_gudang(){
		$this->load->view('warehouse/v_list_warehouse_admin');
	}
	public function activate_warehouse_manajemen($id_warehouse){
		$this->load->model("m_warehouse");
		$this->m_warehouse->set_id_pk_warehouse($id_warehouse);
		$result = $this->m_warehouse->detail_by_id();
		$result = $result->result_array();
		$this->session->id_warehouse = $result[0]["id_pk_warehouse"];
		$this->session->warehouse_nama = $result[0]["warehouse_nama"];

		redirect("warehouse/daftar_akses_gudang");
	}
	public function brg_warehouse($id_warehouse = ""){
		if($id_warehouse == ""){
			$id_warehouse = $this->session->id_warehouse;
		}
		$data["id_warehouse"] = $id_warehouse;

		$this->load->model("m_warehouse");
		$this->m_warehouse->set_id_pk_warehouse($id_warehouse);
		$result = $this->m_warehouse->detail_by_id();
		$data["warehouse"] = $result->result_array();
		
		$this->load->view('brg_warehouse/v_brg_warehouse',$data);
	}
}
