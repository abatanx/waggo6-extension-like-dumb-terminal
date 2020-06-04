<?php
/**
 * waggo6, the extension package like a dumb terminal
 * @copyright 2020 CIEL, K.K.
 * @license MIT
 * Class WGCUICanvas
 */

class WGCUIObject
{
	public $x, $y;
	public $tag, $name, $width, $height, $viewName;
	public $hasForcus;

	/**
	 * @var WGCUIElement
	 */
	public $cuiElement;

	public function __construct()
	{
		$this->tag        = false;
		$this->name       = false;
		$this->viewName   = false;
		$this->cuiElement = false;
		$this->x = 0;
		$this->y = 0;
		$this->width  = 0;
		$this->height = 0;
		$this->hasForcus = false;
	}

	public function setFocus($isFocus)
	{
		$this->hasForcus = $isFocus;
		return $this;
	}
}

