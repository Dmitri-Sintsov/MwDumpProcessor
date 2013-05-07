<?php
namespace MwDump;
use QuestPC as Q;

if ( php_sapi_name() !== 'cli' ) {
	die();
}
 
require_once( 'Boot.php' );

if ( count( $appContext->argv ) < 2 ) {
	Q\Gl::dieUsage( 'dumpBackup_script_output.xml output.xml' );
}

class MwDump {

	protected $wikiParser;

	public function __construct() {
		$this->wikiParser = new WikitextParser();
	}

	/**
	 * Used to sanitize dump using XMLWriter formatting.
	 */
	public function noopHook( PageModel $pageModel ) {
	}
	
	public function addArchiveCategory( PageModel $pageModel ) {
		if ( $pageModel->getSubProp( 'pageProps', 'ns' ) === 0 ) {
			$this->wikiParser->setProp( 'pageModel', $pageModel );
			if ( $this->wikiParser->addCategory( 'Архив' ) ) {
				$pageModel->setTimestamp( Q\Gl::timeStamp() );
			}
		}
	}

	public function renameCategory( PageModel $pageModel ) {
		if ( $pageModel->getSubProp( 'pageProps', 'ns' ) === 0 ) {
			$this->wikiParser->setProp( 'pageModel', $pageModel );
			if ( $this->wikiParser->renameCategory( 'Публикации', 'Наука' ) ) {
				$pageModel->setTimestamp( Q\Gl::timeStamp() );
			}
		}
	}

	public function forceRenameCategory( PageModel $pageModel ) {
		if ( $pageModel->getSubProp( 'pageProps', 'ns' ) === 0 ) {
			$this->wikiParser->setProp( 'pageModel', $pageModel );
			if ( $this->wikiParser->deleteCategory( 'События' ) ) {
				$pageModel->setTimestamp( Q\Gl::timeStamp() );
				$this->wikiParser->addCategory( 'Архив' );
			}
		}
	}

	public function process( $inFileName, $outFileName ) {
		$dumpParser = MwDumpParser::newFromUri( $inFileName );
		$dumpParser->setOutputFileName( $outFileName );
		# $dumpParser->addPageHook( array( $this, 'noopHook' ) );
		# $dumpParser->addPageHook( array( $this, 'addArchiveCategory' ) );
		# $dumpParser->addPageHook( array( $this, 'renameCategory' ) );
		$dumpParser->addPageHook( array( $this, 'forceRenameCategory' ) );
		$dumpParser->parse();
	}

} /* end of MwDump class */

try {
	$mwDump = new MwDump;
	$mwDump->process( $appContext->argv[0], $appContext->argv[1] );
} catch ( \Exception $e ) {
	Q\Dbg\except_die( $e );
}
