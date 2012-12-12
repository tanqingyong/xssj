<?php

class Logger {
	
	/**
	 * 添加一条日志
	 * @param long $user_id 操作用户
	 * @param string $user_name 操作用户名
	 * @param int $action_type 操作类型
	 * @param string $table_name 表名
	 * @param string $key_name 键名
	 * @param long $key_value 键值
	 * @param array $detail 明细 	 
	 * array(table_name =>array(
	 * columns_name=>array(new_value,old_value),
	 * columns ....),
	 * tables ...
	 * )
	 * @param string $comment 备注,默认为""
	 * @return long 添加失败返回0,成功返回新建日志的ID
	 */
	static public function InsertLog($user_id, $user_name, $action_type, $table_name, $key_name, $key_value, $detail, $comment = "") {
		if (empty ( $user_id ) || empty ( $user_name ) || empty ( $action_type )) {
			return 0;
		}
		
		//		print_r(array ("user_id" => $user_id, "user_name" => $user_name, "action_type" => $action_type, "table_name" => $table_name, "key_name" => $key_name, "key_value" => $key_value, "comment" => $comment ));
		//操作日志表
		$insert_id = DB::Insert ( 'LOG', array ("user_id" => $user_id, "user_name" => $user_name, "action_type" => $action_type, "table_name" => $table_name, "key_name" => $key_name, "key_value" => $key_value, "comment" => $comment ) );
		
		if (empty ( $detail ) || ! is_array ( $detail )) {
			return $insert_id;
		}
		
		//拼操作明细表的sql
		$sql = "insert into LOG_DETAIL(log_id,table_name,key_name,key_value,columns_name,old_value,new_value) values";
		foreach ( $detail as $columns ) {
			foreach ( $columns as $k => $v ) {
				$sql = $sql . "(" . $insert_id . ",'" . $table_name . "','" . $key_name . "'," . $key_value . ",'" . $k . "','" . $v [1] . "','" . $v [0] . "'),";
			}
		}
		$sql = substr ( $sql, 0, - 1 );
		DB::Query ( $sql );
		//		echo $sql;
		return $insert_id;
	}
	
	/**
	 * 查询日志
	 * @param int $table_name 表名,默认为空
	 * @param string $key_value 键值,默认为空
	 * @return array 日志记录
	 */
	static public function GueryLog($table_name = null, $key_value = null) {
		$condetion = array ();
		if (! empty ( $key_value )) {
			$condetion ['key_value'] = $key_value;
		}
		if (! empty ( $table_name )) {
			$condetion ['table_name'] = $table_name;
		}
		$logs = DB::GetTableRow ( "LOG", $condetion );
		
		return $logs;
	}
	
	/**
	 * 查询指定用户的操作日志
	 * @param long $user_id 用户号
	 * @param string $pri_key 默认为空
	 * @param string $user_id 默认为空
	 * @return array
	 */
	static public function GetLogByUser($user_id, $table_name = null, $key_value = null) {
		$condetion = array ('table_name' => $table_name );
		
		if (! empty ( $key_value )) {
			$condetion ['key_value'] = $key_value;
		}
		if (! empty ( $table_name )) {
			$condetion ['table_name'] = $table_name;
		}
		$logs = DB::GetTableRow ( "LOG", $condetion );
		
		return $logs;
	}
	
	/**
	 * 取得日志明细
	 * @param string $table_name 表名
	 * @param long $key_value 键值
	 * @return array 明细
	 */
	static public function GetLogDetail($table_name = null, $key_value = null) {
		$condetion = array ();
		
		if (! empty ( $key_value )) {
			$condetion ['key_value'] = $key_value;
		}
		if (! empty ( $table_name )) {
			$condetion ['table_name'] = $table_name;
		}
		$logs = DB::GetTableRow ( "LOG_DETAIL", $condetion );
		
		return $logs;
	}
	
	/**
	 * 取得日志明细
	 * @param unknown_type $log_id 日志号
	 * @param string $table_name 表名
	 * @param long $key_value 键值
	 * @return array 明细
	 */
	static public function GetLogDetailById($log_id, $table_name = null, $key_value = null) {
		$condetion = array ('log_id' => $log_id );
		
		if (! empty ( $key_value )) {
			$condetion ['key_value'] = $key_value;
		}
		if (! empty ( $table_name )) {
			$condetion ['table_name'] = $table_name;
		}
		$logs = DB::GetTableRow ( "LOG_DETAIL", $condetion );
		
		return $logs;
	}
}
