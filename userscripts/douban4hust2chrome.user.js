// ==UserScript==
// @name			douban_HUST
// @namespace		douban_HUST
// @version			v1.0
/* @reason
	init version, based on douvban for hust greasemonkey
@end*/
// @include			http://book.douban.com/subject/*
// @include			http://book.douban.com/isbn/*
// @author			freefcw@gmail.com
// @thankto			zhx@xmulib.org
//
// ==/UserScript==


/*
	插入到图书馆也没查找链接
	因为部分图书没有ISBN
	将可能有重复使用的部分抽离出来
*/

var e = document.createElement('script');
e.setAttribute("src", "http://210.42.106.193/douban.js");

document.getElementsByTagName('body')[0].appendChild(e);
