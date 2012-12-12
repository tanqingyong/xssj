<?php
require_once(dirname(dirname(__FILE__)). '/app.php');
require_once(dirname(dirname(__FILE__)). '/include/library/DB.class.php');
require_once(dirname(dirname(__FILE__)). '/include/library/Cache.class.php');
require_once(dirname(dirname(__FILE__)). '/include/library/Config.class.php');
require_once(dirname(dirname(__FILE__)). '/include/function/utility.php');
function export_excel($datas=NULL,$table_array,$data_field_array=array(),$excel_name){
    if(!$table_array || !$excel_name || !$datas){
        return false;
    }
    header ( 'Content-Type: application/vnd.ms-excel' );
    get_filename_for_http_header($excel_name.".csv");
    header ( 'Cache-Control: max-age=0' );
    $fp = fopen ( 'php://output', 'a' );
    foreach( $table_array as $key=>$value ){
         $table_array [$key] = iconv ( 'utf-8', 'gbk', $value );
    }
    fputcsv ( $fp, $table_array );
    $cnt = 0;
    $limit = 100000;
   
    if(!is_array($datas)&&$datas){
        global $INI;
        $host = (string) $INI['db']['host'];
        $user = (string) $INI['db']['user'];
        $pass = (string) $INI['db']['pass'];
        $name = (string) $INI['db']['name'];
        $mysqli = new mysqli ( $host, $user, $pass, $name );
        if (mysqli_connect_errno ()) {
            printf ( "Connect failed: %s\n", mysqli_connect_error () );
            exit ();
        }
        $sql = $datas;
        $mysqli->query ( "set names utf8 ;" );
        $result = $mysqli->query ( $sql );
        while ( $row = $result->fetch_assoc() ) {
            $row = array_change_key_case($row, CASE_LOWER);
            $data_row=array();
            $cnt ++;
            if ($limit == $cnt) {
                ob_flush ();
                flush ();
                $cnt = 0;
            }
            foreach($table_array as $key=>$value){
                $data_row[]= iconv('utf-8','gbk',$row[$key]);
            }
            fputcsv ( $fp, $data_row );
        }
        if(!is_null($data_field_array)){
            fputcsv ( $fp, $data_field_array );
        }
    }else{
        foreach ( $datas as $data ) {
            $data_row=array();
            $cnt ++;
            if ($limit == $cnt) {
                ob_flush ();
                flush ();
                $cnt = 0;
            }
            foreach($table_array as $key=>$value){
                $data_row[]= iconv('utf-8','gbk',$data[$key]);
            }
            fputcsv ( $fp, $data_row );
        }  
    }
}
function get_export_type($type){
    if( $type == 'int' || $type == 'real' ){
        return PHPExcel_Cell_DataType::TYPE_NUMERIC;
    }
    return PHPExcel_Cell_DataType::TYPE_STRING;
}
/* creates a compressed zip file */ 
function create_zip($files = array(),$destination = '',$overwrite = false, $path) { 
    //if the zip file already exists and overwrite is false, return false 
    if(file_exists($destination) && !$overwrite) { return false; } 
	//vars 
	$valid_files = array(); 
	//if files were passed in... 
	if(is_array($files)) { 
		//cycle through each file 
		foreach($files as $file) { 
			//make sure the file exists 
			if(file_exists($path.$file)) { 	
			    $valid_files[] =$file; 
			} 
		} 
    }else{
    	if(file_exists($path.$files)){
    	   $valid_files[] =$files; 
    	}
    }
	//if we have good files... 
	if(count($valid_files)) { 
		//create the archive 
		$zip = new ZipArchive(); 
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) { 
		    return false; 
		} 
		//add the files 
		foreach($valid_files as $file) { 
		    $zip->addFile($path.$file,$file); 
		} 
		//debug 
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status; 
		
		//close the zip -- done! 
		$zip->close();
		
		//delete the file
	    foreach($valid_files as $file){
            unlink($path.$file);
        }
		//check to make sure the file exists 
		return file_exists($destination); 
    }else { 
        return false; 
    } 
} 
function delete_zip($dir,$mark,$update_time){
    static $i = 0;
    $files = Array();
    $d = opendir($dir);
    while ($file = readdir($d)){
        if ($file == '.' || $file == '..') continue;
        if(preg_match("/$mark$update_time.zip$/",$file)) continue;
        if(preg_match("/$mark\d{10}.zip$/",$file))
            unlink($dir.$file);
    }
}
  