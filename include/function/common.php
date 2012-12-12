<?php
/* import other */
import('configure');
import('current');
import('rewrite');
import('utility');
import('mailer');
import('pay');
import('sms');
import('upgrade');
import('cron');
import('logger');
/**
 * 用于输出页面，，
 * 
 * @param $tFile 文件名 传入的文件名是显示内容的文件名
 * @param array $arr_var 参数  文件中所需的参数
 */
function template($tFile,$arr_var) {
	
	if(!is_array($arr_var)) 
		settype($arr_var,'array');
	ob_start();
	include_once (DIR_VIEWS ."/manage_html_header.php");//add header content
	include_once (DIR_VIEWS ."/manage_header.php");//add header content
	
 	$vFile = DIR_VIEWS . '/' . str_replace('/','_',$tFile) . '.php';
    if(false===file_exists($vFile)){
        die ("View File [$vFile] Not Found!");
    }
  	include_once($vFile);
  	include_once(DIR_VIEWS ."/manage_footer.php");//add footer content
  	include_once(DIR_VIEWS ."/manage_html_footer.php");//add footer content
  	
  	$content = ob_get_clean();	
    return  $content;	
}

function render($tFile, $vs=array()) {
    ob_start();
    foreach($GLOBALS AS $_k=>$_v) {
        ${$_k} = $_v;
    }
	foreach($vs AS $_k=>$_v) {
		${$_k} = $_v;
	}
	include template($tFile);
    return render_hook(ob_get_clean());
}

function render_hook($c) {
	global $INI;
	$c = preg_replace('#href="/#i', 'href="'.WEB_ROOT.'/', $c);
	$c = preg_replace('#src="/#i', 'src="'.WEB_ROOT.'/', $c);
	$c = preg_replace('#action="/#i', 'action="'.WEB_ROOT.'/', $c);

	/* theme */
	$page = strval($_SERVER['REQUEST_URI']);
	if($INI['skin']['theme'] && !preg_match('#/manage/#i',$page)) {
		$themedir = WWW_ROOT. '/static/theme/' . $INI['skin']['theme'];
		$checkfile = $themedir. '/css/index.css';
		if ( file_exists($checkfile) ) {
			$c = preg_replace('#/static/css/#', "/static/theme/{$INI['skin']['theme']}/css/", $c);
			$c = preg_replace('#/static/img/#', "/static/theme/{$INI['skin']['theme']}/img/", $c);
		}
	}
	$c = preg_replace('#([\'\=\"]+)/static/#', "$1{$INI['system']['cssprefix']}/static/", $c);
	if (strtolower(cookieget('locale','zh_cn'))=='zh_tw') {
		include_once(DIR_FUNCTION  . '/tradition.php');
		$c = str_replace(explode('|',$_charset_simple), explode('|',$_charset_tradition),$c);
	}
	/* encode id */
	$c = rewrite_hook($c);
	$c = obscure_rep($c);
	return $c;
}

function output_hook($c) {
	global $INI;
	if ( 0==abs(intval($INI['system']['gzip'])))  die($c);
	$HTTP_ACCEPT_ENCODING = $_SERVER["HTTP_ACCEPT_ENCODING"]; 
	if( strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false ) 
		$encoding = 'x-gzip'; 
	else if( strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false ) 
		$encoding = 'gzip'; 
	else $encoding == false;
	if (function_exists('gzencode')&&$encoding) {
		$c = gzencode($c);
		header("Content-Encoding: {$encoding}"); 
	}
	$length = strlen($c);
	header("Content-Length: {$length}");
	die($c);
}

$lang_properties = array();
function I($key) { 
    global $lang_properties, $LC;
    if (!$lang_properties) {
        $ini = DIR_ROOT . '/i18n/' . $LC. '/properties.ini';
        $lang_properties = Config::Instance($ini);
    }
    return isset($lang_properties[$key]) ?
        $lang_properties[$key] : $key;
}

function json($data, $type='eval') {
    $type = strtolower($type);
    $allow = array('eval','alert','updater','dialog','mix', 'refresh');
    if (false==in_array($type, $allow))
        return false;
    Output::Json(array( 'data' => $data, 'type' => $type,));
}

function redirect($url=null, $notice=null, $error=null) {
	$url = $url ? obscure_rep($url) : $_SERVER['HTTP_REFERER'];
	$url = $url ? $url : '/';
	if ($notice) Session::Set('notice', $notice);
	if ($error) Session::Set('error', $error);
    header("Location: {$url}");
    exit;
}
function write_php_file($array, $filename=null){
	$v = "<?php\r\n\$INI = ";
	$v .= var_export($array, true);
	$v .=";\r\n?>";
	return file_put_contents($filename, $v);
}

