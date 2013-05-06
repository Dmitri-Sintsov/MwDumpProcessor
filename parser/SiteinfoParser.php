<?php

namespace MwDump;
use QuestPC as Q;

class SiteinfoParser extends Q\XmlOneLevelParser {

	protected static $levelBasePath = 'mediawiki/siteinfo';

	protected static $tagHandlers = array(
		'sitename' => array( self::TAG_MANDATORY, 'setSitename' ),
		'base' => array( self::TAG_MANDATORY, 'setBase' ),
		'generator' => array( self::TAG_MANDATORY, 'setGenerator' ),
		'case' => array( self::TAG_MANDATORY, 'setCase' ),
		# example of name override, because 'parseTag_namespaces/namespace'
		# is invalid function name.
		'namespaces/namespace' => array(
			self::TAG_MANDATORY,
			array( 'parseTag_namespaces' ),
			'logMultipleNameAttrVal'
		),
	);

	protected function parseTag_namespaces( $vals ) {
		foreach ( $vals as $val ) {
			$this->model->addNamespace( $val );
		}
	}

	public function logException( Q\SdvException $e ) {
		Q\Dbg\except( $e );
		Q\Dbg\log(__METHOD__,$this->dumpTagVals());
	}

} /* end of SiteinfoParser class */
