// ==UserScript==
// @name           douban_HUST beta
// @namespace      douban_HUST beta
// @include        http://www.douban.com/subject/*
// @include        http://www.douban.com/isbn/*
// @author		   freefcw@gmail.com
// 2009-01-04 Adds Ajax to  get book info.
//
// ==/UserScript==
if(typeof unsafeWindow.jQuery !== "undefined") {
  var jQuery = unsafeWindow.jQuery;
  var $ = jQuery; 
}

$(document).ready(function(){
	GM_log('i\'m running');
	if ($('#nav a.now span').text() == '读书') {
		// get book title
		GM_log('running 1');
		var title = $('h1').text();
		//title = encodeURI(title);
		// get book isbn
		$("#info .obmo .pl").each(function(i){
			GM_log('running 2');
			if ($(this).text() == 'ISBN:'){
			  var isbn = $(this)[0].nextSibling.nodeValue;
			  isbn = isbn.substr(1,13);
			  GM_log('before ajax,now isbn is '+isbn);
			  //此处使用了jquery的json,可以参考一下手册
			  $.getJSON("http://acm.hust.edu.cn/getbook.php?callback=?", { 'isbn' : isbn}, function(json){ 
			  	  	GM_log('ajax finished!');
					if (json.ok > 0){
						var openLink = "http://202.114.9.29/search*chx/i?SEARCH="+isbn;
						var htmlStr = "<h2>在哪借这本书?  ·  ·  ·  ·  ·  · </h2>";
						htmlStr += "<div class=indent><li><a href='"+openLink+"' target='_blank'>华中科技大学图书馆馆藏</a></li>";

						htmlStr += '<ul class="bs">';
						try
						{
							for (i=0;i<json.ok;i++)
							{
								htmlStr += "<li style='font-size:12px'>"+i+'.'+json.data[i].place+"    "+json.data[i].i+json.data[i].s +"</li>";	
							} 
						}
						catch (e)
						{
						}
							
						htmlStr += "</ul></div></br>";

						$("#tablerm div:eq(0)").after(htmlStr);
					}
					else if(json.ok == 0){
						var openLink = "http://202.114.9.29/search*chx/t?SEARCH="+title;
						var htmlStr = "<h2>在哪借这本书?  ·  ·  ·  ·  ·  · </h2>";
						htmlStr += "<div class=indent><li><a href='"+openLink+"' target='_blank'>华中科技大学大学图书馆馆藏</a></li></div>";
						$("#tablerm div:eq(0)").after(htmlStr);
					}
			  });
			  return false;
			}
		});
	}
});