<?php
/**
 * waggo6, the extension package like a dumb terminal
 * @copyright 2020 CIEL, K.K.
 * @license MIT
 * Class WGCUICanvas
 */

require_once __DIR__ . '/extension.php';
require_once WGCONF_DIR_FRAMEWORK_CONTROLLER . "/WGFPCController.php";

abstract class WGCUIPCController extends WGFPCController
{
	/**
	 * @var WGCUICanvas $pageCanvas
	 */
	public $pageCanvas;

	public function __construct()
	{
		parent::__construct();
		$this->appCanvas->setTemplate( __DIR__ . '/tpl/cuiroot.html' );
	}

	public function initCanvas()
	{
		parent::initCanvas();
		$this->pageCanvas = new WGCUICanvas( 80, 25 );
	}

	public function views()
	{
		return $this->pageCanvas->loadLayout( $this->layout() )->makeViews();
	}

	public function element( $name )
	{
		return $this->pageCanvas->findCUIObject( $this->view( $name ) );
	}

	public function layout()
	{
		return $this->defaultTemplate();
	}

	protected function render()
	{
		parent::render();
	}

	protected function _FN01()
	{
		return false;
	}

	protected function _FN02()
	{
		return false;
	}

	protected function _FN03()
	{
		return false;
	}

	protected function _FN04()
	{
		return false;
	}

	protected function _FN05()
	{
		return false;
	}

	protected function _FN06()
	{
		return false;
	}

	protected function _FN07()
	{
		return false;
	}

	protected function _FN08()
	{
		return false;
	}

	protected function _FN09()
	{
		return false;
	}

	protected function _FN10()
	{
		return false;
	}

	protected function _FN11()
	{
		return false;
	}

	protected function _FN12()
	{
		return false;
	}

	protected function defaultTemplate()
	{
		$s = realpath( $_SERVER["SCRIPT_FILENAME"] );
		$d = dirname( $s );
		$b = basename( $s );
		$e = "_" . preg_replace( '/\..+$/', '.txt', $b );
		$p = "{$d}/{$e}";

		return $p;
	}
}

