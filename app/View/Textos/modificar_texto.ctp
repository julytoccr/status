<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('gestionar_terminos'),
    "sidebar" => 'textos/textos'
));
$this->end();?>

<?php echo $this->Html->link(__('volver'),'/textos/buscador_todos_lenguajes/'.$cont_ses_buscador.'/'.$busqueda.'/'.$tipo_busqueda.'/'.$etiqueta_id.'/'.$lenguaje_actual.'/'.$pagina); ?>
<?php 
	echo $this->Form->create('Textos',array('action'=>'modificar_texto'));
	echo $this->Form->input('Texto.cont_ses_buscador',array('type'=>'hidden','value'=>$cont_ses_buscador));
	echo $this->Form->input('Texto.busqueda',array('type'=>'hidden','value'=>$busqueda));
	echo $this->Form->input('Texto.tipo_busqueda',array('type'=>'hidden','value'=>$tipo_busqueda));
	echo $this->Form->input('Texto.id',array('type'=>'hidden'));
	echo $this->Form->input('Texto.etiqueta_id',array('type'=>'hidden'));
	echo $this->Form->input('Texto.lenguaje_id',array('type'=>'hidden'));
	echo $this->Form->input('Texto.lenguaje_id_session',array('type'=>'hidden','value'=>$lenguaje_id_session));
	echo $this->Form->input('Texto.lenguaje',array('type'=>'hidden','value'=>$lenguaje_actual));
	echo $this->Form->input('Texto.pagina',array('type'=>'hidden','value'=>$pagina));
	echo $this->Form->input('Texto.texto');
	echo $this->Form->end(__('form_guardar'));
?>
