<?php
/**
 * waggo6, the extension package like a dumb terminal
 * @copyright 2020 CIEL, K.K.
 * @license MIT
 * Class WGCUICanvas
 */

class WGCUICanvas extends WGHtmlCanvas
{
	protected $cuiWidth, $cuiHeight;

	protected $matrixLine, $cssLine;

	/**
	 * @var WGCUIObject[]
	 */
	protected $elements;
	protected $staticElements;
	protected $groupedElements;

	protected $arrayViews;

	public function __construct( $cuiWidth, $cuiHeight )
	{
		parent::__construct();

		$this->cuiWidth  = $cuiWidth;
		$this->cuiHeight = $cuiHeight;

		$this->html['charViewportWidth']  = sprintf( '%fvw', 100 / $cuiWidth * 2.0 );
		$this->html['charViewportHeight'] = sprintf( '%fvw', 100 / $cuiWidth * 2.0 );

		$this->html['cuiViewportWidth']  = sprintf( '%fvw', 100 );
		$this->html['cuiViewportHeight'] = sprintf( '%fvw', 100 / $cuiWidth * 2.0 * $cuiHeight );

		$this->html['w'] = sprintf( "%svw", 100 / $cuiWidth );
		$this->html['h'] = sprintf( "%svw", 100 / $cuiWidth * 2.0 );
	}

	public function loadLayout( $file )
	{
		$this->arrayViews     = [];
		$this->elements       = [];
		$this->staticElements = [];
		$this->cssLine        = [];
		$this->matrixLine     = array_fill( 0, $this->cuiHeight, array_fill( 0, $this->cuiWidth, false ) );

		$contents = file_get_contents( $file );
		list( $layout, $setting ) = preg_split( '/^=+$/m', $contents );

		// Layout tag settings
		$tags     = [];
		$settings = explode( "\n", $setting );

		// Add Function Key Lines
		$fnKeysLayoutLines = [];
		for ( $fn = 1; $fn <= 12; $fn ++ )
		{
			$fnKeysLayoutLines[] = sprintf( "FN%02d FN%02d CUISubmit", $fn, $fn );
		}

		// Merge user layout + fn layout
		$settings = array_merge( $settings, $fnKeysLayoutLines );

		foreach ( $settings as $sl )
		{
			$sp = preg_split( '/\s+/', trim( $sl ) );
			if ( count( $sp ) >= 3 )
			{
				list( $tag, $name, $type ) = $sp;
				if ( ! empty( $tag ) && ! empty( $name ) && ! empty( $type ) )
				{
					$t       = new stdClass();
					$t->tag  = $tag;
					$t->name = $name;
					$t->type = $type;
					$tags[]  = $t;
				}
			}
		}
		usort( $tags, function ( $a, $b ) {
			$len = strlen( $b->tag ) - strlen( $a->tag );

			return $len !== 0 ? $len : strcmp( $a->tag, $b->tag );
		} );

		// Parse layout lines
		$layoutLines = array_slice( explode( "\n", $layout ), 0, $this->cuiHeight );

		// Interactive Elements
		foreach ( $layoutLines as $y => &$line )
		{
			foreach ( $tags as $tag )
			{
				$line = preg_replace_callback( '/^(.*)(' . $tag->tag . '\.*)(.*)$/u', function ( $m ) use ( $y, $tag ) {
					$element                         = new WGCUIObject();
					$element->tag                    = $tag->tag;
					$element->name                   = $tag->name;
					$element->x                      = wgcui_mb_strwidth( $m[1] );
					$element->y                      = $y;
					$element->width                  = wgcui_mb_strwidth( $m[2] );
					$element->height                 = 1;
					$element->cuiElement             = new $tag->type;
					$element->cuiElement->inputWidth = $element->width;
					$this->elements[]                = $element;

					return $m[1] . str_pad( '', wgcui_mb_strwidth( $m[2] ), ' ' ) . $m[3];
				}, $line );
			}
		}
		unset( $line );

		usort( $this->elements, function ( $a, $b ) {
			return $a->y - $b->y === 0 ? $a->x - $b->x : $a->y - $b->y;
		} );

		// Static Elements
		foreach ( $layoutLines as $y => &$line )
		{
			do
			{
				$backup_line = $line;
				$line        = preg_replace_callback( '/([^\-|\\\+ ]+)/u', function ( $m ) use ( $y, $line ) {
					$pos = mb_strpos( $line, $m[1] );
					$x   = wgcui_mb_strwidth( mb_substr( $line, 0, $pos ) );

					$static                 = new WGCUIStaticObject();
					$static->x              = $x;
					$static->y              = $y;
					$static->label          = $m[1];
					$static->width          = wgcui_mb_strwidth( $m[1] );
					$static->height         = 1;
					$this->staticElements[] = $static;

					return str_pad( '', wgcui_mb_strwidth( $m[1] ), ' ' );
				}, $line );
			}
			while ( $line !== $backup_line );
		}
		unset( $line );

		// Border
		foreach ( $layoutLines as $y => $line )
		{
			$x   = 0;
			$len = mb_strlen( $line );
			for ( $i = 0; $i < $len; $i ++ )
			{
				$c = mb_substr( $line, $i, 1 );
				switch ( $c )
				{
					case '-':
					case '+':
					case '|':
						$this->matrixLine[ $y ][ $x ] = true;
						break;
				}
				$x += wgcui_mb_strwidth( $c );
			}
		}

		foreach ( [ WGCUICSSLine::HORIZONTAL, WGCUICSSLine::VERTICAL ] as $direction )
		{
			list( $ii, $jj ) =
				$direction === WGCUICSSLine::HORIZONTAL ?
					[ $this->cuiHeight, $this->cuiWidth ] : [ $this->cuiWidth, $this->cuiHeight ];

			for ( $i = 0; $i < $ii; $i ++ )
			{
				$cssLine = false;
				for ( $j = 0; $j < $jj; $j ++ )
				{
					list( $x, $y ) = $direction === WGCUICSSLine::HORIZONTAL ? [ $j, $i ] : [ $i, $j ];
					$f = $this->matrixLine[ $y ][ $x ];

					if ( $f )
					{
						if ( $cssLine instanceof WGCUICSSLine )
						{
							$cssLine->length ++;
						}
						else
						{
							$cssLine         = new WGCUICSSLine( $direction );
							$cssLine->x      = $x;
							$cssLine->y      = $y;
							$cssLine->length = 1;
						}
					}
					else
					{
						if ( $cssLine instanceof WGCUICSSLine )
						{
							if ( $cssLine->isValidLength() )
							{
								$this->cssLine[] = $cssLine;
							}
							$cssLine = false;
						}
					}
				}
				if ( $cssLine instanceof WGCUICSSLine )
				{
					if ( $cssLine->isValidLength() )
					{
						$this->cssLine[] = $cssLine;
					}
				}
			}
		}

		$this->groupedElements = [];
		foreach ( $this->elements as $element )
		{
			$this->groupedElements[ $element->name ][] = $element;
		}

		foreach ( $this->groupedElements as $name => $element_array )
		{
			if ( count( $element_array ) === 1 )
			{
				$element_array[0]->viewName = $name;
			}
			else
			{
				foreach ( $element_array as $n => $element )
				{
					$element->viewName = sprintf( "%s_%d", $name, $n );
				}
			}
		}

		return $this;
	}

