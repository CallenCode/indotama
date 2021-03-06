<?php
defined("BASEPATH") or exit("No direct script");
class Cabang extends CI_Controller{
    public function __construct(){
        parent::__construct();
    }
    public function columns(){
        $response["status"] = "SUCCESS";
        $this->load->model("m_cabang");
        $columns = $this->m_cabang->columns();
        if(count($columns) > 0){
            for($a = 0; $a<count($columns); $a++){
                $response["content"][$a]["col_name"] = $columns[$a]["col_disp"];
            }
        }
        else{
            $response["status"] = "ERROR";
        }
        echo json_encode($response);
    }
    public function content(){
        $response["status"] = "SUCCESS";
        $response["content"] = array();

        $order_by = $this->input->get("orderBy");
        $order_direction = $this->input->get("orderDirection");
        $page = $this->input->get("page");
        $search_key = $this->input->get("searchKey");
        $data_per_page = 20;
        $id_toko = $this->input->get("id_toko");
        $this->load->model("m_cabang");
        $this->m_cabang->set_id_fk_toko($id_toko);
        $result = $this->m_cabang->content($page,$order_by,$order_direction,$search_key,$data_per_page);

        if($result["data"]->num_rows() > 0){
            $result["data"] = $result["data"]->result_array();
            for($a = 0; $a<count($result["data"]); $a++){
                
                $response["content"][$a]["id"] = $result["data"][$a]["id_pk_cabang"];
                $response["content"][$a]["daerah"] = $result["data"][$a]["cabang_daerah"];
                $response["content"][$a]["notelp"] = $result["data"][$a]["cabang_notelp"];
                $response["content"][$a]["alamat"] = $result["data"][$a]["cabang_alamat"];
                $response["content"][$a]["status"] = $result["data"][$a]["cabang_status"];
                $response["content"][$a]["create_date"] = $result["data"][$a]["cabang_create_date"];
                $response["content"][$a]["last_modified"] = $result["data"][$a]["cabang_last_modified"];
            }
        }
        else{
            $response["status"] = "ERROR";
        }
        $response["page"] = $this->pagination->generate_pagination_rules($page,$result["total_data"],$data_per_page);
        $response["key"] = array(
            "daerah",
            "notelp",
            "alamat",
            "status",
            "last_modified"
        );
        echo json_encode($response);
    }
    public function register(){
        $response["status"] = "SUCCESS";
        $this->form_validation->set_rules("id_toko","id_toko","required");
        $this->form_validation->set_rules("daerah","daerah","required");
        $this->form_validation->set_rules("alamat","alamat","required");
        $this->form_validation->set_rules("notelp","notelp","required");
        if($this->form_validation->run()){
            $this->load->model("m_cabang");
            $id_fk_toko = $this->input->post("id_toko");
            $cabang_daerah = $this->input->post("daerah");
            $cabang_status = "AKTIF";
            $cabang_alamat = $this->input->post("alamat");
            $cabang_notelp = $this->input->post("notelp");
            
            if($this->m_cabang->set_insert($cabang_daerah,$cabang_notelp,$cabang_status,$cabang_alamat,$id_fk_toko)){
                if($this->m_cabang->insert()){
                    $response["msg"] = "Data is recorded to database";
                }
                else{
                    $response["status"] = "ERROR";
                    $response["msg"] = "Insert function error";
                }
            }
            else{
                $response["status"] = "ERROR";
                $response["msg"] = "Setter function error";
            }
        }
        else{
            $response["status"] = "ERROR";
            $response["msg"] = validation_errors();
        }
        echo json_encode($response);
    }
    public function update(){
        $response["status"] = "SUCCESS";
        $this->form_validation->set_rules("id","id","required");
        $this->form_validation->set_rules("daerah","daerah","required");
        $this->form_validation->set_rules("alamat","alamat","required");
        $this->form_validation->set_rules("notelp","notelp","required");
        if($this->form_validation->run()){
            $this->load->model("m_cabang");
            $id_pk_cabang = $this->input->post("id");
            $cabang_daerah = $this->input->post("daerah");
            $cabang_alamat = $this->input->post("alamat");
            $cabang_notelp = $this->input->post("notelp");
            
            if($this->m_cabang->set_update($id_pk_cabang,$cabang_daerah,$cabang_notelp,$cabang_alamat)){
                if($this->m_cabang->update()){
                    $response["msg"] = "Data is updated to database";
                }
                else{
                    $response["status"] = "ERROR";
                    $response["msg"] = "Update function error";
                }
            }
            else{
                $response["status"] = "ERROR";
                $response["msg"] = "Setter function error";
            }
        }
        else{
            $response["status"] = "ERROR";
            $response["msg"] = validation_errors();
        }
        echo json_encode($response);
    }
    public function delete(){
        $response["status"] = "SUCCESS";
        $id_toko = $this->input->get("id");
        if($id_toko != "" && is_numeric($id_toko)){
            $id_pk_toko = $id_toko;
            $this->load->model("m_cabang");
            if($this->m_cabang->set_delete($id_pk_toko)){
                if($this->m_cabang->delete()){
                    $response["msg"] = "Data is removed to database";
                }
                else{
                    $response["status"] = "ERROR";
                    $response["msg"] = "Delete function error";
                }
            }
            else{
                $response["status"] = "ERROR";
                $response["msg"] = "Setter function error";
            }
        }
        else{
            $response["status"] = "ERROR";
            $response["msg"] = "Invalid ID";
        }
        echo json_encode($response);
    }
    public function list_cabang_admin(){
        $response["status"] = "SUCCESS";
        $response["content"] = array();

        $order_by = $this->input->get("orderBy");
        $order_direction = $this->input->get("orderDirection");
        $page = $this->input->get("page");
        $search_key = $this->input->get("searchKey");
        $data_per_page = 20;
        
        $this->load->model("m_cabang_admin");
        $this->m_cabang_admin->set_id_fk_user($this->session->id_user);
        $result = $this->m_cabang_admin->list_cabang_admin($page,$order_by,$order_direction,$search_key,$data_per_page);

        if($result["data"]->num_rows() > 0){
            $result["data"] = $result["data"]->result_array();
            for($a = 0; $a<count($result["data"]); $a++){
                $response["content"][$a]["id"] = $result["data"][$a]["id_pk_cabang"];
                $response["content"][$a]["toko"] = $result["data"][$a]["toko_nama"];
                $response["content"][$a]["daerah"] = $result["data"][$a]["cabang_daerah"];
                $response["content"][$a]["notelp"] = $result["data"][$a]["cabang_notelp"];
                $response["content"][$a]["alamat"] = $result["data"][$a]["cabang_alamat"];
                $response["content"][$a]["status"] = $result["data"][$a]["cabang_status"];
                $response["content"][$a]["create_date"] = $result["data"][$a]["cabang_create_date"];
                $response["content"][$a]["last_modified"] = $result["data"][$a]["cabang_last_modified"];
            }
        }
        else{
            $response["status"] = "ERROR";
        }
        $response["page"] = $this->pagination->generate_pagination_rules($page,$result["total_data"],$data_per_page);
        $response["key"] = array(
            "toko",
            "daerah",
            "notelp",
            "alamat",
            "status",
            "last_modified"
        );
        echo json_encode($response);
    }
    public function columns_cabang_admin(){
        $response["status"] = "SUCCESS";
        $this->load->model("m_cabang_admin");
        $this->m_cabang_admin->set_cabang_admin_columns();
        $columns = $this->m_cabang_admin->columns();
        if(count($columns) > 0){
            for($a = 0; $a<count($columns); $a++){
                $response["content"][$a]["col_name"] = $columns[$a]["col_disp"];
            }
        }
        else{
            $response["status"] = "ERROR";
        }
        echo json_encode($response);
    }
}