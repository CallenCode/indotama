<?php
#Session required: user,id_toko
?>
<li>
    <hr class="light-grey-hr mb-10" />
</li>
<li>
    <a href="#"><?php #redirect ke daftar toko yang dia boleh akses?> 
        <div class="pull-left">
            <span class="right-nav-text">MANAJEMEN TOKO</span>
        </div>
        <hr/>
    </a>
</li>
<?php if($this->session->multiple_toko_access):?>
<li>
    <a href="<?php echo base_url();?>toko/daftar_akses_toko"><?php #redirect ke daftar toko yang dia boleh akses?> 
        <div class="pull-left">
            <span class="right-nav-text">TOKO</span>
        </div>
        <div class="clearfix"></div>
    </a>
</li>
<?php endif;?>
<?php 
if($this->session->id_toko):?>
<li>
    <a href="#">
        <div class="pull-left">
            <span class="right-nav-text">AKTIF: <?php echo strtoupper($this->session->nama_toko);?></span>
        </div>
        <div class="clearfix"></div>
    </a>
</li>
<?php
if(isset($toko)):
for($a = 0; $a<count($toko); $a++):?>
<li>
    <a href="<?php echo base_url();?><?php echo $toko[$a]["menu_name"];?>">
        <div class="pull-left">
            <i class="md-<?php echo $toko[$a]["menu_icon"];?>"></i>
            <span class="right-nav-text" style="margin-left:20px"><?php echo strtoupper($toko[$a]["menu_display"]);?></span>
        </div>
        <div class="clearfix"></div>
    </a>
</li>
<?php endfor;endif;endif;?>