<?php
include "../../../wp-config.php";
date_default_timezone_set("PRC");
$getinfo = 'V'.get_option('wptm_version').','.get_bloginfo('name').','.get_bloginfo('wpurl').'/';
define('ROOT_PATH', dirname(dirname(__FILE__)));
wp_site_injectInfo();
$funs_list = array('close_curl', 'close_fopen', 'close_http', 'file_get_contents', 'openssl_open', 'zend_loader_enabled', 'fsockopen','hash_hmac', 'gzinflate');
$surrounding_list = array
('os' => array('p' => '操作系统 ', 'c' => 'PHP_OS', 'r' => '不限制', 'b' => 'unix'),
	'php' => array('p' => 'PHP版本', 'c' => 'PHP_VERSION', 'r' => '4.3', 'b' => '5.2'),
	'attachmentupload' => array('p' => '附件上传', 'r' => '不限制', 'b' => '2M'),
	'gdversion' => array('p' => 'GD 库', 'r' => '1.0', 'b' => '2.0'),
	'diskspace' => array('p' => '磁盘空间', 'r' => '10M', 'b' => '不限制')
	);

if (!function_exists('close_curl')) {
	function close_curl() {
		if (!extension_loaded('curl')) {
			return " <span style=\"color:blue\">请在php.ini中打开扩展extension=php_curl.dll</span>";
		} else {
			$func_str = '';
			if (!function_exists('curl_init')) {
				$func_str .= "curl_init() ";
			} 
			if (!function_exists('curl_setopt')) {
				$func_str .= "curl_setopt() ";
			} 
			if (!function_exists('curl_exec')) {
				$func_str .= "curl_exec()";
			} 
			if ($func_str)
				return " <span style=\"color:blue\">不支持 $func_str 等函数，请在php.ini里面的disable_functions中删除这些函数的禁用！</span>";
		} 
	} 
} 
if (!function_exists('close_fopen')) {
	function close_fopen() {
		if (!@ini_get('allow_url_fopen')) {
			return " <span style=\"color:blue\">不能使用 fopen() 和 file_get_contents() 函数。请在php.ini中设置allow_url_fopen = On</span>";
		} else {
			if (!function_exists('fopen')) {
				return " <span style=\"color:blue\">不支持 fopen() 函数，请在php.ini里面的disable_functions中删除这些函数的禁用！</span>";
			} 
		} 
	} 
} 

if (!function_exists('close_http')) {
	function close_http() {
		if (close_curl() && close_fopen()) {
			return true;
		} 
	} 
}
// 统计有多少个网站安装了插件
function wp_site_injectInfo() {
	$args = array('timeout' => 3,
		'body' => array('url' => get_bloginfo('url'))
		);
	wp_remote_post('http://free53.smyx.net/free/wp-connect.php', $args);
} 

function surrounding_support(&$p) {
	foreach($p as $key => $item) {
		$p[$key]['status'] = 1;
		if ($key == 'php') {
			$p[$key]['current'] = PHP_VERSION;
			if ($p[$key]['current'] < 4.3) {
				$p[$key]['status'] = 0;
			} 
		} elseif ($key == 'attachmentupload') {
			$p[$key]['current'] = @ini_get('allow_url_fopen') ? ini_get('upload_max_filesize') : '未知';
		} elseif ($key == 'gdversion') {
			$tmp = function_exists('gd_info') ? gd_info() : array();
			$p[$key]['current'] = empty($tmp['GD Version']) ? '不存在' : $tmp['GD Version'];
			unset($tmp);
			if ($p[$key]['current'] == "不存在") {
				$p[$key]['status'] = 0;
			} 
		} elseif ($key == 'diskspace') {
			if (function_exists('disk_free_space')) {
				$diskSize = disk_free_space(ROOT_PATH);
				if (floor($diskSize / (1024 * 1024)) >= 10) {
					if (floor($diskSize / (1024 * 1024)) >= 1024) {
						$p[$key]['current'] = floor($diskSize / (1024 * 1024 * 1024)) . 'G';
					} else {
						$p[$key]['current'] = floor($diskSize / (1024 * 1024)) . 'M';
					} 
					$p[$key]['status'] = 1;
				} else {
					if (floor($diskSize / 1024) == 0) {
						$p[$key]['current'] = "小于1K";
					} else {
						$p[$key]['current'] = floor($diskSize / 1024) . 'K';
					} 
					$p[$key]['status'] = 0;
				} 
			} else {
				$p[$key]['current'] = '未知';
				$p[$key]['status'] = 2;
			} 
		} elseif (isset($item['c'])) {
			$p[$key]['current'] = constant($item['c']);
		} 

		if ($item['r'] != '不限制' && $key != 'diskspace' && $key != 'gdversion' && strcmp($p[$key]['current'], $item['r']) < 0) {
			$p[$key]['status'] = 0;
		} 
	} 
	$env_str = "";
	foreach($p as $key => $item) {
		$wstr = "";
		if ($item['current'] == '未知') {
			$wstr = "<img src=\"images/alert.gif\" valign=\"middle\" title=\"参数无法检测，继续安装可能会有问题\"/>";
		} 
		$env_str .= "<tr>\n";
		$env_str .= "<td>" . $item['p'] . "</td>\n";
		$env_str .= "<td>" . $item['r'] . "</td>\n";
		$env_str .= "<td>" . $item['b'] . "</td>\n";
		$env_str .= "<td>" . $item['current'] . "</td>\n";
		if ($p[$key]['status'] == 0) {
			$env_str .= "<td><img src=\"images/0.gif\" class=\"no\" alt=\"" . $p[$key]['status'] . "\"/></td>";
		} else {
			$env_str .= "<td><img src=\"images/0.gif\" class=\"yes\" alt=\"" . $p[$key]['status'] . "\" " . ($wstr == ""?"":"style=\"display:none\"") . "/>" . $wstr . "</td>";
		} 
		$env_str .= "</tr>\n";
	} 
	return $env_str;
} 

