<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('gestionar_terminos'),
    "sidebar" => 'textos/textos'
));
$this->end();?>
<?php echo $this->Form->create('Textos',array('action'=>'alta_nuevo_termino'))?>
<?php
echo '<table>';
echo '<tr><td valign="top"  style="font-size: 14px; color: #666666;">'.__('etiqueta').':</td><td>'.$this->Form->input('Texto.etiqueta',array('label'=>false));
echo '</td></tr><tr colspan="2"><td><br></td></tr>';
foreach($lenguajes as $leng){
	 echo '<tr><td valign="top" style="font-size: 14px; color: #666666;">'.__('texto_en_lenguaje', array($leng['Lenguaje']['lenguaje_estandard'])).'</td><td>'.$this->Form->input('Texto.'.$leng['Lenguaje']['lenguaje_estandard'], array('label'=>false,'type'=>'textarea','rows'=>3, 'cols'=>50));
	 echo '</td></tr><tr colspan="2"><td><br></td></tr>';
}
echo '</table>';
?>
<?php echo $this->Form->end(__('aceptar'))?>
