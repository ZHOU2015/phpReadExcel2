<?php
defined('IN_DESTOON') or exit('Access Denied');
#最新函数
/**
 * 根据用户ID取得用户信息
 */
function userinfoByid($userid, $cache = 1) {
	global $db, $dc, $CFG;
	if(!intval($userid)) return array();
	$user = array();
	if($cache && $CFG['db_expires']) {
		$user = $dc->get('user-'.$userid);
		if($user) return $user;
	}
	$user = $db->get_one("SELECT * FROM {$db->pre}member m, {$db->pre}company c WHERE m.userid=c.userid AND m.userid='$userid'");
	if($cache && $CFG['db_expires'] && $user) $dc->set('user-'.$userid, $user, $CFG['db_expires']);
	return $user;
}
/**
 * 取得分类名称
 */
function get_catname($catid){
	global $db;
	if (!$catid) {
		return '';
	}
	$res = $db->get_one("SELECT catname FROM {$db->pre}category WHERE catid = $catid");
	return $res['catname'];
}
/**
 * 取得分类的编码
 */
function get_cat_code($catid)
{
	global $db;
	$res = $db->get_one("SELECT bianhao FROM {$db->pre}category WHERE catid = $catid");
	return $res['bianhao'];
}
/**
 * 取得会员名称
 */
function get_username($userid){
	global $db;
	if (!$userid) {
		return '';
	}
	$res = $db->get_one("SELECT username FROM {$db->pre}member WHERE userid = $userid");
	return $res['username'];
}
/**
 * 取得会员名称
 */
function get_userid($username){
	global $db;
	if (!$username) {
		return '';
	}
	$res = $db->get_one("SELECT userid FROM {$db->pre}member WHERE username = '$username'");
	return $res['userid'];
}
/**
 * 取得分类的上级带有属性的分类
 */
function parent_attr_catid($catid){
	global $db;
	if (!$catid) {
		return 0;
	} 
	$res = $db->get_one("SELECT catid,is_field,parentid FROM {$db->pre}category WHERE catid = $catid");
	if (!$res['is_field']) {
		$catid = parent_attr_catid($res['parentid']);
	}
	return $catid;
}
/**
 * 获取分类的属性，本类没有，取得父类的属性值
 * catid int  
 */
function get_cat_attr($catid){
	global $db;
	if (!$catid) {
		return '';
	}
	//取得带有属性的上级分类
	$catid = parent_attr_catid($catid);
	if (!$catid) {
		return '';
	}

	$tb = 'attr_'.$catid;
	$res = $db->query("SELECT * FROM {$db->pre}fields WHERE tb = '$tb'");
	$fds = array();
	while ($r = $db->fetch_array($res)) {
		$fds[] = $r;
	}
	$html = fields_html('<td class="tl">', '<td>', '', $fds);

	//加上类属性
	$html2 = '';
	$tb = 'class_'.$catid;
	$res = $db->query("SELECT * FROM {$db->pre}fields WHERE tb = '$tb'");
	if ($db->num_rows($res)) {
		# 如果有类的属性 弄成表格
		$fdc = array();
		$html2 = '<tr><td></td><td><table><tr><th>操作</th>';
		while ($r = $db->fetch_array($res)) {
			$html2 .= '<th>'.$r['title'].'</th>';
			$fdc[] = $r;
		}
		$html2 .= '</tr><tr><td class="class_row"><a href="javascript:add_row();">+</a></td>';
		foreach ($fdc as $k => $v) {
			$html2 .= '<td><input type="text" name="post_fields['.$v['name'].'][]" value="" '.$v['addition'].'/></td>';
		}

		$html2 .= '</tr></table></td></tr>';
		$html .= $html2; 
	}
	

	if ($html) {
		$html .= '<input type="hidden" name="attr_catid" value="'.$catid.'">';
	}
	return $html;
}

/**
 * 取得属性表
 */
