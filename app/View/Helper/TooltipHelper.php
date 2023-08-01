<?php
/**
 * Tooltip Helper
 * @author Joe And Richard
 * @license LGPL2
 * @version 0.12
 *
 * @package helpers
 */
class TooltipHelper extends Helper {
	var $helpers=array('Html');

	function afterRender(){
		$this->Html->script('tooltip/tooltip', array('block'=>'script'));
		$this->Html->script('effects', array('block'=>'script'));
		$this->Html->css('tooltip/tooltip', null, array('block'=>'css'));
	}
	
	function show($header,$content,$element_id=null) {
	    if ($element_id!==null) $class='for_' . $element_id;
		else $class='';

		return "<div class=\"tooltip $class\"><h1>$header</h1><p>$content</p></div>";
	}
}
?>
