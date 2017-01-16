<?php defined('IN_DESTOON') or exit('Access Denied');?><?php include template('header-reg');?>
<!--头部2 结束-->
<div class="reg_m">
<div class="m">
<div class="login_box">
<div class="login_box_form">
<form method="post" action="<?php echo $DT['file_login'];?>" onsubmit="return Dcheck();">
<input name="forward" type="hidden" value="<?php echo $forward;?>"/>
<input name="auth" type="hidden" value="<?php echo $auth;?>"/>
<ul>
<li>
<label>用3户名</label>
<p style="display:none;"><select name="option">
<option value="username" selected>用户名</option>
</select></p>
  <input name="username" type="text" id="username" value="<?php echo $username;?>" placeholder="请输入您的用户名">
  <span class="uuser"></span>
</li>
<li>
<label>密码</label>
  <input type="password" name="password" id="password" <?php if(isset($password)) { ?> value="<?php echo $password;?>"<?php } ?>
 placeholder="请输入密码">
  <span class="unlock"></span>
</li>
<?php if($MOD['captcha_login']) { ?>
<li>
<label>验证码：</label>
<?php include template('captcha', 'chip');?>
</li>
<?php } ?>
<li class="checkboxs clearfix">
<div class="fl">
<input type="checkbox" name="cookietime" value="1" id="cookietime" checked><label>记住用户名</label>
</div>
<div class="rembero fl">
<a href="send.php">忘记密码？</a><i>|</i><a href="<?php echo $MODULE['2']['linkurl'];?><?php echo $DT['file_register'];?>">免费注册</a>
</div>
</li>
<li><input type="submit" name="submit" value="登 录"></li>
</ul>
</form>
</div>
<?php if($oa) { ?>
<div class="other_lg">
<div class="other_lg_t">
<h4>使用合作账号登录</h4>
</div>
<ul class="other_lg_box clearfix">
<?php if(is_array($OAUTH)) { foreach($OAUTH as $k => $v) { ?>
<?php if($v['enable']) { ?>
<li><a href="<?php echo DT_PATH;?>api/oauth/<?php echo $k;?>/connect.php" title="<?php echo $v['name'];?>">
<img src="<?php echo DT_PATH;?>api/oauth/<?php echo $k;?>/ico.png" class="fimg"/>
<img src="<?php echo DT_PATH;?>api/oauth/<?php echo $k;?>/ico_hover.png" alt="" class="limg"/>
</a></li>
<?php } ?>
<?php } } ?>
</ul>
</div>
<?php } ?>
</div>
</div>
</div>
<!--底部2 开始-->
<?php include template('footer-reg');?>
</body>
</html>