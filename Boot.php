<?php
namespace MwDump;
use QuestPC as Q;

/**
 * Web root of application should contain 'sdv_engine.txt' file, which
 * should contain the filesystem directory path of the questpc framework.
 * Also, 'sdv_engine.txt' file is used to find $appContext->IP
 * (installation path) of application in hard cases (see questpc/Init.php).
 */
$appContext = new \stdClass();
$appContext->frmDir = @file_get_contents( __DIR__ . '/sdv_engine.txt' );
if ( $appContext->frmDir === false ) {
	throw new \Exception( 'Cannot find framework path file' );
}
$appContext->frmDir = rtrim( str_replace( '\\', '/', $appContext->frmDir ), "/ \n\r\t" );
if ( !file_exists( "{$appContext->frmDir}/Init.php" ) ) {
	throw new \Exception( "Specified framework path is invalid: {$appContext->frmDir}/Init.php" );
}
require_once( "{$appContext->frmDir}/Init.php" );
