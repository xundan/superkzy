/**
 * Created by LX on 2017/6/17.
 */
/*!
 * CityPicker v1.1.0
 * https://github.com/tshi0912/citypicker
 *
 * Copyright (c) 2015-2016 Tao Shi
 * Released under the MIT license
 *
 * Date: 2016-09-09T12:11:57.119Z
 */

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as anonymous module.
        define(['jquery', 'ChineseDistricts'], factory);
    } else if (typeof exports === 'object') {
        // Node / CommonJS
        factory(require('jquery'), require('ChineseDistricts'));
    } else {
        // Browser globals.
        factory(jQuery, ChineseDistricts);
    }
})(function ($, ChineseDistricts) {

    'use strict';

    if (typeof ChineseDistricts === 'undefined') {
        throw new Error('The file "city-picker.data.js" must be included first!');
    }

    var NAMESPACE = 'citypicker';
    var EVENT_CHANGE = 'change.' + NAMESPACE;
    var PROVINCE = 'province';
    var CITY = 'city';
    var DISTRICT = 'district';
    var AREA = 'area';

    function CityPicker(element, options) {
        this.$element = $(element);
        this.$dropdown = null;
        this.options = $.extend({}, CityPicker.DEFAULTS, $.isPlainObject(options) && options);
        this.active = false;
        this.dems = [];
        this.needBlur = false;
        this.init();
    }

    CityPicker.prototype = {
        constructor: CityPicker,

        init: function () {

            this.defineDems();

            this.render();

            this.bind();

            this.active = true;
        },

        render: function () {
            var lastAreaArr = this.getLastArea().split(',');
            var p = this.getPosition(),
                placeholder = this.$element.attr('placeholder') || this.options.placeholder,
                overlay = '<div class="overlay"></div>',
                textspan = '<span class="city-picker-span" style="' +
                    'line-height: 40px;text-align: center;background-color: #fafafa">' +
                        //this.getWidthStyle(p.width) + 'height:' +
                        //p.height + 'px;line-height:' + (p.height - 1) + 'px;">' +
                    (placeholder ? '<span class="placeholder" style="padding: 12px">' + placeholder + '</span>' : '') +
                    '<span class="title"></span>' +
                    //'<div class="arrow"></div>' +
                    '</span>',

                dropdown = '<div class="city-picker-dropdown" style="left:0px;top:100%;' +
                        //this.getWidthStyle(p.width, true) +
                    'width: ' + document.documentElement.clientWidth + 'px' +
                    '">' +
                    '<div class="city-select-wrap">' +
                    '<div class="city-select-last">' +
                    '<span style="margin-right: 10px">最近搜索</span>' +
                    '<a class="last_search" data-content=' + lastAreaArr[0] + '>' + (lastAreaArr[0] ? this.getDetailedArea(lastAreaArr[0]) : '') + '</a>' +
                    '<a class="last_search" data-content=' + lastAreaArr[1] + '>' + (lastAreaArr[1] ? this.getDetailedArea(lastAreaArr[1]) : '') + '</a>' +
                    '<a class="last_search" data-content=' + lastAreaArr[2] + '>' + (lastAreaArr[2] ? this.getDetailedArea(lastAreaArr[2]) : '') + '</a>' +
                    '<a class="last_search" data-content=' + lastAreaArr[3] + '>' + (lastAreaArr[3] ? this.getDetailedArea(lastAreaArr[3]) : '') + '</a>' +
                    '</div>' +
                    '<div class="city-select-tab">' +
                    '<a class="active" data-count="province">省份</a>' +
                    (this.includeDem('city') ? '<a data-count="city">城市</a>' : '') +
                    (this.includeDem('district') ? '<a data-count="district">区县</a>' : '') +
                    (this.includeDem('area') ? '<a data-count="area">地区</a>' : '') +
                    '</div>' +
                    '<div class="city-select-content">' +
                    '<div class="city-select province" data-count="province"></div>' +
                    (this.includeDem('city') ? '<div class="city-select city" data-count="city"></div>' : '') +
                    (this.includeDem('district') ? '<div class="city-select district" data-count="district"></div>' : '') +
                    (this.includeDem('area') ? '<div class="city-select area" data-count="area"></div>' : '') +
                    '</div></div>';

            this.$element.addClass('city-picker-input');
            //this.$lastsearch = $(lastsearch).insertAfter(this.$element);
            this.$overlay = $(overlay).insertAfter(this.$element);
            this.$textspan = $(textspan).insertAfter(this.$element);
            this.$dropdown = $(dropdown).insertAfter(this.$textspan);
            var $select = this.$dropdown.find('.city-select');

            // setup this.$province, this.$city and/or this.$district object
            $.each(this.dems, $.proxy(function (i, type) {
                this['$' + type] = $select.filter('.' + type + '');
            }, this));

            this.refresh();
        },

        refresh: function (force) {
            // clean the data-item for each $select
            var $select = this.$dropdown.find('.city-select');
            $select.data('item', null);
            // parse value from value of the target $element
            var val = this.$element.val() || '';
            val = val.split('/');
            $.each(this.dems, $.proxy(function (i, type) {
                if (val[i] && i < val.length) {
                    this.options[type] = val[i];
                } else if (force) {
                    this.options[type] = '';
                }
                this.output(type);
            }, this));
            this.tab(PROVINCE);
            this.feedText();
            this.feedVal();
        },

        defineDems: function () {
            var stop = false;
            $.each([PROVINCE, CITY, DISTRICT, AREA], $.proxy(function (i, type) {
                if (!stop) {
                    this.dems.push(type);
                }
                if (type === this.options.level) {
                    stop = true;
                }
            }, this));
        },

        includeDem: function (type) {
            return $.inArray(type, this.dems) !== -1;
        },

        getPosition: function () {
            var p, h, w, s, pw;
            p = this.$element.position();
            s = this.getSize(this.$element);
            h = s.height;
            w = s.width;
            if (this.options.responsive) {
                pw = this.$element.offsetParent().width();
                if (pw) {
                    w = w / pw;
                    if (w > 0.99) {
                        w = 1;
                    }
                    w = w * 100 + '%';
                }
            }

            return {
                top: p.top || 0,
                left: p.left || 0,
                height: h,
                width: w
            };
        },

        getSize: function ($dom) {
            var $wrap, $clone, sizes;
            if (!$dom.is(':visible')) {
                $wrap = $("<div />").appendTo($("body"));
                $wrap.css({
                    "position": "absolute !important",
                    "visibility": "hidden !important",
                    "display": "block !important"
                });

                $clone = $dom.clone().appendTo($wrap);

                sizes = {
                    width: $clone.outerWidth(),
                    height: $clone.outerHeight()
                };

                $wrap.remove();
            } else {
                sizes = {
                    width: $dom.outerWidth(),
                    height: $dom.outerHeight()
                };
            }

            return sizes;
        },

        getWidthStyle: function (w, dropdown) {
            if (this.options.responsive && !$.isNumeric(w)) {
                return 'width:' + w + ';';
            } else {
                return 'width:' + (dropdown ? Math.max(320, w) : w) + 'px;';
            }
        },

        bind: function () {
            var $this = this;
            $(document).on('click', (this._mouteclick = function (e) {
                var $target = $(e.target);
                var $dropdown, $span, $input;
                if ($target.is('.city-picker-span')) {
                    $span = $target;
                } else if ($target.is('.city-picker-span *')) {
                    $span = $target.parents('.city-picker-span');
                }
                if ($target.is('.city-picker-input')) {
                    $input = $target;
                }
                if ($target.is('.city-picker-dropdown')) {
                    $dropdown = $target;
                } else if ($target.is('.city-picker-dropdown *')) {
                    $dropdown = $target.parents('.city-picker-dropdown');
                }
                if ((!$input && !$span && !$dropdown) ||
                    ($span && $span.get(0) !== $this.$textspan.get(0)) ||
                    ($input && $input.get(0) !== $this.$element.get(0)) ||
                    ($dropdown && $dropdown.get(0) !== $this.$dropdown.get(0))) {
                    $this.close(true);
                    console.log($target);
                    if($target.is('.overlay')){
                        //点阴影区取消搜索
                        ck_log('district','取消选地址');
                        console.log('xxxxxxxxxxxxxxx',$target.parents().children('input'));
                        $target.parents().children('input').eq(0).citypicker('reset');
                        $(document).trigger('render_flag');
                    }
                }
                //if($target.is('.overlay')){
                //    $this.close(true);
                //}

            }));

            this.$element.on('change', (this._changeElement = $.proxy(function () {
                console.log('element change');
                this.close(true);
                this.refresh(true);
            }, this))).on('focus', (this._focusElement = $.proxy(function () {
                this.needBlur = true;
                this.open();
            }, this))).on('blur', (this._blurElement = $.proxy(function () {
                if (this.needBlur) {
                    this.needBlur = false;
                    this.close(true);
                }
            }, this)));

            this.$textspan.on('click', function (e) {
                var $target = $(e.target), type;
                $this.needBlur = false;
                if ($target.is('.select-item')) {
                    type = $target.data('count');
                    $this.open(type);
                } else {
                    if ($this.$dropdown.is(':visible')) {
                        $this.close();
                    } else {
                        $this.open();
                    }
                }
            }).on('mousedown', function () {
                $this.needBlur = false;
            });

            this.$dropdown.on('click', '.city-select a', function () {
                var $select = $(this).parents('.city-select');
                var $active = $select.find('a.active');
                var last = $select.next().length === 0;
                $active.removeClass('active');
                $(this).addClass('active');
                if ($(this).data('code') === undefined) {
                    console.log('点击选择全部', $this.getText(), $this.getVal(), $this.getCode());
                    $this.feedText();
                    $this.feedVal(true);
                    $this.close();
                    $this.saveLastArea();
                }
                //else{
                if ($active.data('code') !== $(this).data('code')) {
                    $select.data('item', {
                        address: $(this).attr('title'), code: $(this).data('code')
                    });
                    $(this).trigger(EVENT_CHANGE);
                    if (($(this).data('code') === undefined)) {
                        $(this).parents('.city-select').data('item', null);
                    }
                    $this.feedText();
                    $this.feedVal(true);
                    if (last) {
                        console.log('点击选择地址', $this.getText(), $this.getVal(), $this.getCode);
                        $this.close();
                        $this.saveLastArea();
                    }
                    console.log('test', ChineseDistricts[$this.getCode()], $this.getVal());
                    if (!ChineseDistricts[$this.getCode()]) {
                        //如果area没内容直接关闭下拉框
                        $this.close();
                        $this.saveLastArea();
                    }
                }
                //}
            }).on('click', '.city-select-tab a', function () {
                if (!$(this).hasClass('active')) {
                    var type = $(this).data('count');
                    $this.tab(type);
                }
            }).on('mousedown', function () {
                $this.needBlur = false;
            }).on('click', '.last_search', function () {
                console.log('历史搜索的点击值', $(this).data('content'));
                var LastAreaArr = $(this).data('content').split('/');
                var $input = $(this).parents('.city-picker-dropdown').siblings('input');
                var lastArea = $(this).data('content');
                //$this.saveLastArea($(this).data('content'));
                $input.citypicker('reset');
                $input.citypicker('destroy');
                $input.citypicker({
                    province: LastAreaArr[0],
                    city: LastAreaArr[1],
                    district: LastAreaArr[2],
                    area: LastAreaArr[3]
                });
                console.log($this);
                $this.saveLastArea(lastArea);

            });

            if (this.$province) {
                this.$province.on(EVENT_CHANGE, (this._changeProvince = $.proxy(function () {
                    this.output(CITY);
                    this.output(DISTRICT);
                    this.output(AREA);
                    this.tab(CITY);
                }, this)));
            }

            if (this.$city) {
                this.$city.on(EVENT_CHANGE, (this._changeCity = $.proxy(function () {
                    this.output(DISTRICT);
                    this.output(AREA);
                    this.tab(DISTRICT);
                }, this)));
            }

            if (this.$district) {
                this.$district.on(EVENT_CHANGE, (this._changeCity = $.proxy(function () {
                    this.output(AREA);
                    this.tab(AREA);
                }, this)));
            }
        },

        open: function (type) {
            type = type || PROVINCE;
            this.$dropdown.show();
            this.$textspan.addClass('open').addClass('focus');
            this.tab(type);
            this.$overlay.css('display', 'block');
            //打开下拉框，开始选择地址，记录初始选择操作
            ck_log('district','打开地址栏');
            console.log('open component')
        },

        close: function (blur) {
            this.$dropdown.hide();
            this.$textspan.removeClass('open');
            if (blur) {
                this.$textspan.removeClass('focus');
                //console.log('close true');
            }
            //console.log('close');
            this.$overlay.css('display', 'none');
        },

        unbind: function () {

            $(document).off('click', this._mouteclick);

            this.$element.off('change', this._changeElement);
            this.$element.off('focus', this._focusElement);
            this.$element.off('blur', this._blurElement);

            this.$textspan.off('click');
            this.$textspan.off('mousedown');

            this.$dropdown.off('click');
            this.$dropdown.off('mousedown');

            if (this.$province) {
                this.$province.off(EVENT_CHANGE, this._changeProvince);
            }

            if (this.$city) {
                this.$city.off(EVENT_CHANGE, this._changeCity);
            }
        },

        getText: function () {
            var text = '';
            this.$dropdown.find('.city-select')
                .each(function () {
                    var item = $(this).data('item'),
                        type = $(this).data('count');
                    if (item) {
                        //text += ($(this).hasClass('province') ? '' : '/') + '<span class="select-item" data-count="' +
                        //    type + '" data-code="' + item.code + '">' + item.address + '</span>';
                        text = '<span class="select-item" data-count="' +
                            type + '" data-code="' + item.code + '">' + item.address + '</span>';
                    }
                });
            return text;
        },

        getPlaceHolder: function () {
            return this.$element.attr('placeholder') || this.options.placeholder;
        },

        feedText: function () {
            var text = this.getText();
            if (text) {
                this.$textspan.find('>.placeholder').hide();
                this.$textspan.find('>.title').html(this.getText()).show();
            } else {
                this.$textspan.find('>.placeholder').text(this.getPlaceHolder()).show();
                this.$textspan.find('>.title').html('').hide();
            }
        },

        getCode: function (count) {
            var obj = {}, arr = [];
            this.$textspan.find('.select-item')
                .each(function () {
                    var code = $(this).data('code');
                    var count = $(this).data('count');
                    obj[count] = code;
                    arr.push(code);
                });
            return count ? obj[count] : arr.join('/');
        },

        getVal: function () {
            var text = '';
            this.$dropdown.find('.city-select')
                .each(function () {
                    var item = $(this).data('item');
                    if (item) {
                        text += ($(this).hasClass('province') ? '' : '/') + item.address;
                    }
                });
            return text;
        },

        feedVal: function (trigger) {
            this.$element.val(this.getVal());
            if (trigger) {
                this.$element.trigger('cp:updated');
            }
        },

        output: function (type) {
            var options = this.options;
            //var placeholders = this.placeholders;
            var $select = this['$' + type];
            var data = type === PROVINCE ? {} : [];
            var item;
            var districts;
            var code;
            var matched = null;
            var value;

            if (!$select || !$select.length) {
                return;
            }

            item = $select.data('item');

            value = (item ? item.address : null) || options[type];

            code = (
                type === PROVINCE ? 86 :
                    type === CITY ? this.$province && this.$province.find('.active').data('code') :
                        type === DISTRICT ? this.$city && this.$city.find('.active').data('code') :
                            type === AREA ? this.$district && this.$district.find('.active').data('code') : code
            );

            districts = $.isNumeric(code) ? ChineseDistricts[code] : null;
            if ($.isPlainObject(districts)) {
                $.each(districts, function (code, address) {
                    var provs;
                    if (type === PROVINCE) {
                        provs = [];
                        for (var i = 0; i < address.length; i++) {
                            if (address[i].address === value) {
                                matched = {
                                    code: address[i].code,
                                    address: address[i].address
                                };
                            }
                            provs.push({
                                code: address[i].code,
                                address: address[i].address,
                                selected: address[i].address === value
                            });
                        }
                        data[code] = provs;
                    } else {
                        if (address === value) {
                            matched = {
                                code: code,
                                address: address
                            };
                        }
                        data.push({
                            code: code,
                            address: address,
                            selected: address === value
                        });
                    }
                });
            }

            $select.html(type === PROVINCE ? this.getProvinceList(data) :
                this.getList(data, type));
            $select.data('item', matched);
        },

        getProvinceList: function (data) {
            var list = [],
                $this = this,
                simple = this.options.simple;

            $.each(data, function (i, n) {
                list.push('<dl class="clearfix">');
                list.push('<dt>' + i + '</dt><dd>');
                $.each(n, function (j, m) {
                    list.push(
                        '<a' +
                        ' title="' + (m.address || '') + '"' +
                        ' data-code="' + (m.code || '') + '"' +
                        ' class="' +
                        (m.selected ? ' active' : '') +
                        '">' +
                        ( simple ? $this.simplize(m.address, PROVINCE) : m.address) +
                        '</a>');
                });
                list.push('</dd></dl>');
            });

            return list.join('');
        },

        getList: function (data, type) {
            var list = [],
                $this = this,
                simple = this.options.simple;
            list.push('<dl class="clearfix"><dd>');
            var allChooseString;
            if(type == CITY){
                allChooseString = '全省';
            }else if(type == DISTRICT){
                allChooseString = '全市';
            }else if(type == AREA){
                allChooseString = '全区';
            }
            list.push(
                '<a' +
                ' class="city-picker-count-all">' +
                allChooseString +
                    //'全部' +
                '</a>');
            $.each(data, function (i, n) {
                list.push(
                    '<a' +
                    ' title="' + (n.address || '') + '"' +
                    ' data-code="' + (n.code || '') + '"' +
                    ' class="' +
                    (n.selected ? ' active' : '') +
                    '">' +
                    ( simple ? $this.simplize(n.address, type) : n.address) +
                    '</a>');
            });
            list.push('</dd></dl>');

            return list.join('');
        },

        simplize: function (address, type) {
            address = address || '';
            if (type === PROVINCE) {
                return address.replace(/[省,市,自治区,壮族,回族,维吾尔]/g, '');
            } else if (type === CITY) {
                return address.replace(/[市,地区,回族,蒙古,苗族,白族,傣族,景颇族,藏族,彝族,壮族,傈僳族,布依族,侗族]/g, '')
                    .replace('哈萨克', '').replace('自治州', '').replace(/自治县/, '');
            } else if (type === DISTRICT) {
                return address.length > 2 ? address.replace(/[市,区,县,旗,自治]/g, '') : address;
            } else if (type === AREA) {
                return address;
            }
        },

        tab: function (type) {
            var $selects = this.$dropdown.find('.city-select');
            var $tabs = this.$dropdown.find('.city-select-tab > a');
            var $select = this['$' + type];
            var $tab = this.$dropdown.find('.city-select-tab > a[data-count="' + type + '"]');
            if ($select) {
                $selects.hide();
                $select.show();
                $tabs.removeClass('active');
                $tab.addClass('active');
            }
        },

        reset: function () {
            this.$element.val(null).trigger('change');
        },

        destroy: function () {
            this.unbind();
            this.$element.removeData(NAMESPACE).removeClass('city-picker-input');
            this.$textspan.remove();
            this.$dropdown.remove();
        },

        //只获取最下级的地址
        getDetailedArea: function (lastArea) {
            if (typeof lastArea == 'string') {
                var lastAreaList = lastArea.split('/');
                return lastAreaList[lastAreaList.length - 1];
            } else {
                return false;
            }
        },

        //ajax读取历史数据库
        getLastArea: function () {
            if (cookie('last_area_select')) {
                return cookie('last_area_select');
            } else {
                var url = "{:U('AreaSearch/getLastArea')}";
                //url = 'http://localhost:8080/superkzy/index.php/Home/areaSearch/getLastArea';
                //url = 'http://192.168.23.100:8080/superkzy/index.php/Home/areaSearch/getLastArea';
                url = "http://www.xuncl.com/index.php/Home/AreaSearch/getLastArea";
                var result='';
                $.ajax({
                    type: "post",
                    url: url,
                    data: {},
                    async: false,
                    success: function (data) {
                        //data = '重庆市/两江新区/北部新区,山西省/太原市/阳曲县,陕西省/榆林市/神木县/瓷窑塔煤矿';
                        if(data == 'none'){
                            data='';
                        }
                        result = data;
                        cookie('last_area_select', data);
                    }
                });
                return result;
            }
        },

        //ajax存取更新历史数据库
        saveLastArea: function (lastArea) {
            console.log('getareaforlastarea', this.getVal(), this.getCode());
            var lastAreaString = this.getVal();
            if (lastAreaString) {
            } else {
                lastAreaString = lastArea;
            }

            //选完地址了，记录终结操作
            ck_log('district','选择了地址：'+lastAreaString);
            var url = "{:U('AreaSearch/setLastArea')}";
            //url = 'http://localhost:8080/superkzy/index.php/Home/areaSearch/setLastArea';
            //url = 'http://192.168.23.100:8080/superkzy/index.php/Home/areaSearch/setLastArea';
            url = "http://www.xuncl.com/index.php/Home/areaSearch/setLastArea";
            $.ajax({
                type: "post",
                url: url,
                data: {lastArea: lastAreaString},
                //async: false,
                success: function (data) {
                    console.log('ajaxSaveArea', data);
                    cookie('last_area_select',null);
                }
            });
            $(document).trigger('render_flag');
        }

    };

    CityPicker.DEFAULTS = {
        simple: false,
        responsive: false,
        placeholder: '请选择省/市/区',
        //level: 'district',
        level: 'area',
        province: '',
        city: '',
        district: ''
    };

    CityPicker.setDefaults = function (options) {
        $.extend(CityPicker.DEFAULTS, options);
    };

    // Save the other citypicker
    CityPicker.other = $.fn.citypicker;

    // Register as jQuery plugin
    $.fn.citypicker = function (option) {
        var args = [].slice.call(arguments, 1);

        return this.each(function () {
            var $this = $(this);
            var data = $this.data(NAMESPACE);
            var options;
            var fn;

            if (!data) {
                if (/destroy/.test(option)) {
                    return;
                }

                options = $.extend({}, $this.data(), $.isPlainObject(option) && option);
                $this.data(NAMESPACE, (data = new CityPicker(this, options)));
            }

            if (typeof option === 'string' && $.isFunction(fn = data[option])) {
                fn.apply(data, args);
            }
        });
    };

    $.fn.citypicker.Constructor = CityPicker;
    $.fn.citypicker.setDefaults = CityPicker.setDefaults;

    // No conflict
    $.fn.citypicker.noConflict = function () {
        $.fn.citypicker = CityPicker.other;
        return this;
    };

    $(function () {
        $('[data-toggle="city-picker"]').citypicker();
    });
});