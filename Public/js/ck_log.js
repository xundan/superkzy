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
    var url = "{:U('Log/fetch')}";
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
    alert("test");
    ck_log();
});
