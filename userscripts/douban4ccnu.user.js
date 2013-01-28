// ==UserScript==
// @name			douban_ccnu
// @namespace		douban_ccnu
// @version			v0.2
/* @reason
	修复豆瓣页面变动带来的问题，添加了自动更新
@end*/
// @include			http://www.douban.com/subject/*
// @include			http://www.douban.com/isbn/*
// @author			freefcw@gmail.com
// @thankto			zhx@xmulib.org
// 2009-01-04 Adds Ajax to get book info.
//
// ==/UserScript==
if(typeof unsafeWindow.jQuery !== "undefined") {
  var jQuery = unsafeWindow.jQuery;
  var $ = jQuery; 
}

var thisScript = {
name: "douban_HUST", //脚本名称，请自行修改
id: "39921", //脚本在userscripts.org的id，请自行修改
version:"2.0" // 当前脚本版本号，请自行修改
}
var updater = new Updater(thisScript); // 用thisScript对象创建一个更新器对象
updater.check(24); //检查是否有更新

$(document).ready(function(){
	if ($('#nav a.now span').text() == '读书') {
		// get book title
		var title = $('h1').text();
		//title = encodeURI(title);
		// get book isbn
		$("#info .obmo .pl").each(function(i){
			if ($(this).text() == 'ISBN:'){
				var isbn = $(this)[0].nextSibling.nodeValue;
				isbn = isbn.substr(1,13);

				setTimeout(function(){GM_xmlhttpRequest({
					method: 'GET',
					url: 'http://210.42.106.193/ccnu.php?isbn='+isbn,
					headers: {
						'User-agent': 'Mozilla/4.0 (compatible) Greasemonkey',
						'Accept': 'application/atom+xml,application/xml,text/xml',
					},
					onload: function(res) {
						//GM_log('ajax finished!status:'+ res.status+res.statusText);
						var json = eval('('+res.responseText+')');

						if (json.ok > 0 ){			
							var openLink = json.href;
							var htmlStr = '<h2>在哪借这本书?  ·  ·  ·  ·  ·  · </h2>';
							htmlStr += '<div class="indent"><h4 style="margin-bottom: 0px"><a href="'+openLink+'" target="_blank">华中师范大学图书馆馆藏</a><span style="margin-left: 10px">索书号 : '+json.index+'</span></h4>';

							htmlStr += '<ul class="bs">';
							try
							{
								for (i=0;i<json.ok;i++)
								{
									htmlStr += '<li><span style="float:left">'+json.data[i].place+'</span><span style="float: right">'+json.data[i].s+'</span><br style="clear:both"/></li>';
								} 
							}
							catch (e)
							{
							}
							
							htmlStr += '</ul></div></br>';

							$(".aside div:eq(0)").after(htmlStr);
						}
						else{
							//GM_log('no such book');
							var openLink = 'http://202.114.34.15/opac/openlink.php?strText='+title+'&strSearchType=title';
							var htmlStr = '<h2>在哪借这本书?  ·  ·  ·  ·  ·  · </h2>';
							htmlStr += '<div class="indent"><li><a href='+openLink+' target="_blank">华中师范大学图书馆馆藏</a></li></div>';
							$("#tablerm div:eq(0)").after(htmlStr);
						}
					}
				})},500);
			}
		});
	}
});