<?php
/**
 * waggo6, the extension package like a dumb terminal
 * @copyright 2020 CIEL, K.K.
 * @license MIT
 * Class WGCUICanvas
 */

class WGCUIStaticObject extends WGCUIObject
{
	public $label;

	public function __construct()
	{
		parent::__construct();
		$this->label = '';
	}
}

