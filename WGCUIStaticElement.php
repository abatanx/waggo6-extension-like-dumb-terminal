<?php
/**
 * waggo6, the extention package like a dumb terminal
 * @copyright 2020 CIEL, K.K.
 * @license MIT
 * Class WGCUICanvas
 */

class WGCUIStaticElement extends WGCUIElement
{
	public $label;

	public function __construct()
	{
		parent::__construct();
		$this->label = '';
	}
}

