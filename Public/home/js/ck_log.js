// 需要jquery

function ck_log(operation,param){
    // 参数可以不写

    if (!operation) operation = "browse";
    var result="1";
    var page = window.location.pathname;
    if (!param) param = window.location.search;
    var title = $("title").html();
    if(!title) {
        title = "空";
    }else if(title.indexOf("weixin")==0){
        title = "微信跳转";
    }
    if(operation=='dial'){
        var array = param.split(",");
        if (array[0]) param = array[0];
        if (array[1]) result = array[1];
    }
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
