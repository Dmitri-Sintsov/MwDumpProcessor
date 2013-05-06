<?php
namespace MwDump;
use QuestPC as Q;

if ( !isset( $appContext ) ) {
	die( 'Undefined application context.' );
}

$appContext->localNamespace = 'MwDump';
$appContext->autoloadLocalClasses += array(
	'SiteinfoModel' => 'model/SiteinfoModel.php',
	'PageModel' => 'model/PageModel.php',
	'MwDumpParser' => 'parser/MwDumpParser.php',
	'SiteinfoParser' => 'parser/SiteinfoParser.php',
	'PageParser' => 'parser/PageParser.php',
	'WikitextParser' => 'parser/WikitextParser.php',
);

/**
 * Set local properties of $appContext;
 */
call_user_func( function() {
	global $appContext;
	$appContext->debugging = true;
	$appContext->debugCurl = true;
	$appContext->debugProfiler = true;
	$appContext->debugSQL = true;
	$appContext->debugResourceStreamer = true;
	error_reporting( E_ALL | E_STRICT );
	ini_set( "display_errors", 1 );
	if ( gethostname() === 'cnit19' ) {
		$appContext->forceExternalCurl = true;
	}
} );
# Q\Dbg\log('appContext at the end of LocalSettings.php',$appContext);
