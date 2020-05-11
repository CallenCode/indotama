<?php
$page_title = "Master Barang";
$breadcrumb = array(
    "Master","Barang"
);
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <?php $this->load->view('req/mm_css.php');?>
    </head>

    <body>
        <div class="preloader-it">
            <div class="la-anim-1"></div>
        </div>
        <div class="wrapper theme-1-active pimary-color-pink">

            <?php $this->load->view('req/mm_menubar.php');?>

            <div class="page-wrapper">
                <div class="container-fluid">
                    <div class="row mt-20">
                        <div class="col-lg-12 col-sm-12">
                            <div class="panel panel-default card-view">
                                <div class="panel-heading bg-gradient">
                                    <div class="pull-left">
                                        <h6 class="panel-title txt-light"><?php echo ucwords($page_title);?></h6>
                                    </div>
                                    <div class="clearfix"></div>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">Home</a></li>
                                        <?php for($a = 0; $a<count($breadcrumb); $a++):?>
                                        <?php if($a+1 != count($breadcrumb)):?>
                                        <li class="breadcrumb-item"><?php echo ucwords($breadcrumb[$a]);?></a></li>
                                        <?php else:?>
                                        <li class="breadcrumb-item active"><?php echo ucwords($breadcrumb[$a]);?></li>
                                        <?php endif;?>
                                        <?php endfor;?>
                                    </ol>
                                </div>
                                <div class="panel-wrapper collapse in">
                                    <div class="panel-body">
                                        <div class = "col-lg-12">
                                            <div class = "d-block">
                                                <button type = "button" class = "btn btn-primary btn-sm col-lg-2 col-sm-12" data-toggle = "modal" data-target = "#register_modal" style = "margin-right:10px">Tambah <?php echo ucwords($page_title);?></button>
                                            </div>
                                            <br/>
                                            <br/>
                                            <div class = "align-middle text-center d-block">
                                                <i style = "cursor:pointer;font-size:large;margin-left:10px" class = "text-primary md-edit"></i><b> - Edit </b>   
                                                <i style = "cursor:pointer;font-size:large;margin-left:10px" class = "text-danger md-delete"></i><b> - Delete </b>
                                            </div>
                                            <br/>
                                            <div class = "form-group">
                                                <h5>Search Data Here</h5>
                                                <input id = "search_box" placeholder = "Search data here..." type = "text" class = "form-control input-sm " onkeyup = "search()" style = "width:25%">
                                            </div>
                                            <div class = "table-responsive">
                                                <table class = "table table-bordered table-hover table-striped">
                                                    <thead id = "col_title_container">
                                                    </thead>
                                                    <tbody id = "content_container">
                                                    </tbody>
                                                </table>
                                            </div>
                                            <nav aria-label="Page navigation example">
                                                <ul class="pagination justify-content-center" id = "pagination_container">
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $this->load->view('req/mm_footer.php');?>
                </div>
            </div>
        </div>
        <?php $this->load->view('req/mm_js.php');?>
    </body>
</html>
<datalist id = "list_merk" ></datalist>
<datalist id = "list_jenis" ></datalist>

<script>
    var ctrl = "barang";
    var url_add = "";
</script>
<?php
$data = array(
    "page_title" => "Master Barang"
);
?>
<?php $this->load->view("_core_script/table_func");?>
<?php $this->load->view("_core_script/register_func");?>
<?php $this->load->view("_core_script/update_func");?>
<?php $this->load->view("_core_script/delete_func");?>
<?php $this->load->view("barang/f-add-barang",$data);?>
<?php $this->load->view("barang/f-update-barang",$data);?>
<?php $this->load->view("barang/f-delete-barang",$data);?>
<script>
    $("button[data-toggle='modal']").click(function(){
        load_brg_jenis();
        load_brg_merk();
    });
    function load_brg_jenis(){
        $.ajax({
            url:"<?php echo base_url();?>ws/barang_jenis/list",
            type:"GET",
            async:false,
            dataType:"JSON",
            success:function(respond){
                if(respond["status"] == "SUCCESS"){
                    var html = "";
                    for(var a = 0; a<respond["content"].length; a++){
                        html += "<option value = '"+respond["content"][a]["nama"]+"'>";
                    }
                    $("#list_jenis").html(html);
                }
            },
            error:function(){

            }
        });
    }
    function load_brg_merk(){
        $.ajax({
            url:"<?php echo base_url();?>ws/barang_merk/list",
            type:"GET",
            async:false,
            dataType:"JSON",
            success:function(respond){
                if(respond["status"] == "SUCCESS"){
                    var html = "";
                    for(var a = 0; a<respond["content"].length; a++){
                        html += "<option value = '"+respond["content"][a]["nama"]+"'>";
                    }
                    $("#list_merk").html(html);
                }
            },
            error:function(){

            }
        });
    }
</script>