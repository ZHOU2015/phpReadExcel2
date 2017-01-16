<?php defined('IN_DESTOON') or exit('Access Denied');?><html>
<body>
<link rel="stylesheet" href="<?php echo DT_SKIN;?>css/demo.css">
<div id="header">
<div id="kw" style="position: absolute;margin-top: 10%;margin-left: 20%;">
关键字:<select id="Select1" onchange="slt()">
<option value="-1">全部</option>
<?php foreach($arr as $k=>$v) {?>
<option value="<?php echo $k;?>"><?php echo $v;?></option>
<?php }?>
<input type="text" />
</select>
<input type="button" value="检索关键字"/>
</div>
<form id="formFile" action="" enctype="multipart/form-data" method="post" name="uploadfile">
<div style="width: 100%;">
<div>
<img src="<?php echo DT_SKIN;?>image/d1.png" alt="" style="margin-left: 20%;">
</div>
<div style="margin-top: 1em;margin-bottom: 2em;">
<div>
<input type="file" name="upfile" style="margin-left: 22%;"/>
</div>
<div>
<img src="<?php echo DT_SKIN;?>image/d4.jpg" alt="" style="height: 3em;margin-left: 40%;margin-top: -2em;">
</div>
<div>
<input id="upload" type="submit" value="提取数据" class="button"/>
</div>
</div>
</div>
</form>
</div>
<!--底部2结束-->
<script src="<?php echo DT_SKIN;?>js/jquery-1.7.1.min.js"> </script>
<script type="text/javascript">
$("#upload").click(function(){
$("#formFile").hide();
$("#mainTable").hide();
});
//onload = function() {
//var tables = document.getElementsByTagName("table");
//for ( var k = 0; k <= tables.length; k++) {
//var tab = tables[k];
//var rows = tab.rows.length;
//for ( var i = 0; i <= rows; i++) {
//var vtr = tab.getElementsByTagName("tr")[i];
////鼠标在上面时设置颜色
//vtr.onmouseover = function() {
//this.style.backgroundColor = "#99CCCC";
//}
//
////没有点击鼠标，鼠标离开颜色
//vtr.onmouseout = function() {
//this.style.backgroundColor = "";
//}
//}
//}
//}
</script>
<script type="text/javascript">
function slt()
{
var obj=document.getElementById("Select1");
var index = obj.selectedIndex; // 选中索引
//alert(obj.options[index].value);//这里就是取值
console.log(obj.options[index].value);
}
var oTab=document.getElementById("tab");
var oBt=document.getElementsByTagName("input");
oBt[1].onclick=function(){
for(var i=2;i<oTab.rows.length;i++)
{
var obj=document.getElementById("Select1");
var index = obj.selectedIndex; // 选中索引
var cl = obj.options[index].value;
console.log("cl is"+cl);
if(cl == -1){
console.log("all");
for(var k=0; k<oTab.rows.item(0).cells.length; k++){
var str1=oTab.rows[i].cells[k].innerHTML.toUpperCase();
var str2=oBt[0].value.toUpperCase();
console.log(str1);
console.log(str2);
/***********************************JS实现表格的模糊搜索*************************************/
//表格的模糊搜索的就是通过JS中的一个search()方法，使用格式，string1.search(string2);如果
//用户输入的字符串是其一个子串，就会返回该子串在主串的位置，不匹配则会返回-1，故操作如下
//if(str1.search(str2)!=-1){oTab.tBodies[0].rows[i].style.background='red';}
if(str2 == ""){
oTab.rows[i].cells[k].style.background='white';
}else{
if(str1.search(str2)!=-1){
oTab.rows[i].style.display='';
oTab.rows[i].cells[k].style.background='yellow';
break;
}
//else{oTab.tBodies[0].rows[i].style.background='';}
else{oTab.rows[i].style.display='none';}
}
}
}else{
var str1=oTab.rows[i].cells[cl].innerHTML.toUpperCase();
var str2=oBt[0].value.toUpperCase();
console.log(str1);
console.log(str2);
//使用string.toUpperCase()(将字符串中的字符全部转换成大写)或string.toLowerCase()(将字符串中的字符全部转换成小写)
//所谓忽略大小写的搜索就是将用户输入的字符串全部转换大写或小写，然后把信息表中的字符串的全部转换成大写或小写，最后再去比较两者转换后的字符就行了
/*******************************JS实现表格忽略大小写搜索*********************************/
if(str1==str2){
//oTab.tBodies[0].rows[i].style.background='red';
oTab.rows[i].style.display='';
}
else{
oTab.rows[i].style.display='none';
//oTab.tBodies[0].rows[i].style.background='';
}
/***********************************JS实现表格的模糊搜索*************************************/
//表格的模糊搜索的就是通过JS中的一个search()方法，使用格式，string1.search(string2);如果
//用户输入的字符串是其一个子串，就会返回该子串在主串的位置，不匹配则会返回-1，故操作如下
//if(str1.search(str2)!=-1){oTab.tBodies[0].rows[i].style.background='red';}
if(str1.search(str2)!=-1){
oTab.rows[i].style.display='';
if(str2 == ""){
oTab.rows[i].cells[cl].style.background='white';
}else{
oTab.rows[i].cells[cl].style.background='yellow';
}
}
//else{oTab.tBodies[0].rows[i].style.background='';}
else{oTab.rows[i].style.display='none';}
/***********************************JS实现表格的多关键字搜索********************************/
//表格的多关键字搜索，加入用户所输入的多个关键字之间用空格隔开，就用split方法把一个长字符串以空格为标准，分成一个字符串数组，
//然后以一个循环将切成的数组的子字符串与信息表中的字符串比较
var arr=str2.split(' ');
for(var j=0;j<arr.length;j++)
{
//if(str1.search(arr[j])!=-1){oTab.tBodies[0].rows[i].style.background='red';}
if(str1.search(arr[j])!=-1){oTab.rows[i].style.display='';}
}
}
}
}
//$("#tab").hide();
//$("#formFile").append(document.getElementById("kw"));
$("#formFile").append(document.getElementById("tab"));
</script>
</body>
<!--底部2 开始-->
</html>
<!--头部2 结束-->
