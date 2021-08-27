<?php

class CUIPassword extends CUI
{
	/**
	 * @var WGV6Basic
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
		return sprintf('<input id="%s" type="password" name="%s" value="%s" data-error="%s">',
			htmlspecialchars($this->view->getId()),
			htmlspecialchars($this->view->getName()),
			htmlspecialchars($this->view->getValue()),
			htmlspecialchars($this->view->getError())
		);
	}
}
