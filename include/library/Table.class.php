<?php

class Table
{
	
	public $table_name = null;
	public $pk_name = 'id';// 主键名
	public $pk_value = null;// 主键值
	public $strip_column = array();
	//字段的建值对
	//以用户表为例 ：array("id"=>1,"username"="zhangsan");注意：主键会在两个地方均进行存储
	private $column_values = array();

	public function __get($k=null)
	{
		if ( isset($this->column_values[$k]) )
			return $this->column_values[$k];
		return null;
	}

	public function __set($k=null, $v=null)
	{
		$this->column_values[$k] = $v;
	}

	public function _set_values($vs=array())
	{
		$this->column_values = $vs;
		if ( isset($vs[$this->pk_name]))
		{
			$this->pk_value = $vs[$this->pk_name];
		}
	}

	/**
	 * Table 类的构造函数
	 * @param $n tablename
	 * @param $record 键值对   array("wwt_id"=>123,"wwt_username"=>"lisi")
	 * @param $pre 前缀：如果该值不为空，则在进行设置属性值时会去掉前缀
	 */
	public function __construct($n=null, $record=array(), $pre='')
	{
		if ( is_array($n) )
		{
			$this->_set_values($n);
			return;
		}

		$this->table_name = $n;
		if (strlen($pre)) {
			foreach($record AS $k=>$v) {
				if (0===strpos($k,$pre)) {
					$k = substr($k, strlen($pre));
					if ($k) $this->$k = $v;
					if ($k==$this->pk_name) {
						$this->pk_value = $v;
					}
				}
			}
		} else {
			$this->_set_values( $record );
		}
	}

	public function SetPk($k=null, $v=null)
	{
		if ( $k && $v )
		{
			$this->pk_name = $k;
			$this->pk_value = $v;
			$this->$k = $v;
		}
	}

	public function Get($k=null)
	{
		if (null==$k)
			return $this->column_values;
		return $this->__get($k);
	}

	public function Set($k, $v=null)
	{
		$this->column_values[$k] = $v;
	}

	public function Plus($k=null, $v=1)
	{
		if ( array_key_exists($k, $this->column_values) )
		{
			$this->column_values[$k] += $v;
		}
		else throw new Exception( 'Table ' .$this->table_name. ' no column '. $k );
	}

	public function SetStrip() {
		$fields = func_get_args();
		if ( empty($fields) )
			return true;
		if ( is_array($fields[0]) )
			$fields = $fields[0];
		$this->strip_column = $fields;
	}
	/**
	 * 虽然该函数签名中并没有列出参数，但在实际调用中如果没有传入参数，该函数就是只返回true而已，而不会执行insert操作。
	 * 同时如果传入的参数在对象的属性中并不存在，同样不会执行insert操作
	 * 该函数只接收第一个参数，在函数内部通过func_get_args（）去获取传入的值，
	 * 
	 * 执行成功后会返回刚插入id，如果没有执行insert操作则返回true
	 */
	public function Insert()
	{
		$fields = func_get_args();
		if ( empty($fields) )
			return true;

		if ( is_array($fields[0]) )
			$fields = $fields[0];

		$up_array = array();
		//$this->column_values中多个值，但是否要更新要根据$fields中的值，因而不能直接用。
		//同时也要保证$fields中的值在$this->column_values中存在才行。
		foreach( $fields AS $f )
		{
			if ( array_key_exists($f, $this->column_values) )
			{
				$up_array[$f] = $this->BuildDBValue($this->column_values[$f], $f);
			}
		}
		if (empty($up_array) )
			return true;

		return DB::Insert($this->table_name, $up_array);
	}
	//该函数与Insert相似
	public function Update()
	{
		$fields = func_get_args();
		if ( empty($fields) )
			return true;

		if ( is_array($fields[0]) )
			$fields = $fields[0];

		$up_array = array();
		foreach( $fields AS $f )
		{
			if ( array_key_exists($f, $this->column_values) )
			{
				$up_array[$f] = $this->BuildDBValue($this->column_values[$f], $f);
			}
		}
		if (empty($up_array) )
			return true;

		if ($this->pk_value) {
			return self::UpdateCache($this->table_name, $this->pk_value, $up_array);
		} else {
			return $this->pk_value = $this->id = DB::Insert($this->table_name, $up_array);
		}
	}
	/**
	 * 更新物理数据库的同时更新缓存
	 * @param $n 表名
	 * @param $id 参见DB::Update函数的介绍
	 * @param $r 参见DB::Update函数的介绍
	 */
	static public function UpdateCache($n, $id, $r=array()) {
		DB::Update($n, $id, $r);
		return Cache::Del(Cache::GetObjectKey($n,$id));
	}

