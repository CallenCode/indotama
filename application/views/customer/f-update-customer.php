
<div class = "modal fade" id = "update_modal">
    <div class = "modal-dialog">
        <div class = "modal-content">
            <div class = "modal-header">
                <h4 class = "modal-title">Ubah Data <?php echo ucwords($page_title);?></h4>
            </div>
            <div class = "modal-body">
            <?php 
            $notif_data = array(
                "page_title"=>$page_title
            );
            $this->load->view('_notification/update_error',$notif_data); ?>
                <form id = "update_form" method = "POST">
                    <input type="hidden" name="id_pk_cust" id = "id_edit"> 
                    
                    <div class = "form-group col-lg-6">
                        <h5>Nama Lengkap</h5>
                        <input type="text" class="form-control" name="cust_name" id = "name_edit" required>
                    </div>
                    
                    <div class = "form-group col-lg-6">
                        <h5>Panggilan</h5>
                        <select name="cust_suff" class="form-control" id = "suff_edit">
                            <option value="0" disabled>Pilih Panggilan</option>
                            <option value="MR">Mr</option>
                            <option value="MRS">Mrs</option>
                            <option value="MS">Ms</option>
                            <option value="BAPAK">Bpk</option>
                            <option value="IBU">Ibu</option>
                            <option value="NONA">Nona</option>
                        </select>
                    </div>
                    
                    <div class = "form-group col-lg-6">
                        <h5>Perusahaan</h5>
                        <input type="text" class="form-control" name="cust_perusahaan" id = "perusahaan_edit" required>
                    </div>
                    
                    <div class = "form-group col-lg-6">
                        <h5>Email</h5>
                        <input type="email" class="form-control" name="cust_email" id = "email_edit" required>
                    </div>
                    
                    <div class = "form-group col-lg-6">
                        <h5>No Telp</h5>
                        <input type="text" class="form-control" name="cust_telp" id = "telp_edit" required>
                    </div>
                    
                    <div class = "form-group col-lg-6">
                        <h5>No HP</h5>
                        <input type="text" class="form-control" name="cust_hp" id = "hp_edit" required>
                    </div>
                    
                    <div class = "form-group col-lg-6">
                        <h5>Alamat</h5>
                        <input type="text" class="form-control" name="cust_alamat" id = "alamat_edit" required>
                    </div>
                    
                    <div class = "form-group col-lg-6">
                        <h5>Keterangan</h5>
                        <input type="text" class="form-control" name="cust_keterangan" id = "keterangan_edit" required>
                    </div>
                    <div class = "form-group">
                        <button type = "button" class = "btn btn-sm btn-danger" data-dismiss = "modal">Cancel</button>
                        <button type = "button" onclick = "update_func()" class = "btn btn-sm btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function load_edit_content(id){
        $("#id_edit").val(content[id]["id"]);
        $("#name_edit").val(content[id]["name"]);
        $("#suff_edit").val(content[id]["suff"]);
        $("#perusahaan_edit").val(content[id]["perusahaan"]);
        $("#email_edit").val(content[id]["email"]);
        $("#telp_edit").val(content[id]["telp"]);
        $("#hp_edit").val(content[id]["hp"]);
        $("#alamat_edit").val(content[id]["alamat"]);
        $("#keterangan_edit").val(content[id]["keterangan"]);
    }
</script>