function get_attr_table($catid){
	global $db;
	// $tb = $db->pre.'attr_'.$catid;
	$tb = $db->pre.'attr_'.get_top_parentid($catid);
	$have = is_have_table($tb);
	if ($have) {
		return $tb;
	}else{
		return false;
	}
}

/**
 * 取得属性表
 */
function get_class_table($catid){
	global $db;
	$tb = $db->pre.'class_'.$catid;
	$have = is_have_table($tb);
	if ($have) {
		return $tb;
	}else{
		return false;
	}
}
/**
 * 取得特征表
 */
function get_feature_table($catid){
	global $db;
	// $tb = $db->pre.'feature_'.$catid;
	$tb = $db->pre.'feature_'.get_top_parentid($catid);
	$have = is_have_table($tb);
	if ($have) {
		return $tb;
	}else{
		return false;
	}
}
/**
 * 判断表是否存在 
 */
function is_have_table($attr_table){
	global $db;
	return $db->num_rows($db->query("SHOW TABLES LIKE '{$attr_table}' "));
}

/**
 * 取属性表列表
 */
function get_attr_list($attr_catid, $itemid){
	global $db;
	if (!intval($itemid)) {
		return array();
	}
	// $tb = get_attr_table($attr_catid);
	$tb = 'attr_'.get_top_parentid($attr_catid);
	if (!$tb) {
		return array();
	}
	$cat_info = get_cat($attr_catid);
	if (!$cat_info['parentid']) {
		return array();
	}
	$arrparentid = substr($cat_info['arrparentid'], 2).','.$attr_catid;
	$res = $db->query("SELECT name FROM {$db->pre}fields WHERE tb = '$tb' AND catid IN ($arrparentid)");
	$fields = '';
	while ($r = $db->fetch_array($res)) {
		$fields .= '`'.$r['name'].'`,';
	}
	$fields = rtrim($fields, ','); 
	if (!$fields) {
		return array();
	}
	$res = $db->get_one("SELECT $fields FROM {$db->pre}{$tb} WHERE itemid = $itemid");
	// $data = array();
	// while ($r = $db->fetch_array($res)) {
	// 	$data[] = $r;
	// }
	return $res;
}

/**
 * 取属性类表列表
 */
function get_class_list($attr_catid, $itemid){
	global $db;
	if (!intval($itemid)) {
		return array();
	}
	// $tb = get_class_table($attr_catid);
	$tb = 'class_'.get_top_parentid($attr_catid);
	if (!$tb) {
		return array();
	}
	$cat_info = get_cat($attr_catid);
	if (!$cat_info['parentid']) {
		return array();
	}
	$arrparentid = substr($cat_info['arrparentid'], 2).','.$attr_catid;
	// 取类属性ID
	$res = $db->query("SELECT itemid FROM {$db->pre}fields WHERE tb = '$tb' AND type = 'class' AND catid IN ($arrparentid)");
	$class_itemid = '';
	while ($r = $db->fetch_array($res)) {
		$class_itemid .= $r['itemid'].',';
	}
	$class_itemid = rtrim($class_itemid,',');
	if (!$class_itemid) {
		return array();
	} 
	// 取字段名
	$res = $db->query("SELECT name FROM {$db->pre}fields_class WHERE itemid IN($class_itemid)");
	$fields = '';
	while ($r = $db->fetch_array($res)) {
		$fields .= $r['name'].',';
	}
	$fields = rtrim($fields, ','); 
	if (!$fields) {
		return array();
	}
	// 取数据
	$res = $db->query("SELECT $fields FROM {$db->pre}{$tb} WHERE itemid = $itemid");
	$data = array();
	while ($r = $db->fetch_array($res)) {
		$data[] = $r;
	}
	return $data;
}

/**
 * 判断字段是不是类属性
 */