function write_ini_file($array, $filename=null){   
	$ok = null;   
	if ($filename) {
		$s =  ";;;;;;;;;;;;;;;;;;\r\n";
		$s .= ";; SYS_INIFILE\r\n";
		$s .= ";;;;;;;;;;;;;;;;;;\r\n";
	}
	foreach($array as $k=>$v) {   
		if(is_array($v))   { 
			if($k != $ok) {   
				$s  .=  "\r\n[{$k}]\r\n";
				$ok = $k;   
			} 
			$s .= write_ini_file($v);
		}else   {   
			if(trim($v) != $v || strstr($v,"["))
				$v = "\"{$v}\"";   
			$s .=  "$k = \"{$v}\"\r\n";
		} 
	}

	if(!$filename) return $s;   
	return file_put_contents($filename, $s);
}   

function save_config($type='ini') {
	return configure_save();
	global $INI; $q = ZSystem::GetSaveINI($INI);
	if ( strtoupper($type) == 'INI' ) {
		if (!is_writeable(SYS_INIFILE)) return false;
		return write_ini_file($q, SYS_INIFILE);
	} 
	if ( strtoupper($type) == 'PHP' ) {
		if (!is_writeable(SYS_PHPFILE)) return false;
		return write_php_file($q, SYS_PHPFILE);
	} 
	return false;
}

function save_system($ini) {
	$system = Table::Fetch('system', 1);
	$ini = ZSystem::GetUnsetINI($ini);
	$value = Utility::ExtraEncode($ini);
	$table = new Table('system', array('value'=>$value));
	if ( $system ) $table->SetPK('id', 1);
	return $table->update(array( 'value'));
}

/* user relative */
function need_login($wap=false) {
	if ( Session::Get('user_id')>0 ) {
		if (is_post()) {
			unset($_SESSION['loginpage']);
			unset($_SESSION['loginpagepost']);
		}
		return true;
	}
	return redirect(WEB_ROOT . '/manage/login.php');	
}

function need_post() {
	return is_post() ? true : redirect(WEB_ROOT . '/index.php');
}
function need_manager($super=false) {
	need_login();
	if ( ! is_manager() ) {
		echo template('manage_no_right');die();
	}
}
//验证
function is_advanced_user($super=false, $weak=false) {
	return Session::Get('user_grade') >  1 ;
}

function need_advanced_user($super=false) {
	if ( ! is_advanced_user() ) {
		echo template('manage_no_right');die();
	}
}

function need_open($b=true) {
	if (true===$b) {
		return true;
	}
	if ($AJAX) json('本功能未开放', 'alert');
	Session::Set('error', '你访问的功能页未开放');
	redirect( WEB_ROOT . '/index.php');
}

function need_auth($b=true) {
	global $AJAX, $INI, $login_user;
	if (is_string($b)) {
		$auths = $INI['authorization'][$login_user['id']];
		$bs = explode('|', $b);
		$b = is_manager(true); 
		if ($b) return true;
		foreach($bs AS $bo) if(!$b) $b = in_array($bo, $auths);
	}
	if (true===$b) {
		return true;
	}
	if ($AJAX) json('无权操作', 'alert');
	echo template('manage_no_right');die();
}

function is_manager($super=false, $weak=false) {
	if(Session::Get('user_grade') == 3){
		return true;
	}
	return false;
}

function is_newbie(){ return (cookieget('newbie')!='N'); }
function is_get() { return ! is_post(); }
function is_post() {
	return strtoupper($_SERVER['REQUEST_METHOD']) == 'POST';
}

function is_login() {
	return isset($_SESSION['user_id']);
}

function get_loginpage($default=null) {
	$loginpage = Session::Get('loginpage', true);
	if ($loginpage)  return $loginpage;
	if ($default) return $default;
	return WEB_ROOT . '/index.php';
}

function cookie_city($city) {
	global $hotcities;
	if($city) { 
		cookieset('city', $city['id']);
		return $city;
	} 
	$city_id = cookieget('city'); 
	$city = Table::Fetch('category', $city_id);
	if (!$city) $city = get_city();
	if (!$city) $city = array_shift($hotcities);
	if ($city) return cookie_city($city);
	return $city;
}

