<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($_POST['start'])) {
	include "../../../wp-config.php";
	function denglu_exportComment_ajax($start = 0) {
		@ini_set("max_execution_time", 300);
		if (function_exists('import_comments_to_denglu')) {
			$data = import_comments_to_denglu(); 
		} else {
			return var_dump("请先在评论设置勾选开启“社会化评论”功能。");
		} 
		// $data = array_slice(range(1,605), $start, 50, true);
		// sleep(1);
		// return var_dump($data);
		if ($data) {
			$wptm_basic = get_option('wptm_basic');
			$data = json_encode($data);
			class_exists('Denglu') or require(dirname(__FILE__) . "/class/Denglu.php");
			$api = new Denglu($wptm_basic['appid'], $wptm_basic['appkey'], 'utf-8');
			try {
				$comments = $api -> importComment($data);
			} 
			catch(DengluException $e) { // 获取异常后的处理办法(请自定义)
				// wp_die($e -> geterrorDescription()); //返回错误信息
				echo $e -> geterrorDescription();
				return;
			} 
			// return var_dump($comments);
			if ($comments && is_array($comments)) {
				foreach ($comments as $comment) {
					if ($comment['id']) wp_update_comment_agent($comment['comment_ID'], $comment['id']);
					if (is_array($comment['children'])) {
						foreach ($comment['children'] as $children) {
							if ($children['id']) wp_update_comment_agent($children['comment_ID'], $children['id']);
						} 
					} 
				} 
				echo count($comments) + $start; //继续
			} 
		} else {
			echo 'success'; //成功
		} 
	} 
	denglu_exportComment_ajax($_POST['start']);
} 

?>