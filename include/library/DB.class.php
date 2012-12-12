<?php
/**
 * @author shwdai@gmail.com
 */
class DB
{
	static private $mInstance = null;

	static private $mConnection = null;

	static public $mDebug = false;

	static public $mError = null;

	static public $mCount = 0;

	static public function &Instance()
	{
		if ( null == self::$mInstance )
		{ 
			$class = __CLASS__;
			self::$mInstance = new $class;
		}
		return self::$mInstance;
	}

	function __construct()
	{
		global $INI;
		if (isset($INI['db']) && $INI['db']) {
			$host = (string) $INI['db']['host'];
			$user = (string) $INI['db']['user'];
			$pass = (string) $INI['db']['pass'];
			$name = (string) $INI['db']['name'];
		} else {
			$config = Config::Instance('php');
			$host = (string) $config['db']['host'];
			$user = (string) $config['db']['user'];
			$pass = (string) $config['db']['pass'];
			$name = (string) $config['db']['name'];
		}

		self::$mConnection = mysql_connect( $host, $user, $pass );

		if ( mysql_errno() )
			throw new Exception("Connect failed: " . mysql_error());

		@mysql_select_db( $name, self::$mConnection );
		@mysql_query( "SET NAMES UTF8;", self::$mConnection );
	}

	static function GetLinkId() {
		self::Instance();
		return self::$mConnection;
	}

	function __destruct()
	{
		self::Close();
	}

	static public function Debug()
	{
		self::$mDebug = !self::$mDebug;
	}

	static public function Close()
	{
		if ( is_resource( self::$mConnection ) )
		{
			@mysql_close( self::$mConnection );
		}

		self::$mConnection = null;
		self::$mInstance = null;
	}


	static public function EscapeString( $string )
	{
		self::Instance();
		return @mysql_real_escape_string( $string, self::$mConnection );
	}

	static public function GetInsertId()
	{
		self::Instance();
		return intval( @mysql_insert_id(self::$mConnection) );
	}

	static public function Query( $sql )
	{
		self::Instance();

		if ( self::$mDebug )
		{
			error_log("++++++++++++$sql");
		}

		$result = @mysql_query( $sql, self::$mConnection );
		self::$mCount++;

		if ( $result )
		{
			return $result;
		}
		else
		{
			self::$mError = mysql_error();
		}

		self::Close();
		return false;
	}

	static public function NextRecord($query) {
		return array_change_key_case(mysql_fetch_assoc($query),CASE_LOWER);
	}

	static public function GetTableRow($table, $condition)
	{
		return self::LimitQuery($table, array(
					'condition' => $condition,
					'one' => true,
					));
	}
	
	static public function GetTableRows($table, $condition)
	{
		return self::LimitQuery($table, array(
					'condition' => $condition,
					'one' => false,
					));
	}
	
	/**
	 * 该函数只能通过一个表的id字段进行查询
	 * @param $table 表名
	 * @param $ids 参数值
	 */
	static public function GetDbRowById($table, $ids=array()) { 
		$one = is_array($ids) ? false : true;
		settype($ids, 'array');
		$idstring = join('\',\'', $ids);
		if(preg_match('/[\s]/', $idstring)) return array();
		$q = "SELECT * FROM `{$table}` WHERE id IN ('{$idstring}')";
		$r = self::GetQueryResult($q, $one);
		if ($one) return $r;
		return Utility::AssColumn($r, 'id');
	}
	/**
	 * 对一个表进行进行查询，可以用于分页（取部分数据）
	 * @param $table
	 * @param $options
	 * 示例：
	 * $users = DB::LimitQuery('user', array(
	 *									'condition' => $condition,//需符合 BuildCondition 函数的要求
	 *									'order' => 'ORDER BY id DESC',
	 *									'size' => $pagesize,
     *									'offset' => $offset,
     *									'select' => 'id,username,password'//如果没有这一项就取全部字段
	 *								));
	 */
	static public function LimitQuery($table, $options=array())
	{
		$condition = isset($options['condition']) ? $options['condition'] : null;
		$one = isset($options['one']) ? $options['one'] : false;
		$offset = isset($options['offset']) ? abs(intval($options['offset'])) : 0;
		if ( $one ) {
			$size = 1;
		} else {
			$size = isset($options['size']) ? abs(intval($options['size'])) : null;
		}
		$select = isset($options['select']) ? $options['select'] : '*';
		$order = isset($options['order']) ? $options['order'] : null;
		$cache = isset($options['cache'])?abs(intval($options['cache'])):0;

		$condition = self::BuildCondition( $condition );
		$condition = (null==$condition) ? null : "WHERE $condition";

		$limitation = $size ? "LIMIT $offset,$size" : null;

		$sql = "SELECT {$select} FROM `$table` $condition $order $limitation";
		return self::GetQueryResult( $sql, $one, $cache);
	}
	/**
	 * 执行查询，主要用于跨表的关联操作。如果只查一个表的数据用已有的api即可。
	 * @param string $sql
	 * @param boolean $one 是否只取一条数据，默认为true只取一条数据
	 * @param int $cache 是否从cache中取数据：如果该值大于0则取数据。默认值为0
	 */
	static public function GetQueryResult( $sql, $one=true, $cache=0 )
	{
		$mkey = Cache::GetStringKey($sql);
		if ( $cache > 0 ) {
			$ret = Cache::Get($mkey);
			if ( $ret ) return $ret;
		}

		$ret = array();
		if ( $result = self::Query($sql) )
		{
			while ( $row = mysql_fetch_assoc($result) )
			{
				$row = array_change_key_case($row, CASE_LOWER);
				if ( $one )
				{
					$ret = $row;
					break;
				}else{ 
					array_push( $ret, $row );
				}
			}

			@mysql_free_result( $result );
		}
		if ($ret) Cache::Set($mkey, $ret, 0, $cache);
		return $ret;
	}