function ename_city($ename=null) {
	return DB::LimitQuery('category', array(
		'condition' => array(
			'zone' => 'city',
			'ename' => $ename,
		),
		'one' => true,
	));
}

function cookieset($k, $v, $expire=0) {
	$pre = substr(md5($_SERVER['HTTP_HOST']),0,4);
	$k = "{$pre}_{$k}";
	if ($expire==0) {
		$expire = time() + 365 * 86400;
	} else {
		$expire += time();
	}
	setCookie($k, $v, $expire, '/');
}

function cookieget($k, $default='') {
	$pre = substr(md5($_SERVER['HTTP_HOST']),0,4);
	$k = "{$pre}_{$k}";
	return isset($_COOKIE[$k]) ? strval($_COOKIE[$k]) : $default;
}

function moneyit($k) {
	return rtrim(rtrim(sprintf('%.2f',$k), '0'), '.');
}

function debug($v, $e=false) {
	global $login_user_id;
	if ($login_user_id==100000) {
		echo "<pre>";
		var_dump( $v);
		if($e) exit;
	}
}

function getparam($index=0, $default=0) {
	if (is_numeric($default)) {
		$v = abs(intval($_GET['param'][$index]));
	} else $v = strval($_GET['param'][$index]);
	return $v ? $v : $default;
}
function getpage() {
	$c = abs(intval($_GET['page']));
	return $c ? $c : 1;
}
function pagestring($count, $pagesize, $wap=false) {
	$p = new Pager($count, $pagesize, 'page');
	if ($wap) {
		return array($pagesize, $p->offset, $p->genWap());
	}
	return array($pagesize, $p->offset, $p->genBasic());
}

function uencode($u) {
	return base64_encode(urlEncode($u));
}
function udecode($u) {
	return urlDecode(base64_decode($u));
}

/* share link */
function share_renren($team) {
	global $login_user_id;
	global $INI;
	if ($team)  {
		$query = array(
				'link' => $INI['system']['wwwprefix'] . "/team.php?id={$team['id']}&r={$login_user_id}",
				'title' => $team['title'],
				);
	}
	else {
		$query = array(
				'link' => $INI['system']['wwwprefix'] . "/r.php?r={$login_user_id}",
				'title' => $INI['system']['sitename'] . '(' .$INI['system']['wwwprefix']. ')',
				);
	}

	$query = http_build_query($query);
	return 'http://share.renren.com/share/buttonshare.do?'.$query;
}

function share_kaixin($team) {
	global $login_user_id;
	global $INI;
	if ($team)  {
		$query = array(
				'rurl' => $INI['system']['wwwprefix'] . "/team.php?id={$team['id']}&r={$login_user_id}",
				'rtitle' => $team['title'],
				'rcontent' => strip_tags($team['summary']),
				);
	}
	else {
		$query = array(
				'rurl' => $INI['system']['wwwprefix'] . "/r.php?r={$login_user_id}",
				'rtitle' => $INI['system']['sitename'] . '(' .$INI['system']['wwwprefix']. ')',
				'rcontent' => $INI['system']['sitename'] . '(' .$INI['system']['wwwprefix']. ')',
				);
	}
	$query = http_build_query($query);
	return 'http://www.kaixin001.com/repaste/share.php?'.$query;
}

function share_douban($team) {
	global $login_user_id;
	global $INI;
	if ($team)  {
		$query = array(
				'url' => $INI['system']['wwwprefix'] . "/team.php?id={$team['id']}&r={$login_user_id}",
				'title' => $team['title'],
				);
	}
	else {
		$query = array(
				'url' => $INI['system']['wwwprefix'] . "/r.php?r={$login_user_id}",
				'title' => $INI['system']['sitename'] . '(' .$INI['system']['wwwprefix']. ')',
				);
	}
	$query = http_build_query($query);
	return 'http://www.douban.com/recommend/?'.$query;
}

function share_sina($team) {
	global $login_user_id;
	global $INI;
	if ($team)  {
		$query = array(
				'url' => $INI['system']['wwwprefix'] . "/team.php?id={$team['id']}&r={$login_user_id}",
				'title' => $team['title'],
				);
	}
	else {
		$query = array(
				'url' => $INI['system']['wwwprefix'] . "/r.php?r={$login_user_id}",
				'title' => $INI['system']['sitename'] . '(' .$INI['system']['wwwprefix']. ')',
				);
	}
	$query = http_build_query($query);
	return 'http://v.t.sina.com.cn/share/share.php?'.$query;
}

