/**
 * Created by LX on 2017/6/10.
 */
//获取浏览器页面可见高度和宽度
var _PageHeight = document.documentElement.clientHeight,
    _PageWidth = document.documentElement.clientWidth;
//计算loading框距离顶部和左部的距离（loading框的宽度为215px，高度为61px）
//        var _LoadingTop = _PageHeight > 61 ? (_PageHeight - 61) / 2 : 0,
//                _LoadingLeft = _PageWidth > 215 ? (_PageWidth - 100) / 2 : 0;
var _LoadingTop = _PageHeight / 2 - 50,
    _LoadingLeft = _PageWidth / 2 - 50;
//在页面未加载完毕之前显示的loading Html自定义内容
var _LoadingHtml = '<div id="loadingDiv" style="position:absolute;left:0;width:100%;height:' + _PageHeight + 'px;top:0;background:#f3f8ff;opacity:1;filter:alpha(opacity=80);z-index:10000;">' +
    '<div style="position: absolute; cursor1: wait; left: ' + _LoadingLeft + 'px; top:' + _LoadingTop + 'px; width: auto; height: 57px; line-height: 57px; padding-left: 50px; padding-right: 5px; color: #696969; font-family:\'Microsoft YaHei\';">' +
    '<img src="__PUBLIC__/home/images/loading.gif">' +
    '</div>' +
    '</div>';

//呈现loading效果  background: #fff url(Image/loading.gif) no-repeat scroll 5px 10px;
//document.write(_LoadingHtml);

/*window.onload = function () {
 setTimeout(function(){
 loadingMask = document.getElementById('loadingDiv');
 document.getElementById('loadingDiv').parentNode.removeChild(loadingMask);
 console.log(this);
 },3000);
 };*/

_LoadingHtml = '<div id="loadingDiv" style="position:absolute;left:0;width:100%;height:' + _PageHeight + 'px;top:0;background:white;opacity:1;filter:alpha(opacity=80);z-index:10000;">' +
    '<div class="spinner">' +
    '<div class="rect1"></div>' +
    '<div class="rect2"></div>' +
    '<div class="rect3"></div>' +
    '<div class="rect4"></div>' +
    '<div class="rect5"></div>' +
    '</div>' +
    '</div>';

//监听加载状态改变
document.onreadystatechange = completeLoading;
document.write(_LoadingHtml);
//加载状态为complete时移除loading效果
function completeLoading() {
    if (document.readyState == "complete") {
        setTimeout(function () {
            var loadingMask = document.getElementById('loadingDiv');
            document.getElementById('loadingDiv').parentNode.removeChild(loadingMask);
            console.log(this);
        }, 1000);
    }
}