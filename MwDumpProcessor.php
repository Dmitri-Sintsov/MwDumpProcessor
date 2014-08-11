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
		return true;
	}
	
	public function addArchiveCategory( PageModel $pageModel ) {
		if ( $pageModel->getSubProp( 'pageProps', 'ns' ) === PageModel::NS_MAIN ) {
			$this->wikiParser->setProp( 'pageModel', $pageModel );
			if ( $this->wikiParser->addCategory( 'Архив' ) ) {
				$pageModel->setTimestamp( Q\Gl::timeStamp() );
			}
		}
		return true;
	}

	public function renameCategory( PageModel $pageModel ) {
		if ( $pageModel->getSubProp( 'pageProps', 'ns' ) === PageModel::NS_MAIN ) {
			$this->wikiParser->setProp( 'pageModel', $pageModel );
			if ( $this->wikiParser->renameCategory( 'Публикации', 'Наука' ) ) {
				$pageModel->setTimestamp( Q\Gl::timeStamp() );
			}
		}
		return true;
	}

	public function forceRenameCategory( PageModel $pageModel ) {
		if ( $pageModel->getSubProp( 'pageProps', 'ns' ) === PageModel::NS_MAIN ) {
			$this->wikiParser->setProp( 'pageModel', $pageModel );
			if ( $this->wikiParser->deleteCategory( 'События' ) ) {
				$pageModel->setTimestamp( Q\Gl::timeStamp() );
				$this->wikiParser->addCategory( 'Архив' );
			}
		}
		return true;
	}

	public function filterFiles( PageModel $pageModel ) {
		return $pageModel->getSubProp( 'pageProps', 'ns' ) === PageModel::NS_FILE;
	}

	public function touchModified( PageModel $pageModel ) {
		if ( $pageModel->getSubProp( 'revProps', 'base36sha1' ) !==
			$pageModel->getSubProp( 'textProps', 'base36sha1' ) ) {
			$pageModel->setTimestamp( time() );
			$pageModel->setRevBase36Sha1( $pageModel->getSubProp( 'textProps', 'base36sha1' ) );
			# Q\Dbg\log('modifiedPage',$pageModel);
			return true;
		} else {
			return false;
		}
	}

	public function process( $inFileName, $outFileName ) {
		$dumpParser = MwDumpParser::newFromUri( $inFileName );
		$dumpParser->setOutputFileName( $outFileName );
		# $dumpParser->addPageHook( array( $this, 'noopHook' ) );
		# $dumpParser->addPageHook( array( $this, 'addArchiveCategory' ) );
		# $dumpParser->addPageHook( array( $this, 'renameCategory' ) );
		# $dumpParser->addPageHook( array( $this, 'forceRenameCategory' ) );
		# $dumpParser->addPageHook( array( $this, 'filterFiles' ) );
		$dumpParser->addPageHook( array( $this, 'touchModified' ) );
		$dumpParser->parse();
	}

} /* end of MwDump class */

try {
	$mwDump = new MwDump;
	$mwDump->process( $appContext->argv[0], $appContext->argv[1] );
} catch ( \Exception $e ) {
	Q\Dbg\except_die( $e );
}
