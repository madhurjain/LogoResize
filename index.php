<?php

/* Get Data */
if(isset($_GET['image']))
{
	$image = $_GET['image'];
}
else
{
	die("Error : Please specify an image");
}

if(isset($_GET['width']))
{
	$width = $_GET['width'];
}
else
{
	die("Error : Please specify width");
}

if(isset($_GET['height']))
{
	$height = $_GET['height'];
}
else
{
	die("Error : Please specify height");
}

if(isset($_GET['padding']))
{
	$padding = $_GET['padding'];
}
else
{
	die("Error : Please specify padding");
}

/* Check for constraints */
if($width <= 640 && $height <= 480)
{
	if($padding <= (min($width, $height)/4))
	{
		include("logoresize.php");  
		$resizeObj = new LogoResize($image, $width, $height, $padding);
	}
	else
	{
		die("Constraints Error : Padding <= 25% of min(width, height)");
	}
}
else
{
	die("Constraints Error : Width <= 640, Height <= 480");
}



?>