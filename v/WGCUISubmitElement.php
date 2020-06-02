<?php

class WGCUISubmitElement extends WGCUIElement
{
	/**
	 * @var WGV6Object
	 */
	public $view;

	public function __construct()
	{
		parent::__construct();
		$this->view = new WGV6BasicSubmit();
	}

	public function view()
	{
		return $this->view;
	}

	public function renderer()
	{
		return sprintf('<button id="%s" name="%s" data-error="%s">%s</button>',
			htmlspecialchars($this->view->getId()),
			htmlspecialchars($this->view->getName()),
			htmlspecialchars($this->view->getError()),
			htmlspecialchars($this->view->getValue())
		);
	}
}