	static public function SaveTableRow($table, $condition)
	{
		return self::Insert($table, $condition);
	}
	/**
	 * 向一个表中插入一条记录
	 * 
	 * @param $table 表名
	 * @param array $condition 要插入数据的键值对,如 array("wwt_id"=>123,"wwt_username"=>"lisi");
	 * 
	 * @return fixed 如果insert成功就返回最新的id值，如果不成功就返回false
	 */ 
	static public function Insert( $table, $condition )
	{
		self::Instance();

		$sql = "INSERT INTO `$table` SET ";
		$content = null;

		foreach ( $condition as $k => $v )
		{
			$v_str = null;
			if ( is_numeric($v) )
				$v_str = "'{$v}'";
			else if ( is_null($v) )
				$v_str = 'NULL';
			else
				$v_str = "'" . self::EscapeString($v) . "'";

			$content .= "`$k`=$v_str,";
		}

		$content = trim($content, ',');
		$sql .= $content;

		$result = self::Query ($sql);
		if ( false==$result )
		{
			self::Close();
			return false;
		}
		($insert_id = self::GetInsertId()) || ($insert_id =  true) ;
		return $insert_id;
	}

	static public function DelTableRow($table=null, $condition=array())
	{
		return self::Delete($table, $condition);
	}
	
	/**
	 * 对一个表的数据进行删除
	 * @param $table 表名
	 * @param $condition 操作条件 需符合 BuildCondition 函数的要求
	 */
	static public function Delete($table=null, $condition = array())
	{
		if ( null==$table || empty($condition) )
			return false;
		self::Instance();

		$condition = self::BuildCondition($condition);
		$condition = (null==$condition) ? null : "WHERE $condition";
		$sql = "DELETE FROM `$table` $condition";
		return DB::Query( $sql );
	}
	/**
	 * 对一个表的数据进行更新
	 * 
	 * @param $table 表名
	 * @param fixed $id 
	 * 该字段的类型有两种：
	 * 1.数字、字符串等“单值”类型，这时的查询条件为 $pkname = $id
	 * 2.数组  若为数组，其值应符合BuildCondition函数的要求，通过BuildCondition来构造查询条件
	 * @param array $updaterow 键值对 array("password" => "123546","state" => 1 )
	 * @param $pkname 主键名
	 */
	static public function Update( $table=null, $id=1, $updaterow=array(), $pkname='id' )
	{

		if ( null==$table || empty($updaterow) || null==$id)
			return false;

		if ( is_array($id) ) $condition = self::BuildCondition($id);
		else $condition = "`$pkname`='".self::EscapeString($id)."'";

		self::Instance();

		$sql = "UPDATE `$table` SET ";
		$content = null;

		foreach ( $updaterow as $k => $v )
		{
			$v_str = null;
			if ( is_numeric($v) )
				$v_str = "'{$v}'";
			else if ( is_null($v) )
				$v_str = 'NULL';
			else if ( is_array($v) )
				$v_str = $v[0]; //for plus/sub/multi; 
			else
				$v_str = "'" . self::EscapeString($v) . "'";

			$content .= "`$k`=$v_str,";
		}

		$content = trim($content, ',');
		$sql .= $content;
		$sql .= " WHERE $condition";

		$result = self::Query ($sql);

		if ( false==$result )
		{
			self::Close();
			return false;
		}

		return true;
	}


	/**
	 * @param string $table
	 * @param array $select_map
	 */
	static public function GetFieldByKey($table, $field_name,$key_value){
		return self::LimitQuery($table, array(
					'condition' => $condition,
					'one' => true,
					));
	}
	
