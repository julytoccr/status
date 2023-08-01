<?php
$this->start('css');
echo $this->Html->css('estatusr');
$this->end();
?>

<?php 
if(isset($agrupacion))
{
	echo '<div style = "text-align: center"> <h3>';
	
	echo $this->Html->link(__('resolver'),'/ejecuciones/iniciar_agrupacion/' . $agrupacion); 
	
	echo '</div> </h3>';
}
?>

<div id="problema_enunciado">
	<?php echo $problema['enunciado'];?>
	</br>
</div>
<div id="problema_preguntas">
<table>
	<?php for($i=0;$i<count($preguntas);$i++):?>
	<?php
		$preg=$preguntas[$i];
		$j=$i+1;
	?>
		<tr class="fila_pregunta">
			<td class="pregunta">
				<?php echo "$j. {$preg['enunciado']}"?>
			</td>
		</tr>
	<?php endfor;?>
</table>
</div>
<div id="grafico">
	<table>
		<tr><td><b><?php echo __('problema_pesos_preguntas');?></b></td></tr>
		<tr><td><?php echo $this->Html->image($img_pesos_url,array('border'=>0,'alt'=>''))?></td></tr>
	</table>
</div>
