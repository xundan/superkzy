function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = location.search.substr(1).match(reg);
    if (r != null) return decodeURI(r[2]);
    return null;
}
$(function () {
    $('body').on('click', '.collapse-switch', function () {
        var $this = $(this);
        $this.find('.collapse-arrow').toggleClass('arrow-expanded');
        $this.parent('li').find('.collapse').slideToggle('in');
        $this.parent('li').find('.content').toggleClass('brief_content');
    }).on('click', '.btn-trigger', function () {
        var $this = $(this);
        if ($this.css('right') == '-20px') {
            $this.addClass('ready');
            $this.closest('.float-btn-group').toggleClass('open');
        } else if ($this.css('right') == '0px') {
            $this.closest('.float-btn-group').toggleClass('open');
            setTimeout(function () {
                if (!$this.closest('.float-btn-group').hasClass('open')) {
                    $this.removeClass('ready');
                }
            }, 3000);
        }
    }).on('click', '.card-container', function () {
        var $this = $(this);
        $this.parent('li').find('.collapse-arrow').toggleClass('arrow-expanded');
        $this.parent('li').find('.collapse').slideToggle('in');
        $this.parent('li').find('.content').toggleClass('brief_content');
    });
});

