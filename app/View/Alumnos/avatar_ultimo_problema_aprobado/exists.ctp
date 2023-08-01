<div align="center">
	<?php
	echo $this->element('alumno/avatar',array('avatar'=>$data['Alumno']['avatar']));
	?>
	<br/>
	<b><?php echo h($data['Alumno']['alias'])?></b>
	<br/>
	<b><?php echo h($data['Problema']['nombre'])?></b>
</div>
