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
$wfCiteErrors = array(
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

for ( $i = 0; $i < count( $wfCiteErrors['system'] ); ++$i )
	// System errors are negative integers
	define( $wfCiteErrors['system'][$i], -($i + 1) );
for ( $i = 0; $i < count( $wfCiteErrors['user'] ); ++$i )
	// User errors are positive integers
	define( $wfCiteErrors['user'][$i], $i + 1 );

function wfCite() {
	global $wgMessageCache;

	$wgMessageCache->addMessages(
		array(
			/*
			   Debug & errors
			*/
			'cite_croak' => 'Cite croaked; $1: $2',

			'cite_error' => 'Cite error $1; $2',
			'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY => 'Invalid call; expecting a key that matched /^[^0-9]+$/',
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

			'cite_reference_link' => '<sup id="$1">[[#$2|<nowiki>[</nowiki>$3<nowiki>]</nowiki>]]</sup>',
			'cite_references_link_one' => '<li><cite id="$1">[[#$2|^]] $3</cite></li>',
			'cite_references_link_many' => '<li>^ <cite id="$1">$2 $3</cite></li>',
			'cite_references_link_many_format' => '[[#$1|<sup>$2</sup>]]',
			'cite_references_link_many_sep' => "\xc2\xa0", // &nbsp;
			'cite_references_link_many_and' => "\xc2\xa0", // &nbps;

			'cite_references_prefix' => '<ol>',
			'cite_references_suffix' => '</ol>',
		)
	);
	
	class Cite {
		/**#@+ @access private */
		var $mRefs = array();
		var $mI = 0, $mJ = 0;
		var $mParser, $mParserOptions;
		/**#@-*/

		function Cite() {
			$this->setHooks();
			$this->genParser();
		}

		/**#@+ @access private */

		function ref( $str, $argv ) {
			$key = $this->refArg( $argv );
			
			if ( $str !== null ) {
				if ( $str === '' )
					return $this->error( CITE_ERROR_REF_NO_INPUT );
				if ( is_string( $key ) )
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
		 * @access private
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
				return $this->link( $this->mJ++, $str );
			} else if ( is_string( $key ) )
				// Valid key
				if ( ! @is_array( $this->mRefs[$key] ) ) {
					// First occourance
					$this->mRefs[$key] = array(
						'text' => $str,
						'count' => 0
					);
					return $this->link( $key, $str, 0 );
				} else
					// We've been here before
					return $this->link( $key, $str, ++$this->mRefs[$key]['count'] );
			else
				$this->croak( CITE_ERROR_STACK_INVALID_INPUT, serialize( array( $key, $str ) ) );
		}
		
		/**
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

		function referencesFormat() {
			$ent = array();
			
			foreach ( $this->mRefs as $k => $v )
				$ent[] = $this->referencesFormatEntry( $k, $v );
			
			$prefix = wfMsgForContentNoTrans( 'cite_references_prefix' );
			$suffix = wfMsgForContentNoTrans( 'cite_references_suffix' );
			$content = implode( "\n", $ent );
			
			return $this->parse( $prefix . $content . $suffix );
		}

		function referencesFormatEntry( $key, $val ) {
			global $wgContLang;
			
			if ( ! is_array( $val ) )
				return
					wfMsgForContentNoTrans(
						'cite_references_link_one',
						$this->key( $key, false ),
						$this->key( $key, true ),
						$val
					);
			else {
				$links = array();

				for ( $i = 0; $i <= $val['count']; ++$i ) {
					$links[] = wfMsgForContentNoTrans(
							'cite_references_link_many_format',
							$this->key( $key, true, $i ),
							$wgContLang->formatNum( $i + 1 )
					);
				}

				$list = $this->listToText( $links );

				return
					wfMsgForContentNoTrans( 'cite_references_link_many',
						$this->key( $key, false ),
						$list,
						$val['text']
					);
			}
		}

		function key( $key, $ref, $num = null ) {
			if ( $ref === true ) {
				// _ref
				$prefix = wfMsgForContent( 'cite_reference_link_prefix' );
				$suffix = wfMsgForContent( 'cite_reference_link_suffix' );
				if ( isset( $num ) )
					$key = wfMsgForContentNoTrans( 'cite_reference_link_key_with_num', $key, $num );
			} else if ( $ref === false ) {
				// _note
				$prefix = wfMsgForContent( 'cite_references_link_prefix' );
				$suffix = wfMsgForContent( 'cite_references_link_suffix' );
				if ( isset( $num ) )
					$key = wfMsgForContentNoTrans( 'cite_reference_link_key_with_num', $key, $num );
			}

			return $prefix . $key . $suffix;
		}

		function link( $key, $val, $num = null ) {
			global $wgContLang;
			
			return
				$this->parse(
					wfMsgForContentNoTrans(
						'cite_reference_link',
						$this->key( $key, true, $num ),
						$this->key( $key, false ),
						$wgContLang->formatNum( ++$this->mI )
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
		 * @param array $l
		 * @return string
		 */
		function listToText( $arr ) {
			$cnt = count( $arr );

			$sep = wfMsgForContentNoTrans( 'cite_references_link_many_sep' );
			$and = wfMsgForContentNoTrans( 'cite_references_link_many_and' );

			if ( $cnt == 1 )
				return (string)$arr[0];
			else {
				$t = array_slice( $arr, 0, $cnt - 1 );
				return implode( $sep, $t ) . $and . $arr[$cnt - 1];
			}
		}

		function parse( $in ) {
			global $wgTitle;

			$ret = $this->mParser->parse( $in, $wgTitle, $this->mParserOptions, false );
			$text = $ret->getText();
			
			return $this->fixTidy( $text );
			// This had trouble with including stuff
			//return $wgOut->parse( $in, false );
		}

		function fixTidy( $text ) {
			global $wgUseTidy;

			if ( ! $wgUseTidy )
				return $text;
			else {
				$text = preg_replace( '/^<p>\s*/', '', $text );
				$text = preg_replace( '/\s*<\/p>\s*/', '', $text );
				
				wfDebugLog( 'misc', "'''$text'''" );

				return $text;
			}
		}

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
		 * @param int $id ID for the error
		 * @return string
		 */
		function error( $id ) {
			if ( $id > 0 )
				// User errors are positive
				return $this->parse( wfMsgforContent( 'cite_error', $id, wfMsgForContent( "cite_error_$id" ) ) );
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
