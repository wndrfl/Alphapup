<?php
namespace Alphapup\Component\Carto\SQL;

class ExprBuilder
{
	private
		$_aliasCounter = array(),
		$_tables = array();
		
	private function _generateAlias($alias=null)
	{
		if(is_null($alias)) {
			$alias = rand(1,200);
		}
		
		if(!isset($this->_aliasCounter[$alias])) {
			$this->_aliasCounter[$alias] = 0;
			return $alias;
		}else{
			$alias .= ++$this->_aliasCounter[$alias];
			return $alias;
		}
	}
	
	public function column($tableName,$name)
	{	
		$table = $this->table($tableName);
		
		return new Expr\Column($table,$name);
	}
	
	public function isEqualTo($leftExpr,$rightExpr)
	{
		return new Expr\Comparison($leftExpr,Expr\Comparison::O_EQUALS,$rightExpr);
	}
	
	public function select($columns=null)
	{
		if(is_null($columns)) {
			return $this;
		}
		
		$columns = is_array($columns) ? $columns : func_get_args();
		
		return new Expr\Select($columns);
	}
	
	public function selectColumn($column,$alias)
	{
		return new Expr\SelectColumn($column,$alias);
	}
	
	public function table($name,$alias=null)
	{	
		$table = new Expr\Table($name,$alias);
		
		return $table;
	}
}