<?php

class CUISubmit extends CUI
{
	/**
	 * @var WGV6Basic
	 */
	public $view;

	public function __construct()
	{
		parent::__construct();
		$this->view = new WGCUIV6Submit();
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
