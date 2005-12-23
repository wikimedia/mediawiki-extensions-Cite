<?php
if ( ! defined( 'MEDIAWIKI' ) )
	die();
/**#@+
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfCite';
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'Cite',
	'author' => 'Ævar Arnfjörð Bjarmason',
	'description' => 'adds <nowiki><ref[ id]></nowiki> and <nowiki><references/></nowiki> tags, for citations',
	'url' => 'http://avar.lir.dk/mw/HEAD/wiki/Cite'
);

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
		'CITE_ERROR_REFERENCES_INVALID_PARAMETERS'
	)
);

for ( $i = 0; $i < count( $wgCiteErrors['system'] ); ++$i )
	// System errors are negative integers
	define( $wgCiteErrors['system'][$i], -($i + 1) );
for ( $i = 0; $i < count( $wgCiteErrors['user'] ); ++$i )
	// User errors are positive integers
	define( $wgCiteErrors['user'][$i], $i + 1 );

function wfCite() {
	global $wgMessageCache;

	$wgMessageCache->addMessages(
		array(
			/*
			   Debug & errors
			*/
			'cite_croak' => 'Cite croaked; $1: $2',

			'cite_error' => 'Cite error $1; $2',
			'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY => 'Invalid call; expecting a non-integer key',
			'cite_error_' . CITE_ERROR_REF_NO_KEY => 'Invalid call; no key specified',
			'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS => 'Invalid call; too many keys specified',
			'cite_error_' . CITE_ERROR_REF_NO_INPUT => 'Invalid call; no input specified',
			'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT => 'Invalid input; expecting none',
			'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => 'Invalid parameters; expecting none',

			/*
			   Output formatting
			*/
			'cite_reference_link_key_with_num' => '$1_$2',
			// Ids produced by <ref>
			'cite_reference_link_prefix' => '_ref_',
			'cite_reference_link_suffix' => '',
			// Ids produced by <references>
			'cite_references_link_prefix' => '_note_',
			'cite_references_link_suffix' => '',

			'cite_reference_link' => '<sup id="$1" class="reference">[[#$2|<nowiki>[</nowiki>$3<nowiki>]</nowiki>]]</sup>',
			'cite_references_link_one' => '<li><cite id="$1">[[#$2|^]] $3</cite></li>',
			'cite_references_link_many' => '<li>^ <cite id="$1">$2 $3</cite></li>',
			'cite_references_link_many_format' => '[[#$1|<sup>$2</sup>]]',
			'cite_references_link_many_sep' => "\xc2\xa0", // &nbsp;
			'cite_references_link_many_and' => "\xc2\xa0", // &nbps;

			// Although I could just use # instead of <li> above and nothing here that
			// will break on input that contains linebreaks
			'cite_references_prefix' => '<ol class="references">',
			'cite_references_suffix' => '</ol>',
		)
	);
	
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
		 *	),
		 *	0 => 'Anonymous reference',
		 *	1 => 'Another anonymous reference',
		 *	'some key' => array(
		 *		'text' => 'this one occurs once'
		 *		'count' => 0
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
		 * @var object
		 */
		var $mParser, $mParserOptions;
		
		/**#@-*/

		/**
		 * Constructor
		 */
		function Cite() {
			$this->setHooks();
			$this->genParser();
		}

		/**#@+ @access private */

		/**
		 * Callback function for <ref>
		 *
		 * @param string $str Input
		 * @param array $argv Arguments
		 * @return string
		 */
		function ref( $str, $argv ) {
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
		 * @param array $argv The argument vector
		 *
		 * @return mixed false on invalid input, a string on valid
		 *               input and null on no input
		 */
		function refArg( $argv ) {
			if ( count( $argv ) > 1 )
				// There should only be one key
				return false;
			else if ( count( $argv ) == 1 )
				// Key given.
				return array_shift( $argv );
			else
				// No key
				return null;
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
				if ( ! @is_array( $this->mRefs[$key] ) ) {
					// First occourance
					$this->mRefs[$key] = array(
						'text' => $str,
						'count' => 0
					);
					return $this->linkRef( $key, 0 );
				} else
					// We've been here before
					return $this->linkRef( $key, ++$this->mRefs[$key]['count'] );
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
		function references( $str, $argv ) {
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
			$ent = array();
			
			foreach ( $this->mRefs as $k => $v )
				$ent[] = $this->referencesFormatEntry( $k, $v );
			
			$prefix = wfMsgForContentNoTrans( 'cite_references_prefix' );
			$suffix = wfMsgForContentNoTrans( 'cite_references_suffix' );
			$content = implode( "\n", $ent );
			
			return $this->parse( $prefix . $content . $suffix );
		}

		/**
		 * Format a single entry for the referencesFormat() function
		 *
		 * @param string $key The key of the reference
		 * @param mixed $val The value of the reference, string for anonymous
		 *                   references, array for user-suppplied
		 *
		 * @return string Wikitext
		 */
		function referencesFormatEntry( $key, $val ) {
			global $wgContLang;
			
			if ( ! is_array( $val ) )
				return
					wfMsgForContentNoTrans(
						'cite_references_link_one',
						$this->referencesKey( $key ),
						$this->refKey( $key ),
						$val
					);
			else {
				$links = array();

				for ( $i = 0; $i <= $val['count']; ++$i ) {
					$links[] = wfMsgForContentNoTrans(
							'cite_references_link_many_format',
							$this->refKey( $key, $i ),
							$wgContLang->formatNum( $i + 1 )
					);
				}

				$list = $this->listToText( $links );

				return
					wfMsgForContentNoTrans( 'cite_references_link_many',
						$this->referencesKey( $key ),
						$list,
						$val['text']
					);
			}
		}

		/**
		 * Return an id for use in wikitext output based on a key and
		 * optionally the # of it, used in <references>, not <ref>
		 * (since otherwise it would link to itself)
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
		 * @param int $num The # of the key, used for distinguishing
		 *                 multiple occourances of the same key
		 *
		 * @return string
		 */
		function linkRef( $key, $num = null ) {
			global $wgContLang;
			
			return
				$this->parse(
					wfMsgForContentNoTrans(
						'cite_reference_link',
						$this->refKey( $key, $num ),
						$this->referencesKey( $key ),
						$wgContLang->formatNum( ++$this->mOutCnt )
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
			global $wgTitle;

			$ret = $this->mParser->parse( $in, $wgTitle, $this->mParserOptions, false );
			$text = $ret->getText();
			
			return $this->fixTidy( $text );
		}

		/**
		 * Tidy treats all input as a block, it will e.g. wrap most
		 * input in <p> if it isn't already, fix that and return the fixed text
		 *
		 * @param string $text The text to fix
		 * @return string The fixed text
		 */
		function fixTidy( $text ) {
			global $wgUseTidy;

			if ( ! $wgUseTidy )
				return $text;
			else {
				$text = preg_replace( '#^<p>\s*#', '', $text );
				$text = preg_replace( '#\s*</p>\s*#', '', $text );
				
				wfDebugLog( 'misc', "'''$text'''" );

				return $text;
			}
		}

		/**
		 * $wgOut->parse() has issues with the elements defined in
		 * setHooks() being used inside includes templates so I'm
		 * rolling my own parser
		 */
		function genParser() {
			$this->mParser = new Parser;
			$this->mParserOptions = new ParserOptions;
		}

		/**
		 * Initialize the parser hooks
		 */
		function setHooks() {
			global $wgParser;
			
			$wgParser->setHook( 'ref' , array( &$this, 'ref' ) );
			$wgParser->setHook( 'references' , array( &$this, 'references' ) );
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
						wfMsgforContent( 'cite_error', $id, wfMsgForContent( "cite_error_$id" ) ) .
						'</strong>'
					);
			else if ( $id < 0 )
				return wfMsgforContent( 'cite_error', $id );
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

	new PersistentObject( new Cite );
}

/**#@-*/
