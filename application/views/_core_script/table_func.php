<script>
    var colCount = 1; //ragu either 1/0
    var orderBy = 0;
    var orderDirection = "ASC";
    var searchKey = "";
    var page = 1;
    var content = [];
    function refresh(req_page = 1) {
        page = req_page;
        $.ajax({
            url: "<?php echo base_url();?>ws/"+ctrl+"/content?orderBy="+orderBy+"&orderDirection="+orderDirection+"&page="+page+"&searchKey="+searchKey+"&"+url_add,
            type: "GET",
            dataType: "JSON",
            success: function(respond) {
                var html = "";
                if(respond["status"] == "SUCCESS"){
                    content = respond["content"];
                    for(var a = 0; a<respond["content"].length; a++){
                        html += "<tr>";
                        for(var b = 0; b<respond["key"].length; b++){
                            html += "<td class = 'align-middle text-center'>"+respond["content"][a][respond["key"][b]]+"</td>";
                        }
                        html += "<td class = 'align-middle text-center'><i style = 'cursor:pointer;font-size:large' data-toggle = 'modal' class = 'text-primary md-edit' data-target = '#update_modal' onclick = 'load_edit_content("+a+")'></i> | <i style = 'cursor:pointer;font-size:large' data-toggle = 'modal' class = 'text-danger md-delete' data-target = '#delete_modal' onclick = 'load_delete_content("+a+")'></i></td>";
                        html += "</tr>";
                    }
                }
                else{
                    html += "<tr>";
                    html += "<td colspan = "+colCount+" class = 'align-middle text-center'>No Records Found</td>";
                    html += "</tr>";
                }
                $("#content_container").html(html);
                pagination(respond["page"]);
                
            },
            error: function(){
                var html = "";
                html += "<tr>";
                html += "<td colspan = "+colCount+" class = 'align-middle text-center'>No Records Found</td>";
                html += "</tr>";
                $("#content_container").html(html);
                
                html = "";
                html += '<li class="page-item"><a class="page-link" style = "cursor:not-allowed"><</a></li>';
                html += '<li class="page-item"><a class="page-link" style = "cursor:not-allowed">></a></li>';
                $("#pagination_container").html(html);
            }
        });
        function pagination(page_rules){
            html = "";
            if(page_rules["previous"]){
                html += '<li class="page-item"><a class="page-link" onclick = "refresh('+(page_rules["before"])+')"><</a></li>';
            }
            else{
                html += '<li class="page-item"><a class="page-link" style = "cursor:not-allowed"><</a></li>';
            }
            if(page_rules["first"]){
                html += '<li class="page-item"><a class="page-link" onclick = "refresh('+(page_rules["first"])+')">'+(page_rules["first"])+'</a></li>';
                html += '<li class="page-item"><a class="page-link">...</a></li>';
            }
            if(page_rules["before"]){
                html += '<li class="page-item"><a class="page-link" onclick = "refresh('+(page_rules["before"])+')">'+page_rules["before"]+'</a></li>';
            }
            html += '<li class="page-item active"><a class="page-link" onclick = "refresh('+(page_rules["current"])+')">'+page_rules["current"]+'</a></li>';
            if(page_rules["after"]){
                html += '<li class="page-item"><a class="page-link" onclick = "refresh('+(page_rules["after"])+')">'+page_rules["after"]+'</a></li>';
            }
            if(page_rules["last"]){
                html += '<li class="page-item"><a class="page-link">...</a></li>';
                html += '<li class="page-item"><a class="page-link" onclick = "refresh('+(page_rules["last"])+')">'+page_rules["last"]+'</a></li>';
            }
            if(page_rules["next"]){
                html += '<li class="page-item"><a class="page-link" onclick = "refresh('+(page_rules["after"])+')">></a></li>';
            }
            else{
                html += '<li class="page-item"><a class="page-link" style = "cursor:not-allowed">></a></li>';
            }
            $("#pagination_container").html(html);
        }
    }
    function sort(colNum){
        if(parseInt(colNum) != orderBy){
            orderBy = colNum; 
            orderDirection = "ASC";
            var orderDirectionHtml = ' <span class="badge badge-primary align-top" id = "orderDirection">ASC</span>';
            $("#orderDirection").remove();
            $("#col"+colNum).append(orderDirectionHtml);
        }
        else{
            var direction = $("#orderDirection").text();
            if(direction == "ASC"){
                orderDirection = "DESC";
            }
            else{
                orderDirection = "ASC";
            }
            $("#orderDirection").text(orderDirection);
        }
        refresh();
    }
    function search(){
        searchKey = $("#search_box").val();
        refresh();
    }
    function tblheader(){
        $.ajax({
            url: "<?php echo base_url();?>ws/"+ctrl+"/columns",
            type: "GET",
            dataType: "JSON",
            async:false,
            success: function(respond) {
                var html = "";
                if(respond["status"] == "SUCCESS"){
                    colCount = respond["content"].length+1; //sama col action
                    html += "<tr>";
                    for(var a = 0; a<respond["content"].length; a++){
                        html += "<th id = 'col"+a+"' style = 'cursor:pointer' onclick = 'sort("+a+")' class = 'text-center align-middle'>"+respond["content"][a]["col_name"];
                        if(a == 0){
                            html += " <span class='badge badge-primary align-top' id = 'orderDirection'>ASC</span>";
                        }
                        html += "</th>";
                    }
                    html += "<th class = 'text-center align-middle'>Action</th>";
                    html += "</tr>";
                }
                else{
                    html += "<tr>";
                    html += "<th class = 'align-middle text-center'>Columns is not defined</th>";
                    html += "</tr>";
                }
                $("#col_title_container").html(html);
                
            },
            error: function(){
                var html = "<tr>";
                html += "<th class = 'align-middle text-center'>Columns is not defined</th>";
                html += "</tr>";
                $("#col_title_container").html(html);
            }
        });
    }
    document.onreadystatechange = function () {
        if (document.readyState === 'complete') {
            tblheader();
            refresh();
        }
    }
</script>