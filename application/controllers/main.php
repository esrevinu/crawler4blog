<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
ini_set('display_errors','On');
class Main extends Controller {
	function __construct()
	{
		parent::__construct();
// 		if ( $this->input->cookie("admin",true) != "admin") exit('No direct URL access allowed');
		$this->load->model('Blog_model');
	}

	public function index()
	{
		$this->load->library('pagination');
		$config['base_url'] = site_url('main/index');
		$config['total_rows'] = $this->db->count_all('blog');
		$config['per_page'] = 5;
		$config['uri_segment'] = 3;  // 表示第 3 段 URL 为当前页数，如 index.php/控制器/方法/页数，如果表示当前页的 URL 段不是第 3 段，请修改成需要的数值。
		$config['num_links'] = 4;
		
		$config['full_tag_open'] = "<div class='pagination'><ul>";
		$config['full_tag_close'] = '</ul></div>';
		//自定义数字超链接的样式
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		//自定义当前页的样式
		$config['cur_tag_open'] = '<li class="active"><span>';
		$config['cur_tag_close'] = '</span></li>';
		//显示下一页和上一页
		$config['next_link'] = '&gt;';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		
		$config['prev_link'] = '&lt;';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$blog_list = $this->Blog_model->listAllByPage($config['per_page'],$this->uri->segment(3));
		$author_list = $this->Blog_model->listAllAuthor();
		$status_list = $this->Blog_model->listAllStatus();
		
		$this->assign('authors',$author_list->result_array());
		$this->assign('status',$status_list->result_array());
		$this->assign('items',$blog_list->result_array());
		$this->assign('pagination',$this->pagination->create_links());
// 		var_dump($this->pagination->create_links());
		$this->display('blog/list.html');
	}
	public function listByAuthor()
	{
		$query = $this->Blog_model->listByAuthor();
		$author_list = $this->Blog_model->listAllAuthor();
		$status_list = $this->Blog_model->listAllStatus();
		
		$this->assign('authors',$author_list->result_array());
		$this->assign('status',$status_list->result_array());
		$this->assign('items',$query->result_array());
		$this->assign('pagination',"");
		$this->display('blog/list.html');
	}
	public function listByStatus()
	{
		$query = $this->Blog_model->listByStatus();
		$author_list = $this->Blog_model->listAllAuthor();
		$status_list = $this->Blog_model->listAllStatus();
	
		$this->assign('authors',$author_list->result_array());
		$this->assign('status',$status_list->result_array());
		$this->assign('items',$query->result_array());
		$this->assign('pagination',"");
		$this->display('blog/list.html');
	}
	public function detail()
	{
		$query = $this->Blog_model->findById();
		$this->assign('item',$query->row_array());
		$this->display('blog/detail.html');
	}
	public function add()
	{
		if($this->input->post()==NULL)
			$this->display('blog/add.html');
		else 
		{
			if($this->Blog_model->add())
				redirect(site_url('main'));	
			else
				echo "数据库插入数据失败！";
		}
	}
	public function delete()
	{
		if($this->Blog_model->delete())
			redirect(site_url('main'));
		else
			echo "数据库删除数据失败！";
	}
	public function edit()
	{
		if($this->input->post()==NULL)
		{
			$query = $this->Blog_model->findById();
			$this->assign('item',$query->row_array());
			$this->display('blog/edit.html');
		}
		else
		{
			if($this->Blog_model->updateById())
				redirect(site_url('main'));
			else
				echo "数据库更新数据失败！";
		}
		
	}
	public function import()
	{
		if($this->input->post()==NULL)
		{
			$this->display('blog/import.html');
		}
		else
		{
			if($this->input->post("sinaurl")!=NULL)
			{
				if($this->Blog_model->importSina())
					echo "sinaurl success";
				else
					echo "数据库更新数据失败！";
			}
			if($this->input->post("sohuurl")!=NULL)
			{
				if($this->Blog_model->importSohu())
					echo "sohuurl success";
				else
					echo "数据库更新数据失败！";
			}
			
			if($this->input->post("neteaseurl")!=NULL)
			{
				if($this->Blog_model->importNetease())
					echo "neteaseurl success";
				else
					echo "数据库更新数据失败！";
			}
// 			redirect(site_url('main'));
		}
	}
}