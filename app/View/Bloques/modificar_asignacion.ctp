<?php 
$tiempos=array(
	'30'=>__('minutos',array(30)),
	'45'=>__('minutos',array(45)),
	'60'=>__('hora',array(1)),
	'90'=>__('horas',array('1.5')),
	'120'=>__('horas',array(2)),
	'240'=>__('horas',array(4)),
	'720'=>__('horas', array(12)),
	'1440'=>__('dia', array(1)),
	'2880'=>__('dias', array(2)),
	'10080'=>__('semana', array(1)),
	'20160'=>__('semanas', array(2)),
	'30240'=>__('semanas', array(3))
);
echo $this->Form->create(null,array('action'=>'modificar_asignacion_post/'));
?>

<table class="tabla_listado">
<?php
foreach ($bloqs as $a_id => $problemas):
	foreach ($problemas as $p_id => $p):
?>
	<tr>
		<td><?php echo $p['nombre']?></td>
		<td>
			<?php echo $this->Form->select("{$a_id}.{$p_id}", $tiempos, array('default'=>$p['tiempo'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
<?php endforeach; ?>
</table>
<?php echo $this->Form->submit(__('asignar'))?>
<?php echo $this->Form->end()?>
