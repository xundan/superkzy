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
        $this.find('.collapse-arrow').toggleClass('arrow-expanded');
        $this.parent('li').find('.collapse').slideToggle('in');
        $this.parent('li').find('.content').toggleClass('brief_content');
    });
});

