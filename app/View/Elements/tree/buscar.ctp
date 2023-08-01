<?php 
echo $this->Form->create(null,array('onSubmit' => 'return false;'));
echo $this->Form->input('nombre');
echo $this->Form->input('Problema.problema_nombre',array('type'=>'checkbox'));
echo $this->Form->input('Problema.problema_descripcion',array('type'=>'checkbox'));
echo $this->Form->input('Problema.problema_enunciado',array('type'=>'checkbox'));
echo $this->Form->input('Problema.pregunta_enunciado',array('type'=>'checkbox'));
echo $this->Form->input('Problema.profesor_nombre',array('type'=>'checkbox'));
echo $this->Js->submit(__('buscar_form'), array(
        'url' => array(
            'action' => 'buscar_problemas'
        ),
        'update' => '#page_container',
        'buffer' => false,
        'success' => 'eval(data);',
    )
);
echo $this->Form->end();
?>