function share_mail($team) {
	global $login_user_id;
	global $INI;
	if (!$team) {
		$team = array(
				'title' => $INI['system']['sitename'] . '(' . $INI['system']['wwwprefix'] . ')',
				);
	}
	$pre[] = "发现一好网站--{$INI['system']['sitename']}，他们每天组织一次团购，超值！";
	if ( $team['id'] ) {
		$pre[] = "今天的团购是：{$team['title']}";
		$pre[] = "我想你会感兴趣的：";
		$pre[] = $INI['system']['wwwprefix'] . "/team.php?id={$team['id']}&r={$login_user_id}";
		$pre = mb_convert_encoding(join("\n\n", $pre), 'GBK', 'UTF-8');
		$sub = "有兴趣吗：{$team['title']}";
	} else {
		$sub = $pre[] = $team['title'];
	}
	$sub = mb_convert_encoding($sub, 'GBK', 'UTF-8');
	$query = array( 'subject' => $sub, 'body' => $pre, );
	$query = http_build_query($query);
	return 'mailto:?'.$query;
}

function domainit($url) {
	if(strpos($url,'//')) { preg_match('#[//]([^/]+)#', $url, $m);
} else { preg_match('#[//]?([^/]+)#', $url, $m); }
return $m[1];
}

// that the recursive feature on mkdir() is broken with PHP 5.0.4 for
function RecursiveMkdir($path) {
	if (!file_exists($path)) {
		RecursiveMkdir(dirname($path));
		@mkdir($path, 0777);
	}
}

function upload_image($input, $image=null, $type='team', $scale=false) {
	$year = date('Y'); $day = date('md'); $n = time().rand(1000,9999).'.jpg';
	$z = $_FILES[$input];
	if ($z && strpos($z['type'], 'image')===0 && $z['error']==0) {
		if (!$image) { 
			RecursiveMkdir( IMG_ROOT . '/' . "{$type}/{$year}/{$day}" );
			$image = "{$type}/{$year}/{$day}/{$n}";
			$path = IMG_ROOT . '/' . $image;
		} else {
			RecursiveMkdir( dirname(IMG_ROOT .'/' .$image) );
			$path = IMG_ROOT . '/' .$image;
		}
		if ($type=='user') {
			Image::Convert($z['tmp_name'], $path, 48, 48, Image::MODE_CUT);
		} 
		else if($type=='team') {
			move_uploaded_file($z['tmp_name'], $path);
		}
		if($type=='team' && $scale) {
			$npath = preg_replace('#(\d+)\.(\w+)$#', "\\1_index.\\2", $path); 
			Image::Convert($path, $npath, 200, 120, Image::MODE_CUT);
		}
		return $image;
	} 
	return $image;
}

function user_image($image=null) {
	global $INI;
	$image = $image ? $image : 'img/user-no-avatar.gif';
	return "/static/{$image}";
}

function team_image($image=null, $index=false) {
	global $INI;
	if (!$image) return null;
	if ($index) {
		$path = WWW_ROOT . '/static/' . $image;
		$image = preg_replace('#(\d+)\.(\w+)$#', "\\1_index.\\2", $image); 
		$dest = WWW_ROOT . '/static/' . $image;
		if (!file_exists($dest) && file_exists($path) ) {
			Image::Convert($path, $dest, 200, 120, Image::MODE_SCALE);
		}
	}
	return "{$INI['system']['imgprefix']}/static/{$image}";
}

function userreview($content) {
	$line = preg_split("/[\n\r]+/", $content, -1, PREG_SPLIT_NO_EMPTY);
	$r = '<ul>';
	foreach($line AS $one) {
		$c = explode('|', htmlspecialchars($one));
		$c[2] = $c[2] ? $c[2] : '/';
		$r .= "<li>{$c[0]}<span>－－<a href=\"{$c[2]}\" target=\"_blank\">{$c[1]}</a>";
		$r .= ($c[3] ? "（{$c[3]}）":'') . "</span></li>\n";
	}
	return $r.'</ul>';
}

function invite_state($invite) {
	if ('Y' == $invite['pay']) return '已返利';
	if ('C' == $invite['pay']) return '审核未通过';
	if ('N' == $invite['pay'] && $invite['buy_time']) return '待返利';
	if (time()-$invite['create_time']>7*86400) return '已过期';
	return '未购买';
}