	public function makeViews()
	{
		$views = [];
		/**
		 * @var WGCUIObject $element
		 */
		foreach ( $this->elements as $element )
		{
			$element->cuiElement->view()->setId( $element->viewName );
			$views[ $element->viewName ] = $element->cuiElement->view();
		}

		return $views;
	}

	/**
	 * @param string $name Name of registered grouped views.
	 *
	 * @return WGV6Object[]
	 */
	public function getGroupedViews( $name )
	{
		if ( isset( $this->groupedElements[ $name ] ) )
		{
			return array_map( function ( $v ) {
				return $v->cuiElement->view();
			}, $this->groupedElements[ $name ] );
		}

		return [];
	}

	/**
	 * @param string $name Name of registered grouped views.
	 *
	 * @return int Count of grouped view elements.
	 */
	public function getGroupedViewsCount( $name )
	{
		return count( $this->getGroupedViews( $name ) );
	}

	/**
	 * Search CUIObject
	 *
	 * @param WGV6Basic
	 *
	 * @return WGCUIObject|boolean Return CUIObject if it found, else false.
	 */
	public function findCUIObject( $view )
	{
		foreach ( $this->elements as $e )
		{
			if ( $e->cuiElement->view() == $view )
			{
				return $e;
			}
		}

		return false;
	}

	public function makeHtml()
	{
		// Rendering
		foreach ( $this->cssLine as $cssLine )
		{
			if ( $cssLine->direction == WGCUICSSLine::HORIZONTAL )
			{
				$this->html['cssHorizontalLines'][] = [
					'x'   => $cssLine->x,
					'y'   => $cssLine->y,
					'len' => $cssLine->length
				];
			}
			else
			{
				$this->html['cssVerticalLines'][] = [
					'x'   => $cssLine->x,
					'y'   => $cssLine->y,
					'len' => $cssLine->length
				];
			}
		}

		foreach ( $this->staticElements as $static )
		{
			$this->html['cssStatics'][] = [
				'x'      => $static->x,
				'y'      => $static->y,
				'width'  => $static->width,
				'height' => $static->height,
				'label'  => $static->label
			];
		}

		/**
		 * @var WGCUIObject $element
		 */
		foreach ( $this->elements as $element )
		{
			$this->html['cssElements'][] = [
				'x'      => $element->x,
				'y'      => $element->y,
				'view'   => $element->cuiElement->renderer(),
				'width'  => $element->width,
				'height' => $element->height
			];
		}

		/**
		 * Focus
		 */
		$elementSortBuffer = array_filter( $this->elements, function ( $v ) {
			return $v->hasForcus;
		} );
		usort( $elementSortBuffer, function ( $a, $b ) {
			/**
			 * @var WGCUIObject
			 */
			if ( $a->y !== $b->y )
			{
				return $a->y - $b->y;
			}
			if ( $a->x !== $b->x )
			{
				return $a->x - $b->x;
			}

			return 0;
		} );
		$elementSortBuffer = array_values( $elementSortBuffer );
		if ( count( $elementSortBuffer ) > 0 )
		{
			/**
			 * @var WGCUIObject[] $elementSortBuffer
			 */
			$this->html['focusElement'] = $elementSortBuffer[0]->cuiElement->view()->getId();
		}

		return $this;
	}

	function build()
	{
		$this->makeHtml();

		return HtmlTemplate::buffer( __DIR__ . '/cui.html', $this->html );
	}

	function buildAndFlush()
	{
		$this->makeHtml();
		HtmlTemplate::include( __DIR__ . '/cui.html', $this->html );
	}
}
