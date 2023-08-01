<?php
if (isset($alumno_problemas)){
	echo '<div>';
	echo $this->element('alumno/home', array('data'=>$alumno_problemas));
	echo '</div>';
}
?>
	