function team_state(&$team) {
	if ( $team['now_number'] >= $team['min_number'] ) {
		if ($team['max_number']>0) {
			if ( $team['now_number']>=$team['max_number'] ){
				if ($team['close_time']==0) {
					$team['close_time'] = $team['end_time'];
				}
				return $team['state'] = 'soldout';
			}
		}
		if ( $team['end_time'] <= time() ) {
			$team['close_time'] = $team['end_time'];
		}
		return $team['state'] = 'success';
	} else {
		if ( $team['end_time'] <= time() ) {
			$team['close_time'] = $team['end_time'];
			return $team['state'] = 'failure';
		}
	}
	return $team['state'] = 'none';
}

function current_team($city_id=0) {
	$today = strtotime(date('Y-m-d'));
	$cond = array(
			'city_id' => array(0, abs(intval($city_id))),
			'team_type' => 'normal',
			"begin_time <= {$today}",
			"end_time > {$today}",
			);
	$order = 'ORDER BY sort_order DESC, begin_time DESC, id DESC';

	/* normal team */
	$team = DB::LimitQuery('team', array(
				'condition' => $cond,
				'one' => true,
				'order' => $order,
				));
	if ($team) return $team;

	/* seconds team */
	$cond['team_type'] = 'seconds';
	unset($cond['begin_time']);	
	$order = 'ORDER BY sort_order DESC, begin_time ASC, id DESC';
	$team = DB::LimitQuery('team', array(
				'condition' => $cond,
				'one' => true,
				'order' => $order,
				));

	return $team;
}

function state_explain($team, $error='false') {
	$state = team_state($team);
	$state = strtolower($state);
	switch($state) {
		case 'none': return '正在进行中';
		case 'soldout': return '已售光';
		case 'failure': if($error) return '团购失败';
		case 'success': return '团购成功';
		default: return '已结束';
	}
}

function get_zones($zone=null) {
	$zones = array(
			'city' => '城市列表',
			'group' => '项目分类',
			'public' => '讨论区分类',
			'grade' => '用户等级',
			'express' => '快递公司',
			'partner' => '商户分类',
			);
	if ( !$zone ) return $zones;
	if (!in_array($zone, array_keys($zones))) {
		$zone = 'city';
	}
	return array($zone, $zones[$zone]);
}

function down_xls($data, $keynames, $name='dataxls') {
	$xls[] = "<html><meta http-equiv=content-type content=\"text/html; charset=UTF-8\"><body><table border='1'>";
	$xls[] = "<tr><td>ID</td><td>" . implode("</td><td>", array_values($keynames)) . '</td></tr>';
	foreach($data As $o) {
		$line = array(++$index);
		foreach($keynames AS $k=>$v) {
			$line[] = $o[$k];
		}
		$xls[] = '<tr><td>'. implode("</td><td>", $line) . '</td></tr>';
	}
	$xls[] = '</table></body></html>';
	$xls = join("\r\n", $xls);
	header('Content-Disposition: attachment; filename="'.$name.'.xls"');
	die(mb_convert_encoding($xls,'UTF-8','UTF-8'));
}

function option_hotcategory($zone='city', $force=false, $all=false) {
	$cates = option_category($zone, $force, true);
	$r = array();
	foreach($cates AS $id=>$one) {
		if ('Y'==strtoupper($one['display'])) $r[$id] = $one;
	}
	return $all ? $r: Utility::OptionArray($r, 'id', 'name');
}

function option_category($zone='city', $force=false, $all=false) {
	$cache = $force ? 0 : 86400*30;
	$cates = DB::LimitQuery('category', array(
		'condition' => array( 'zone' => $zone, ),
		'order' => 'ORDER BY sort_order DESC, id DESC',
		'cache' => $cache,
	));
	$cates = Utility::AssColumn($cates, 'id');
	return $all ? $cates : Utility::OptionArray($cates, 'id', 'name');
}

function option_yes($n, $default=false) {
	global $INI;
	if (false==isset($INI['option'][$n])) return $default;
	$flag = trim(strval($INI['option'][$n]));
	return abs(intval($flag)) || strtoupper($flag) == 'Y';
}

function option_yesv($n, $default='N') {
	return option_yes($n, $default=='Y') ? 'Y' : 'N';
}

function magic_gpc($string) {
	if(SYS_MAGICGPC) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = magic_gpc($val);
			}
		} else {
			$string = stripslashes($string);
		}
	}
	return $string;
}

function team_discount($team, $save=false) {
	if ($team['market_price']<0 || $team['team_price']<0 ) {
		return '?';
	}
	return moneyit((10*$team['team_price']/$team['market_price']));
}

