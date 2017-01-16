<?php
defined('IN_DESTOON') or exit('Access Denied');
//if($_userid) dheader($MOD['linkurl']);
//require DT_ROOT.'/module/'.$module.'/common.inc.php';
if(is_uploaded_file($_FILES['upfile']['tmp_name'])){
	$upfile=$_FILES["upfile"];
//获取数组里面的值
	$name=$upfile["name"];//上传文件的文件名
	$type=$upfile["type"];//上传文件的类型
	$size=$upfile["size"];//上传文件的大小
	$tmp_name=$upfile["tmp_name"];//上传文件的临时存放路径
//判断是否为图片
	switch ($type){
		case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':$okType=true;
			break;
//		case 'image/jpeg':$okType=true;
//			break;
//		case 'image/gif':$okType=true;
//			break;
//		case 'image/png':$okType=true;
//			break;
		default:
			echo "default";
			break;
	}
	if($okType){
		/**
		 * 0:文件上传成功<br/>
		 * 1：超过了文件大小，在php.ini文件中设置<br/>
		 * 2：超过了文件的大小MAX_FILE_SIZE选项指定的值<br/>
		 * 3：文件只有部分被上传<br/>
		 * 4：没有文件被上传<br/>
		 * 5：上传文件大小为0
		 */
		$error=$upfile["error"];//上传后系统返回的值
//		echo "================<br/>";
//		echo "上传文件名称是：".$name."<br/>";
//		echo "上传文件类型是：".$type."<br/>";
//		echo "上传文件大小是：".$size."<br/>";
//		echo "上传后系统返回的值是：".$error."<br/>";
//		echo "上传文件的临时存放路径是：".$tmp_name."<br/>";
//
//		echo "开始移动上传文件<br/>";
//把上传的临时文件移动到up目录下面
		move_uploaded_file($tmp_name,'up/'.$name);
		$des = 'up/'.$name;
		//echo "done!";

		@set_time_limit(0);
		@ini_set("memory_limit", "1024M");
		@error_reporting(E_ALL);
		@date_default_timezone_set('Asia/ShangHai');
		if (!file_exists($des)) {
			exit("not found ".$des."\n");
		}

		/** Include PHPExcel_IOFactory */
		require_once DT_ROOT.'/module/sell/Classes/PHPExcel/IOFactory.php';
		$objPHPExcel = PHPExcel_IOFactory::load($des);
		$sheet = $objPHPExcel->getActiveSheet();  // 读取第一個工作表
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$highestColumn = $sheet->getHighestColumn();   // 取得总列数 A开始

		$arr = array();
		echo "<div id='mainTable' class='middleDiv'>";
		echo "<table class='table' id='tab'>";
		/** 循环读取每个单元格的数据 */
		for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
			echo "<tr>";
			for ($column = 'A'; $column <= $highestColumn; $column++) {//列数是以A列开始
				$dataset[] = $sheet->getCell($column.$row)->getValue();
				//echo $column.$row.":".$sheet->getCell($column.$row)->getValue()."<br />";
				echo "<td>".$sheet->getCell($column.$row)->getValue()."</td>";
				if($row == 1){
					$arr[] = $sheet->getCell($column.$row)->getValue();
				}
			}
			echo "</tr>";
			//echo "<br />";
		}
		echo "</table>";
		echo "</div>";

//		$destination="up/".$name;
//		echo "================<br/>";
//		echo "上传信息：<br/>";
//		if($error==0){
//			echo "文件上传成功啦！";
//			echo "<br>图片预览:<br>";
//			echo "<img src=".$destination.">";
////echo " alt=\"图片预览:\r文件名:".$destination."\r上传时间:\">";
//		}elseif ($error==1){
//			echo "超过了文件大小，在php.ini文件中设置";
//		}elseif ($error==2){
//			echo "超过了文件的大小MAX_FILE_SIZE选项指定的值";
//		}elseif ($error==3){
//			echo "文件只有部分被上传";
//		}elseif ($error==4){
//			echo "没有文件被上传";
//		}else{
//			echo "上传文件大小为0";
//		}
	}else{
		echo "请上传xlsx！";
	}

}
include template('demo', $module);
?>