function is_class_fields($tb, $name){
	global $db;
	if (!$tb || !$name) {
		return 0;
	}
	$tb = str_replace($db->pre, '', $tb);
	$res = $db->get_one("SELECT type FROM {$db->pre}fields WHERE tb = '$tb' AND name = '$name'");
	if ($res['type'] == 'class') {
		return 1;
	}else{
		return 0;
	}
}

/*更新特征值表*/
function feature_update($post_fields, $table, $itemid, $keyname = 'itemid', $fd = array()){	
	global $FD, $db; 
	if(!$table || !$itemid) return '';
	if($fd) $FD = $fd;
	$sql = "INSERT INTO {$table} (`itemid`, `attr`, `value`, `unit`) VALUES ";
	$sql2 = '';
	foreach($FD as $k=>$v) {
		if ($v['name'] && $post_fields[$v['name']]['value']) {					
			$mk = $v['name'];
			if ($post_fields[$mk]['value']) {
				# 如果存在特征值，则分解成数组
				$mv_arr = explode('|', $post_fields[$mk]['value']); 
				foreach ($mv_arr as $key => $value) { 
					$unit = $post_fields[$v['name']]['unit'];
					if (intval($value)) $sql2 .= "('$itemid','$mk','$value','$unit'),";
				}
			}			
		}
	}
	$sql2 = rtrim($sql2, ','); 
	if ($sql2) {
		$sql = $sql.$sql2;
		# 如果是单独的表，则先删除所有的，再加上新的
		$db->query("DELETE FROM {$table} WHERE itemid = $itemid");		
		if($sql) $db->query($sql);
	}
	
	return 1;
}

/**
 * 历史浏览记录
 * $data 记录信息
 */
function add_history($data){ 
  global $db;
  $userid = $data['userid'];
  if(!is_array($data) || empty($data)){  //如果不是数据直接返回
  	return true;
  }
  $ok = $db->query("SELECT count(*) as num FROM {$db->pre}history WHERE linkurl = '{$data['linkurl']}' AND userid = '$userid'");
  if($ok['num']){ //如果已经有此记录，直接返回
  	return true;
  }
  $sql = "REPLACE INTO {$db->pre}history (userid,mid,itemid,title,linkurl,times) VALUES ('$userid','$data[mid]','$data[itemid]','$data[title]','$data[linkurl]','$data[times]')"; 
  $db->query($sql);
  $num = $db->query("SELECT count(*) as num FROM {$db->pre}history WHERE userid = '$userid'");
  if($num['num'] > 100){//如果记录数据大于100，则删除前90条。
  	$sql = "DELETE FROM {$db->pre}history WHERE userid = '$userid' ORDER BY times ASC LIMIT 80";
  	$db->query($sql);
  }
  return true;
}

/**
 * 调取历史浏览记录
 * $userid 用户ID
 */
function read_history($userid, $limit = 10, $order = 'times DESC'){ 
  global $db;
  if(!$userid){  //如果没有用户，则直接返回
  	return array();
  }
  $sql = "SELECT * FROM {$db->pre}history WHERE userid = '$userid' ORDER BY $order LIMIT $limit"; 
  $res = $db->query($sql);
  $history_list = array();
  while($r = $db->fetch_array($res)) {
  		$table = get_table($r['mid']);
  		$da = get_item_info($r['itemid'], $r['mid']);
  		if ($da) {
  			$r['thumb'] = $da['thumb']; 
  			$r['xinghao'] = $da['xinghao']; 
  			$r['price'] = $da['price']; 
  		}else{
  			continue;
  		}
		$history_list[] = $r;
  }
  return $history_list;
}

/**
 * 调取某个栏目的所有内容列表 （itemid, title)
 */
function get_all_mid($mid = 0, $userid = 0){
	global $db;
	$lists = array();
	if (!intval($mid)) {
		return $lists; 
	}
	$table = get_table($mid);
	$itemids = '';
	$w = '';
	if ($userid) {
		$res = $db->query("SELECT itemid FROM {$db->pre}brand_user WHERE userid = $userid");
		while ($r = $db->fetch_array($res)) {
			$itemids .= $r['itemid'].',';
		}
		$itemids = rtrim($itemids, ',');
		$w .= " AND itemid in ($itemids)";
	}
	
	$res = $db->query("SELECT itemid, title FROM {$table} WHERE status = 3 $w ORDER BY itemid desc");
	while ($r = $db->fetch_array($res)) {
		$lists[] = $r;
	}
	return $lists;
}

