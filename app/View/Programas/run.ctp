<?php
	echo '<b>' . $programa['descripcion'] . '</b>';
	echo $this->Form->create();

	echo $this->Form->hidden('Programa.id');

	for ($i=1;$i<=7;$i++)
	{
		if (! $programa["param$i"]) continue;

		$fieldname="Programa.param$i";
		switch ($programa["tipo_param$i"])
		{
			case 0://numérico
				echo $this->Form->input($fieldname,array('label'=>$programa["param$i"]));
				break;
			case 1://asignatura
				echo $this->Form->input($fieldname,array('options'=>$asignaturas,'label'=>$programa["param$i"]));
				break;
			case 2://problema
				echo $this->Form->input($fieldname,array('options'=>$problemas,'label'=>$programa["param$i"]));
				break;
		}
	}
	echo $this->Form->end(__('ejecutar'));
?>
<?php if (isset($mensajes)): ?>

<h2><?php echo __('salida'); ?></h2>

<pre>
<?php foreach ($mensajes as $line): ?>
	<?php echo $line?><br/>
<?php endforeach; ?>
</pre>
<?php echo $grafico ?>

<?php endif; ?>
