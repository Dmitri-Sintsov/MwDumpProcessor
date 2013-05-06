<?php

namespace MwDump;
use QuestPC as Q;

class SiteinfoModel extends Q\FeaturesObject {

	protected $props;
	protected $namespaces = array();

	function __construct() {
		$this->props = new \stdClass();
	}

	public function setSitename( $sitename ) {
		$this->props->sitename = $sitename;
	}

	public function setBase( $base ) {
		$this->props->base = $base;
	}

	public function setGenerator( $generator ) {
		$this->props->generator = $generator;
	}

	public function setCase( $case ) {
		$this->props->caseVal = $case;
	}

	public function addNamespace( $nsDef ) {
		$this->namespaces[] = $nsDef;
	}

	public function getTagArray() {
		# Q\Dbg\log(__METHOD__,$this);
		$result = array( '@tag' => 'siteinfo',
			array( '@tag' => 'sitename', $this->props->sitename ),
			array( '@tag' => 'base', $this->props->base ),
			array( '@tag' => 'generator', $this->props->generator ),
			array( '@tag' => 'case', $this->props->caseVal ),
		);
		$namespaces = array( '@tag' => 'namespaces' );
		foreach ( $this->namespaces as $nsDef ) {
			$nsTag = (array) $nsDef->attrs;
			$nsTag['@tag'] = 'namespace';
			if ( $nsDef->text !== '' ) {
				$nsTag[] = $nsDef->text;
			}
			$namespaces[] = $nsTag;
		}
		$result[] = $namespaces;
		return $result;
	}

} /* end of SiteinfoModel class */
