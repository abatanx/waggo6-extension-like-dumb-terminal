<?php

abstract class WGCUIElement
{
	/**
	 * @var int
	 */
	public $inputWidth;

	/**
	 * @return WGV6Object
	 */
	abstract public function view();
	abstract public function renderer();

	public function __construct()
	{
		$this->inputWidth = 0;
	}

	public function finishedInitElement()
	{
	}
}
