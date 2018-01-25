var myScroll, pullDownEl, pullDownOffset, pullUpEl, pullUpOffset;
var time_now, update_time;
/**
 * 初始化iScroll控件
 */
function loaded() {
    pullDownEl = document.getElementById('pullDown');
    pullDownOffset = pullDownEl.offsetHeight;
    pullUpEl = document.getElementById('pullUp');
    pullUpOffset = pullUpEl.offsetHeight;
    console.log('PDO:'+pullDownOffset,'PUO:'+pullUpOffset);
    setTimeout(function () {
        myScroll = new iScroll('wrapper', {
//        scrollbarClass: 'myScrollbar', /* 重要样式 */
            fixedScrollbar: true,
            hideScrollbar: true,
            useTransition: true,
            topOffset: pullDownOffset,
//        probeType: 3,
//        mouseWheel: false,
            scrollbars: true,
//        disableMouse: false,
//        disablePointer: false,
            //点击事件
            click: false,
            taps: false,
//        tap: true,
            preventDefault: false,//（把这句加上去哦）
//                preventDefaultException: {tagName: /^(INPUT|TEXTAREA|BUTTON|SELECT|A)$/},
            onRefresh: function () {
                console.log('refresh');
                if (pullDownEl.className.match('loading')) {
                    pullDownEl.className = '';
                    pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新...';
//                pullDownEl.querySelector('.pullDownLabel').innerHTML = '数据更新时间：' + update_time+'下拉刷新';
                } else if (pullUpEl.className.match('loading')) {
                    pullUpEl.className = '';
                    pullUpEl.querySelector('.pullUpLabel').innerHTML = '上拉加载更多...';
                }
                document.getElementById("pullUp").style.display = "none";
                time_now = new Date();
                update_time = time_now.toLocaleString();
//            document.getElementById("show").innerHTML="onRefresh: up["+pullUpEl.className+"],down["+pullDownEl.className+"],Y["+this.y+"],maxScrollY["+this.maxScrollY+"],minScrollY["+this.minScrollY+"],scrollerH["+this.scrollerH+"],wrapperH["+this.wrapperH+"]";

            },
            onScrollMove: function () {
//            document.getElementById("show").innerHTML="onScrollMove: up["+pullUpEl.className+"],down["+pullDownEl.className+"],Y["+this.y+"],maxScrollY["+this.maxScrollY+"],minScrollY["+this.minScrollY+"],scrollerH["+this.scrollerH+"],wrapperH["+this.wrapperH+"]";
                if (this.y > 0) {
                    pullDownEl.className = 'flip';
                    pullDownEl.querySelector('.pullDownLabel').innerHTML = '松手开始更新...</br>' + '最后更新时间：' + update_time;
                    this.minScrollY = 0;
                }
                if (this.y < 0 && pullDownEl.className.match('flip')) {
                    pullDownEl.className = '';
                    pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新...';
                    this.minScrollY = -pullDownOffset;
                }

                if (this.scrollerH < this.wrapperH && this.y < (this.minScrollY - pullUpOffset) || this.scrollerH > this.wrapperH && this.y < (this.maxScrollY - pullUpOffset)) {
                //if (this.scrollerH < this.wrapperH && this.y < (this.minScrollY - pullUpOffset) || this.scrollerH > this.wrapperH && this.y < (this.maxScrollY - pullUpOffset/2)) {
                    document.getElementById("pullUp").style.display = "";
                    pullUpEl.className = 'flip';
                    pullUpEl.querySelector('.pullUpLabel').innerHTML = '松手开始更新...</br>' + '最后更新时间：' + update_time;
                }
                if (this.scrollerH < this.wrapperH && this.y > (this.minScrollY - pullUpOffset) && pullUpEl.className.match('flip') || this.scrollerH > this.wrapperH && this.y > (this.maxScrollY - pullUpOffset) && pullUpEl.className.match('flip')) {
                    document.getElementById("pullUp").style.display = "none";
                    pullUpEl.className = '';
                    pullUpEl.querySelector('.pullUpLabel').innerHTML = '上拉加载更多...';
                }

                //console.log('scrollerH:'+this.scrollerH,'wrapperH:'+this.wrapperH,'maxScrollY:'+this.maxScrollY,'y:'+this.y);
                //if (this.y <= this.maxScrollY) {
                //    pullUpAction();
                //}
            },
            onScrollEnd: function () {
//            document.getElementById("show").innerHTML="onScrollEnd: up["+pullUpEl.className+"],down["+pullDownEl.className+"],Y["+this.y+"],maxScrollY["+this.maxScrollY+"],minScrollY["+this.minScrollY+"],scrollerH["+this.scrollerH+"],wrapperH["+this.wrapperH+"]";
                if (pullDownEl.className.match('flip')) {
                    pullDownEl.className = 'loading';
                    pullDownEl.querySelector('.pullDownLabel').innerHTML = '加载中...';
                    pullDownAction(); // Execute custom function (ajax call?)
                } else if (pullUpEl.className.match('flip')) {
                    pullUpEl.className = 'loading';
                    pullUpEl.querySelector('.pullUpLabel').innerHTML = '加载中...';
                    pullUpAction(); // Execute custom function (ajax call?)
                }
            }
        });
        console.dir(myScroll.options);
    }, 100);
}
//初始化绑定iScroll控件
document.addEventListener('touchmove', function (event) {
    // 判断默认行为是否可以被禁用
    if (event.cancelable) {
        // 判断默认行为是否已经被禁用
        if (!event.defaultPrevented) {
            event.preventDefault();
        }
    }
}, false);
document.addEventListener('DOMContentLoaded', loaded, false);