/**
 * 调取某个栏目的所有内容列表 （itemid, title)
 */
function get_item_info($itemid, $mid = 0, $w = 'status=3'){
	global $db;
	$item = array();
	if (!intval($mid) && !intval($itemid)) {
		return $item; 
	}
	$table = get_table($mid);
	$item = $db->get_one("SELECT * FROM {$table} WHERE $w AND itemid = $itemid");
	$item['thumb'] = $item['thumb'] ? $item['thumb'] : DT_SKIN.'image/nopic150.gif';
	return $item;
}

/**
 * 调取某个栏目的所有内容列表 （itemid, title)
 */
function get_item_field($field, $mid = 0, $w = 'status=3'){
	global $db;
	if (!intval($mid) && !intval($field)) {
		return ''; 
	}
	$table = get_table($mid);
	$item = $db->get_one("SELECT `$field` FROM {$table} WHERE $w");
	if ($field == 'thumb') {
		$item['thumb'] = $item['thumb'] ? $item['thumb'] : DT_SKIN.'image/nopic150.gif';
	}
	return $item[$field];
}

/**
 * 取得详细信息
 */
function get_content($itemid, $mid)
{
	global $db;
	if (!intval($itemid) || !intval($mid)) {
		return '';
	}
	$table = get_table($mid,1);
	$res = $db->get_one("SELECT content FROM {$table} WHERE itemid = $itemid");
	return $res['content'];
}
/**
 * 取得某个厂商的所有品牌列表
 */
function get_changshang_brand($itemid){
	global $db;
	$lists = array();
	if (!intval($mid)) {
		return $lists; 
	}
	$table = get_table($mid);
	$res = $db->query("SELECT itemid, title FROM {$table} WHERE status = 3 ORDER BY itemid desc");
	while ($r = $db->fetch_array($res)) {
		$lists[] = $r;
	}
	return $lists;
}

/*取得品牌名称*/
function get_brand_name($brandid, $mid = 13){
	global $db;
	$table = get_table($mid);
	if (!$table || !$brandid) {
		return '';
	}
	$res = $db->get_one("SELECT title FROM {$table} WHERE itemid = $brandid");
	return $res['title'];
}

/*根据品牌取生产厂商的名称和链接*/
function get_changshang_name($brandid = ''){
	global $db;
	if (!intval($brandid)) {
		return '';
	}
	$r = $db->get_one("SELECT changshang FROM {$db->pre}brand_13 WHERE itemid = $brandid");
	if (!$r){
		return '';
	}

	$res = $db->get_one("SELECT title, linkurl FROM {$db->pre}brand_23 WHERE itemid = ".$r['changshang']);
	$html = '<a href="/firms/'.$res['linkurl'].'" target="_blank" title="'.$res['title'].'">'.$res['title'].'</a>';
	return $html;
}

/*根据品牌取生产厂商的名称 或 itmeid */
function get_changshang_by_brandid($brandid = '', $field='title'){
	global $db;
	if (!intval($brandid)) {
		return '';
	}
	$r = $db->get_one("SELECT changshang FROM {$db->pre}brand_13 WHERE itemid = $brandid");
	if (!$r){
		return '';
	}
	if ($field == 'itemid') {
		return $r['changshang'];
	}

	$res = $db->get_one("SELECT $field FROM {$db->pre}brand_23 WHERE itemid = ".$r['changshang']);
	return $res[$field];
}

/**
 *  企业是否已经认证
 */
