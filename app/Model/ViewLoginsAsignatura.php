<?php
class ViewLoginsAsignatura extends AppModel
{
	var $name = 'ViewLoginsAsignatura';
	var $useTable = 'view_logins_asignatura';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Usuario'
	);
}
?>
