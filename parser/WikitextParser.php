<?php
namespace MwDump;
use QuestPC as Q;

/**
 * This is of course not a real wikitext parser, but a quick hack in a short deadline.
 * Either Parsoid calls or MediaWiki Parser bootstrap should be performed instead.
 *
 * However, the current purpose of this software is an example of usage questpc framework
 * XML parsers.
 */
class WikitextParser extends Q\FeaturesObject {

	const CAT_MATCH_ANY = '/\[\[\s*(?:категория|category)\s*:\s*([\w\p{L}]+)\s*\]\]/iu';

	protected $pageModel;

	# list of "settable" properties
	protected static $propTypes = array(
		'pageModel' => 'MwDump\\PageModel',
	);

	/**
	 * Adds new category only when it does not exists.
	 */
	public function addCategory( $catName ) {
		$text = $this->pageModel->getSubProp( 'revProps', 'text' );
		$expr = '/\[\[\s*(?:категория|category)\s*:\s*' . preg_quote( $catName, '/' ) . '\s*\]\]/iu';
		if ( preg_match( $expr, $text ) ) {
			# Category already exists.
			return;
		}
		# Q\Dbg\log(__METHOD__,$text);
		# todo: Wikitext parsing and manipulation should be properly implemented via Parsoid.
		$text .= "\n[[Category:{$catName}]]";
		$this->pageModel->setText( $text );
	}

} /* end of WikitextParser class */