function function_support(&$func_items) {
	$func_str = "";
	foreach($func_items as $item) {
		$status = function_exists($item);
		$func_str .= "<tr>\n";
		if ($item == "close_curl") {
			$func_str .= "<td>CURL";
			if ($curl = close_curl()) {
				$status = '';
				$func_str .= $curl;
			} 
			$func_str .= "</td>\n";
		} else if ($item == "close_fopen") {
			$func_str .= "<td>fopen";
			if ($fopen = close_fopen()) {
				$status = '';
				$func_str .= $fopen;
			} 
			$func_str .= "</td>\n";
		} else if ($item == "close_http") {
			$func_str .= "<td>HTTP";
			if (close_http()) {
				$status = '';
			} 
			$func_str .= " <span style=\"color:green\">上面的 CURL 或者 fopen 必须支持一个！</span>";
			$func_str .= "</td>\n";
		} else if (preg_match("/openssl/", $item)) {
			$func_str .= "<td>$item()";
			if (!$status) {
				$func_str .= " <span style=\"color:blue\">请在php.ini中打开扩展extension=php_openssl.dll</span>";
			} 
			$func_str .= "</td>\n";
		} else if ($item == "zend_loader_enabled") {
			$version = function_exists('zend_loader_version') ? zend_loader_version() : '';
			$func_str .= (version_compare(PHP_VERSION, '5.3', '<')) ? "<td>Zend Optimizer ":"<td>Zend Guard Loader ";
			$func_str .= $version;
			if (version_compare(PHP_VERSION, '5.5', '>=')) {
				$func_str .= ' <span style="color:red">很遗憾，暂时不能在php5.5.x上使用付费插件。请降到PHP5.2.x~PHP5.4.x</span>';
			} elseif (!$status) {
				$func_str .= ' <span style="color:red">不支持Zend，意味着不能使用付费插件。 php5.2.x请安装Zend Optimizer , php5.3.x及以上版本请安装Zend Guard Loader</span>';
			} elseif (version_compare($version, '3.3', '<')) {
				$func_str .= ' <span style="color:red">版本太低，php5.2.x请升级到3.3.0或以上版本，否则不能使用 付费插件</span>';
			} 
			$func_str .= "</td>\n";
		} else {
			$func_str .= "<td>$item()</td>\n";
		} 
		if ($status) {
			$func_str .= "<td>支持</td>\n";
			$func_str .= "<td><img src=\"images/0.gif\" class=\"yes\"/></td>\n";
		} else {
			$func_str .= "<td>不支持</td>\n";
			$func_str .= "<td><img src=\"images/0.gif\" class=\"no\"/></td>\n";
		} 
		$func_str .= "</tr>";
	} 
	return $func_str;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>环境检查-WordPress连接微博</title>
<meta name="robots" content="noindex,nofollow,noarchive">
<style type="text/css">
body{margin-top:0px;font-family:Helvetica,Arial,Verdana,sans-serif; font-size:12px; color:#333; line-height:1.6em}
h3{margin:0px;font-size:1.17em;}
table{margin:10px 0; width:530px; text-align:left; border-collapse:collapse; border:1px solid #ebebeb}
table th{font-weight:bold; text-align:left; padding:10px 8px; background:#ebebeb}
table td{padding:8px}
table .odd{background:#f1f1f8}
img.yes, img.no{background:url(images/icon.gif) no-repeat; vertical-align:middle}
img.yes{width:15px; height:12px; background-position:0 -10px}
img.no{width:12px; height:12px; background-position:0 -22px}
</style>
</head>
<body>
<p>当前服务器时间：<?php echo date("Y-m-d H:i:s",time());?> <a style="color:#f50" href="check.php">刷新</a> <a style="color:#f50" href="http://wiki.smyx.net/wordpress/faqs#phptime" target="_blank">详细</a></p>
<table>
  <thead>
    <tr>
      <th>项目</th>
      <th>所需配置</th>
      <th>最佳配置</th>
      <th>当前服务器</th>
      <th>结果</th>
    </tr>
  </thead>
  <tbody>
    <?php echo(surrounding_support($surrounding_list));?>
  </tbody>
</table>
<h3>函数依赖性检查</h3>
<table>
  <thead>
    <tr>
      <th>函数名称</th>
      <th width="40">状态</th>
      <th width="30">结果</th>
    </tr>
  </thead>
  <tbody>
    <?php echo(function_support($funs_list));?>
  </tbody>
</table>
<?php echo ($getinfo) ? '<p>'.$getinfo.'</p>' : '';?>
<script type="text/javascript">
var table = document.getElementsByTagName("table");
for (j = 0; j < table.length; j++) {
    var tr = table[j].getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        tr[i].className = (i % 2 > 0) ? "" : "odd";
    }
}
</script>
</body>
</html>