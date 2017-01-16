<?php defined('IN_DESTOON') or exit('Access Denied');?><div class="footer2">
<div class="m">
<div class="tac">
<?php $cont = tag("table=webpage&condition=item=4&areaid=$cityid&order=listorder desc,itemid desc&template=null");?>
<?php if(is_array($cont)) { foreach($cont as $k => $t) { ?>
<?php if($k) { ?><i>|</i><?php } ?>
<a href="<?php if($t['domain']) { ?><?php echo $t['domain'];?><?php } else { ?><?php echo linkurl($t['linkurl'], 1);?><?php } ?>
" target="_blank"><?php echo $t['title'];?></a>
<?php } } ?>
<?php $cont = tag("table=webpage&condition=item=1&areaid=$cityid&order=listorder desc,itemid desc&template=null");?>
<?php if(is_array($cont)) { foreach($cont as $k => $t) { ?>
<i>|</i><a href="<?php if($t['domain']) { ?><?php echo $t['domain'];?><?php } else { ?><?php echo linkurl($t['linkurl'], 1);?><?php } ?>
" target="_blank"><?php echo $t['title'];?></a>
<?php } } ?>
</div>
<p><?php echo $DT['copyright'];?><a href="http://www.miitbeian.gov.cn" target="_blank"><?php echo $DT['icpno'];?></a></p>
</div>
</div>
<!--底部2结束-->