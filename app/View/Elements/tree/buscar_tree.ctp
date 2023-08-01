<?php
$t = $this->Tree->create('problemas');
echo $t->close_all(false);
echo $t->remove_class(".problema",'item_highlight', false);

foreach ($problemas as $data){
	foreach ($data['Problema']['Carpeta'] as $carpeta ){
		echo $t->open('carp_'.$carpeta['id'],false);
		$dom_id = "problemas_carp_{$carpeta['id']}_{$data['Problema']['id']}";
		echo $t->add_class("#$dom_id",'item_highlight');
	}
}
?>
