<?php
/**
 * waggo6, the extention package like a dumb terminal
 * @copyright 2020 CIEL, K.K.
 * @license MIT
 * Class WGCUICanvas
 */

class WGCUICanvas extends WGHtmlCanvas
{
	protected $cuiWidth, $cuiHeight;

	protected $matrixLine, $cssLine;
	protected $elements, $staticElements;
	protected $groupedElements;

	protected $arrayViews;

	public function __construct($cuiWidth, $cuiHeight)
	{
		parent::__construct();

		$this->cuiWidth  = $cuiWidth ;
		$this->cuiHeight = $cuiHeight;

		$this->html['charViewportWidth']  = sprintf('%fvw', 100 / $cuiWidth * 2.0);
		$this->html['charViewportHeight'] = sprintf('%fvw', 100 / $cuiWidth * 2.0);

		$this->html['cuiViewportWidth']   = sprintf('%fvw', 100 );
		$this->html['cuiViewportHeight']  = sprintf('%fvw', 100 / $cuiWidth * 2.0 * $cuiHeight);

		$this->html['w'] = sprintf("%svw", 100 / $cuiWidth);
		$this->html['h'] = sprintf("%svw", 100 / $cuiWidth * 2.0);
	}

	public function loadLayout($file)
	{
		$this->arrayViews     = [];
		$this->elements       = [];
		$this->staticElements = [];
		$this->cssLine        = [];
		$this->matrixLine     = array_fill( 0, $this->cuiHeight, array_fill( 0, $this->cuiWidth, false ) );

		$contents = file_get_contents( $file );
		list( $layout, $setting ) = preg_split( '/^=+$/m', $contents );

		// Layout tag settings
		$tags = [];
		$settings = explode( "\n", $setting );

		// Add Function Key Lines
		$fnKeysLayoutLines = [];
		for( $fn=1; $fn<=12; $fn++ )
		{
			$fnKeysLayoutLines[] = sprintf("FN%02d FN%02d JVCUISubmitElement", $fn, $fn);
		}

		// Merge user layout + fn layout
		$settings = array_merge($settings, $fnKeysLayoutLines);

		foreach ( $settings as $sl )
		{
			$sp = preg_split( '/\s+/', trim( $sl ) );
			if( count($sp) >= 3 )
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
					$element             = new WGCUIElement();
					$element->tag        = $tag->tag;
					$element->name       = $tag->name;
					$element->x          = mb_strwidth( $m[1] );
					$element->y          = $y;
					$element->width      = mb_strwidth( $m[2] );
					$element->height     = 1;
					$element->cuiElement = new $tag->type;
					$element->cuiElement->inputWidth = $element->width;
					$this->elements[]    = $element;

					return $m[1] . str_pad( '', mb_strwidth( $m[2] ), ' ' ) . $m[3];
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
				$line        = preg_replace_callback( '/([^\-\|\+ ]+)/u', function ( $m ) use ( $y, $line ) {
					$pos = mb_strpos( $line, $m[1] );
					$x   = mb_strwidth( mb_substr( $line, 0, $pos ) );

					$static                 = new WGCUIStaticElement();
					$static->x              = $x;
					$static->y              = $y;
					$static->label          = $m[1];
					$static->width          = mb_strwidth( $m[1] );
					$static->height         = 1;
					$this->staticElements[] = $static;

					return str_pad( '', mb_strwidth( $m[1] ), ' ' );
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
				$x += mb_strwidth( $c );
			}
			$y ++;
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
		 * @var WGCUIElement $element
		 */
		foreach( $this->elements as $element )
		{
			$v = new WGV6BasicElement();
			$views[$element->viewName] = $element->cuiElement->view();
		}
		return $views;
	}

	/**
	 * @param string $name key name
	 * @return WGV6Object[]
	 */
	public function getGroupedViews($name)
	{
		if( isset($this->groupedElements[$name]) )
		{
			return array_map( function($v) { return $v->cuiElement->view(); }, $this->groupedElements[$name]);
		}
		return [];
	}

	public function makeHtml()
	{
		// Rendering
		foreach( $this->cssLine as $cssLine )
		{
			if( $cssLine->direction == WGCUICSSLine::HORIZONTAL )
			{
				$this->html['cssHorizontalLines'][] = [ 'x' => $cssLine->x, 'y' => $cssLine->y, 'len' => $cssLine->length];
			}
			else
			{
				$this->html['cssVerticalLines'][] = [ 'x' => $cssLine->x, 'y' => $cssLine->y, 'len' => $cssLine->length];
			}
		}

		foreach( $this->staticElements as $static )
		{
			$this->html['cssStatics'][] = [
				'x' => $static->x, 'y' => $static->y,
				'width' => $static->width, 'height' => $static->height,
				'label' => $static->label
			];
		}

		/**
		 * @var WGCUIElement $element
		 */
		foreach( $this->elements as $element )
		{
			$view = sprintf('<input id="%s" type="text" name="%s" value="%s" data-error="%s">',
				$this->html[$element->viewName.':id'],
				$this->html[$element->viewName.':name'],
				$this->html[$element->viewName.':value'],
				$this->html[$element->viewName.':error']
			);

			$this->html['cssElements'][] = [
				'x' => $element->x, 'y' => $element->y, 'view' => $element->cuiElement->renderer(),
				'width' => $element->width, 'height' => $element->height
			];
		}

		return $this;
	}

	function build()
	{
		$this->makeHtml();
		return HtmlTemplate::buffer(__DIR__ . '/cui.html', $this->html);
	}

	function buildAndFlush()
	{
		$this->makeHtml();
		HtmlTemplate::include(__DIR__ . '/cui.html', $this->html);
	}
}
