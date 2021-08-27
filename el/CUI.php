<?php

abstract class CUI
{
	/**
	 * @var int
	 */
	public $inputWidth;

	/**
	 * @return WGV6Basic
	 */
	abstract public function view();

	/**
	 * @return string
	 */
	abstract public function renderer();

	/**
	 * WGCUIElement constructor.
	 */
	public function __construct()
	{
		$this->inputWidth = 0;
	}

	public function finishedInitElement()
	{
	}
}
