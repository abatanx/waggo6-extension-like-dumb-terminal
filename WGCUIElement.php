<?php
/**
 * waggo6, the extention package like a dumb terminal
 * @copyright 2020 CIEL, K.K.
 * @license MIT
 * Class WGCUICanvas
 */

class WGCUIElement
{
	public $x, $y;
	public $tag, $name, $width, $height, $viewName;

	/**
	 * @var JVCUIElement
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
	}
}