	private function BuildDBValue($v, $f=null) {
		if (is_array($v)) return ','. join(',', $v) . ',';
		global $striped_field;
		if (is_array($striped_field) && in_array($f, $striped_field)) {
			$v = strip_tags($v);
		}
		return in_array($f,$this->strip_column) ? stripslashes($v) : $v;
	}

	static private function _Fetch($n=null, $ids=array()) {
		$r = Cache::GetObject($n, $ids);
		$diff = array_diff($ids, array_keys($r));
		if(!$diff) return $r;
		$rr = DB::GetDbRowById($n, array_values($diff));
		Cache::SetObject($n, $rr);
		$r = array_merge($r, $rr);
		return Utility::SortArray($r, $ids, 'id');
	}
	/**
	 * 直接从数据库中获取数据，即使缓存中存在也是从数据库中取，且查询条件只是id
	 * 
	 * @param $n 表名
	 * @param $ids 查询条件中的id值
	 */
	static public function FetchForce($n=null, $ids=array()) {
		if ( empty($ids) || !$ids ) return array();
		$single = is_array($ids) ? false : true;
		settype($ids, 'array'); 
		$ids = array_values($ids);
		$ids = array_diff($ids, array(NULL));

		$r = DB::GetDbRowById($n, $ids);
		Cache::SetObject($n, $r);
		return $single ? array_pop($r):Utility::SortArray($r,$ids,'id');
	}	
	/**
	 * 该函数和FetchForce（）差别有两个：1.即可以根据id查询数据，也可以根据其他字段 2.如果根据id查询数据，如果该id在缓存中存在则会从缓存中获取数据
	 * @param string $n tablename
	 * @param array $ids 查询字段满足的条件
	 * @param string $k 查询用字段名
	 */
	static public function Fetch($n=null,$ids=array(),$k='id')
	{
		if ( empty($ids) || !$ids ) return array();
		$single = is_array($ids) ? false : true;

		settype($ids, 'array'); $ids = array_values($ids);
		$ids = array_diff($ids, array(NULL));

		if ($k=='id') { 
			$r = self::_Fetch($n, $ids);
			return $single ? array_pop($r) : $r;
		}

		$result = DB::LimitQuery($n, array(
					'condition' => array( $k => $ids, ),
					'one' => $single,
					));

		if ( $single ) { return $result; }
		return $result;
	}
	/**
	 * 该函数有两个功能：如果$sum参数不为空，则计算该字段的总和；如果为空则计算有多少条数据
	 * @param $n 表名
	 * @param $condition 查询条件
	 * @param $sum 是否要计算某个字段的总值
	 */
	static public function Count($n=null, $condition=null, $sum=null)
	{
		$condition = DB::BuildCondition( $condition );
		$condition = null==$condition ? null : "WHERE $condition";
		$zone = $sum ? "SUM({$sum})" : "COUNT(1)";
		$sql = "SELECT {$zone} AS count FROM `$n` $condition";
		$row = DB::GetQueryResult($sql, true);
		return $sum ? (0+$row['count']) : intval($row['count']);
	}
	/**
	 * 删除数据，注意：只能根据一个字段进行判断
	 * @param $n 表名
	 * @param $id 条件值
	 * @param $k 字段名，默认为 id
	 */
	static public function Delete($n=null, $id=null, $k='id')
	{
		settype( $id, 'array' );
		$idstring = join('\',\'', $id);
		if(preg_match('/[\s]/', $idstring)) return false;
		$sql = "DELETE FROM `$n` WHERE `{$k}` IN('$idstring')";
		DB::Query( $sql );
		if ($k!='id') return true;
		Cache::ClearObject($n, $id);
		return True;
	}
}
?>
