// 需要jquery

function ck_log(operation,param){
    // 参数可以不写

    if (!operation) operation = "browse";
    var result="OK";
    var page = window.location.pathname;
    if (!param) param = window.location.search;
    var title = $("title").html();
    var subData = {
        "page":page,
        "param":param,
        "title":title,
        "oper":operation,
        "result":result
    };
    //var url = "http://localhost/superkzy/index.php/Home/Log/fetch";
    var url = "http://www.kuaimei56.com/index.php/Home/Log/fetch";
    $.ajax({
        type:"post",
        url:url,
        data:subData,
        success:function(data){
        },
        error:function(XMLHttpRequest,textStatus,errorThrown){
        }
    });
}

$(document).ready(function(){
    ck_log();
});
