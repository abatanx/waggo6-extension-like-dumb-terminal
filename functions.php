<?php
/**
 * waggo6, the extension package like a dumb terminal
 * @copyright 2020 CIEL, K.K.
 * @license MIT
 */

function wgcui_utf8_codepoint($c)
{
	return hexdec( bin2hex( mb_convert_encoding( $c, 'UCS-4', 'UTF-8' ) ) );
}

function wgcui_mb_strwidth( $str )
{
	$wr = 0;
	$len = mb_strlen( $str );
	for ( $i = 0; $i < $len; $i ++ )
	{
		$c = mb_substr( $str, $i, 1 );
		$w = mb_strwidth( $c );
		$cp = wgcui_utf8_codepoint($c);
		if( $cp >= 0x1f300 && $cp <= 0x1f5ff ) $w = 2;		// https://www.unicode.org/charts/PDF/U1F300.pdf
		$wr += $w;
	}
	return $wr;
}
