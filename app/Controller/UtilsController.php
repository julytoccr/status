<?php
/**
 * Utils Controller
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 */

class UtilsController extends AppController
{
    var $name = 'Utils';
    var $uses=array('Tarea');
    var $helpers=array();

  function beforeFilter(){
    //parent::beforeFilter();
    if (! $this->_is_localhost_call(array('192.168.0.100')))
    {
      die('Only from localhost');
    }
    $this->Auth->allow('ejecutar_tareas');  
  }

  function ejecutar_tareas($prioridad=null)
  {
    $this->autoRender=false;
    if ($this->Tarea->ejecutar_pendientes($prioridad))
    {
      echo 'OK';
    }
    else
    {
      echo 'ERR';
    }

  }
}
