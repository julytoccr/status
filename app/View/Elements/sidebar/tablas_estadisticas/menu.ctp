<?php
	echo $this->Js->link(__('menu_normal'), '/tablas_estadisticas/normal', array('update' => '#tablas'));
	echo $this->Tooltip->show(__('menu_normal'),__('explic_normal'));
	
	echo $this->Js->link(__('menu_tstudent'), '/tablas_estadisticas/tstudent', array('update' => '#tablas'));
	echo $this->Tooltip->show(__('menu_tstudent'),__('explic_tstudent'));

	echo $this->Js->link(__('menu_chiquadrado'), '/tablas_estadisticas/chi2', array('update' => '#tablas'));
	echo $this->Tooltip->show(__('menu_chiquadrado'),__('explic_chiquadrado'));

	echo $this->Js->link(__('menu_fisher'), '/tablas_estadisticas/fisher', array('update' => '#tablas'));
	echo $this->Tooltip->show(__('menu_fisher'),__('explic_fisher'));
	
	echo $this->Js->writeBuffer();
?>
