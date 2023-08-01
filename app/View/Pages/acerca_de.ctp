
<?php
	if($this->Session->check('usuario.lenguaje')){
		echo __('cabecera_acercade');
	}else{
		echo 'e-status is an educational product developed by the Statistics and Operational Investigation Department (DEIO) of the UPC. Several Computer Science Faculty students have taken part, under the direction of José Antonio González, professor of the DEIO, in the development of the current e-status features, as a part of their respective Final Projects:';
	}
	?>
<ul>
 	<li>Daniel Jesús García Martínez</li>
 	<li>Miguel Matute Martínez</li>
    <li>Gema Albiach Écija</li>
    <li>Miguel Ángel Gómez Bosquet</li>
    <li>José María Cerdeira Rodríguez</li>
    <li>David Rodríguez González</li>
    <li>Miguel Ros Martín</li>
    <li>Daniel Carrillo Carrillo</li>
</ul>
<i>
<?php
	if($this->Session->check('usuario.lenguaje')){
		echo __('logo_estatus_creador', array('Elena Segura'));
	}else{
		echo 'The e-status logo was designed by Elena Segura';
	}
?>
</i>
