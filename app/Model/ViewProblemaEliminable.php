<?php
class ViewProblemaEliminable extends AppModel
{
	var $name = 'ViewProblemaEliminable';
	var $useTable = 'view_problemas_eliminables';
	var $belongsTo = array('Problema');
}
?>
