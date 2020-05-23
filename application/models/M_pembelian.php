<?php
defined("BASEPATH") or exit("No Direct Script");
date_default_timezone_set("Asia/Jakarta");
class M_pembelian extends CI_Model{
    private $tbl_name = "MSTR_PEMBELIAN";
    private $columns = array();
    private $id_pk_pembelian;
    private $pem_pk_nomor;
    private $pem_tgl;
    private $pem_status;
    private $id_fk_supp;
    private $id_fk_cabang;
    private $pem_create_date;
    private $pem_last_modified;
    private $id_create_data;
    private $id_last_modified;

    public function __construct(){
        parent::__construct();
        $this->set_column("pem_pk_nomor","Nomor Pembelian",true);
        $this->set_column("pem_tgl","Tanggal Pembelian",false);
        $this->set_column("pem_status","Status",false);
        $this->set_column("sup_perusahaan","Supplier",false);
        $this->set_column("pem_last_modified","Last Modified",false);
        $this->pem_create_date = date("Y-m-d H:i:s");
        $this->pem_last_modified = date("Y-m-d H:i:s");
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
    public function install(){
        $sql = "
        DROP TABLE MSTR_PEMBELIAN;
        CREATE TABLE MSTR_PEMBELIAN(
            ID_PK_PEMBELIAN INT PRIMARY KEY AUTO_INCREMENT,
            PEM_PK_NOMOR VARCHAR(30),
            PEM_TGL DATE,
            PEM_STATUS VARCHAR(15),
            ID_FK_SUPP INT,
            ID_FK_CABANG INT,
            PEM_CREATE_DATE DATETIME,
            PEM_LAST_MODIFIED DATETIME,
            ID_CREATE_DATA INT,
            ID_LAST_MODIFIED INT
        );
        DROP TABLE MSTR_PEMBELIAN_LOG;
        CREATE TABLE MSTR_PEMBELIAN_LOG(
            ID_PK_PEMBELIAN_LOG INT PRIMARY KEY AUTO_INCREMENT,
            EXECUTED_FUNCTION VARCHAR(30),
            ID_PK_PEMBELIAN INT,
            PEM_PK_NOMOR VARCHAR(30),
            PEM_TGL DATE,
            PEM_STATUS VARCHAR(15),
            ID_FK_SUPP INT,
            ID_FK_CABANG INT,
            PEM_CREATE_DATE DATETIME,
            PEM_LAST_MODIFIED DATETIME,
            ID_CREATE_DATA INT,
            ID_LAST_MODIFIED INT,
            ID_LOG_ALL INT
        );
        DROP TRIGGER IF EXISTS TRG_AFTER_INSERT_PEMBELIAN;
        DELIMITER $$
        CREATE TRIGGER TRG_AFTER_INSERT_PEMBELIAN
        AFTER INSERT ON MSTR_PEMBELIAN
        FOR EACH ROW
        BEGIN
            SET @ID_USER = NEW.ID_LAST_MODIFIED;
            SET @TGL_ACTION = NEW.PEM_LAST_MODIFIED;
            SET @LOG_TEXT = CONCAT(NEW.ID_LAST_MODIFIED,' ','INSERT DATA AT' , NEW.PEM_LAST_MODIFIED);
            CALL INSERT_LOG_ALL(@ID_USER,@TGL_ACTION,@LOG_TEXT,@ID_LOG_ALL);
            
            INSERT INTO MSTR_PEMBELIAN_LOG(EXECUTED_FUNCTION,ID_PK_PEMBELIAN,PEM_PK_NOMOR,PEM_TGL,PEM_STATUS,ID_FK_SUPP,ID_FK_CABANG,PEM_CREATE_DATE,PEM_LAST_MODIFIED,ID_CREATE_DATA,ID_LAST_MODIFIED,ID_LOG_ALL) VALUES ('AFTER INSERT',NEW.ID_PK_PEMBELIAN,NEW.PEM_PK_NOMOR,NEW.PEM_TGL,NEW.PEM_STATUS,NEW.ID_FK_SUPP,NEW.ID_FK_CABANG,NEW.PEM_CREATE_DATE,NEW.PEM_LAST_MODIFIED,NEW.ID_CREATE_DATA,NEW.ID_LAST_MODIFIED,@ID_LOG_ALL);
        END$$
        DELIMITER ;
        
        DROP TRIGGER IF EXISTS TRG_AFTER_UPDATE_PEMBELIAN;
        DELIMITER $$
        CREATE TRIGGER TRG_AFTER_UPDATE_PEMBELIAN
        AFTER UPDATE ON MSTR_PEMBELIAN
        FOR EACH ROW
        BEGIN
            SET @ID_USER = NEW.ID_LAST_MODIFIED;
            SET @TGL_ACTION = NEW.PEM_LAST_MODIFIED;
            SET @LOG_TEXT = CONCAT(NEW.ID_LAST_MODIFIED,' ','UPDATE DATA AT' , NEW.PEM_LAST_MODIFIED);
            CALL INSERT_LOG_ALL(@ID_USER,@TGL_ACTION,@LOG_TEXT,@ID_LOG_ALL);
            
            INSERT INTO MSTR_PEMBELIAN_LOG(EXECUTED_FUNCTION,ID_PK_PEMBELIAN,PEM_PK_NOMOR,PEM_TGL,PEM_STATUS,ID_FK_SUPP,ID_FK_CABANG,PEM_CREATE_DATE,PEM_LAST_MODIFIED,ID_CREATE_DATA,ID_LAST_MODIFIED,ID_LOG_ALL) VALUES ('AFTER UPDATE',NEW.ID_PK_PEMBELIAN,NEW.PEM_PK_NOMOR,NEW.PEM_TGL,NEW.PEM_STATUS,NEW.ID_FK_SUPP,NEW.ID_FK_CABANG,NEW.PEM_CREATE_DATE,NEW.PEM_LAST_MODIFIED,NEW.ID_CREATE_DATA,NEW.ID_LAST_MODIFIED,@ID_LOG_ALL);
        END$$
        DELIMITER ;
        ";
        executeQuery($sql);
    }
    public function content($page = 1,$order_by = 0, $order_direction = "ASC", $search_key = "",$data_per_page = ""){
        $order_by = $this->columns[$order_by]["col_name"];
        $search_query = "";
        if($search_key != ""){
            $search_query .= "AND
            ( 
                pem_pk_nomor LIKE '%".$search_key."%' OR
                pem_tgl LIKE '%".$search_key."%' OR
                pem_status LIKE '%".$search_key."%' OR
                id_fk_supp LIKE '%".$search_key."%' OR
                pem_create_date LIKE '%".$search_key."%' OR
                pem_last_modified LIKE '%".$search_key."%' OR
                id_create_data LIKE '%".$search_key."%' OR
                id_last_modified LIKE '%".$search_key."%'
            )";
        }
        $query = "
        SELECT id_pk_pembelian,pem_pk_nomor,pem_tgl,pem_status,sup_perusahaan,pem_last_modified
        FROM ".$this->tbl_name." 
        INNER JOIN MSTR_SUPPLIER ON MSTR_SUPPLIER.ID_PK_SUP = ".$this->tbl_name.".ID_FK_SUPP
        WHERE PEM_STATUS != ? AND SUP_STATUS = ? AND ID_FK_CABANG = ? ".$search_query."  
        ORDER BY ".$order_by." ".$order_direction." 
        LIMIT 20 OFFSET ".($page-1)*$data_per_page;
        $args = array(
            "NONAKTIF","AKTIF",$this->id_fk_cabang
        );
        $result["data"] = executeQuery($query,$args);
        
        $query = "
        SELECT id_pk_pembelian
        FROM ".$this->tbl_name." 
        INNER JOIN MSTR_SUPPLIER ON MSTR_SUPPLIER.ID_PK_SUP = ".$this->tbl_name.".ID_FK_SUPP
        WHERE PEM_STATUS != ? AND SUP_STATUS = ? AND ID_FK_CABANG = ? ".$search_query."
        ORDER BY ".$order_by." ".$order_direction;
        $result["total_data"] = executeQuery($query,$args)->num_rows();
        return $result;
    }
    public function columns(){
        return $this->columns;
    }
    public function list(){
        $query = "
        SELECT id_pk_pembelian,pem_pk_nomor,pem_tgl,pem_status,sup_perusahaan,pem_last_modified,toko_nama,cabang_daerah
        FROM ".$this->tbl_name." 
        INNER JOIN MSTR_SUPPLIER ON MSTR_SUPPLIER.ID_PK_SUP = ".$this->tbl_name.".ID_FK_SUPP
        INNER JOIN MSTR_CABANG ON MSTR_CABANG.ID_PK_CABANG = ".$this->tbl_name.".ID_FK_CABANG
        INNER JOIN MSTR_TOKO ON MSTR_TOKO.ID_PK_TOKO = MSTR_CABANG.ID_FK_TOKO
        WHERE PEM_STATUS = ? AND SUP_STATUS = ? AND CABANG_STATUS = ? AND TOKO_STATUS = ?";
        $args = array(
            "AKTIF","AKTIF","AKTIF","AKTIF"
        );

        if($this->id_fk_cabang != ""){
            $query .= " AND ID_FK_CABANG = ?";
            array_push($args,$this->id_fk_cabang);
        }
        return executeQuery($query,$args);
    }
    public function detail_by_no(){
        $sql = "
        SELECT id_pk_pembelian,pem_pk_nomor,pem_tgl,pem_status,sup_perusahaan,pem_last_modified,cabang_daerah,cabang_notelp,cabang_alamat,toko_nama
        FROM ".$this->tbl_name." 
        INNER JOIN MSTR_SUPPLIER ON MSTR_SUPPLIER.ID_PK_SUP = ".$this->tbl_name.".ID_FK_SUPP
        INNER JOIN MSTR_CABANG ON MSTR_CABANG.ID_PK_CABANG = ".$this->tbl_name.".ID_FK_CABANG
        INNER JOIN MSTR_TOKO ON MSTR_TOKO.ID_PK_TOKO = MSTR_CABANG.ID_FK_TOKO
        WHERE PEM_STATUS = ? AND SUP_STATUS = ? AND CABANG_STATUS = ? AND TOKO_STATUS = ? AND PEM_PK_NOMOR = ?";
        $args = array(
            "AKTIF","AKTIF","AKTIF","AKTIF",$this->pem_pk_nomor
        );
        return executeQuery($sql,$args);
    }
    public function insert(){
        if($this->check_insert()){
            $data = array(
                "pem_pk_nomor" => $this->pem_pk_nomor,
                "pem_tgl" => $this->pem_tgl,
                "pem_status" => $this->pem_status,
                "id_fk_supp" => $this->id_fk_supp,
                "id_fk_cabang" => $this->id_fk_cabang,
                "pem_create_date" => $this->pem_create_date,
                "pem_last_modified" => $this->pem_last_modified,
                "id_create_data" => $this->id_create_data,
                "id_last_modified" => $this->id_last_modified
            );
            return insertRow($this->tbl_name,$data);
        }
        else{
            return false;
        }
    }
    public function update(){
        if($this->check_update()){
            $where = array(
                "id_pk_pembelian" => $this->id_pk_pembelian,
            );
            $data = array(
                "pem_pk_nomor" => $this->pem_pk_nomor,
                "pem_tgl" => $this->pem_tgl,
                "id_fk_supp" => $this->id_fk_supp,
                "pem_last_modified" => $this->pem_last_modified,
                "id_last_modified" => $this->id_last_modified
            );
            updateRow($this->tbl_name,$data,$where);
            return true;
        }
        else{
            return false;
        }
    }
    public function delete(){
        if($this->check_delete()){
            $where = array(
                "id_pk_pembelian" => $this->id_pk_pembelian,
            );
            $data = array(
                "pem_status" => "NONAKTIF",
                "pem_last_modified" => $this->pem_last_modified,
                "id_last_modified" => $this->id_last_modified
            );
            updateRow($this->tbl_name,$data,$where);
            return true;
        }
        else{
            return false;
        }
    }
    public function check_insert(){
        if($this->pem_pk_nomor == ""){
            return false;
        }
        if($this->pem_tgl == ""){
            return false;
        }
        if($this->pem_status == ""){
            return false;
        }
        if($this->id_fk_supp == ""){
            return false;
        }
        if($this->id_fk_cabang == ""){
            return false;
        }
        if($this->pem_create_date == ""){
            return false;
        }
        if($this->pem_last_modified == ""){
            return false;
        }
        if($this->id_create_data == ""){
            return false;
        }
        if($this->id_last_modified == ""){
            return false;
        }
        else return true;
    }
    public function check_update(){
        if($this->id_pk_pembelian == ""){
            return false;
        }
        if($this->pem_pk_nomor == ""){
            return false;
        }
        if($this->pem_tgl == ""){
            return false;
        }
        if($this->id_fk_supp == ""){
            return false;
        }
        if($this->pem_last_modified == ""){
            return false;
        }
        if($this->id_last_modified == ""){
            return false;
        }
        else return true;
    }
    public function check_delete(){
        if($this->id_pk_pembelian == ""){
            return false;
        }
        if($this->pem_last_modified == ""){
            return false;
        }
        if($this->id_last_modified == ""){
            return false;
        }
        else return true;
    }
    public function set_insert($pem_pk_nomor,$pem_tgl,$pem_status,$id_fk_supp,$id_fk_cabang){
        if(!$this->set_pem_pk_nomor($pem_pk_nomor)){
            return false;
        }
        if(!$this->set_pem_tgl($pem_tgl)){
            return false;
        }
        if(!$this->set_pem_status($pem_status)){
            return false;
        }
        if(!$this->set_id_fk_supp($id_fk_supp)){
            return false;
        }
        if(!$this->set_id_fk_cabang($id_fk_cabang)){
            return false;
        }
        return true;
    }
    public function set_update($id_pk_pembelian,$pem_pk_nomor,$pem_tgl,$id_fk_supp){
        if(!$this->set_id_pk_pembelian($id_pk_pembelian)){
            return false;
        }
        if(!$this->set_pem_pk_nomor($pem_pk_nomor)){
            return false;
        }
        if(!$this->set_pem_tgl($pem_tgl)){
            return false;
        }
        if(!$this->set_id_fk_supp($id_fk_supp)){
            return false;
        }
        return true;
    }
    public function set_delete($id_pk_pembelian){
        if(!$this->set_id_pk_pembelian($id_pk_pembelian)){
            return false;
        }
        return true;
    }
    public function set_id_pk_pembelian($id_pk_pembelian){
        if($id_pk_pembelian != ""){
            $this->id_pk_pembelian = $id_pk_pembelian;
            return true;
        }
        return false;
    }
    public function set_pem_tgl($pem_tgl){
        if($pem_tgl != ""){
            $this->pem_tgl = $pem_tgl;
            return true;
        }
        return false;
    }
    public function set_pem_pk_nomor($pem_pk_nomor){
        if($pem_pk_nomor != ""){
            $this->pem_pk_nomor = $pem_pk_nomor;
            return true;
        }
        return false;
    }
    public function set_pem_status($pem_status){
        if($pem_status != ""){
            $this->pem_status = $pem_status;
            return true;
        }
        return false;
    }
    public function set_id_fk_supp($id_fk_supp){
        if($id_fk_supp != ""){
            $this->id_fk_supp = $id_fk_supp;
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
    public function get_id_pk_pembelian(){
        return $this->id_pk_pembelian;
    }
    public function get_pem_pk_nomor(){
        return $this->pem_pk_nomor;
    }
    public function get_pem_tgl(){
        return $this->pem_tgl;
    }
    public function get_pem_status(){
        return $this->pem_status;
    }
    public function get_id_fk_supp(){
        return $this->id_fk_supp;
    }
    public function get_id_fk_cabang(){
        return $this->id_fk_cabang;
    }
}