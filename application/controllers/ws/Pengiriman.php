<?php
defined("BASEPATH") or exit("No Direct Script");
class pengiriman extends CI_Controller{
    public function __construct(){
        parent::__construct();
    }
    public function columns(){
        $response["status"] = "SUCCESS";
        $this->load->model("m_pengiriman");
        $columns = $this->m_pengiriman->columns();
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
        $type = strtoupper($this->input->get("type")); //CABANG / WAREHOUSE
        
        $this->load->model("m_pengiriman");
        $flag = true;
        if($type == "WAREHOUSE" && $this->session->id_warehouse){
            $this->m_pengiriman->set_id_fk_warehouse($this->session->id_warehouse);
        }
        else if($type == "CABANG" && $this->session->id_cabang){
            $this->m_pengiriman->set_id_fk_cabang($this->session->id_cabang);
        }
        else{
            $flag = false;
            $response["status"] = "ERROR";
            $response["msg"] = "Type not registered";
        }

        if($flag){
            $this->m_pengiriman->set_pengiriman_tempat($type);
            $result = $this->m_pengiriman->content($page,$order_by,$order_direction,$search_key,$data_per_page);
            if($result["data"]->num_rows() > 0){
                $result["data"] = $result["data"]->result_array();
                for($a = 0; $a<count($result["data"]); $a++){
                    $response["content"][$a]["id"] = $result["data"][$a]["id_pk_pengiriman"];
                    $response["content"][$a]["tgl"] = $result["data"][$a]["pengiriman_tgl"];
                    $response["content"][$a]["status"] = $result["data"][$a]["pengiriman_status"];
                    $response["content"][$a]["id_penjualan"] = $result["data"][$a]["id_fk_penjualan"];
                    $response["content"][$a]["tempat"] = $result["data"][$a]["pengiriman_tempat"];
                    $response["content"][$a]["last_modified"] = $result["data"][$a]["pengiriman_last_modified"];
                    $response["content"][$a]["nomor_penj"] = $result["data"][$a]["penj_nomor"];
                    $response["content"][$a]["perusahaan_cust"] = strtoupper($result["data"][$a]["cust_perusahaan"]);
                    $response["content"][$a]["name_cust"] = strtoupper($result["data"][$a]["cust_name"]);
                    $response["content"][$a]["suff_cust"] = strtoupper($result["data"][$a]["cust_suff"]);
                    $response["content"][$a]["hp_cust"] = $result["data"][$a]["cust_hp"];
                    $response["content"][$a]["email_cust"] = $result["data"][$a]["cust_email"];
                    $response["content"][$a]["nomor"] = $result["data"][$a]["penj_nomor"];
                    if(strtoupper($response["content"][$a]["tempat"]) == "WAREHOUSE"){
                        $response["content"][$a]["id_tempat_pengiriman"] = $result["data"][$a]["id_fk_warehouse"];
                    }
                    else if(strtoupper($response["content"][$a]["tempat"]) == "CABANG"){
                        $response["content"][$a]["id_tempat_pengiriman"] = $result["data"][$a]["id_fk_cabang"];

                    }
                }
            }
            else{
                $response["status"] = "ERROR";
            }
            $response["page"] = $this->pagination->generate_pagination_rules($page,$result["total_data"],$data_per_page);
            $response["key"] = array(
                "tgl",
                "nomor_penj",
                "status",
                "last_modified",
            );
        }
        echo json_encode($response);
    }
    public function list(){
        $response["status"] = "SUCCESS";
        $this->load->model("m_pengiriman");
        $result = $this->m_pengiriman->list();
        if($result->num_rows()){
            $result = $result->result_array();
            for($a = 0; $a<count($result); $a++){
                $response["content"][$a]["id"] = $result[$a]["id_pk_brg_jenis"];
                $response["content"][$a]["nama"] = $result[$a]["brg_jenis_nama"];
                $response["content"][$a]["status"] = $result[$a]["brg_jenis_status"];
                $response["content"][$a]["last_modified"] = $result[$a]["brg_jenis_last_modified"];
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
        $this->form_validation->set_rules("id_penjualan","Nomor","required");
        $this->form_validation->set_rules("tgl_pengiriman","Tanggal Penerimaan","required");
        if($this->form_validation->run()){
            $pengiriman_tgl = $this->input->post("tgl_pengiriman");
            $pengiriman_status = "AKTIF";
            $id_fk_penjualan = $this->input->post("id_penjualan");
            $pengiriman_tempat = $this->input->post("type");
            $id_tempat_pengiriman = $this->input->post("id_tempat_pengiriman"); //id_warehouse or id_cabang

            $this->load->model("m_pengiriman");
            if($this->m_pengiriman->set_insert($pengiriman_tgl,$pengiriman_status,$id_fk_penjualan,$pengiriman_tempat,$id_tempat_pengiriman)){
                $id_pengiriman = $this->m_pengiriman->insert();
                if($id_pengiriman){
                    $response["msg"] = "Data is recorded to database";

                    $check = $this->input->post("check");
                    if($check != ""){
                        $counter = 0;
                        foreach($check as $a){
                            $this->form_validation->reset_validation();
                            $this->form_validation->set_rules("id_brg".$a,"id_brg","required");
                            $this->form_validation->set_rules("notes".$a,"notes","required");
                            $this->form_validation->set_rules("qty_kirim".$a,"qty_kirim","required");
                            $this->form_validation->set_rules("id_satuan".$a,"id_satuan","required");
                            if($this->form_validation->run()){
                                $id_fk_brg_penjualan = $this->input->post("id_brg".$a);
                                $brg_pengiriman_note = $this->input->post("notes".$a);
                                $brg_pengiriman_qty = $this->input->post("qty_kirim".$a);
                                $id_fk_pengiriman = $id_pengiriman;
                                $id_fk_satuan = $this->input->post("id_satuan".$a);

                                $this->load->model("m_brg_pengiriman");
                                if($this->m_brg_pengiriman->set_insert($brg_pengiriman_qty,$brg_pengiriman_note,$id_fk_pengiriman,$id_fk_brg_penjualan,$id_fk_satuan)){
                                    if($this->m_brg_pengiriman->insert()){
                                        $response["statusitm"][$counter] = "SUCCESS";
                                        $response["msgitm"][$counter] = "Item is recorded to database";
                                    }
                                    else{
                                        
                                        $response["statusitm"][$counter] = "ERROR";
                                        $response["msgitm"][$counter] = "Insert Item function error";
                                    }
                                }
                                else{
                                    $response["statusitm"][$counter] = "ERROR";
                                    $response["msgitm"][$counter] = "Setter Item function error";
                                }
                            }
                        }
                    }
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
        $this->form_validation->set_rules("tgl_pengiriman","tgl_pengiriman","required");
        if($this->form_validation->run()){
            $id_pk_pengiriman = $this->input->post("id");
            $pengiriman_tgl = $this->input->post("tgl_pengiriman");
            $this->load->model("m_pengiriman");
            if($this->m_pengiriman->set_update($id_pk_pengiriman,$pengiriman_tgl)){
                if($this->m_pengiriman->update()){
                    $response["msg"] = "Data is updated to database";
                    $check = $this->input->post("check");
                    if($check != ""){
                        $counter = 0;
                        foreach($check as $a){
                            $this->load->model("m_brg_pengiriman");
                            $id_pk_brg_pengiriman = $this->input->post("id_brg_kirim".$a);
                            $brg_pengiriman_qty = $this->input->post("qty_kirim".$a);
                            $brg_pengiriman_note = $this->input->post("notes".$a);
                            $id_fk_satuan = $this->input->post("id_satuan".$a);
                            

                            if($this->m_brg_pengiriman->set_update($id_pk_brg_pengiriman,$brg_pengiriman_qty,$brg_pengiriman_note,$id_fk_satuan)){
                                if($this->m_brg_pengiriman->update()){
                                    $response["statusitm"][$counter] = "SUCCESS";
                                    $response["msgitm"][$counter] = "Item is updated to database";
                                }
                                else{  
                                    $response["statusitm"][$counter] = "ERROR";
                                    $response["msgitm"][$counter] = "Update Item function error";
                                }
                            }
                            else{
                                $response["statusitm"][$counter] = "ERROR";
                                $response["msgitm"][$counter] = "Setter Item function error";
                            }
                        }
                    }
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
            $this->load->model("m_pengiriman");
            if($this->m_pengiriman->set_delete($id)){
                if($this->m_pengiriman->delete()){
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
    public function brg_pengiriman(){
        $response["status"] = "SUCCESS";
        $id_pengiriman = $this->input->get("id");
        $this->load->model("m_brg_pengiriman");
        $this->m_brg_pengiriman->set_id_fk_pengiriman($id_pengiriman);
        $result = $this->m_brg_pengiriman->list();
        if($result->num_rows() > 0){
            $result = $result->result_array();
            for($a = 0; $a<count($result); $a++){
                $response["content"][$a]["id"] = $result[$a]["id_pk_brg_pengiriman"];
                $response["content"][$a]["qty"] = $result[$a]["brg_pengiriman_qty"];
                $response["content"][$a]["note"] = $result[$a]["brg_pengiriman_note"];
                $response["content"][$a]["id_pengiriman"] = $result[$a]["id_fk_pengiriman"];
                $response["content"][$a]["id_brg_penjualan"] = $result[$a]["id_fk_brg_penjualan"];
                $response["content"][$a]["id_satuan"] = $result[$a]["id_fk_satuan"];
                $response["content"][$a]["last_modified"] = $result[$a]["brg_pengiriman_last_modified"];
                $response["content"][$a]["qty_brg_penjualan"] = $result[$a]["brg_penjualan_qty"];
                $response["content"][$a]["satuan_brg_penjualan"] = $result[$a]["brg_penjualan_satuan"];
                $response["content"][$a]["harga_brg_penjualan"] = $result[$a]["brg_penjualan_harga"];
                $response["content"][$a]["note_brg_penjualan"] = $result[$a]["brg_penjualan_note"];
                $response["content"][$a]["status_brg_penjualan"] = $result[$a]["brg_penjualan_status"];
                $response["content"][$a]["satuan"] = $result[$a]["satuan_nama"];
                $response["content"][$a]["nama_brg"] = $result[$a]["brg_nama"];
            }
        }
        else{
            $response["status"] = "ERROR";
            $response["msg"] = "TIDAK ADA BARANG PENERIMAAN";
        }
        echo json_encode($response);
    }
}