	static public function GetField($table, $select_map = array())
	{
		$fields = array();
		$q = self::Query( "DESC `$table`" );

		while($r=mysql_fetch_assoc($q) )
		{
			$Field = $r['Field'];
			$Type = $r['Type'];

			$type = 'varchar';
			$cate = 'other';
			$extra = null;

			if ( preg_match( '/^id$/i', $Field ) )
				$cate = 'id';
			else if ( preg_match( '/^_time/i', $Field ) )
				$cate = 'integer';
			else if ( preg_match( '/^_number/i', $Field ) )
				$cate = 'integer';
			else if ( preg_match ( '/_id$/i', $Field ) )
				$cate = 'fkey';


			if ( preg_match('/text/i', $Type ) )
			{
				$type = 'text';
				$cate = 'text';
			}
			if ( preg_match('/date/i', $Type ) )
			{
				$type = 'date';
				$cate = 'time';
			}
			else if ( preg_match( '/int/i', $Type) )
			{
				$type = 'int';
			}
			else if ( preg_match( '/(enum|set)\((.+)\)/i', $Type, $matches ) )
			{
				$type = strtolower($matches[1]);
				eval("\$extra=array($matches[2]);");
				$extra = array_combine($extra, $extra);

				foreach( $extra AS $k=>$v)
				{
					$extra[$k] = isset($select_map[$k]) ? $select_map[$k] : $v;
				}
				$cate = 'select';
			}

			$fields[] = array(
					'name' => $Field,
					'type' => $type,
					'extra' => $extra,
					'cate' => $cate,
					);
		}
		return $fields;
	}

	static public function Exist($table, $condition=array())
	{
		$row = self::LimitQuery($table, array(
					'condition' => $condition,
					'one' => true,
					));

		return empty($row) ? false : (isset($row['id']) ? $row['id'] : true);
	}
	/**
	 * 创建查询用的where条件
	 * 
	 * @param $condition 具体的where条件,其有两种形式：
	 * 1. array("id = 5","username= 6","state <> 0")
     *	output：(id = 5)AND (username= 6)AND (state <> 0)
	 * 2.array("id"=> 5,"username"=>null,"order_id" = array(1,22,33,444,45))
	 *	output：`id` = '5' AND `username` IS NULL AND `order_id` IN (1,22,33,444,45)
	 * @param $logic sql连接词，默认为“AND”
	 * 
	 */
	 
	static public function BuildCondition($condition=array(), $logic='AND')
	{
		if ( is_string( $condition ) || is_null($condition) )
			return $condition;

		$logic = strtoupper( $logic );
		$content = null;
		foreach ( $condition as $k => $v )
		{
			$v_str = null;
			$v_connect = '=';

			if ( is_numeric($k) )
			{
				$content .= $logic . ' (' . self::BuildCondition( $v, $logic ) . ')';
				continue;
			}

			$maybe_logic = strtoupper($k);
			if ( in_array($maybe_logic, array('AND','OR')))
			{
				$content .= $logic . ' (' . self::BuildCondition( $v, $maybe_logic ) . ')';
				continue;
			}

			if ( is_numeric($v) ) {
				$v_str = "'{$v}'";
			}
			else if ( is_null($v) ) {
				$v_connect = ' IS ';
				$v_str = ' NULL';
			}
			else if ( is_array($v) ) {
				if ( isset($v[0]) ) {
					$v_str = null;
					foreach($v AS $one) {
						if (is_numeric($one)) {
							$v_str .= ','.$one;
						} else {
							$v_str .= ',\''.self::EscapeString($one).'\'';
						}
					}
					$v_str = '(' . trim($v_str, ',') .')';
					$v_connect = 'IN';
				} else if ( empty($v) ) {
					$v_str = $k;
					$v_connect = '<>';
				} else {
					$v_connect = array_shift(array_keys($v));
					$v_s = array_shift(array_values($v));
					$v_str = "'".self::EscapeString($v_s)."'";
                    $v_str = is_numeric($v_s) ? "'{$v_s}'" : $v_str ;
				}
			} 
			else {
				$v_str = "'".self::EscapeString($v)."'";
			}

			$content .= " $logic `$k` $v_connect $v_str ";
		}

		$content = preg_replace( '/^\s*'.$logic.'\s*/', '', $content );
		$content = preg_replace( '/\s*'.$logic.'\s*$/', '', $content );
		$content = trim($content);

		return $content;
	}

	static public function CheckInt($id)
	{
		$id = intval($id);

		if ( 0>=$id )
			throw new Exception('must int!');

		return $id;
	}
}
?>