function is_renzheng($userid){
	global $db;
	if (!$userid) {
		return false;
	}
	$r1 = $db->get_one("SELECT `validated` FROM {$db->pre}company WHERE userid = $userid");
	$r2 = $db->get_one("SELECT `vcompany` FROM {$db->pre}member WHERE userid = $userid");
	if ($r1['validated'] && $r2['vcompany']) {
		return true;
	}else{
		return false;
	}
}

/**
 * 是否存在某个字母索引的公司
 */
function is_have_zimu($zimu, $mid = 4){
	global $db; 
	if (!$zimu) {
		return 0;
	}
	if ($mid == 4) {
		$n = $db->get_one("SELECT count(*) as num FROM {$db->pre}company WHERE groupid in (6,7) AND capital > 0 AND validated = 1 AND zimu = '{$zimu}'");
		return $n['num'];
	}else{
		$table = get_table($mid);
		$n = $db->get_one("SELECT count(*) as num FROM {$table} WHERE status = 3 AND zimu = '{$zimu}'");
		return $n['num'];
	}
	
}

/* 判断是不是用户的推荐产品 */
function is_elite($userid, $itemid, $yxid, $mid = 5){
	global $db;
	if (!intval($userid) || !intval($itemid) || !intval($yxid)) {
		return '';
	}
	if ($mid == 5) {
		$table = $db->pre.'sell_yingxiao';
	}
	$r = $db->get_one("SELECT elite FROM {$table} WHERE userid = $userid AND itemid = $itemid AND yxid = $yxid"); 
	return $r['elite'];
}

/*取得商铺的名称*/
function get_shop_name($username){
	global $db;
	if (!$username) {
		return '';
	}
	$res = $db->get_one("SELECT shopname FROM {$db->pre}shop WHERE username = '$username'");
	return $res['shopname'];
}
/*根据商品ID取产品型号*/
function get_sell_xinghao_mall($itemid){
	global $db;
	$xinghao = '';
	if (!$itemid) {
		return $xinghao;
	}
	$res = $db->get_one("SELECT sellid FROM {$db->pre}mall WHERE itemid = '$itemid'");
	if (!$res['sellid']) {
		return $xinghao;
	}
	return get_sell_xinghao($res['sellid']);
}
/*根据产品ID取产品型号*/
function get_sell_xinghao($itemid){
	global $db;
	$xinghao = '';
	if (!$itemid) {
		return $xinghao;
	}
	$res = $db->get_one("SELECT xinghao FROM {$db->pre}sell_5 WHERE itemid = '$itemid'");
	return $res['xinghao'];
}

/*取用户保证金*/
function get_deposit($username){
	global $db;
	if (!$username) {
		return 0;
	} 
	$r = $db->get_one("SELECT deposit FROM {$db->pre}member WHERE username = '$username'");
	return $r['deposit'];
}
/*隐藏手机中间四位数字*/
function hidtel($phone){
    $IsWhat = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i',$phone); //固定电话
    if($IsWhat == 1){
        return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i','$1****$2',$phone);
    }else{
        return  preg_replace('/(1[345678]{1}[0-9])[0-9]{4}([0-9]{4})/i','$1****$2',$phone);
    }
}
/*订单生成规则 生成订单号 规则  年月日秒分毫秒 + 3位随机数*/
function make_order_id() {
	global $db;
	list($tmp1, $tmp2) = explode(' ', microtime());
	$times = date('YmdHis');
	$times .= str_pad(sprintf('%.0f', (floatval($tmp1)) * 1000), 3, '0',STR_PAD_RIGHT);
	$order_id = $times;
	// $order_id = $times.random(3, $chars = '0123456789');
	// $order_id = str_pad($order_id, 14, '3',STR_PAD_RIGHT);
	$res = $db->get_one("SELECT count(*) as num FROM {$db->pre}mall_order WHERE orderid = '$order_id'");
	if ($res['numb'] > 0) {
		return make_order_id();
	}
	return $order_id;
}
/*取得某产品的平均价格，此产品如果没有会员上线，则显示暂无*/
function get_avg_price($itemid){
	global $db;
	$table = get_table(16);
	$res = $db->get_one("SELECT AVG(price) as price FROM {$table} WHERE sellid = $itemid");
	return $res['price'] > 0 ? '￥'.sprintf("%.2f", $res['price']) : '暂无';
}
/*判断会员是否有此商品销售  返回此产品在商城的ID*/
function is_have_shop($itemid, $username){
	global $db;
	if (!$itemid || !$username) {
		return $itemid;
	}
	$table = get_table(16);
	$res = $db->get_one("SELECT itemid FROM {$table} WHERE sellid=$itemid AND username = '$username'");
	return $res['itemid'];
}

