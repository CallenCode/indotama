<?php
defined("BASEPATH") or exit("no direct script");
date_default_timezone_set("asia/jakarta");
class M_pengiriman extends ci_model{
    private $tbl_name = "mstr_pengiriman";
    private $columns = array();
    private $id_pk_pengiriman;
    private $pengiriman_tgl;
    private $pengiriman_status;
    private $id_fk_penjualan;
    private $pengiriman_tempat;
    private $id_fk_warehouse;
    private $id_fk_cabang;
    private $pengiriman_create_date;
    private $pengiriman_last_modified;
    private $id_create_data;
    private $id_last_modified;

    public function __construct(){
        parent::__construct();
        $this->set_column("pengiriman_tgl","tanggal pengiriman",true);
        $this->set_column("penj_nomor","nomor penjualan",false);
        $this->set_column("pengiriman_status","status",false);
        $this->set_column("pengiriman_last_modified","last modified",false);
        $this->pengiriman_create_date = date("y-m-d h:i:s");
        $this->pengiriman_last_modified = date("y-m-d h:i:s");
        $this->id_create_data = $this->session->id_user;
        $this->id_last_modified = $this->session->id_user;
    }
    private function set_column($col_name,$col_disp,$order_by){
        $array = array(
            "col_name" => $col_name,
            "col_disp" => $col_disp,
            "order_by" => $order_by
        );
        $this->columns[count($this->columns)] = $array; //terpaksa karena array merge gabisa.
    }
    public function columns(){
        return $this->columns;
    }
    public function install(){
        $sql = "
        drop table if exists mstr_pengiriman;
        create table mstr_pengiriman(
            id_pk_pengiriman int primary key auto_increment,
            pengiriman_tgl datetime, 
            pengiriman_status varchar(15), 
            id_fk_penjualan int, 
            pengiriman_tempat varchar(30) comment 'warehouse/cabang', 
            id_fk_warehouse int, 
            id_fk_cabang int, 
            pengiriman_create_date datetime, 
            pengiriman_last_modified datetime, 
            id_create_data int, 
            id_last_modified int 
        );
        drop table if exists mstr_pengiriman_log;
        create table mstr_pengiriman_log(
            id_pk_pengiriman_log int primary key auto_increment,
            executed_function varchar(30),
            id_pk_pengiriman int,
            pengiriman_tgl datetime, 
            pengiriman_status varchar(15), 
            id_fk_penjualan int, 
            pengiriman_tempat varchar(30) comment 'warehouse/cabang', 
            id_fk_warehouse int, 
            id_fk_cabang int, 
            pengiriman_create_date datetime, 
            pengiriman_last_modified datetime, 
            id_create_data int, 
            id_last_modified int, 
            id_log_all int 
        );
        drop trigger if exists trg_after_insert_pengiriman;
        delimiter $$
        create trigger trg_after_insert_pengiriman
        after insert on mstr_pengiriman
        for each row
        begin
            set @id_user = new.id_last_modified;
            set @tgl_action = new.pengiriman_last_modified;
            set @log_text = concat(new.id_last_modified,' ','insert data at' , new.pengiriman_last_modified);
            call insert_log_all(@id_user,@tgl_action,@log_text,@id_log_all);
            
            insert into mstr_pengiriman_log(executed_function,id_pk_pengiriman,pengiriman_tgl,pengiriman_status,id_fk_penjualan,pengiriman_tempat,id_fk_warehouse,id_fk_cabang,pengiriman_create_date,pengiriman_last_modified,id_create_data,id_last_modified,id_log_all) values ('after insert',new.id_pk_pengiriman,new.pengiriman_tgl,new.pengiriman_status,new.id_fk_penjualan,new.pengiriman_tempat,new.id_fk_warehouse,new.id_fk_cabang,new.pengiriman_create_date,new.pengiriman_last_modified,new.id_create_data,new.id_last_modified,@id_log_all);
        end$$
        delimiter ;
        
        drop trigger if exists trg_after_update_pengiriman;
        delimiter $$
        create trigger trg_after_update_pengiriman
        after update on mstr_pengiriman
        for each row
        begin
            set @id_user = new.id_last_modified;
            set @tgl_action = new.pengiriman_last_modified;
            set @log_text = concat(new.id_last_modified,' ','update data at' , new.pengiriman_last_modified);
            call insert_log_all(@id_user,@tgl_action,@log_text,@id_log_all);
            
            insert into mstr_pengiriman_log(executed_function,id_pk_pengiriman,pengiriman_tgl,pengiriman_status,id_fk_penjualan,pengiriman_tempat,id_fk_warehouse,id_fk_cabang,pengiriman_create_date,pengiriman_last_modified,id_create_data,id_last_modified,id_log_all) values ('after update',new.id_pk_pengiriman,new.pengiriman_tgl,new.pengiriman_status,new.id_fk_penjualan,new.pengiriman_tempat,new.id_fk_warehouse,new.id_fk_cabang,new.pengiriman_create_date,new.pengiriman_last_modified,new.id_create_data,new.id_last_modified,@id_log_all);
        end$$
        delimiter ;
        ";
        executequery($sql);
    }
    public function content($page = 1,$order_by = 0, $order_direction = "asc", $search_key = "",$data_per_page = ""){
        $order_by = $this->columns[$order_by]["col_name"];
        $search_query = "";
        if($search_key != ""){
            $search_query .= "and
            (
                id_pk_pengiriman like '%".$search_key."%' or
                penj_nomor like '%".$search_key."%' or 
                pengiriman_tgl like '%".$search_key."%' or
                pengiriman_status like '%".$search_key."%' or
                pengiriman_tempat like '%".$search_key."%' or
                pengiriman_last_modified like '%".$search_key."%'
            )";
        }
        if(strtolower($this->pengiriman_tempat) == "cabang"){
            $query = "
            select id_pk_pengiriman,pengiriman_tgl,pengiriman_status,id_fk_penjualan,pengiriman_tempat,".$this->tbl_name.".id_fk_warehouse,".$this->tbl_name.".id_fk_cabang,pengiriman_last_modified,penj_nomor,cust_perusahaan, cust_name, cust_suff, cust_hp, cust_email,penj_nomor
            from ".$this->tbl_name." 
            inner join mstr_penjualan on mstr_penjualan.id_pk_penjualan = ".$this->tbl_name.".id_fk_penjualan
            inner join mstr_customer on mstr_customer.id_pk_cust = mstr_penjualan.id_fk_customer
            inner join mstr_cabang on mstr_cabang.id_pk_cabang = ".$this->tbl_name.".id_fk_cabang
            inner join mstr_toko on mstr_toko.id_pk_toko = mstr_cabang.id_fk_toko
            where pengiriman_status = ? and cust_status = ? and cabang_status = ? and toko_status = ? and ".$this->tbl_name.".id_fk_cabang = ? ".$search_query."  
            order by ".$order_by." ".$order_direction." 
            limit 20 offset ".($page-1)*$data_per_page;
            $args = array(
                "aktif","aktif","aktif","aktif",$this->id_fk_cabang
            );
            $result["data"] = executequery($query,$args);
            $query = "
            select id_pk_pengiriman
            from ".$this->tbl_name." 
            inner join mstr_penjualan on mstr_penjualan.id_pk_penjualan = ".$this->tbl_name.".id_fk_penjualan
            inner join mstr_customer on mstr_customer.id_pk_cust = mstr_penjualan.id_fk_customer
            inner join mstr_cabang on mstr_cabang.id_pk_cabang = ".$this->tbl_name.".id_fk_cabang
            inner join mstr_toko on mstr_toko.id_pk_toko = mstr_cabang.id_fk_toko
            where pengiriman_status = ? and cust_status = ? and cabang_status = ? and toko_status = ? and ".$this->tbl_name.".id_fk_cabang = ? ".$search_query."  
            order by ".$order_by." ".$order_direction;
            $result["total_data"] = executequery($query,$args)->num_rows();
        }
        else{
            $query = "
            select id_pk_pengiriman,pengiriman_tgl,pengiriman_status,id_fk_penjualan,pengiriman_tempat,".$this->tbl_name.".id_fk_warehouse,".$this->tbl_name.".id_fk_cabang,pengiriman_last_modified,penj_nomor,cust_perusahaan, cust_name, cust_suff, cust_hp, cust_email
            from ".$this->tbl_name." 
            inner join mstr_penjualan on mstr_penjualan.id_pk_penjualan = ".$this->tbl_name.".id_fk_penjualan
            inner join mstr_customer on mstr_customer.id_pk_cust = mstr_penjualan.id_fk_customer
            inner join mstr_warehouse on mstr_warehouse.id_pk_warehouse = ".$this->tbl_name.".id_fk_warehouse
            where pengiriman_status = ? and cust_status = ? and ".$this->tbl_name.".id_fk_warehouse = ? ".$search_query." 
            order by ".$order_by." ".$order_direction." 
            limit 20 offset ".($page-1)*$data_per_page;
            $args = array(
                "aktif","aktif",$this->id_fk_warehouse
            );
            $result["data"] = executequery($query,$args);
            $query = "
            select id_pk_penjualan
            from ".$this->tbl_name." 
            inner join mstr_penjualan on mstr_penjualan.id_pk_penjualan = ".$this->tbl_name.".id_fk_penjualan
            inner join mstr_customer on mstr_customer.id_pk_cust = mstr_penjualan.id_fk_customer
            inner join mstr_warehouse on mstr_warehouse.id_pk_warehouse = ".$this->tbl_name.".id_fk_warehouse
            where pengiriman_status = ? and cust_status = ? and ".$this->tbl_name.".id_fk_warehouse = ? ".$search_query." 
            order by ".$order_by." ".$order_direction;
            $result["total_data"] = executequery($query,$args)->num_rows();
        }
        return $result;
    }
    public function insert(){
        if($this->check_insert()){
            $data = array(
                "pengiriman_tgl" => $this->pengiriman_tgl,
                "pengiriman_status" => $this->pengiriman_status,
                "id_fk_penjualan" => $this->id_fk_penjualan,
                "pengiriman_tempat" => $this->pengiriman_tempat,
                "pengiriman_create_date" => $this->pengiriman_create_date,
                "pengiriman_last_modified" => $this->pengiriman_last_modified,
                "id_create_data" => $this->id_create_data,
                "id_last_modified" => $this->id_last_modified
            );
            if(strtolower($this->pengiriman_tempat) == "warehouse"){
                $data["id_fk_warehouse"] = $this->id_fk_warehouse;
            }
            else if(strtolower($this->pengiriman_tempat) == "cabang"){
                $data["id_fk_cabang"] = $this->id_fk_cabang;
            }
            return insertrow($this->tbl_name,$data);
        }
        return false;
    }
    public function update(){
        if($this->check_update()){
            $where = array(
                "id_pk_pengiriman" => $this->id_pk_pengiriman
            );
            $data = array(
                "pengiriman_tgl" => $this->pengiriman_tgl,
                "pengiriman_last_modified" => $this->pengiriman_last_modified,
                "id_last_modified" => $this->id_last_modified
            );
            updaterow($this->tbl_name,$data,$where);
            return true;
        }
        return false;
    }
    public function delete(){
        if($this->check_delete()){
            $where = array(
                "id_pk_pengiriman" => $this->id_pk_pengiriman
            );
            $data = array(
                "pengiriman_status" => "nonaktif",
                "pengiriman_last_modified" => $this->pengiriman_last_modified,
                "id_last_modified" => $this->id_last_modified
            );
            updaterow($this->tbl_name,$data,$where);
            return true;
        }
        return false;
    }
    public function check_insert(){
        if($this->pengiriman_tgl == ""){
            return false;
        }
        if($this->pengiriman_status == ""){
            return false;
        }
        if($this->id_fk_penjualan == ""){
            return false;
        }
        if(strtolower($this->pengiriman_tempat) == ""){
            return false;
        }
        
        if(strtolower($this->pengiriman_tempat) == "warehouse"){
            if($this->id_fk_warehouse == ""){
                return false;
            }
        }
        else if(strtolower($this->pengiriman_tempat) == "cabang"){
            if($this->id_fk_cabang == ""){
                return false;
            }
        }
        if($this->pengiriman_create_date == ""){
            return false;
        }
        if($this->pengiriman_last_modified == ""){
            return false;
        }
        if($this->id_create_data == ""){
            return false;
        }
        if($this->id_last_modified == ""){
            return false;
        }
        return true;
    }
    public function check_update(){
        if($this->id_pk_pengiriman == ""){
            return false;
        }
        if($this->pengiriman_tgl == ""){
            return false;
        }
        if($this->pengiriman_last_modified == ""){
            return false;
        }
        if($this->id_last_modified == ""){
            return false;
        }
        else return true;
    }
    public function check_delete(){
        if($this->id_pk_pengiriman == ""){
            return false;
        }
        if($this->pengiriman_last_modified == ""){
            return false;
        }
        if($this->id_last_modified == ""){
            return false;
        }
        else return true;
    }
    public function set_insert($pengiriman_tgl,$pengiriman_status,$id_fk_penjualan,$pengiriman_tempat,$id_tempat_pengiriman){
        if(!$this->set_pengiriman_tgl($pengiriman_tgl)){
            return false;
        }
        if(!$this->set_pengiriman_status($pengiriman_status)){
            return false;
        }
        if(!$this->set_id_fk_penjualan($id_fk_penjualan)){
            return false;
        }
        if(!$this->set_pengiriman_tempat($pengiriman_tempat)){
            return false;
        }
        if(strtolower($pengiriman_tempat) == "warehouse"){
            if(!$this->set_id_fk_warehouse($id_tempat_pengiriman)){
                return false;
            }
        }
        else if(strtolower($pengiriman_tempat) == "cabang"){
            if(!$this->set_id_fk_cabang($id_tempat_pengiriman)){
                return false;
            }
        }
        return true;
    }
    public function set_update($id_pk_pengiriman,$pengiriman_tgl){
        if(!$this->set_id_pk_pengiriman($id_pk_pengiriman)){
            return false;
        }
        if(!$this->set_pengiriman_tgl($pengiriman_tgl)){
            return false;
        }
        return true;
    }
    public function set_delete($id_pk_pengiriman){
        if(!$this->set_id_pk_pengiriman($id_pk_pengiriman)){
            return false;
        }

        return true;
    }
    public function set_id_pk_pengiriman($id_pk_pengiriman){
        if($id_pk_pengiriman != ""){
            $this->id_pk_pengiriman = $id_pk_pengiriman;
            return true;
        }
        return false;
    }
    public function set_pengiriman_tgl($pengiriman_tgl){
        if($pengiriman_tgl != ""){
            $this->pengiriman_tgl = $pengiriman_tgl;
            return true;
        }
        return false;
    }
    public function set_pengiriman_status($pengiriman_status){
        if($pengiriman_status != ""){
            $this->pengiriman_status = $pengiriman_status;
            return true;
        }
        return false;
    }
    public function set_id_fk_penjualan($id_fk_penjualan){
        if($id_fk_penjualan != ""){
            $this->id_fk_penjualan = $id_fk_penjualan;
            return true;
        }
        return false;
    }
    public function set_pengiriman_tempat($pengiriman_tempat){
        if($pengiriman_tempat != ""){
            $this->pengiriman_tempat = $pengiriman_tempat;
            return true;
        }
        return false;
    }
    public function set_id_fk_warehouse($id_fk_warehouse){
        if($id_fk_warehouse != ""){
            $this->id_fk_warehouse = $id_fk_warehouse;
            return true;
        }
        return false;
    }
    public function set_id_fk_cabang($id_fk_cabang){
        if($id_fk_cabang != ""){
            $this->id_fk_cabang = $id_fk_cabang;
            return true;
        }
        return false;
    }
    public function get_id_pk_pengiriman(){
        return $this->id_pk_pengiriman;
    }
    public function get_pengiriman_tgl(){
        return $this->pengiriman_tgl;
    }
    public function get_pengiriman_status(){
        return $this->pengiriman_status;
    }
    public function get_id_fk_penjualan(){
        return $this->id_fk_penjualan;
    }
    public function get_pengiriman_tempat(){
        return $this->pengiriman_tempat;
    }
    public function get_id_fk_warehouse(){
        return $this->id_fk_warehouse;
    }
    public function get_id_fk_cabang(){
        return $this->id_fk_cabang;
    }
}