<?php
namespace cocolait\helper;
/**
 * 分页操作类
 * 使用案例：
 * $pageTotal 总记录数  $show 一页显示多少条
   $pages = new Page($pageTotal, $show);
   $pages page分页参数  $pageNums  总页数  第三参数是 分页额外参数 只能是字符串
   $pageinfo = $pages->pageInfo($pages, $pageNums, "&keyword=$keyword");
 * Class Page
 * @package cocolait\helper
 */
class CpPage{
	
	public $total;//总记录数
	public $show;//显示条数
    public $pageNums;//总页数
    public $start;//起始位置
    public $page;//当前页
    public $limit;//每页显示的信息 
    	
	public function __construct($total,$show){
		$this->total = $total;
		$this->show = $show;
		$this->pageNums = ceil($this->total/$this->show);
		$this->page = empty($_GET['page'])?1:$_GET['page'];
		$this->start = ($this->page-1)*$this->show;
		$this->limit =" limit ".$this->start.",".$this->show;
	}
	
	public function prePage($page){
		if($page>1){
			return $page-1;
		}else{
			return 1;
		}
	}
	
	public function nextPage($page,$pageNums){
		if($page<$pageNums){
			return $page+1;
		}else{
			return $pageNums;
		}
	}
	
    public function pageInfo($page,$pageNums,$strDatax=null){
		if(empty($strDatax)){
	    	$strData = "";
	    	$strData.="<a href='?page=1'>首页</a>&nbsp;&nbsp;";
	    	$strData.="<a href='?page=".$this->prePage($page)."'>上一页</a>&nbsp;&nbsp;";
	    	$strData.="<a href='?page=".$this->nextPage($page, $pageNums)."'>下一页</a>&nbsp;&nbsp;";
	    	$strData.="<a href='?page=".$pageNums."'>尾页</a>&nbsp;&nbsp;";
    	}else{
    		$strData = "";
    		$strData.="<a href='?page=1".$strDatax."'>首页</a>&nbsp;&nbsp;";
    		$strData.="<a href='?page=".$this->prePage($page).$strDatax."'>上一页</a>&nbsp;&nbsp;";
    		$strData.="<a href='?page=".$this->nextPage($page, $pageNums).$strDatax."'>下一页</a>&nbsp;&nbsp;";
    		$strData.="<a href='?page=".$pageNums.$strDatax."'>尾页</a>&nbsp;&nbsp;&nbsp;&nbsp;";
    	}
		$strData.="第".$page."页/共".$pageNums."页&nbsp;&nbsp;&nbsp;";
		$strData.="共".$this->total."条记录&nbsp;&nbsp;&nbsp;";
		$strData.="跳转到&nbsp;&nbsp;&nbsp;<input name='page' id='p' min=1 max=".$pageNums." style='width:50px;' type='number'/>&nbsp;&nbsp;&nbsp;页&nbsp;&nbsp;&nbsp;<button onclick='jump();'>跳转</button>";
		$str = "<script>
				function jump()
				{
					var p =document.getElementById('p').value;
					if(p>0 && p <=".$this->pageNums.")
					{
						location.href ='?page='+p+'{$strDatax}';
					}
				}
			</script>";

		return $strData.$str;
    }
	public function pageInf($page,$pageNums,$strDatax=null){

		if(empty($strDatax)){
			$strData = "";
			$strData.="<a href='?page=1'>首页</a>&nbsp;&nbsp;";
			$strData.="<a href='?page=".$this->prePage($page)."'>上一页</a>&nbsp;&nbsp;";
			$strData.="第".$page."页/共".$pageNums."页&nbsp;&nbsp;&nbsp;";
			$strData.="共".$this->total."条记录";
			$strData.="<a href='?page=".$this->nextPage($page, $pageNums)."'>下一页</a>&nbsp;&nbsp;";

		}else{
			$strData = "";

			$strData.="<a href='?page=1".$strDatax."'>首页</a>&nbsp;&nbsp;";
			$strData.="<a href='?page=".$this->prePage($page).$strDatax."'>上一页</a>&nbsp;&nbsp;";
			$strData.="第".$page."页/共".$pageNums."页&nbsp;&nbsp;&nbsp;";
			$strData.="共".$this->total."条记录";
			$strData.="<a href='?page=".$this->nextPage($page, $pageNums).$strDatax."'>下一页</a>&nbsp;&nbsp;";
			$strData.="<a href='?page=".$pageNums.$strDatax."'>尾页</a>&nbsp;&nbsp;&nbsp;&nbsp;";

		}
		return $strData;
	}
}
