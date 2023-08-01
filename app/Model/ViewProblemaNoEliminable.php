<?php
class ViewProblemaNoEliminable extends AppModel
{
	var $name = 'ViewProblemaNoEliminable';
	var $useTable = 'view_problemas_no_eliminables';
	var $belongsTo = array('Problema');
}
?>
