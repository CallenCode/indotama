<?php
defined("BASEPATH") or exit("No Direct Script");
class Satuan extends CI_Controller{
    public function __construct(){
        parent::__construct();
    }
    public function columns(){
        $response["status"] = "SUCCESS";
        $this->load->model("m_satuan");
        $columns = $this->m_satuan->columns();
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
        
        $this->load->model("m_satuan");
        $result = $this->m_satuan->content($page,$order_by,$order_direction,$search_key,$data_per_page);

        if($result["data"]->num_rows() > 0){
            $result["data"] = $result["data"]->result_array();
            for($a = 0; $a<count($result["data"]); $a++){
                $response["content"][$a]["id"] = $result["data"][$a]["id_pk_satuan"];
                $response["content"][$a]["nama"] = $result["data"][$a]["satuan_nama"];
                $response["content"][$a]["rumus"] = $result["data"][$a]["satuan_rumus"];
                $response["content"][$a]["status"] = $result["data"][$a]["satuan_status"];
                $response["content"][$a]["last_modified"] = $result["data"][$a]["satuan_last_modified"];
            }
        }
        else{
            $response["status"] = "ERROR";
        }
        $response["page"] = $this->pagination->generate_pagination_rules($page,$result["total_data"],$data_per_page);
        $response["key"] = array(
            "nama",
            "rumus",
            "status",
            "last_modified"
        );
        echo json_encode($response);
    }
    public function list(){
        $response["status"] = "SUCCESS";
        $this->load->model("m_satuan");
        $result = $this->m_satuan->list();
        if($result->num_rows()){
            $result = $result->result_array();
            for($a = 0; $a<count($result); $a++){
                $response["content"][$a]["id"] = $result[$a]["id_pk_satuan"];
                $response["content"][$a]["nama"] = $result[$a]["satuan_nama"];
                $response["content"][$a]["rumus"] = $result[$a]["satuan_rumus"];
                $response["content"][$a]["status"] = $result[$a]["satuan_status"];
                $response["content"][$a]["last_modified"] = $result[$a]["satuan_last_modified"];
            }
        }
        else{
            $response["status"] = "ERROR";
            $response["msg"] = "No data is recorded in database";
        }
        echo json_encode($response);
    }
    public function register(){
        $response["status"] = "SUCCESS";
        $this->form_validation->set_rules("nama","nama","required");
        $this->form_validation->set_rules("rumus","rumus","required");
        if($this->form_validation->run()){
            $satuan_nama = $this->input->post("nama");
            $satuan_rumus = $this->input->post("rumus");
            $satuan_status = "AKTIF";
            $this->load->model("m_satuan");
            if($this->m_satuan->set_insert($satuan_nama,$satuan_status,$satuan_rumus)){
                if($this->m_satuan->insert()){
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
        $this->form_validation->set_rules("nama","nama","required");
        $this->form_validation->set_rules("rumus","rumus","required");
        if($this->form_validation->run()){
            $id_pk_satuan = $this->input->post("id");
            $satuan_nama = $this->input->post("nama");
            $satuan_rumus = $this->input->post("rumus");
            $this->load->model("m_satuan");
            if($this->m_satuan->set_update($id_pk_satuan,$satuan_nama,$satuan_rumus)){
                if($this->m_satuan->update()){
                    $response["msg"] = "Data is recorded to database";
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
        $id = $this->input->get("id");
        if($id != "" && is_numeric($id)){
            $this->load->model("m_satuan");
            if($this->m_satuan->set_delete($id)){
                if($this->m_satuan->delete()){
                    $response["msg"] = "Data is deleted from database";
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
            $response["msg"] = "Invalid ID Supplier";
        }
        echo json_encode($response);
    }
}