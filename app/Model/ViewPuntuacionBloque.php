<?php
class ViewPuntuacionBloque extends AppModel
{
	var $name = 'ViewPuntuacionBloque';
	public $actsAs = array('Containable');
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Alumno',
			'Bloque'
	);

}
?>
