<?php
/**
 * waggo6, the extention package like a dumb terminal
 * @copyright 2020 CIEL, K.K.
 * @license MIT
 * Class WGCUICanvas
 */

class WGCUICSSLine
{
	const HORIZONTAL = 0, VERTICAL = 1;
	public $x, $y, $length, $direction;

	public function __construct($direction)
	{
		$this->x = 0;
		$this->y = 0;
		$this->length = 0;
		$this->direction = $direction;
	}

	public function isValidLength()
	{
		return $this->length > 1;
	}
}

