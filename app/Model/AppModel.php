<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	
	public $cacheQueries = true;
	
	function unbindAll($model=null){
		foreach(array('hasOne', 'hasMany', 'belongsTo', 'hasAndBelongsToMany')  as $a){
			if(! isset($this->$a))
				continue;
			foreach($this->$a as $classname=>$v){
				$realClassName = is_array($v) ? $classname : $v;
				if(! isset($model[$a]) || ! in_array($realClassName, $model[$a])){
					$this->unbindModel(array($a=>array($realClassName)));
				}
			}
		}
	}
	
	function setConditions($conditions,$ass_name,$ass_type='belongsTo')
	{
		$association=$this->{$ass_type}[$ass_name];

		$association['conditions']=$conditions;
		$this->bindModel(array($ass_type =>array($ass_name=> $association)));
	}
	
	function _auxAssoc($id,$assoc_name)
	{
		//disable query cache
		$back_cache=$this->cacheQueries;
		$this->cacheQueries=false;

		$this->recursive=1;
		$this->unbindAll(array('hasAndBelongsToMany'=>array($assoc_name)));
		$data=$this->findById($id);
		$assoc_data=array();
		foreach ($data[$assoc_name] as $assoc)
		{
			$assoc_data[]=$assoc['id'];
		}
		unset($data[$assoc_name]);
		$data[$assoc_name][$assoc_name]=$assoc_data;

		//restore previous setting of query cache
		$this->cacheQueries=$back_cache;

		return $data;
	}
	
	function addAssoc($id,$assoc_name,$assoc_id)
	{
		$data=$this->_auxAssoc($id,$assoc_name);
		if (!is_array($assoc_id)) $assoc_id=array($assoc_id);
		$data[$assoc_name][$assoc_name]=am($data[$assoc_name][$assoc_name],$assoc_id);
		return $this->save($data);
	}
	
	function deleteAssoc($id,$assoc_name,$assoc_id)
	{
		$data=$this->_auxAssoc($id,$assoc_name);
		if (!is_array($assoc_id)) $assoc_id=array($assoc_id);
		$result=array();
		foreach ($data[$assoc_name][$assoc_name] as $id)
		{
			if (!in_array($id, $assoc_id)) $result[]=$id;
		}
		$data[$assoc_name][$assoc_name]=$result;
		return $this->save($data);
	}
}
