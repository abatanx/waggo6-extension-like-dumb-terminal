<?php
/**
 * waggo6
 * @copyright 2020 CIEL, K.K., project waggo.
 * @license MIT
 */

class WGCUIPagination extends WGV6Object
{
	private
		$limit  = 0,
		$count  = 0,
		$total  = 0,
		$page   = 0;

	protected
		$pageKey,
		$limitKey;

	public function __construct( $limit, $pagekey = "wgpp", $limitkey = "wgpl" )
	{
		parent::__construct();
		if ( ! is_numeric( $limit ) )
		{
			die( "WGCUIPagination, Invalid limit parameter, '{$limit}'.\n" );
		}

		$this->pageKey  = $pagekey;
		$this->limitKey = $limitkey;

		$this->page  = ( ! @wg_inchk_int( $this->page, $_GET[ $this->pageKey ], 1 ) ) ? 1 : $this->page;
		$this->limit = ( ! @wg_inchk_int( $this->limit, $_GET[ $this->limitKey ], 1 ) ) ? $limit : $this->limit;
	}

	public function url( $page, $remakeopts = "" )
	{
		return wg_remake_uri( [ $this->pageKey => $page, $this->limitKey => $this->limit ] );
	}

	public function offset()
	{
		$offset = ( $this->page - 1 ) * $this->limit;

		return $offset;
	}

	public function limit()
	{
		$limit = $this->limit;

		return $limit;
	}

	public function setTotal( $total )
	{
		$this->total = $total;

		// ページ数チェック
		$mp = (int) ( ( $this->total - 1 ) / $this->limit ) + 1;
		if ( $this->page < 1 )
		{
			$this->page = 1;
		}
		if ( $this->page > $mp )
		{
			$this->page = $mp;
		}
	}

	public function getPage()
	{
		return $this->page;
	}

	public function getMaxPage()
	{
		return (int) ( ( $this->total - 1 ) / $this->limit ) + 1;
	}

	public function isFirstPage()
	{
		return ( $this->page <= 1 );
	}

	public function isLastPage()
	{
		$mp = (int) ( ( $this->total - 1 ) / $this->limit ) + 1;

		return ( $this->page >= $mp );
	}

	public function count()
	{
		$this->count ++;

		return $this->limit * ( $this->page - 1 ) + $this->count;
	}

	public function countRevert()
	{
		$this->count ++;

		return $this->total - ( $this->limit * ( $this->page - 1 ) + $this->count ) + 1;
	}

	protected function firstCaption()
	{
		return "";
	}

	protected function lastCaption()
	{
		return "";
	}

	protected function allCaption()
	{
		return "";
	}

	public function getPrevURL()
	{
		return $this->page > 1 ? $this->url( $this->page - 1 ) : false;
	}

	public function goPrevPage()
	{
		$u = $this->getPrevURL();
		if( $u ) wg_location( $u );
	}

	public function getNextURL()
	{
		$max_page = $this->getMaxPage();

		return $this->page < $max_page ? $this->url( $this->page + 1 ) : false;
	}

	public function goNextPage()
	{
		$u = $this->getNextURL();
		if( $u ) wg_location( $u );
	}

	public function formHtml()
	{
		return $this->showHtml();
	}

	public function showHtml()
	{
		return "";
	}
}
