<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('idiomas'),
    "sidebar" => 'lenguajes/menu'
));
$this->end();?>
<table class="tabla_listado">
	<tr>
		<th></th>		
		<th><?php echo __('lenguaje'); ?></th>
		<th><?php echo __('lenguaje_hereda'); ?></th>
	</tr>
<?php 
foreach($lenguajes as $leng): 
?>
	<tr>
		<td><?php echo $this->Html->link(__('cambiar_herencia'),'/lenguajes/cambiar_lenguaje_herencia/'.$leng['lenguaje_estandard'],null,__('confirmar_cambiar_herencia'));?></td>
		<td><?php echo $leng['lenguaje_propio'].' ('.$leng['lenguaje_estandard'].')' ?></td>
		<td><?php 
			if($leng['lenguaje_hereda']==0){
				$hereda=' ';
			}else{
				$hereda=$leng['lenguaje_hereda_texto'];
			}	
			echo $hereda; ?></td>		
	</tr>
<?php 
endforeach; ?>
</table>
