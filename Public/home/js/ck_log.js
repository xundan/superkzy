// 需要jquery

function ck_log(operation,result){
    // 参数可以不写
    if (!operation) operation = "browse";
    if (!result) result="OK";
    var page = window.location.pathname;
    var param = window.location.search;
    var subData = {
        "page":page,
        "param":param,
        "oper":operation,
        "result":result
    };
    //http://localhost/superkzy/index.php/Home/Feedback/{:U('Log/fetch')} 404 (Not Found)
    var url = "../Log/fetch";
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
