<?php

$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config.php';

$cfg['directory_list'] = array_merge(
	$cfg['directory_list'],
	[
		'../../extensions/Gadgets',
	]
);

$cfg['exclude_analysis_directory_list'] = array_merge(
	$cfg['exclude_analysis_directory_list'],
	[
		'../../extensions/Gadgets',
	]
);

/**
 * Quick implementation of a recursive directory list.
 * @param string $dir The directory to list
 * @param ?array &$result Where to put the result
 */
function wfCollectPhpFiles( string $dir, ?array &$result = [] ) {
	if ( !is_dir( $dir ) ) {
		return;
	}
	foreach ( scandir( $dir ) as $f ) {
		if ( $f === '.' || $f === '..' ) {
			continue;
		}
		$fullName = $dir . DIRECTORY_SEPARATOR . $f;
		wfCollectPhpFiles( $fullName, $result );
		if ( is_file( $fullName ) && preg_match( '/\.php$/D', $fullName ) ) {
			$result[] = $fullName;
		}
	}
}

// Exclude src/DOM in favour of .phan/stubs/DomImpl.php
wfCollectPhpFiles( "{$VP}/vendor/wikimedia/parsoid/src/DOM", $cfg['exclude_file_list'] );

return $cfg;