/**
 * 判断是不是最终分类  返回is_field
 */
function is_last_cat($catid){
	global $db;
	if (!$catid) {
		return 0;
	}
	$res = get_cat($catid);
	return $res['is_field'];
}
/**
 * 取顶级父栏目ID
 */
function get_top_parentid($catid){
	global $db;
	$cinfo = get_cat($catid);
	if ($cinfo['parentid'] == 0) {
		return $catid;
	}else{
		return get_top_parentid($cinfo['parentid']);
	}
}
/**
 * 是不是可以推送信息的用户
 */
function is_down_user($userid){
	global $db, $_userid;
	$userid = $userid ? $userid : $_userid;
	if (!$userid) {
		return false;
	}
	$res = $db->get_one("SELECT istuisong FROM {$db->pre}company WHERE userid = $userid");
	return $res['istuisong'];
}

/**
 * 获取用户的单位名称 department 取的部门字段
 */
function get_user_danwei($userid){
	global $db;
	if (!$userid) {
		return '';
	}
	$res = $db->get_one("SELECT department FROM {$db->pre}member WHERE userid = $userid");
	return $res['department'];
}

// 取分类的上级树
function cat_pos2($catid, $str = ' &raquo; ') {
	global $db;
	if(!$catid) return '';
	$cat_info = get_cat($catid);
	$arrparentids = $cat_info['arrparentid'].','.$cat_info['catid'];
	$arrparentid = explode(',', $arrparentids);
	$pos = '';
	$CATEGORY = array();
	$result = $db->query("SELECT catid,moduleid,catname,linkurl FROM {$db->pre}category WHERE catid IN ($arrparentids)", 'CACHE');
	while($r = $db->fetch_array($result)) {
		$CATEGORY[$r['catid']] = $r;
	}
	foreach($arrparentid as $catid) {
		if(!$catid || !isset($CATEGORY[$catid])) continue;
		$pos .= $CATEGORY[$catid]['catname'].$str;
	}
	$_len = strlen($str);
	if($str && substr($pos, -$_len, $_len) === $str) $pos = substr($pos, 0, strlen($pos)-$_len);
	return $pos;
}

// 取产品字段属性的分类名称，根据分类ID
function get_field_typename($typeid){
	global $db;
	if (!$typeid) {
		return '';
	}
	$res = $db->get_one("SELECT typename FROM {$db->pre}fields_type WHERE typeid = $typeid");
	return $res['typename'];
}

//读取分类所有字段
function get_maincat_all($catid, $moduleid, $level = -1, $is_shop = 0, $is_sell = 0) {
	global $db;
	$condition = $catid ? "parentid=$catid" : "moduleid=$moduleid AND parentid=0";
	$condition .= ' and status = 0';
	if($level >= 0) $condition .= " AND level=$level";
	if ($is_shop)  $condition .= " AND is_shop = $is_shop";
	if ($is_sell)  $condition .= " AND is_sell = $is_sell";
	$cat = array();
	$result = $db->query("SELECT * FROM {$db->pre}category WHERE $condition ORDER BY listorder,catid ASC", 'CACHE');
	while($r = $db->fetch_array($result)) {
		$cat[] = $r;
	}
	return $cat;
}
?>