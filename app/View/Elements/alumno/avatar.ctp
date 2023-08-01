<?php
	if ($avatar)
	{
		$img='avatars/' . $avatar;
	}
	else
	{
		$img='no_avatar.jpg';
	}
	if (!isset($width)) $width=120;
	if (!isset($height)) $height=120;
	echo $this->Html->image($img,array('width'=>$width,'height'=>$height));
?>
