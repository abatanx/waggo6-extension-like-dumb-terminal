<?php

class WGCUITextElement extends WGCUIElement
{
	/**
	 * @var WGV6Object
	 */
	public $view;

	public function __construct()
	{
		parent::__construct();
		$this->view = new WGV6BasicElement();
	}

	public function view()
	{
		return $this->view;
	}

	public function renderer()
	{
		return sprintf('<input id="%s" type="text" name="%s" maxlength="%d" value="%s" data-error="%s">',
			htmlspecialchars($this->view->getId()),
			htmlspecialchars($this->view->getName()),
			$this->inputWidth,
			htmlspecialchars($this->view->getValue()),
			htmlspecialchars($this->view->getError())
		);
	}
}