function team_origin($team, $quantity=0, $express_price = 0) {
	$origin = $quantity * $team['team_price'];
	if ($team['delivery'] == 'express'
			&& ($team['farefree']==0 || $quantity < $team['farefree'])
		) {
			$origin += $express_price;
		}
	return $origin;
}

function index_get_team($city_id) {
	global $INI;
	$multi = option_yes('indexmulti');
	if (!$multi) return current_team($city_id);
	$city_id = abs(intval($city_id));
	$now = time();
	$size = abs(intval($INI['system']['sideteam']));
	if ($size<=1) return current_team($city_id);

	$oc = array( 
			'city_id' => array($city_id, 0), 
			'team_type' => 'normal',
			"begin_time < '{$now}'",
			"end_time > '{$now}'",
			);
	$teams = DB::LimitQuery('team', array(
				'condition' => $oc,
				'order' => 'ORDER BY `sort_order` DESC, `id` DESC',
				'size' => $size,
				));
	if(count($teams) == 1) return array_pop($teams);
	return $teams;
}

function error_handler($errno, $errstr, $errfile, $errline) {
	switch ($errno) {
		case E_PARSE:
		case E_ERROR:
			echo "<b>Fatal ERROR</b> [$errno] $errstr<br />\n";
			echo "Fatal error on line $errline in file $errfile";
			echo "PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
			exit(1);
			break;
		default: break;
	}
	return true;
}
/* for obscureid */
function obscure_rep($u) {
	if(!option_yes('encodeid')) return $u;
	if(preg_match('#/manage/#', $_SERVER['REQUEST_URI'])) return $u;
	return preg_replace_callback('#(\?|&)id=(\d+)(\b)#i', obscure_cb, $u);
}
function obscure_did() {
	$gid = strval($_GET['id']);
	if ($gid && preg_match('/^ZT/', $gid)) {
		$id = base64_decode(substr($gid,2))>>2;
		if($id) $_GET['id'] = $id;
	}
}
function obscure_cb($m) {
	$eid = obscure_eid($m[2]);
	return "{$m[1]}id={$eid}{$m[3]}";
}
function obscure_eid($id) {
	return 'ZT'.base64_encode($id<<2);
}
obscure_did();
/* end */

/* for post trim */
function trimarray($o) {
	if (!is_array($o)) return trim($o);
	foreach($o AS $k=>$v) { $o[$k] = trimarray($v); }
	return $o;
}
$_POST = trimarray($_POST);
/* end */

/* verifycapctch */
function verify_captcha($reason='none', $rurl=null) {
	if (option_yes($reason, false)) {
		$v = strval($_REQUEST['vcaptcha']);
		if(!$v || !Utility::CaptchaCheck($v)) {
			Session::Set('error', '验证码不匹配，请重新输入');
			redirect($rurl);
		}
	}
	return true;
}

/*
 * 获得K3部门数据
 * return array
 */
function get_k3_data(){
	$arr_k3_d = array();
	$query_k3_d = "select number,name from k3_department;";
	$result_k3_d = DB::Query($query_k3_d);
	while($row = mysql_fetch_array($result_k3_d, MYSQL_ASSOC)){
		$arr_k3_d[$row['name']] = $row['number'];
	}
	
	return $arr_k3_d;
}

/*
 * 返回纠正后核算项目
 *
 */
