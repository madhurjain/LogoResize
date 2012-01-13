<?php

Class LogoResize
{
	private $image;
	private $imageType;
	private $resizedImage;
	private $width;
	private $height;
	
	public function __construct($imageUrl, $resizedWidth, $resizedHeight, $padding)
	{
		$this->image = $this->loadImage($imageUrl);

		/* Get width/height of the original image */
		$this->width = imagesx($this->image);
		$this->height = imagesy($this->image);
		
		$this->resizeImage($resizedWidth, $resizedHeight, $padding);
		
		$fileName = basename($imageUrl);
		
		switch($this->imageType)
		{
			case IMAGETYPE_JPEG:
				header('Content-Disposition: inline;filename='.$fileName); 
				header('Content-Type: image/jpeg');
				imagejpeg($this->resizedImage);
				break;
			case IMAGETYPE_PNG:
				header('Content-Disposition: inline;filename='.$fileName); 
				header('Content-Type: image/png');
				imagepng($this->resizedImage);
				break;
			case IMAGETYPE_GIF:
				header('Content-Disposition: inline;filename='.$fileName); 
				header('Content-Type: image/gif');
				imagegif($this->resizedImage);
				break;
			default:
				break;			
		}

		imagedestroy($this->image);
		imagedestroy($this->resizedImage);
	}
	
	private function loadImage($imageLoc)
	{
		$imageInfo = getimagesize($imageLoc);
		$this->imageType = $imageInfo[2];
	
		switch($this->imageType)
		{
			case IMAGETYPE_JPEG:
				$img = @imagecreatefromjpeg($imageLoc);
				break;
			case IMAGETYPE_PNG:
				$img = @imagecreatefrompng($imageLoc);
				break;
			case IMAGETYPE_GIF:
				$img = @imagecreatefromgif($imageLoc);
				break;
			default:
				$img = false;
				break;
		}
		return $img;
	}
	
	private function getSizeByFixedWidth($logoWidth)
	{
		$ratio = $this->height / $this->width;
		$logoHeight = $logoWidth * $ratio;
		return $logoHeight;
	}
	
	private function getSizeByFixedHeight($logoHeight)
	{
		$ratio = $this->width / $this->height;
		$logoWidth = $logoHeight * $ratio;
		return $logoWidth;
	}
	
	private function resizeImage($resizedWidth, $resizedHeight, $padding)
	{
		/* Cooked Up */
		if($resizedHeight > $this->height)
		{
			if(($padding*2) > ($resizedHeight - $this->height))
			{
				$logoHeight = $resizedHeight - ($padding * 2);
			}
			else
			{
				$logoHeight = $this->height + ($resizedHeight - ($padding * 2) - $this->height);
			}
		}
		else
		{
			$logoHeight = $resizedHeight - ($padding*2);
		}

		if($resizedWidth > $this->width)
		{
			if(($padding*2) > ($resizedWidth - $this->width))
			{
				$logoWidth = $resizedWidth - ($padding * 2);
			}
			else
			{
				$logoWidth = $this->width + ($resizedWidth - ($padding * 2) - $this->width);
			}
		}
		else
		{
			$logoWidth = $resizedWidth - ($padding*2);
		}
		
		if($logoWidth < $logoHeight)
		{
			$logoHeight = $this->getSizeByFixedWidth($logoWidth);
		}
		else if($logoWidth > $logoHeight)
		{
			$logoWidth = $this->getSizeByFixedHeight($logoHeight);
		}
		else
		{
			if($this->height < $this->width)
			{
				$logoHeight = $this->getSizeByFixedWidth($logoWidth);
			}
			else if($this->height > $this->width)
			{
				$logoWidth = $this->getSizeByFixedHeight($logoHeight);
			}
		}
		
	/*
		if($this->height < $this->width)		// Wide(Landscape)
		{
			$logoWidth = ($resizedWidth - ($padding * 2));
			$logoHeight = $this->getSizeByFixedWidth($logoWidth);
		}
		else if($this->height > $this->width)	// Tall(Portrait)
		{
			$logoHeight = ($resizedHeight - ($padding * 2));
			$logoWidth = $this->getSizeByFixedHeight($logoHeight);
		}
		else									// Square
		{
			if($resizedHeight < $resizedWidth)
			{
				$logoWidth = ($resizedWidth - ($padding * 2));
				$logoHeight = $this->getSizeByFixedWidth($logoWidth);
			}
			else if($resizedHeight > $resizedWidth)
			{
				$logoHeight = ($resizedHeight - ($padding * 2));
				$logoWidth = $this->getSizeByFixedHeight($logoHeight);
			}
			else
			{
				$logoWidth = ($resizedWidth - ($padding * 2));
				$logoHeight = ($resizedHeight - ($padding * 2));
			}			
		}
		*/
		$xLoc = ($resizedWidth - $logoWidth) / 2;
		$yLoc = ($resizedHeight - $logoHeight) / 2;
		
		$this->resizedImage = imagecreatetruecolor($resizedWidth, $resizedHeight);
		
		$this->setImageBackground();
		
		imagecopyresampled($this->resizedImage, $this->image, $xLoc, $yLoc, 0, 0, $logoWidth, $logoHeight, $this->width, $this->height);  

	}
	
	private function setImageBackground()
	{
		$colorIndex = imagecolorat($this->image, 1, 1);
		$colorRGB = imagecolorsforindex($this->image, $colorIndex);
		
		$bgColor = imagecolorallocatealpha($this->resizedImage, $colorRGB['red'], $colorRGB['green'], $colorRGB['blue'], $colorRGB['alpha']);
		
		imagefill($this->resizedImage, 0, 0, $bgColor);
		
		//imagesavealpha($this->resizedImage, true);
		//$trans_colour = imagecolorallocatealpha($this->resizedImage, 0, 0, 0, 127);
		//imagefill($this->resizedImage, 0, 0, $trans_colour);
	}
}



?>