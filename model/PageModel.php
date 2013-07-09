<?php
namespace MwDump;
use QuestPC as Q;

class PageModel extends Q\FeaturesObject {

	const NS_MAIN = 0;
	const NS_FILE = 6;

	protected $pageProps;
	protected $revProps;

	function __construct() {
		$this->pageProps = new \stdClass();
		$this->revProps = new \stdClass();
	}

	public function setTitle( $title ) {
		$this->pageProps->title = $title;
	}

	public function setNamespace( $ns ) {
		if ( !ctype_digit( $ns ) ) {
			SdvException::throwRecoverable( 'Namespace index must be non-negative integer', __METHOD__, $ns );
		}
		$this->pageProps->ns = intval( $ns );
	}

	public function setPageId( $pageId ) {
		$this->pageProps->id = $pageId;
	}

	public function setRedirect( $title ) {
		$this->pageProps->redirectTitle = $title;
	}

	public function setRevisionId( $revId ) {
		$this->revProps->id = $revId;
	}

	public function setParentId( $parentId ) {
		$this->revProps->parentId = $parentId;
	}

	public function setTimestamp( $ts ) {
		$ts = Q\Gl::timeStamp( Q\Gl::TS_UNIX, $ts );
		if ( $ts === false ) {
			SdvException::throwRecoverable( 'Invalid timestamp of revision', __METHOD__, $ts );
		}
		$this->revProps->ts = $ts;
	}

	public function setUserName( $userName ) {
		$this->revProps->userName = $userName;
	}

	public function setUserId( $uid ) {
		$this->revProps->uid = $uid;
	}

	public function setRevMinor( $val ) {
		$this->revProps->isMinor = true;
	}
	
	public function setRevComment( $comment ) {
		$this->revProps->comment = $comment;
	}

	public function setTextAttrs( $textAttrs ) {
		$this->revProps->textAttrs = $textAttrs;
	}

	public function setText( $text ) {
		$this->revProps->text = $text;
		$this->revProps->base36sha1 = Q\Gl::baseConvert( sha1( $text ), 16, 36, 31 );
		$this->revProps->textAttrs->bytes = strlen( $text );
	}

	public function setRevBase36Sha1( $base36sha1 ) {
		$this->revProps->base36sha1 = $base36sha1;
	}

	public function setRevModel( $revModel ) {
		$this->revProps->model = $revModel;
	}

	public function setRevFormat( $revFormat ) {
		$this->revProps->format = $revFormat;
	}

	public function getTagArray() {
		# Q\Dbg\log(__METHOD__,$this);
		$result = array( '@tag' => 'page',
			array( '@tag' => 'title', $this->pageProps->title ),
			array( '@tag' => 'ns', $this->pageProps->ns ),
			array( '@tag' => 'id', $this->pageProps->id ),
		);
		if ( property_exists( $this->pageProps, 'redirectTitle' ) ) {
			$result[] = array( '@tag' => 'redirect', 'title' => $this->pageProps->redirectTitle );
		}
		$revision = array( '@tag' => 'revision',
			array( '@tag' => 'id', $this->revProps->id )
		);
		if ( property_exists( $this->revProps, 'parentId' ) ) {
			$revision[] = array( '@tag' => 'parentid', $this->revProps->parentId );
		}
		$textTagArray = (array) $this->revProps->textAttrs;
		$textTagArray['@tag'] = 'text';
		if ( $this->revProps->text !== '' ) {
			$textTagArray[] = $this->revProps->text;
		}
		array_push( $revision,
			array( '@tag' => 'timestamp', Q\Gl::timeStamp( Q\Gl::TS_ISO_8601, $this->revProps->ts ) ),
			array( '@tag' => 'contributor',
				array( '@tag' => 'username', $this->revProps->userName ),
				array( '@tag' => 'id', $this->revProps->uid ),
			)
		);
		if ( property_exists( $this->revProps, 'isMinor' ) &&
				$this->revProps->isMinor ) {
			$revision[] = array( '@tag' => 'minor' );
		}
		if ( property_exists( $this->revProps, 'comment' ) ) {
			$revision[] = array( '@tag' => 'comment', $this->revProps->comment );
		}
		array_push( $revision,
			$textTagArray,
			array( '@tag' => 'sha1', $this->revProps->base36sha1 ),
			array( '@tag' => 'model', $this->revProps->model ),
			array( '@tag' => 'format', $this->revProps->format )
		);
		$result[] = $revision;
		return $result;
	}

} /* end of PageModel class */
