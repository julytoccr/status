<?php
$hostname = SERVER_NAME; //php_uname('n'); no funciona, dice 'ka' en vez de 'ka.upc.edu' //gethostname(); this only works on php 5.3 and above //$_SERVER["HTTP_HOST"]; esto no sirve porque las tareas se ejecutan desde 127.0.0.0
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>e-status :: Notification</title>
<link rel="shortcut icon" href="http://<?php echo $hostname;?>?/favicon.ico" type="image/x-icon" /><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><link rel="stylesheet" type="text/css" href="http://<?php echo $hostname;?>/css/default.css" />
<script type="text/javascript" src="http://<?php echo $hostname;?>/js/prototype.js"></script><script type="text/javascript" src="http://<?php echo $hostname;?>/js/scriptaculous.js"></script><script type="text/javascript" src="http://<?php echo $hostname;?>/js/effects.js"></script><script type="text/javascript" src="http://<?php echo $hostname;?>/js/controls.js"></script><script type="text/javascript" src="http://<?php echo $hostname;?>/js/herramientas.js"></script><script type="text/javascript" src="http://<?php echo $hostname;?>/js/flash_message.js"></script>
<script type="text/javascript"><!--
Event.observe(window,'load',function() { new Insertion.Bottom($(document.body),'<div id=\"page_container\" style=\"display: none;\"/>');
});
--></script>
 
<!--[if lt IE 7]>
			<script type="text/javascript" src="http://<?php echo $hostname;?>/js/ie.js"></script>		<link rel="stylesheet" type="text/css" href="http://<?php echo $hostname;?>/css/ie.css" />		<script type="text/javascript" src="http://<?php echo $hostname;?>/js/ie7/ie7-standard-p.js"></script>	<![endif]-->
 
</head>
<body>
	<div id="header">
	  <div class="subHeader">
	  	    	<a href="http://<?php echo $hostname;?>/" >Start</a>|
	    	<a href="http://www-eio.upc.es/personal/homepages/josean/e-status/help-en.html" >Help</a>|
	    	<a href="http://<?php echo $hostname;?>/pages/acerca_de" >About</a>
	  </div>
	  <div class="midHeader">
	        <img src="http://<?php echo $hostname;?>/img/logo_transp.gif" alt="e-status logo" height="90" />	  </div>
	</div>
  
    <div class="main" style="color: black; background-color: white; text-align: justify; line-height: 1.5em; margin: 0 0 0 0em; padding: 0mm 0mm 0mm 0mm; border-left: 1px solid rgb(153,153,153)">
    	<h1>
    		<img src="http://<?php echo $hostname;?>/img/squares.gif" alt="" align="left" />    		&nbsp;
    		<?php echo __('notificacion'); ?></h1>
		<br/>
			    <div id="content_for_layout">
				<?php echo $this->fetch('content');?>
			    </div>
	</div>
    <div id="footer">
      <div class="left">
        
      </div>
 
      <div class="right">
      </div>
    </div>
</body>
</html>