function correct_hsxm($hsxm,$from_city_name,$arr_k3_d){
	if(!empty($hsxm)){
		$arr_temp = explode('---', $hsxm);
		
		if('全国'!=$arr_temp[2])
			$arr_temp[2] .= '站';

		if(!empty($arr_k3_d[$arr_temp[2]]))
			$arr_number = $arr_k3_d[$arr_temp[2]];
			
		if(is_null($arr_number))
			$arr_number = '000';
			
		if('全国'==$arr_temp[2]){
			if(empty($from_city_name))
				$from_city_name = '全国';
				
			return "$arr_temp[0]---".$arr_number."---$from_city_name";
		}else{
			return "$arr_temp[0]---".$arr_number."---$arr_temp[2]";
		}
	}
	return false;
}

	/**
	 * 从文件运行SQL脚本文件
	 *
	 * @uses $date_suffix 表名日期后缀，格式201107
	 * @param string $scriptlocation sql文件路径
	 */
	function run_sql_script($scriptlocation, $dblink, $date_suffix) {
		if ($script = file_get_contents($scriptlocation)) {
	
			$script = preg_replace('/\-\-.*\n/', '', $script);
			$sql_statements =  preg_split('/;[\n\r]+/', $script);
			foreach($sql_statements as $statement) {
				$statement = trim($statement);
				$statement = str_replace("date_suffix",$date_suffix,$statement);
				if (!empty($statement)) {
					$result = mysql_query($statement, $dblink);
				}
			}
			if($result){
				return true;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * 判断是否已经存在该月份支付宝数据表
	 * return 如果存在返回true，否则否会false
	 */
	function check_alipay_index_table($dblink, $date_suffix){
		$date_suffix = 'alipay_data_'.$date_suffix;
		$query = "show tables like '".$date_suffix."'";
	
		$result = mysql_query($query, $dblink);
		if($rows_size=mysql_num_rows($result)){
			return true;
		}else{
			return false;
		}
	}
	
	/*
	 * 转换日期格式 样例：2011年07月18日   转换成 2011-07-18
	 * return  转换后的日期
	 */
	function transformDate($date){
		if($date)
		{
			$date = str_replace('年', '-', $date);
			$date = str_replace('月', '-', $date);
			$date = str_replace('日', '', $date);
			$date = strtotime($date);
			return $date;
	    }else{
	    	return '';
	    }
	}

    /* 筛选空数据，空数据用-替换
     * return string
     */
    function check_value($value){
        if($value)
            return $value;
        else
            return '-';
    }
    
	/*
	 * 如果结束日期是跨月份日期，则设置结束日期为下月1日00:00:00
	 * return 调整后的结束日期
	 */
	function check_end_date($start_date, $end_date){
		
		if($start_date){
            $start_date_array = getdate(strtotime($start_date));
            $end_date_array = getdate(strtotime($end_date));
		
			if($start_date_array['year']==$end_date_array['year']){
				if($end_date_array['mon']>$start_date_array['mon']){
					$end_date = date('Y-m-', strtotime("+1 month", strtotime($start_date))).'01 00:00:00';
				}
			}else{
				return $start_date;
			}
		}
		
		return $end_date;
	}
	
	/*
	 * 获得凭证配置信息
	 * type 收入或者成本,  income、expend
	 */
	function get_voucher_config($type){
		if(!is_null($type)){
			if($type=='income'){
				$config_type = '1';
			}else if($type=='expend'){
				$config_type = '0';
			}
			$query = "select * from voucher_config where config_type='$config_type'";
			
			$result_voucher_config = DB::Query($query);
			return mysql_fetch_array($result_voucher_config, MYSQL_ASSOC);
		}
		return false;
	}
	
	function get_default_start_date($start_date){
		if(empty($start_date)){
			return date('Y-m-d', strtotime("-1 day", time()));
		}
		return $start_date;
	}
	
	function get_default_end_date($end_date){
		if(empty($end_date)){
			return date('Y-m-d', time());
		}
		return $end_date;
	}
	/*
	 * 根据给定的时间获得GMT标准时间
	 * $current_date 指定时间
	 * return GMT标准时间
	 */
	function get_gmt_date($current_date){
		if(!empty($current_date)){
			return date('Y-m-d H:i:s',strtotime("-8 hours", $current_date));
		}
		
		return false;
	}
	
	/*
	 * 根据GMT标准时间获得北京时间
	 * $current_date GMT标准时间
	 * return 北京时间
	 */
	function get_gmt_add_8_date($current_date){
		if(!empty($current_date)){
			return date('Y-m-d H:i:s',strtotime("8 hours", $current_date));
		}
		
		return false;
	}
	
	/**
	 *  以年月格式显示当前时间减一个月的时间，格式模板为:'201107'
	 *  return 年月格式的上个月信息
	 */
	function get_last_month_of_current_year(){
		$last_month_timestamp = strtotime('-1 month',time());
		$last_month_current_year = date('Ym',$last_month_timestamp);
		return $last_month_current_year;
	}
	
	/*
	 * 上传文件，支持单文件、20M、excel类型上传(application/vnd.openxmlformats-officedocument.spreadsheetml.sheet)
	 * $input_name input上传元素名称
	 * $upload_dir 上传路径
	 * $file_type 文件类型
	 * 
	 */
	function upload_file($input_name, $upload_dir){
		if(($_FILES[$input_name]["type"] == 'application/vnd.ms-excel'||$_FILES[$input_name]["type"] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')&&($_FILES[$input_name]["size"] < 20000000)){
			if ($_FILES[$input_name]["error"] > 0){
		    	return false;
		    }else{
			    if (file_exists($upload_dir . $_FILES[$input_name]["name"])){
			    	if(is_file($upload_dir . $_FILES[$input_name]["name"]))
			        	unlink($upload_dir . $_FILES[$input_name]["name"]);
			    }
			    if(move_uploaded_file($_FILES[$input_name]["tmp_name"], iconv("UTF-8","gb2312",$upload_dir . $_FILES[$input_name]["name"]))){
			    	return $upload_dir . $_FILES[$input_name]["name"];
			    	
			    }
			    return false;
		    }
		}else{
		   	return false;
		}
	}
	
	/*
	 * 补全城市名
	 * $city_name 城市名
	 */
	function get_full_city_name($city_name){
		$suffix = '市';
		if(!empty($city_name)&&$city_name!='全国'){
			if(!mb_strpos($city_name, $suffix)){
				$city_name .= $suffix;
			}
		}
		
		return $city_name;
	}
	
	/*
	 * excel日期转换函数
	 */
	function get_excel_value($cell){
		if($cell->getDataType()==PHPExcel_Cell_DataType::TYPE_NUMERIC){
		       $cellstyleformat=$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat();  
		       $formatcode=$cellstyleformat->getFormatCode();
		       if (preg_match('/^(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy]/i', $formatcode)) {  
		             $value=date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($value));  
		       }else{  
		             $value=$value = $cell->getValue();
		       }
		}else{
			$value = $cell->getValue();
		}
		
		return $value;
	}
	
	/*
	 * 获得区域枚举
	 * return array(区域ID=>区域名称,...)
	 */
	
	function get_region_option(){
		global $region;
		$query_region = "select id, name from region";
		if(empty($region)){
			$result_region = DB::Query($query_region);
			while ($row = mysql_fetch_array($result_region, MYSQL_ASSOC)) {
				$region[$row['id']] = $row['name'];
			}
		}
		
		return $region;
	}
	/*
	 * 获得城市枚举
	 * return array(城市ID=>城市名称 ,...)
	 */
	
	function get_city_option(){
		global $cities;
		$query_cities = "select id, name from city";
		if(empty($cities)){
			$result_cities = DB::Query($query_cities);
			while ($row = mysql_fetch_array($result_cities, MYSQL_ASSOC)) {
				$cities[$row['id']] = $row['name'];
			}
		}
		
		return $cities;
	}
	
	/*
	 * 通过区域ID获得城市列表
	 * $region_id 区域ID
	 * return array(城市ID=>城市名称 ,...)
	 */
	function get_cities_by_region_id($region_id){
	    global $cities_region;
		if(!is_null($region_id)){
			$condition = array('area_id'=>$region_id);
			if(empty($cities_region)){
				$result_cities = DB::GetTableRows('city', $condition);
				foreach($result_cities as $row) {
					$cities_region[$row['id']] = $row['name'];
				}
			}
			return $cities_region;
		}
		
		return false;
	}
	
	/*
	 * 通过区域ID获得城市数量
	 * $region_id 区域ID
	 * return int
	 */
	function get_count_city_by_region_id($region_id){
		if(!is_null($region_id)){
			$condition = array('area_id'=>$region_id);
			return Table::Count ('city', $condition);
		}
		
		return false;
	}

	/*
	 * 通过区域ID获得区域名称
	 */
	function get_region_name_by_id($region_id){
		if(!is_null($region_id)){
			global $region;
			if(empty($region)) $region = get_region_option();
			return $region[$region_id];
		}
		
		return false;	
	}
	/*
     * 通过区域ID获得城市名称
     */
    function get_city_name_by_id($city_id){
        if(!is_null($city_id)){
            global $cities;
            if(empty($cities)) $cities = get_city_option();
            return $cities[$city_id];
        }
        
        return false;   
    }
    
    /*
	 * 获得地址栏URL
	 */
	function get_url(){
		$url = trim(substr($_SERVER['REQUEST_URI'],strpos($_SERVER['REQUEST_URI'],'/manage')));
		$url = substr($url, 0, strpos($url,'.php')+4);
	
		return $url;
	}
	
	/**
	 * 获取所有二级菜单的id及url
	 * @return array('菜单id'=>'菜单url')
	 */
	function get_all_menu_url(){
		$menu_url = array();
		$sql = 'SELECT id,url FROM menu WHERE menu_grade = 2';
		$menu_infos = DB::GetQueryResult($sql,false);
		foreach($menu_infos as $menu_info){
			$menu_url[$menu_info['url']] = $menu_info['id'];
		}
		return $menu_url;
	}
	
set_error_handler('error_handler');
