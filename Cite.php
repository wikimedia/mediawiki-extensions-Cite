<?php
if ( ! defined( 'MEDIAWIKI' ) )
	die();
/**#@+
 * A parser extension that adds two tags, <ref> and <references> for adding
 * citations to pages
 *
 * @addtogroup Extensions
 *
 * @link http://meta.wikimedia.org/wiki/Cite/Cite.php Documentation
 * @link http://www.w3.org/TR/html4/struct/text.html#edef-CITE <cite> definition in HTML
 * @link http://www.w3.org/TR/2005/WD-xhtml2-20050527/mod-text.html#edef_text_cite <cite> definition in XHTML 2.0
 *
 * @bug 4579
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfCite';
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'Cite',
	'version' => '1.1',
	'author' => 'Ævar Arnfjörð Bjarmason',
	'description' => 'Adds <nowiki><ref[ name=id]></nowiki> and <nowiki><references/></nowiki> tags, for citations',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Cite/Cite.php',
);
$wgParserTestFiles[] = dirname( __FILE__ ) . "/citeParserTests.txt";

/**
 * Error codes, first array = internal errors; second array = user errors
 */
$wgCiteErrors = array(
	'system' => array(
		'CITE_ERROR_STR_INVALID',
		'CITE_ERROR_KEY_INVALID_1',
		'CITE_ERROR_KEY_INVALID_2',
		'CITE_ERROR_STACK_INVALID_INPUT'
	),
	'user' => array(
		'CITE_ERROR_REF_NUMERIC_KEY',
		'CITE_ERROR_REF_NO_KEY',
		'CITE_ERROR_REF_TOO_MANY_KEYS',
		'CITE_ERROR_REF_NO_INPUT',
		'CITE_ERROR_REFERENCES_INVALID_INPUT',
		'CITE_ERROR_REFERENCES_INVALID_PARAMETERS',
		'CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL',
		'CITE_ERROR_REFERENCES_NO_TEXT'
	)
);

for ( $i = 0; $i < count( $wgCiteErrors['system'] ); ++$i )
	// System errors are negative integers
	define( $wgCiteErrors['system'][$i], -($i + 1) );
for ( $i = 0; $i < count( $wgCiteErrors['user'] ); ++$i )
	// User errors are positive integers
	define( $wgCiteErrors['user'][$i], $i + 1 );

# Internationalisation file
require_once( dirname(__FILE__) . '/Cite.i18n.php' );

