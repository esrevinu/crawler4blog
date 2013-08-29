<?php 
require_once( APPPATH . 'libraries/mySnoopy.php' );
class Blog_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	function listAllByPage($num,$offset)
	{
		return $this->db->get('blog',$num,$offset);
	}
	function listAllAuthor()
	{
		return $this->db->query("select DISTINCT(author) from blog");
	}
	function listAllStatus()
	{
		return $this->db->query("select DISTINCT(status) from blog");
	}
	function listByStatus()
	{
		return $this->db->get_where('blog', array('status' => $this->input->get('status')));
	}
	function listByAuthor()
	{
		return $this->db->get_where('blog', array('author' => $this->input->get('author')));
	}
	
	function add()
	{
		date_default_timezone_set('Asia/Shanghai');
		$now = date('Y-m-d H:i:s');
		$data = array(
				'author' => mysql_real_escape_string($this->input->post('author')),
				'title' => mysql_real_escape_string($this->input->post('title')),
				'content' => $_POST["mycontent"],
				'ctime' => $now
		);
		
		if($this->db->insert('blog', $data))
			return true;
		else
			return false;
	}
	function delete()
	{
		return $this->db->delete('blog', array('id' => $this->input->get('id')));
	}
	/*
	 * 不只是把blog数据从表中获取出来展示，而且要把含有<img>标签的代码中src部分转换成本地保存的图片
	 * status=0,表示含图片，但还未下载
	 * status=1，表示含图片，并且图片已下载
	 * status=2,表示不含图片
	 */
	function findById()
	{
		$query = $this->db->get_where('blog', array('id' => $this->input->get('id')));
		foreach($query->result_array() as $row)
		{
			$id = $row['id'];
			$status = $row['status'];
			$content = $row['content'];
			$src = $row['src'];
		}
		if($status == "0")
		{
			$end = 0;
			if(strpos($src,"sina") == false)
				$flag_start = 'src="';
			else
				$flag_start = 'src ="';			
			$flag_end = '"';
			while(strpos($content,$flag_start,$end)!=false){
// 				echo $content;
				$start = strpos($content,$flag_start,$end);
				$end = strpos($content,$flag_end,$start+strlen($flag_start));
				$src = substr($content,$start+strlen($flag_start),$end-$start-strlen($flag_start));
// 				echo "start=".$start."<br>";
// 				echo "end=".$end."<br>";
// 				echo "src=".$src."<br>";
				$file_name =substr($src,strrpos($src,"/")+1);
				$local = APPPATH."tmp/".$file_name;
				$local = str_replace("amp;", "", $local);
// 				var_dump($local);
				$file=file_get_contents($src);
				file_put_contents($local, $file);
				$content = str_replace($src, "/".$local, $content);
				$data = array(
						'content' => $content,
						'status' => '1'
				);
				$this->db->where('id', $id);
				$this->db->update('blog', $data);
			}
			$query = $this->db->get_where('blog', array('id' => $this->input->get('id')));
			return $query;
		}
		else
			return $query;		
	}
	function updateById()
	{
		date_default_timezone_set('Asia/Shanghai');
		$now = date('Y-m-d H:i:s');
		$data = array(
				'author' => mysql_real_escape_string($this->input->post('author')),
				'title' => mysql_real_escape_string($this->input->post('title')),
				'content' => $_POST["mycontent"],
				'ctime' => $now
		);
		
		$this->db->where('id', $this->input->get('id'));
		return $this->db->update('blog', $data);
	}
	function importSina()
	{
		$sb = new SinaBlog();
		$listurl = $this->input->post('sinaurl');
		$pageNum = $sb->getPageNumber($listurl);
		$i = 1;
		$sum = 0;
		while($i<=$pageNum)
		{
			$results = $sb->getLink(mysql_real_escape_string($listurl));
			$author = $sb->getAuthor($this->input->post('sinaurl'));
			foreach($results as $link)
			{
		    	$content = $sb->getContent($link);
				$title = $sb->getTitle($link);
				$ctime = $sb->getTime($link);
				if(strpos($content,'src ="')!=false){
					$status = "0";
				}
				else
					$status = "2";
				$data = array(
				   	'author' => $author,
				   	'title' => $title,
				   	'content' => $content,
				   	'ctime' => $ctime,
					'src' => $link,
					'status'=>$status
				);
				if($this->db->insert('blog', $data) == false)
				   	return false;
				echo '第'.$sum.'篇已完成<br>';
				echo $link."<br>";
				$sum++;
			}
			$listurl = str_replace($i.".html", ($i+1).".html", $listurl);
			$i++;
		}
		echo '导入完成！<br>';
		return true;
	}
	function importSohu()
	{
		$sb = new SohuBlog();
		$sourceURL = mysql_real_escape_string($this->input->post('sohuurl'));
		$pageNum = $sb->getPageNumber($sourceURL);
		echo "一共".$pageNum."页";
		$listurl = $sb->getListLink($sourceURL);
// 		echo "listurl=".$listurl;
		$author = $sb->getAuthor($sourceURL);
		$author = iconv('GBK', 'UTF-8//IGNORE', $author);
		$i = 1;
		$sum = 0;
		while($i<=$pageNum)
		{
			$results = $sb->getLink($listurl);
			
			foreach($results as $link)
			{
				$content = iconv('GBK', 'UTF-8//IGNORE', $sb->getContent($link));
				$title = iconv('GBK', 'UTF-8//IGNORE', $sb->getTitle($link));
				$ctime = $sb->getTime($link);
				if(strpos($content,'src="')!=false){
					$status = "0";
				}
				else
					$status = "2";
				$data = array(
						'author' => $author,
						'title' => $title,
						'content' => $content,
						'ctime' => $ctime,
						'src' => $link,
						'status'=>$status
				);
				if($this->db->insert('blog', $data) == false)
					return false;
				echo '第'.$sum.'篇已完成<br>';
				echo $link."<br>";
				$sum++;
			}
			if($i == 1)
			{
				$listurl = str_replace("/entry", "-pg_2/entry", $listurl);
				echo "第一页已完成开始第二页<br>";
			}
			else
			{
				$listurl = str_replace("-pg_".$i, "-pg_".($i+1), $listurl);
				echo "第".$i."页已完成开始第".($i+1)."页<br>";
			}
// 			echo $i;
// 			echo "<br>";
// 			echo $listurl;
// 			echo "<br>";
			$i++;
		}
		echo '导入完成！<br>';
		return true;
	}
	function importNetease()
	{
		$nb = new NeteaseBlog();
		$author = iconv('GBK', 'UTF-8//IGNORE',$nb->getAuthor($this->input->post('neteaseurl')));
		$start = 0;
		$limit = 20;
		$results = $nb->getLink(mysql_real_escape_string($this->input->post('neteaseurl')),$start);
// 		var_dump($results);
		$sum = 0;
		while(count($results)>0)
		{
			
			foreach($results as $link)
			{
				$title = iconv('GBK', 'UTF-8//IGNORE', $nb->getTitle($link));
				$content = iconv('GBK', 'UTF-8//IGNORE', $nb->getContent($link));
	// 			echo $content;
				if(strpos($content,'src="')!=false){
					$status = "0";
				}
				else
					$status = "2";
				$ctime = $nb->getTime($link);
				$data = array(
						'author' => $author,
						'title' => $title,
						'content' => $content,
						'ctime' => $ctime,
						'src' => $link,
						'status'=>$status
				);
	// 			var_dump($data);
				if($this->db->insert('blog', $data) == false)
					return false;
				echo '第'.$sum.'篇已完成<br>';
				echo $link."<br>";
				$sum++;
			}
			if(count($results) == 0)
			{
				echo '总计'.$sum.'篇<br>';
				return true;
			}
			else 
			{
				$start+=count($results);
				$results = $nb->getLink(mysql_real_escape_string($this->input->post('neteaseurl')),$start);
				echo "start=".$start."<br>";
				echo "results个数为".count($results)."<br>至今总计".$sum."篇<br>";
			}		
		}
		echo '导入完成！<br>start='.$start."<br>";
		var_dump($results);
		return true;
	}

}