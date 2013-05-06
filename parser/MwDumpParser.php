<?php
namespace MwDump;
use QuestPC as Q;

class MwDumpParser extends Q\XmlReaderParser {

	protected $outFileName = 'result.xml';
	protected $xmlw;
	protected $pageHooks = array();

	public function setOutputFileName( $outFileName ) {
		$this->outFileName = $outFileName;
	}

	protected function getDumperAttributes() {
		$header = (array) $this->context->getAllAttributes();
		$this->xmlw = Q\GenericXmlWriter::openDocument( $this->outFileName );
		$header['@tag'] = 'mediawiki';
		$this->xmlw->startTagArray( $header );
	}
	
	protected function parseSiteinfo() {
		$siteinfoModel = new SiteinfoModel();
		$siteinfoParser = SiteinfoParser::newFromTop( $this );
		$siteinfoParser->parseModel( $siteinfoModel );
		$this->xmlw->writeArray( $siteinfoModel->getTagArray() );
	}

	protected function parsePage() {
		$pageModel = new PageModel();
		$pageParser = PageParser::newFromTop( $this );
		$pageParser->parseModel( $pageModel );
		foreach ( $this->pageHooks as $cb ) {
			call_user_func( $cb, $pageModel );
		}
		$this->xmlw->writeArray( $pageModel->getTagArray() );
	}

	public function addPageHook( $cb ) {
		if ( is_callable( $cb ) ) {
			$this->pageHooks[] = $cb;
		} else {
			SdvException::throwError( 'Attempt to add non-callable hook', __METHOD__, $cb );
		}
	}

	public function parse() {
		$this->setPathHooks(
			array(
				'mediawiki' => 'getDumperAttributes',
				'mediawiki/siteinfo' => 'parseSiteinfo',
				'mediawiki/page' => 'parsePage',
			)
		);
		$this->processCurrentSubtree();
		$this->xmlw->flushDocument();
	}

} /* end of MwDumpParser class */
