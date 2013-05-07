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
	protected $revText;

	# list of "settable" properties
	protected static $propTypes = array(
		'pageModel' => 'MwDump\\PageModel',
	);

	protected function getCatExpr( $catName ) {
		return '/\[\[\s*(?:категория|category)\s*:\s*' . preg_quote( $catName, '/' ) . '\s*\]\]/iu';
	}
	
	public function categoryExists( $catName ) {
		$this->revText = $this->pageModel->getSubProp( 'revProps', 'text' );
		return preg_match( $this->getCatExpr( $catName ), $this->revText );
	}

	/**
	 * Adds new category only when it does not exists.
	 */
	public function addCategory( $catName ) {
		if ( $this->categoryExists( $catName ) ) {
			# Category already exists.
			return false;
		}
		# Q\Dbg\log(__METHOD__,$this->revText);
		# todo: Wikitext parsing and manipulation should be properly implemented via Parsoid.
		$this->revText .= "\n[[Category:{$catName}]]";
		$this->pageModel->setText( $this->revText );
		return true;
	}

	public function renameCategory( $srcCatName, $dstCatName ) {
		if ( $this->categoryExists( $dstCatName ) ) {
			# Page already belongs to destination category.
			return false;
		}
		# Q\Dbg\log(__METHOD__.':src',$this->getCatExpr( $srcCatName ));
		# Q\Dbg\log(__METHOD__.':dst',"[[Category:$dstCatName]]");
		$this->revText = preg_replace( $this->getCatExpr( $srcCatName ), "[[Category:{$dstCatName}]]", $this->revText, -1, $count );
		$this->pageModel->setText( $this->revText );
		return $count > 0;
	}

	public function deleteCategory( $catName ) {
		$this->revText = $this->pageModel->getSubProp( 'revProps', 'text' );
		$this->revText = preg_replace( $this->getCatExpr( $catName ), '', $this->revText, -1, $count );
		$this->pageModel->setText( $this->revText );
		return $count > 0;
	}

} /* end of WikitextParser class */
