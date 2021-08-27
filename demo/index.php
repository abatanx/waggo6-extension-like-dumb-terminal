<?php

require_once 'waggo.php';

class Index extends WGCUIPCController
{
	protected function afterViews()
	{
		$this->view( 'FN01' )->setValue( '+' );
	}

	protected function _FN01()
	{
		$a = $this->view( 'a' )->getValue();
		$b = $this->view( 'b' )->getValue();
		$this->view( 'c' )->setValue( $a + $b );
		return false;
	}

}

Index::START();
