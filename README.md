Crawler4Blog
============

Description
------------

A simple crawler for fetching a person 's netease/sohu/sina's blog to local database according to one URL.

The project is based on CodeIgniter and its view is based on wordpress.

As the target blog website is Chinese website,the language in view is all Chinese.

Just a student playing with some code for fun,take it easy~~

URL is the blog list URL as follows:

***

Sina URL : http://blog.sina.com.cn/s/articlelist_1191258123_0_1.html

Sohu URL : http://jishaochengvip.blog.sohu.com/entry/

Netease URL : http://letaosun.blog.163.com/blog/

***
Solution
------------
There are three steps to get the blog:

		1、Get one person's all blog URL according to the blog list URL;
		2、Get the blog part ,the title,the datetime and the author(according to Regular Expression or string matching) 
		in the page which is parsed by blog URL;
		3、Save them into local database(in this project use MySQL).Of course if a blog contains picture or other 
		rich media content,		we will save it to local file system and modify the content where contains the image URL.
***
Strength
------------
		1、Make fetching blog very simple:Just One URL and One Click.
		2、Use it to write your own blog using the ckeditor which is embedded in our project.
		3、Classify blogs according to tags or authors
		4、Lasy Load Mechanism to access the images in blog to save space and save one copy other than loading the image 
		as soon as the blog is accessed.
Weakness
------------
		1、Lacking an feedback(such as progress bar) to remind user how many blogs we have got and how many blogs waiting 
		to be accessed.
		2、Interface is a little simple~~
***
@esrevinu
universerocker@gmail.com


