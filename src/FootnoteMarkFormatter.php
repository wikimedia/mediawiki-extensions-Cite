<?php

namespace Cite;

use MediaWiki\MediaWikiServices;
use Parser;
use Sanitizer;

/**
 * @license GPL-2.0-or-later
 */
class FootnoteMarkFormatter {

	/**
	 * @var string[][]
	 */
	private $linkLabels = [];

	/**
	 * @var Parser
	 */
	private $parser;

	/**
	 * @var CiteKeyFormatter
	 */
	private $citeKeyFormatter;

	/**
	 * @var CiteErrorReporter
	 */
	private $errorReporter;

	/**
	 * @param Parser $parser
	 * @param CiteErrorReporter $errorReporter
	 * @param CiteKeyFormatter $citeKeyFormatter
	 */
	public function __construct(
		Parser $parser,
		CiteErrorReporter $errorReporter,
		CiteKeyFormatter $citeKeyFormatter
	) {
		$this->parser = $parser;
		$this->citeKeyFormatter = $citeKeyFormatter;
		$this->errorReporter = $errorReporter;
	}

	/**
	 * Generate a link (<sup ...) for the <ref> element from a key
	 * and return XHTML ready for output
	 *
	 * @suppress SecurityCheck-DoubleEscaped
	 * @param string $group
	 * @param array $ref Dictionary with ReferenceStack ref format
	 *
	 * @return string
	 */
	public function linkRef( string $group, array $ref ) : string {
		$language = MediaWikiServices::getInstance()->getContentLanguage();

		$label = $this->getLinkLabel( $group, $ref['number'] );
		if ( $label === null ) {
			$label = $language->formatNum( $ref['number'] );
			if ( $group !== Cite::DEFAULT_GROUP ) {
				$label = "$group $label";
			}
		}
		if ( isset( $ref['extendsIndex'] ) ) {
			$label .= $language->formatNum( '.' . $ref['extendsIndex'] );
		}

		$key = $ref['name'] ?? $ref['key'];
		$count = $ref['name'] ? $ref['key'] . '-' . $ref['count'] : null;
		$subkey = $ref['name'] ? '-' . $ref['key'] : null;

		return $this->parser->recursiveTagParse(
			wfMessage(
				'cite_reference_link',
				$this->citeKeyFormatter->refKey( $key, $count ),
				$this->citeKeyFormatter->getReferencesKey( $key . $subkey ),
				Sanitizer::safeEncodeAttribute( $label )
			)->inContentLanguage()->plain()
		);
	}

	/**
	 * Generate a custom format link for a group given an offset, e.g.
	 * the second <ref group="foo"> is b if $this->mLinkLabels["foo"] =
	 * [ 'a', 'b', 'c', ...].
	 * Return an error if the offset > the # of array items
	 *
	 * @param string $group The group name
	 * @param int $number Expected to start at 1
	 *
	 * @return string|null Returns null if no custom labels for this group exist
	 */
	private function getLinkLabel( string $group, int $number ) : ?string {
		$message = "cite_link_label_group-$group";
		if ( !isset( $this->linkLabels[$group] ) ) {
			$msg = wfMessage( $message )->inContentLanguage();
			$this->linkLabels[$group] = $msg->isDisabled() ? [] : preg_split( '/\s+/', $msg->plain() );
		}

		if ( !$this->linkLabels[$group] ) {
			return null;
		}

		return $this->linkLabels[$group][$number - 1]
			?? $this->errorReporter->plain( 'cite_error_no_link_label_group', $group, $message );
	}

}
