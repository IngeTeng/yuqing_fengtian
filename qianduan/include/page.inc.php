<?php
	/**
	*	page.inc.php
	*	分页方法
	*	@	 author	 Say
	*	@	 date	 2012-12-24
	**/
	//分页方法
	function Page($url,$page,$pagecount){
		$page_str = '';
		//URL处理
		if(strstr($url,'.php?')){
			$url = $url.'&page=';
		}else{
			$url = $url.'?page=';
		}
		//分页样式
		$page_style = '<style type="text/css">
								#page{
									position:absolute;right:0px;top:0px;
								}
								a.active{
									font-weight:bold;
								}
						</style>';
		$page_before = '<div id="page"><a href="index.php">首页</a>';//分页前部分
		$page_after = '<a href="index.php?page='.$pagecount.'">末页</a></div><div class="clear"></div>';//分页后部分
		$page_prev = '';//分页prev
		$page_next = '';//分页next
		$page_tmp = '';//分页中间部分
		//* 分页prev、next值
		if($page > 1){
			$page_prev = '<a href="'.$url.intval($page-1).'">上一页</a>';
		}else{
			$page_prev = '<a>上一页</a>';
		}
		if($page < $pagecount){
			$page_next= '<a href="'.$url.intval($page+1).'">下一页</a>';
		}else{
			$page_next = '<a>下一页</a>';
		}
		//*当分页总数小于6时
		if($pagecount < 6){
			for($i=1;$i<=$pagecount;$i++){
				if($page == $i){
					$page_tmp .= '<a class="active">'.$i.'</a>';
				}else{
					$page_tmp .= '<a href="'.$url.$i.'">'.$i.'</a>';
				}			 
			}
		}else{
			//*当分页总数大于等于6时
			//*当前页小于4时
			if($page<4){
				for($i=1;$i<=5;$i++){
					if($page == $i){
						$page_tmp .= '<a class="active">'.$i.'</a>';
					}else{
						$page_tmp .='<a href="'.$url.$i.'">'.$i.'</a>';
					}
				}
			}else{
				//当前页数大于等于4时
				if($page+2<=$pagecount){
					for($i=$page-2;$i<=$page+2;$i++){
						if($page == $i){
							$page_tmp .= '<a class="active">'.$i.'</a>';
						}else{
							$page_tmp .='<a href="'.$url.$i.'">'.$i.'</a>';
						}
					}
				}else{
					for($i=$pagecount-2;$i<=$pagecount;$i++){
						if($page == $i){
							$page_tmp .= '<a class="active">'.$i.'</a>';
						}else{
							$page_tmp .='<a href="'.$url.$i.'">'.$i.'</a>';
						}
					}
				}
			}
		}
		$page_str = $page_style.$page_before.$page_prev.$page_tmp.$page_next.$page_after;
		return $page_str;

	}
	require_once 'include/page.config.php';
	require_once 'keyword_data.php';
	//分页
	if(empty($pagesize) || !is_numeric($pagesize)){
		$pagesize = 20;
	}
	$num = count($keyword_list_data);	//总数
	$pagecount = ceil($num/$pagesize);//向上取整，计算总页数

?>