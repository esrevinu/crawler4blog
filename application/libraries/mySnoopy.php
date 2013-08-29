<?php
/**
 * Created by JetBrains PhpStorm.
 * User: UNIVERSE
 * Date: 13-7-18
 * Time: 上午9:13
 * To change this template use File | Settings | File Templates.
 */
include 'Snoopy.class.php';
header("Content-type: text/html; charset=utf-8");
ini_set('memory_limit','100M');
class SinaBlog
{
	function getContent($sourceURL)
    {
//         $snoopy = new Snoopy();
//         $snoopy->fetch($sourceURL);
//         $sourceStr = $snoopy->results;
		$sourceStr = file_get_contents($sourceURL);
        $flag_start = '<div id="sina_keyword_ad_area2"';
        $flag_end = '<div class="shareUp">';
        $start =  strpos($sourceStr,$flag_start);
        $end = strpos($sourceStr,$flag_end,$start);
        $destStr = substr($sourceStr,$start,$end-$start);
        //由于其中的<span></span>标签里为垃圾内容但是会显示，所以考虑将它们屏蔽起来，可以将span设置成style="display:none"
        //'<span' 替换成 '<span style="display:none"'
        $destStr = str_replace('<span','<span style="display:none"',$destStr);
        $destStr = str_replace('src','tmp',$destStr);
        $destStr = str_replace('real_tmp','src',$destStr);
        return $destStr;
    }
    function getTitle($sourceURL)
    {
        $snoopy = new Snoopy();
        $snoopy->fetchtext($sourceURL);
        $sourceStr = $snoopy->results;
        $flag_end = "@charset";
        $start =  0;
        $end = strrpos($sourceStr,$flag_end);
        $destStr = substr($sourceStr,$start,$end-$start);
        return $destStr;
    }
    function getTime($sourceURL)
    {
        $snoopy = new Snoopy();
        $snoopy->fetchtext($sourceURL);
        $sourceStr = $snoopy->results;
        preg_match_all('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/',$sourceStr,$m);
        return $m[0][0];
    }
    function getLink($sourceURL)
    {
        $snoopy = new Snoopy();
        $snoopy->fetchlinks($sourceURL);
        $result = array();
        $a = $snoopy->results;
        $re = "/\/s\/blog/";
        foreach($a as $link)
        {
            if (preg_match($re, $link)) {
                array_push($result,$link);
            }
        }
        return $result;
    }
	function getAuthor($sourceURL)
    {
    	$snoopy = new Snoopy();
    	$snoopy->fetchtext($sourceURL);
    	$sourceStr = $snoopy->results;
    	$flag_start = "_";
    	$flag_end = '_新浪博客';
    	$start = strpos($sourceStr,$flag_start);
    	$end = strpos($sourceStr,$flag_end);
    	$destStr = substr($sourceStr,$start+strlen($flag_start),$end-$start-strlen($flag_start));
    	return $destStr;
    }
    function getPageNumber($sourceURL)
    {
    	$snoopy = new Snoopy();
    	$snoopy->fetch($sourceURL);
    	$sourceStr = $snoopy->results;
    	$page_start = "共";
    	$page_end = "页";
    	$start =  strrpos($sourceStr,$page_start);
    	/*
    	 * 如果没找到说明只有一页
    	 */
    	if($start == false)
    		return 1;
    	$end = strrpos($sourceStr,$page_end,$start);
    	$page_num =  substr($sourceStr,$start+strlen($page_start),$end-$start-strlen($page_start));
    	return $page_num;
    }
}
class SohuBlog
{
	function getContent($sourceURL)
	{
		$snoopy = new Snoopy();
		$snoopy->fetch($sourceURL);
		$sourceStr = $snoopy->results;
		$flag_start = '"main-content">';
		$flag_end = '<div style="_width:92px';
		$start =  strpos($sourceStr,$flag_start);
		$end = strrpos($sourceStr,$flag_end);
		$destStr = substr($sourceStr,$start+strlen($flag_start),$end-$start-strlen($flag_start));
		return $destStr;
	}
	function getTitle($sourceURL)
	{
		$snoopy = new Snoopy();
		$snoopy->fetchtext($sourceURL);
		$sourceStr = $snoopy->results;
		$flag_end = "-";
		$start =  0;
		$end = strpos($sourceStr,$flag_end);
		$destStr = substr($sourceStr,$start,$end-$start);
		return $destStr;
	}
	function getAuthor($sourceURL)
	{
		$snoopy = new Snoopy();
		$snoopy->fetch($sourceURL);
		$sourceStr = $snoopy->results;
// 		echo $sourceStr;
		$flag_start = "<title>";
		$flag_end = iconv('UTF-8', 'GBK','-搜狐博客');
		$start = strpos($sourceStr,$flag_start);
		$end = strpos($sourceStr,$flag_end,$start);
		$destStr = substr($sourceStr,$start+strlen($flag_start),$end-$start-strlen($flag_start));
		return $destStr;
	}
	function getTime($sourceURL)
	{
		$snoopy = new Snoopy();
		$snoopy->fetchtext($sourceURL);
		$sourceStr = $snoopy->results;
		preg_match_all('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}/',$sourceStr,$m);
		return $m[0][0];
	}
	function getLink($sourceURL)
	{
		$snoopy = new Snoopy();
		$snoopy->fetch($sourceURL);
		$sourceStr = $snoopy->results;
		$result = array();
		preg_match_all('/(http:\/\/)([\da-z\.]+)(blog\.sohu\.com\/)[\d]+\.html/',$sourceStr,$urlall);
		for($i=0;$i<count($urlall[0]);$i++)
			array_push($result, $urlall[0][$i]);
		$result = array_unique($result);
		return $result;
	}
	function getListLink($sourceURL)
	{
		$snoopy = new Snoopy();
		$snoopy->fetch($sourceURL);
		$sourceStr = $snoopy->results;
		$flag_start = "_ebi = '";
		$flag_end = "';";
		$start =  strpos($sourceStr,$flag_start);
		$end = strpos($sourceStr,$flag_end,$start);
		$token = substr($sourceStr,$start+strlen($flag_start),$end-$start-strlen($flag_start));
		$replace = "action/v_frag-ebi_".$token."/entry/";
		$sourceURL = str_replace("entry", $replace, $sourceURL);
		return $sourceURL;
	}
	function getPageNumber($sourceURL)
	{
		$snoopy = new Snoopy();
		$snoopy->fetch($sourceURL);
		$sourceStr = $snoopy->results;
		$per_page_start = "itemPerPage = ";
		$per_page_end = ";";
		$start =  strpos($sourceStr,$per_page_start);
		$end = strpos($sourceStr,$per_page_end,$start);
		$per_page_num =  substr($sourceStr,$start+strlen($per_page_start),$end-$start-strlen($per_page_start));	
		
		$total_count_start = "totalCount = ";
		$total_count_end = ";";
		$start =  strpos($sourceStr,$total_count_start);
		$end = strpos($sourceStr,$total_count_end,$start);
		$total_count =  substr($sourceStr,$start+strlen($total_count_start),$end-$start-strlen($total_count_start));
	
		if($total_count <= $per_page_num)
			return 1;
		else
			if($total_count % $per_page_num == 0)
			return $total_count/$per_page_num;
		else
			return ceil($total_count/$per_page_num);
	}
}
class NeteaseBlog
{
	function getContent($sourceURL)
	{
		$snoopy = new Snoopy();
		$snoopy->fetch($sourceURL);
		$sourceStr = $snoopy->results;
		$flag_start = 'nbw-blog-start"></div>';
		$flag_end = '<div class="nbw-blog-end"></div>';
		$start =  strpos($sourceStr,$flag_start);
		$end = strpos($sourceStr,$flag_end,$start);
		$destStr = substr($sourceStr,$start+strlen($flag_start),$end-$start-strlen($flag_start));
		return $destStr;
	}
	function getTitle($sourceURL)
	{
		$snoopy = new Snoopy();
		$snoopy->fetchtext($sourceURL);
		$sourceStr = $snoopy->results;
		$flag_end = "html";
		$start =  0;
		$end = strpos($sourceStr,$flag_end);
		$destStr = substr($sourceStr,$start,$end-$start);
		return $destStr;
	}
	function getAuthor($sourceURL)
	{
		$snoopy = new Snoopy();
		$snoopy->fetch($sourceURL);
		$sourceStr = $snoopy->results;
		$flag_start = "<title>";
		$flag_end = iconv('UTF-8', 'GBK','的日志');
		$start = strpos($sourceStr,$flag_start);
		$end = strpos($sourceStr,$flag_end);
		$destStr = substr($sourceStr,$start+strlen($flag_start),$end-$start-strlen($flag_start));
		return $destStr;
	}
	function getTime($sourceURL)
	{
		$snoopy = new Snoopy();
		$snoopy->fetch($sourceURL);
		$sourceStr = $snoopy->results;
		preg_match_all('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}/',$sourceStr,$m);
		return $m[0][0];
	}
	function getAuthorid($sourceURL)
	{
		$snoopy = new Snoopy();
		$snoopy->fetch($sourceURL);
		$sourceStr = $snoopy->results;
		$flag_id_start = "userId:";
		$flag_id_end = ",";
		$start = strpos($sourceStr,$flag_id_start);
		$end = strpos($sourceStr,$flag_id_end,$start);
		$destStr = substr($sourceStr,$start+strlen($flag_id_start),$end-$start-strlen($flag_id_start));
		return $destStr;
	}
	function getLink($sourceURL,$start)
	{	
		$id = self::getAuthorid($sourceURL);
		$dns = explode('.',$sourceURL); 
		$tmp = $dns[0];
		$host_part = explode('//',$tmp);//    http://chajingguancha
		$host_name = $host_part[1];
		$dns = explode('/',$sourceURL);
		$host = $dns[2];			//获取"chaijingguancha.blog.163.com"
		$post_data =array(
				'callCount=1',
				'scriptSessionId=${scriptSessionId}187',
				'c0-scriptName=BlogBeanNew',
				'c0-methodName=getBlogs',
				'c0-id=0',
				'c0-param0=number:'.$id,//凭借列表首页的URL无法获取，只有此选项变化
				'c0-param1=number:'.$start,//从第0项开始
				'c0-param2=number:20',//取20项，经测试最大就是20
				'batchId=184570'//不影响
		);
		
		$post_data = implode('&',$post_data);
// 		echo "post_data=".$post_data."<br>";
		$url='http://api.blog.163.com/'.$host_name.'/dwr/call/plaincall/BlogBeanNew.getBlogs.dwr';
// 		echo "post url=".$url."<br>";
		$refer = "http://api.blog.163.com/crossdomain.html?t=20100205";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_REFERER, $refer);
		ob_start();
		curl_exec($ch);
		$response_str = ob_get_contents();
		ob_end_clean();
		
		$result = array();
		preg_match_all('/(blog\/static\/)([\d]+)/',$response_str,$urlall);
		for($i=0;$i<count($urlall[0]);$i++)
			array_push($result, 'http://'.$host.'/'.$urlall[0][$i]);
		return $result;
	}
}

