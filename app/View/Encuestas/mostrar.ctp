<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('encuestas'),
    "sidebar" => 'encuestas/encuestas'
));
$this->end();?>
<div class="encuestas index">
	<h2><?php echo $encuesta['nombre']?></h2>
	<b><?php echo $encuesta['contenido']?></b>
	<div align="left">
	<?php
		$attributes = array('legend' => false,'value'=>false,'separator'=>'<br/>');
		foreach ($preguntas as $preg){
			echo '<b>' . $preg['PreguntasEncuesta']['contenido'] . '</b><br/><br/>';
			$options=array();
			foreach ($preg['OpcionesEncuesta'] as $op){
				$options[$op['OpcionesEncuesta']['id']]=$op['OpcionesEncuesta']['contenido'];
			}
			echo $this->Form->radio('VotacionesEncuesta.pregunta_' . $preg['PreguntasEncuesta']['id'],$options,$attributes);
			echo '<hr/>';
		}
	?>
	</div>
</div>
