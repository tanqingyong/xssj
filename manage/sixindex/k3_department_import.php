<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
	require_once(dirname(dirname(dirname(__FILE__))). '/include/library/DB.class.php');
	require_once(dirname(dirname(dirname(__FILE__))). '/include/library/Cache.class.php');
	require_once(dirname(dirname(dirname(__FILE__))). '/include/library/Config.class.php');
	require_once(dirname(dirname(dirname(__FILE__))). '/include/library/PHPExcel.php');
	require_once(dirname(dirname(dirname(__FILE__))). '/include/library/PHPExcel/Writer/Excel2007.php');
	
 	need_advanced_user();
	$insert = '';

	$upload_dir = dirname(dirname(dirname(__FILE__)))."/include/data/voucher/";
    if($full_file=upload_file('excel_file', $upload_dir)){
		$filePath = $full_file;
		$PHPExcel = new PHPExcel();
		$PHPReader = new PHPExcel_Reader_Excel2007();
		if(!$PHPReader->canRead($filePath)){
			$PHPReader = new PHPExcel_Reader_Excel5();
			if(!$PHPReader->canRead($filePath)){
				echo 'no Excel';
				return ;
			}
		}
		try{
			$PHPExcel = $PHPReader->load($filePath);
    	}catch(Exception $e){
    		if(!isset($PHPExcel)) return "无法解析文件";
    	}
		$currentSheet = $PHPExcel->getSheet(0);
		$allColumn = $currentSheet->getHighestColumn();
		$allRow = $currentSheet->getHighestRow();
	    error_log($currentSheet->getTitle());
	    //查询总条数
	    $sql_count = 'SELECT MAX(id) as max_id FROM six_indicators';
	    $result = DB::GetQueryResult($sql_count,true);
	    $max_id = $result['max_id'];
		if('Sheet1'==$currentSheet->getTitle()){
			for($currentRow = 2;$currentRow<=$allRow;$currentRow++){
				$max_id = $max_id+1;
				$indicator_date = get_excel_value($currentSheet->getCell("A$currentRow"));
				$region   = get_excel_value($currentSheet->getCell("B$currentRow"));
				$city = get_excel_value($currentSheet->getCell("C$currentRow"));
				$pre_sale_amount   = get_excel_value($currentSheet->getCell("D$currentRow"));
				$pre_positive_profile = get_excel_value($currentSheet->getCell("E$currentRow"));
				$pre_lose_profile   = get_excel_value($currentSheet->getCell("F$currentRow"));
				$pre_profile = get_excel_value($currentSheet->getCell("G$currentRow"));
				$pre_profile_rate   = get_excel_value($currentSheet->getCell("H$currentRow"));

				$insert .= "('$max_id','$indicator_date','$region','$city','$pre_sale_amount',
				'$pre_positive_profile','$pre_lose_profile','$pre_profile','$pre_profile_rate'),";
			}
			
			$insert = substr($insert, 0, strlen($insert)-1);
			$insert = "insert into six_indicators(id,indicator_date,region,city,pre_sale_amount,pre_positive_profile,pre_lose_profile,pre_profile,pre_profile_rate) values$insert;";

			if(DB::Delete("six_indicators",'indicator_date = 201204')){
				if(DB::Query($insert))
					$notice = '数据导入成功。';
			}else{
				$notice = '数据导入失败。';
			}
		}else{
			$notice = '文件格式不正确。';
		}
    }else{
		$notice = '上传文件失败,文件格式不正确。';
    }
	Session::Set('notice', $notice);
	redirect( WEB_ROOT . "/manage/sixindex/index_forecast.php");
?>