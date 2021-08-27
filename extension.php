<?php
/**
 * waggo6, the extension package like a dumb terminal
 * @copyright 2020 CIEL, K.K.
 * @license MIT
 */

global $WGCONF_AUTOLOAD;
$WGCONF_AUTOLOAD = array_merge(
	$WGCONF_AUTOLOAD,
	[
		__DIR__,
		__DIR__ . '/v',
		__DIR__ . '/el'
	]
);

require_once __DIR__ . '/functions.php';
