<?php

namespace MwDump;
use QuestPC as Q;

class PageParser extends Q\XmlOneLevelParser {

	protected static $levelBasePath = 'mediawiki/page';

	protected static $tagHandlers = array(
		'title' => array( self::TAG_MANDATORY, 'setTitle' ),
		'ns' => array( self::TAG_MANDATORY, 'setNamespace' ),
		'id' => array( self::TAG_MANDATORY, 'setPageId' ),
		'redirect' => array( self::TAG_OPTIONAL, null, 'logNameAttrVal' ),
		'revision/id' => array( self::TAG_MANDATORY, 'setRevisionId' ),
		'revision/parentid' => array( self::TAG_OPTIONAL, 'setParentId' ),
		'revision/timestamp' => array( self::TAG_MANDATORY, 'setTimestamp' ),
		'revision/contributor/ip' => array( self::TAG_OPTIONAL, 'setUserIP' ),
		'revision/contributor/username' => array( self::TAG_OPTIONAL, 'setUserName' ),
		'revision/contributor/id' => array( self::TAG_OPTIONAL, 'setUserId' ),
		'revision/comment' => array( self::TAG_OPTIONAL, 'setRevComment' ),
		'revision/minor' => array( self::TAG_OPTIONAL, 'setRevMinor' ),
		'revision/text' => array( self::TAG_MANDATORY, array( 'parsetag_text' ), 'logNameAttrVal' ),
		'revision/sha1' => array( self::TAG_MANDATORY, 'setRevBase36Sha1' ),
		'revision/model' => array( self::TAG_MANDATORY, 'setRevModel' ),
		'revision/format' => array( self::TAG_MANDATORY, 'setRevFormat' ),
	);

	protected function parseTag_redirect( $val ) {
		$this->model->setRedirect( $val->attrs->title );
	}

	protected function parseTag_text( $val ) {
		$this->model->setTextAttrs( $val->attrs );
		$this->model->setText( $val->text );
	}

	public function logException( Q\SdvException $e ) {
		Q\Dbg\except( $e );
		Q\Dbg\log(__METHOD__,$this->dumpTagVals());
	}

} /* end of PageParser class */