function wfCite() {
	# Add messages
	global $wgMessageCache, $wgCiteMessages;
	foreach( $wgCiteMessages as $key => $value ) {
		$wgMessageCache->addMessages( $wgCiteMessages[$key], $key );
	}

	class Cite {
		/**#@+
		 * @access private
		 */

		/**
		 * Datastructure representing <ref> input, in the format of:
		 * <code>
		 * array(
		 * 	'user supplied' => array(
		 *		'text' => 'user supplied reference & key',
		 *		'count' => 1, // occurs twice
		 * 		'number' => 1, // The first reference, we want
		 * 		               // all occourances of it to
		 * 		               // use the same number
		 *	),
		 *	0 => 'Anonymous reference',
		 *	1 => 'Another anonymous reference',
		 *	'some key' => array(
		 *		'text' => 'this one occurs once'
		 *		'count' => 0,
		 * 		'number' => 4
		 *	),
		 *	3 => 'more stuff'
		 * );
		 * </code>
		 *
		 * This works because:
		 * * PHP's datastructures are guarenteed to be returned in the
		 *   order that things are inserted into them (unless you mess
		 *   with that)
		 * * User supplied keys can't be integers, therefore avoiding
		 *   conflict with anonymous keys
		 *
		 * @var array
		 **/
		var $mRefs = array();

		/**
		 * Count for user displayed output (ref[1], ref[2], ...)
		 *
		 * @var int
		 */
		var $mOutCnt = 0;

		/**
		 * Internal counter for anonymous references, seperate from
		 * $mOutCnt because anonymous references won't increment it,
		 * but will incremement $mOutCnt
		 *
		 * @var int
		 */
		var $mInCnt = 0;

		/**
		 * The backlinks, in order, to pass as $3 to
		 * 'cite_references_link_many_format', defined in
		 * 'cite_references_link_many_format_backlink_labels
		 *
		 * @var array
		 */
		var $mBacklinkLabels;

		/**
		 * @var object
		 */
		var $mParser;

		/**
		 * True when a <ref> or <references> tag is being processed.
		 * Used to avoid infinite recursion
		 *
		 * @var boolean
		 */
		var $mInCite = false;

		/**#@-*/

		/**
		 * Constructor
		 */
		function Cite() {
			$this->setHooks();
		}

		/**#@+ @access private */

		/**
		 * Callback function for <ref>
		 *
		 * @param string $str Input
		 * @param array $argv Arguments
		 * @return string
		 */
		function ref( $str, $argv, $parser ) {
			if ( $this->mInCite ) {
				return htmlspecialchars( "<ref>$str</ref>" );
			} else {
				$this->mInCite = true;
				$ret = $this->guardedRef( $str, $argv, $parser );
				$this->mInCite = false;
				return $ret;
			}
		}

		function guardedRef( $str, $argv, $parser ) {
			$this->mParser = $parser;
			$key = $this->refArg( $argv );

			if ( $str !== null ) {
				if ( $str === '' )
					return $this->error( CITE_ERROR_REF_NO_INPUT );
				if ( is_string( $key ) )
					// I don't want keys in the form of /^[0-9]+$/ because they would
					// conflict with the php datastructure I'm using, besides, why specify
					// a manual key if it's just going to be any old integer?
					if ( sprintf( '%d', $key ) === (string)$key )
						return $this->error( CITE_ERROR_REF_NUMERIC_KEY );
					else
						return $this->stack( $str, $key );
				else if ( $key === null )
					return $this->stack( $str );
				else if ( $key === false )
					return $this->error( CITE_ERROR_REF_TOO_MANY_KEYS );
				else
					$this->croak( CITE_ERROR_KEY_INVALID_1, serialize( $key ) );
			} else if ( $str === null ) {
				if ( is_string( $key ) )
					if ( sprintf( '%d', $key ) === (string)$key )
						return $this->error( CITE_ERROR_REF_NUMERIC_KEY );
					else
						return $this->stack( $str, $key );
				else if ( $key === false )
					return $this->error( CITE_ERROR_REF_TOO_MANY_KEYS );
				else if ( $key === null )
					return $this->error( CITE_ERROR_REF_NO_KEY );
				else
					$this->croak( CITE_ERROR_KEY_INVALID_2, serialize( $key ) );

			} else
				$this->croak( CITE_ERROR_STR_INVALID, serialize( $str ) );
		}

		/**
		 * Parse the arguments to the <ref> tag
		 *
		 * @static
		 *
		 * @param array $argv The argument vector
		 * @return mixed false on invalid input, a string on valid
		 *               input and null on no input
		 */
		function refArg( $argv ) {

			$cnt = count( $argv );

			if ( $cnt > 1 )
				// There should only be one key
				return false;
			else if ( $cnt == 1 )
				if ( isset( $argv['name'] ) )
					// Key given.
					return $this->validateName( array_shift( $argv ) );
				else
					// Invalid key
					return false;
			else
				// No key
				return null;
		}

		/**
		 * Since the key name is used in an XHTML id attribute, it must
		 * conform to the validity rules. The restriction to begin with
		 * a letter is lifted since references have their own prefix.
		 *
		 * @fixme merge this code with the various section name transformations
		 * @fixme double-check for complete validity
		 * @return string if valid, false if invalid
		 */
		function validateName( $name ) {
			if( preg_match( '/^[A-Za-z0-9:_.-]*$/i', $name ) ) {
				return $name;
			} else {
				// WARNING: CRAPPY CUT AND PASTE MAKES BABY JESUS CRY
				$text = urlencode( str_replace( ' ', '_', $name ) );
				$replacearray = array(
					'%3A' => ':',
					'%' => '.'
				);
				return str_replace(
					array_keys( $replacearray ),
					array_values( $replacearray ),
					$text );
			}
		}

		/**
		 * Populate $this->mRefs based on input and arguments to <ref>
		 *
		 * @param string $str Input from the <ref> tag
		 * @param mixed $key Argument to the <ref> tag as returned by $this->refArg()
		 * @return string
		 */
		function stack( $str, $key = null ) {
			if ( $key === null ) {
				// No key
				$this->mRefs[] = $str;
				return $this->linkRef( $this->mInCnt++ );
			} else if ( is_string( $key ) )
				// Valid key
				if ( ! isset( $this->mRefs[$key] ) || ! is_array( $this->mRefs[$key] ) ) {
					// First occourance
					$this->mRefs[$key] = array(
						'text' => $str,
						'count' => 0,
						'number' => ++$this->mOutCnt
					);
					return
						$this->linkRef(
							$key,
							$this->mRefs[$key]['count'],
							$this->mRefs[$key]['number']
						);
				} else {
					// We've been here before
					if ( $this->mRefs[$key]['text'] === null && $str !== '' ) {
						// If no text found before, use this text
						$this->mRefs[$key]['text'] = $str;
					};
					return
						$this->linkRef(
							$key,
							++$this->mRefs[$key]['count'],
							$this->mRefs[$key]['number']
						); }
			else
				$this->croak( CITE_ERROR_STACK_INVALID_INPUT, serialize( array( $key, $str ) ) );
		}

		/**
		 * Callback function for <references>
		 *
		 * @param string $str Input
		 * @param array $argv Arguments
		 * @return string
		 */
		function references( $str, $argv, $parser ) {
			if ( $this->mInCite ) {
				if ( is_null( $str ) ) {
					return htmlspecialchars( "<references/>" );
				} else {
					return htmlspecialchars( "<references>$str</references>" );
				}
			} else {
				$this->mInCite = true;
				$ret = $this->guardedReferences( $str, $argv, $parser );
				$this->mInCite = false;
				return $ret;
			}
		}

		function guardedReferences( $str, $argv, $parser ) {
			$this->mParser = $parser;
			if ( $str !== null )
				return $this->error( CITE_ERROR_REFERENCES_INVALID_INPUT );
			else if ( count( $argv ) )
				return $this->error( CITE_ERROR_REFERENCES_INVALID_PARAMETERS );
			else
				return $this->referencesFormat();
		}

		/**
		 * Make output to be returned from the references() function
		 *
		 * @return string XHTML ready for output
		 */
		function referencesFormat() {
			if ( count( $this->mRefs ) == 0 )
				return '';

			$ent = array();
			foreach ( $this->mRefs as $k => $v )
				$ent[] = $this->referencesFormatEntry( $k, $v );

			$prefix = wfMsgForContentNoTrans( 'cite_references_prefix' );
			$suffix = wfMsgForContentNoTrans( 'cite_references_suffix' );
			$content = implode( "\n", $ent );

			// Live hack: parse() adds two newlines on WM, can't reproduce it locally -ævar
			return rtrim( $this->parse( $prefix . $content . $suffix ), "\n" );
		}

		/**
		 * Format a single entry for the referencesFormat() function
		 *
		 * @param string $key The key of the reference
		 * @param mixed $val The value of the reference, string for anonymous
		 *                   references, array for user-suppplied
		 * @return string Wikitext
		 */
		function referencesFormatEntry( $key, $val ) {
			// Anonymous reference
			if ( ! is_array( $val ) )
				return
					wfMsgForContentNoTrans(
						'cite_references_link_one',
						$this->referencesKey( $key ),
						$this->refKey( $key ),
						$val
					);
			else if ($val['text']=='') return
					wfMsgForContentNoTrans(
						'cite_references_link_one',
						$this->referencesKey( $key ),
						$this->refKey( $key, $val['count'] ),
						$this->error(CITE_ERROR_REFERENCES_NO_TEXT)
					);
			// Standalone named reference, I want to format this like an
			// anonymous reference because displaying "1. 1.1 Ref text" is
			// overkill and users frequently use named references when they
			// don't need them for convenience
			else if ( $val['count'] === 0 )
				return
					wfMsgForContentNoTrans(
						'cite_references_link_one',
						$this->referencesKey( $key ),
						$this->refKey( $key, $val['count'] ),
						( $val['text'] != '' ? $val['text'] : $this->error( CITE_ERROR_REFERENCES_NO_TEXT ) )
					);
			// Named references with >1 occurrences
			else {
				$links = array();

				for ( $i = 0; $i <= $val['count']; ++$i ) {
					$links[] = wfMsgForContentNoTrans(
							'cite_references_link_many_format',
							$this->refKey( $key, $i ),
							$this->referencesFormatEntryNumericBacklinkLabel( $val['number'], $i, $val['count'] ),
							$this->referencesFormatEntryAlternateBacklinkLabel( $i )
					);
				}

				$list = $this->listToText( $links );

				return
					wfMsgForContentNoTrans( 'cite_references_link_many',
						$this->referencesKey( $key ),
						$list,
						( $val['text'] != '' ? $val['text'] : $this->error( CITE_ERROR_REFERENCES_NO_TEXT ) )
					);
			}
		}

		/**
		 * Generate a numeric backlink given a base number and an
		 * offset, e.g. $base = 1, $offset = 2; = 1.2
		 * Since bug #5525, it correctly does 1.9 -> 1.10 as well as 1.099 -> 1.100
		 *
		 * @static
		 *
		 * @param int $base The base
		 * @param int $offset The offset
		 * @param int $max Maximum value expected.
		 * @return string
		 */
		function referencesFormatEntryNumericBacklinkLabel( $base, $offset, $max ) {
			global $wgContLang;
			$scope = strlen( $max );
			$ret = $wgContLang->formatNum(
				sprintf("%s.%0{$scope}s", $base, $offset)
			);
			return $ret;
		}

		/**
		 * Generate a custom format backlink given an offset, e.g.
		 * $offset = 2; = c if $this->mBacklinkLabels = array( 'a',
		 * 'b', 'c', ...). Return an error if the offset > the # of
		 * array items
		 *
		 * @param int $offset The offset
		 *
		 * @return string
		 */
		function referencesFormatEntryAlternateBacklinkLabel( $offset ) {
			if ( !isset( $this->mBacklinkLabels ) ) {
				$this->genBacklinkLabels();
			}
			if ( isset( $this->mBacklinkLabels[$offset] ) ) {
				return $this->mBacklinkLabels[$offset];
			} else {
				// Feed me!
				return $this->error( CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL );
			}
		}

		/**
		 * Return an id for use in wikitext output based on a key and
		 * optionally the # of it, used in <references>, not <ref>
		 * (since otherwise it would link to itself)
		 *
		 * @static
		 *
		 * @param string $key The key
		 * @param int $num The number of the key
		 * @return string A key for use in wikitext
		 */
		function refKey( $key, $num = null ) {
			$prefix = wfMsgForContent( 'cite_reference_link_prefix' );
			$suffix = wfMsgForContent( 'cite_reference_link_suffix' );
			if ( isset( $num ) )
				$key = wfMsgForContentNoTrans( 'cite_reference_link_key_with_num', $key, $num );

			return $prefix . $key . $suffix;
		}

		/**
		 * Return an id for use in wikitext output based on a key and
		 * optionally the # of it, used in <ref>, not <references>
		 * (since otherwise it would link to itself)
		 *
		 * @static
		 *
		 * @param string $key The key
		 * @param int $num The number of the key
		 * @return string A key for use in wikitext
		 */
		function referencesKey( $key, $num = null ) {
			$prefix = wfMsgForContent( 'cite_references_link_prefix' );
			$suffix = wfMsgForContent( 'cite_references_link_suffix' );
			if ( isset( $num ) )
				$key = wfMsgForContentNoTrans( 'cite_reference_link_key_with_num', $key, $num );

			return $prefix . $key . $suffix;
		}

		/**
		 * Generate a link (<sup ...) for the <ref> element from a key
		 * and return XHTML ready for output
		 *
		 * @param string $key The key for the link
		 * @param int $count The # of the key, used for distinguishing
		 *                   multiple occourances of the same key
		 * @param int $label The label to use for the link, I want to
		 *                   use the same label for all occourances of
		 *                   the same named reference.
		 * @return string
		 */
		function linkRef( $key, $count = null, $label = null ) {
			global $wgContLang;

			return
				$this->parse(
					wfMsgForContentNoTrans(
						'cite_reference_link',
						$this->refKey( $key, $count ),
						$this->referencesKey( $key ),
						$wgContLang->formatNum( is_null( $label ) ? ++$this->mOutCnt : $label )
					)
				);
		}

		/**
		 * This does approximately the same thing as
		 * Langauge::listToText() but due to this being used for a
		 * slightly different purpose (people might not want , as the
		 * first seperator and not 'and' as the second, and this has to
		 * use messages from the content language) I'm rolling my own.
		 *
		 * @static
		 *
		 * @param array $arr The array to format
		 * @return string
		 */
		function listToText( $arr ) {
			$cnt = count( $arr );

			$sep = wfMsgForContentNoTrans( 'cite_references_link_many_sep' );
			$and = wfMsgForContentNoTrans( 'cite_references_link_many_and' );

			if ( $cnt == 1 )
				// Enforce always returning a string
				return (string)$arr[0];
			else {
				$t = array_slice( $arr, 0, $cnt - 1 );
				return implode( $sep, $t ) . $and . $arr[$cnt - 1];
			}
		}

		/**
		 * Parse a given fragment and fix up Tidy's trail of blood on
		 * it...
		 *
		 * @param string $in The text to parse
		 * @return string The parsed text
		 */
		function parse( $in ) {
			if ( method_exists( $this->mParser, 'recursiveTagParse' ) ) {
				// New fast method
				return $this->mParser->recursiveTagParse( $in );
			} else {
				// Old method
				$ret = $this->mParser->parse(
					$in,
					$this->mParser->mTitle,
					$this->mParser->mOptions,
					// Avoid whitespace buildup
					false,
					// Important, otherwise $this->clearState()
					// would get run every time <ref> or
					// <references> is called, fucking the whole
					// thing up.
					false
				);
				$text = $ret->getText();

				return $this->fixTidy( $text );
			}
		}

		/**
		 * Tidy treats all input as a block, it will e.g. wrap most
		 * input in <p> if it isn't already, fix that and return the fixed text
		 *
		 * @static
		 *
		 * @param string $text The text to fix
		 * @return string The fixed text
		 */
		function fixTidy( $text ) {
			global $wgUseTidy;

			if ( ! $wgUseTidy )
				return $text;
			else {
				$text = preg_replace( '~^<p>\s*~', '', $text );
				$text = preg_replace( '~\s*</p>\s*~', '', $text );
				$text = preg_replace( '~\n$~', '', $text );

				return $text;
			}
		}

		/**
		 * Generate the labels to pass to the
		 * 'cite_references_link_many_format' message, the format is an
		 * arbitary number of tokens seperated by [\t\n ]
		 */
		function genBacklinkLabels() {
			wfProfileIn( __METHOD__ );
			$text = wfMsgForContentNoTrans( 'cite_references_link_many_format_backlink_labels' );
			$this->mBacklinkLabels = preg_split( '#[\n\t ]#', $text );
			wfProfileOut( __METHOD__ );
		}

		/**
		 * Gets run when Parser::clearState() gets run, since we don't
		 * want the counts to transcend pages and other instances
		 */
		function clearState() {
			$this->mOutCnt = $this->mInCnt = 0;
			$this->mRefs = array();

			return true;
		}

		/**
		 * Initialize the parser hooks
		 */
		function setHooks() {
			global $wgParser, $wgHooks;

			$wgParser->setHook( 'ref' , array( &$this, 'ref' ) );
			$wgParser->setHook( 'references' , array( &$this, 'references' ) );

			$wgHooks['ParserClearState'][] = array( &$this, 'clearState' );
		}

		/**
		 * Return an error message based on an error ID
		 *
		 * @param int $id ID for the error
		 * @return string XHTML ready for output
		 */
		function error( $id ) {
			if ( $id > 0 )
				// User errors are positive
				return
					$this->parse(
						'<strong class="error">' .
						wfMsg( 'cite_error', $id, wfMsg( "cite_error_$id" ) ) .
						'</strong>'
					);
			else if ( $id < 0 )
				return wfMsg( 'cite_error', $id );
		}

		/**
		 * Die with a backtrace if something happens in the code which
		 * shouldn't have
		 *
		 * @param int $error  ID for the error
		 * @param string $data Serialized error data
		 */
		function croak( $error, $data ) {
			wfDebugDieBacktrace( wfMsgForContent( 'cite_croak', $this->error( $error ), $data ) );
		}

		/**#@-*/
	}

	new Cite;
}
