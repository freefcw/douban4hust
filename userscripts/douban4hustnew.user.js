/// <reference path="jquery-1.2.6-vsdoc.js" />

// ==UserScript==
// @name			douban_HUSTnew
// @namespace		douban_HUSTnew
// @version			v0.2
// @include			http://www.douban.com/subject/*
// @include			http://www.douban.com/isbn/*
// @author			freefcw@gmail.com
// @modify          xushengs@gmail.com
// @thankto			zhx@xmulib.org
// 2009-01-04 Adds Ajax to get book info.
//
// ==/UserScript==

String.prototype.process = function(o) {
    return this.replace(/\$\{([^\}]+)\}/g, function(a, b) {
        return o[b];
    });
};

if (typeof unsafeWindow.jQuery !== "undefined") {
    var jQuery = unsafeWindow.jQuery;
    var $ = jQuery;
}

var Douban4HUST = new function() {
    var _books = [];
    var _isbn = '', _title = '', _link = '';
    var _extLinkTpl = 'http://202.114.9.29/search*chx/${pn}?SEARCH=${pv}';
    var _itemTpl = ['<li>',
                    '<span style="float:left">&nbsp;索 书 号 : ${index}</span>',
                    '<span style="float:right">${status}</span>',
                    '<br />',
                    '<span style="clear:both">馆藏地点: ${place}</span>',
                    '</li>'].join('');

    // analysis
    function _analyse(res) {
        var p = /<span\s+id=["']?copySection["']?.*?>[^$]*?<\/table>[\r\n]*<\/span>/igm;
        var r = res.responseText.match(p);
        if (r) {
            _link = _extLinkTpl.process({ 'pn': 'i', 'pv': _isbn });
            var s = r[0].replace(/(<.*?>)|(&nbsp;)|([ \t])/ig, '').replace(/(^\s+)|(\s+$)/ig, '').replace(/[\r\n]{2,}/ig, '\n').split('\n');
            var l = s.length;
            for (var i = 3; i < l; i++) {
                _books.push({ 'place': s[i], 'index': s[++i], 'status': s[++i] });
            }
        }
        else {
            _link = _extLinkTpl.process({ 'pn': 't', 'pv': encodeURIComponent(_title) });
        }

        $('#tablerm').prepend(_getHtml());
    }

    // gernerate html
    function _getHtml() {
        var s = [];
        s.push('<h2>在哪借这本书?  ·  ·  ·  ·  ·  · </h2>');
        s.push('<div class="indent">');
        s.push('<h4 style="margin-bottom: 0px;"><a href="' + _link + '" target="_blank">华中科技大学图书馆馆藏</a></h4>');
        var l = _books.length;
        if (l > 0) {
            s.push('<ul class="bs">');
            for (var i = 0; i < l; i++) {
                s.push(_itemTpl.process(_books[i]));
            }
            s.push('</ul>');
        }
        s.push('</div></br>');
        return s.join('');
    }

    // send a request
    function _request() {
        setTimeout(function() {
            GM_xmlhttpRequest({
                method: 'GET',
                url: _extLinkTpl.process({ 'pn': 'i', 'pv': _isbn }),
                headers: {
                    'User-agent': 'Mozilla/4.0 (compatible) Greasemonkey'
                },
                onload: _analyse
            })
        }, 500);
    }

    // start to collect info
    function _start() {
        if ($('#nav a.now span').text() == '读书') {
            _title = $('h1').text();
            $("#info .obmo .pl").each(function(i) {
                if ($(this).text() == 'ISBN:') {
                    _isbn = $.trim($(this)[0].nextSibling.nodeValue);
                    if (_isbn == '') {
                        return;
                    };
                    _request();
                }
            });
        }
    }

    // when dom ready, go!
    $(document).ready(_start);
} ();
