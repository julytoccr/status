<div class="bloque_flotante"  style="background-color: #ffffff; padding: 3px">
	<h2><?php echo $encuesta['Encuesta']['nombre']?></h2>
	<b><?php echo $encuesta['Encuesta']['contenido']?></b>
	<?php if ($encuesta['EncuestasAsignatura']['fecha_fin']): ?>
		<br/><i><?php echo __('fecha_limite');?><?php echo $this->Formato->fecha($encuesta['EncuestasAsignatura']['fecha_fin'])?>
	<?php endif;?>
	<div align="center">
		<b><?php echo $this->Html->link(__('votar'),'/votaciones_encuestas/votar/' . $encuesta['EncuestasAsignatura']['id'])?></b>
	</div>
</div>
