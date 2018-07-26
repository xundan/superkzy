$(document).ready(function () {
    if ($('#indexheadad').length > 0) {
        var headAd01 = $('<div class="col-2" id="headAd01"><a href="https://www.huobi.br.com/zh-cn/notice_detail/?id=1372&inviter_id=11256060"  target="_blank"><img src="//static.feixiaohao.com/themes/default/images/vip/huobi_otc2.jpg"></a></div>');
        var headAd02 = $('<div class="col-1" id="headAd02"><a href="//web.bixin.im/webapp/"  target="_blank"><img src="//static.feixiaohao.com/themes/default/images/vip/bixin3.png"></a></div>');
        var headAd03 = $('<div class="col-2" id="headAd03"><a href="/app/"  target="_blank"><img src="//static.feixiaohao.com/themes/default/images/app/fxh_app_pc2.png"></a></div>');
        $('#indexheadad').append(headAd01).append(headAd02).append(headAd03);
    }

    if ($('.plantListsSpread').length > 0) {
        var leftAd01 = $('<a href="//robot.feixiaohao.com" id="spread2" target="_blank" page="1"><img src="//static.feixiaohao.com/themes/default/images/home_fxhrobot.png"></a>');
        $('.plantListsSpread').append(leftAd01);
    }
    var div2 = $('<div style="height: 100px;overflow: hidden"><a   id="spread3"  href="https://jq.qq.com/?_wv=1027&k=5cNNbAy" title="非小号QQ群" target="_blank"><img src="//static.feixiaohao.com/themes/default/images/index03.png"></a></div>');
    $('.outlink3').append(div2);


    if (document.querySelector('.new-artList-index')) {
        var a = $('<a href="https://www.debi.com" id="spread2_2" target="_blank" page="1"><img src="//static.feixiaohao.com/themes/default/images/vip/debi0510.png" ></a>');
        var box = $('<div class="new-side-box banner"><div class="new-box-tit"><h2>赞助商</h2></div><div class="bannerIner"></div></div>');
        //var b = $('<a style="margin-top:15px;" href="https://hyperpay.tech/zh-cn" id="spread2" target="_blank" page="2"><img src="//static.feixiaohao.com/themes/default/images/vip/hpy7.jpg" ></a>');
        // var c = $('<a style="margin-top:15px;" href="https://web.bixin.im/webapp/" id="spread2_3" target="_blank" page="2"><img src="//static.feixiaohao.com/themes/default/images/vip/bixin5.png" ></a>');
        //$('.new-artList-index').closest('.new-side-box').after(box);
        //a.appendTo('.bannerIner');
        //b.appendTo('.bannerIner');
        // c.appendTo('.bannerIner');
    }


    $('body').on('click', '#spread1_0,#spread1_1,#spread2,#spread2_2,#spread3', function () {
        var thiscode = $(this).attr("id");
        $.ajax({

            url: '//api.feixiaohao.com/sitestat/?code=' + thiscode,
            type: "GET",
            success: function (data) {
                return;
            },
            error: function () {
                return;
            }
        })
    });

});




