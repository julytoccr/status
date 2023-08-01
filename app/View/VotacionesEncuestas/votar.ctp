<div>
<?php echo $this->Form->create(null,array('url'=>'/votaciones_encuestas/votar/' . $encuestas_asignatura_id)); ?>
	<h2><?php echo $encuesta['nombre']?></h2>
	<b><?php echo $encuesta['contenido']?></b>
	<div align="left">
		<?php
			$attributes = array('legend' => false,'value'=>false,'separator'=>'<p><br/>');
			foreach ($preguntas as $preg)
			{
				echo '<b>' . $preg['PreguntasEncuesta']['contenido'] . '</b><br/><br/>';
				$options=array();
				foreach ($preg['OpcionesEncuesta'] as $op)
				{
					$options[$op['OpcionesEncuesta']['id']]=$op['OpcionesEncuesta']['contenido'];
				}
				echo $this->Form->radio('VotacionesEncuesta.pregunta_' . $preg['PreguntasEncuesta']['id'],$options,$attributes);

				echo '<hr/>';
			}
		?>
		<div align="center" id="submit">
		<?php
			echo $this->Form->end(__('Submit'));
		?>
		</div>
		<div align="center" id="gracias" style="display:none">
			<?php echo __('gracias'); ?>
		</div>
	</div>
</div>
