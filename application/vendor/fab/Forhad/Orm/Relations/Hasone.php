<?php namespace Forhad\Orm\Relations;

use Forhad\Orm\Model;

class HasOne extends Relation {

	protected $foreign_key;

	function __construct(Model $parent, Model $related, $foreign_key)
	{
		parent::__construct($parent, $related);

		$this->foreign_key = $foreign_key;
	}

	function setJoin()
	{
		if( $this->eagerLoading )
			return $this->related->where_in($this->foreign_key, $this->eagerKeys);

		else
			return $this->related->where($this->foreign_key, $this->parent->getData( $this->parent->getPrimaryKey() ));
	}

	function match(Model $parent)
	{
		foreach($this->eagerResults as $row)
		{
			if($row->{$this->foreign_key} == $parent->getData( $parent->getPrimaryKey() ))
				return $row;
		}
	}

	function getResults()
	{
		if(empty($this->join)) $this->join = $this->setJoin();

		return $this->join->first();
	}

}