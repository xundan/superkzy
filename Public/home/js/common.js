function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = location.search.substr(1).match(reg);
    if (r != null) return decodeURI(r[2]);
    return null;
}
$(function () {
    $('body').on('click', '.collapse-switch', function () {
        console.log('collapse');
        var $this = $(this);
        console.log(1);
        $this.find('.collapse-arrow').toggleClass('arrow-expanded');
        console.log(2);
        $this.parent('li').find('.collapse').slideToggle('in');
        console.log(3);
        $this.parent('li').find('.content').toggleClass('brief_content');
    });
});

