<?php
	/**
	*	page.inc.php
	*	��ҳ����
	*	@	 author	 Say
	*	@	 date	 2012-12-24
	**/
	//��ҳ����
	function Page($url,$page,$pagecount){
		$page_str = '';
		//URL����
		if(strstr($url,'.php?')){
			$url = $url.'&page=';
		}else{
			$url = $url.'?page=';
		}
		//��ҳ��ʽ
		$page_style = '<style type="text/css">
								#page{
									position:absolute;right:0px;top:0px;
								}
								a.active{
									font-weight:bold;
								}
						</style>';
		$page_before = '<div id="page"><a href="index.php">��ҳ</a>';//��ҳǰ����
		$page_after = '<a href="index.php?page='.$pagecount.'">ĩҳ</a></div><div class="clear"></div>';//��ҳ�󲿷�
		$page_prev = '';//��ҳprev
		$page_next = '';//��ҳnext
		$page_tmp = '';//��ҳ�м䲿��
		//* ��ҳprev��nextֵ
		if($page > 1){
			$page_prev = '<a href="'.$url.intval($page-1).'">��һҳ</a>';
		}else{
			$page_prev = '<a>��һҳ</a>';
		}
		if($page < $pagecount){
			$page_next= '<a href="'.$url.intval($page+1).'">��һҳ</a>';
		}else{
			$page_next = '<a>��һҳ</a>';
		}
		//*����ҳ����С��6ʱ
		if($pagecount < 6){
			for($i=1;$i<=$pagecount;$i++){
				if($page == $i){
					$page_tmp .= '<a class="active">'.$i.'</a>';
				}else{
					$page_tmp .= '<a href="'.$url.$i.'">'.$i.'</a>';
				}			 
			}
		}else{
			//*����ҳ�������ڵ���6ʱ
			//*��ǰҳС��4ʱ
			if($page<4){
				for($i=1;$i<=5;$i++){
					if($page == $i){
						$page_tmp .= '<a class="active">'.$i.'</a>';
					}else{
						$page_tmp .='<a href="'.$url.$i.'">'.$i.'</a>';
					}
				}
			}else{
				//��ǰҳ�����ڵ���4ʱ
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
	//��ҳ
	if(empty($pagesize) || !is_numeric($pagesize)){
		$pagesize = 20;
	}
	$num = count($keyword_list_data);	//����
	$pagecount = ceil($num/$pagesize);//����ȡ����������ҳ��

?>