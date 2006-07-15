<?php
/**
 * Internationalisation file for Cite extension.
 *
 * @package MediaWiki
 * @subpackage Extensions
*/

$wgCiteMessages = array();

$wgCiteMessages['en'] = array(
	/*
		Debug and errors
	*/

	# Internal errors
	'cite_croak' => 'Cite croaked; $1: $2',

	'cite_error_' . CITE_ERROR_STR_INVALID         => 'Internal error; invalid $str',
	'cite_error_' . CITE_ERROR_KEY_INVALID_1       => 'Internal error; invalid key',
	'cite_error_' . CITE_ERROR_KEY_INVALID_2       => 'Internal error; invalid key',
	'cite_error_' . CITE_ERROR_STACK_INVALID_INPUT => 'Internal error; invalid stack key',

	# User errors
	'cite_error' => 'Cite error $1; $2',

	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => 'Invalid call; expecting a non-integer key',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => 'Invalid call; no key specified',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => 'Invalid call; invalid keys, e.g. too many or wrong key specified',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => 'Invalid call; no input specified',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => 'Invalid input; expecting none',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => 'Invalid parameters; expecting none',
	'cite_error_' . CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL  => "Ran out of custom backlink labels, define more in the \"''cite_references_link_many_format_backlink_labels''\" message",

	/*
	   Output formatting
	*/
	'cite_reference_link_key_with_num' => '$1_$2',
	# Ids produced by <ref>
	'cite_reference_link_prefix'       => '_ref-',
	'cite_reference_link_suffix'       => '',
	# Ids produced by <references>
	'cite_references_link_prefix'      => '_note-',
	'cite_references_link_suffix'      => '',

	'cite_reference_link'                              => '<sup id="$1" class="reference">[[#$2|<nowiki>[</nowiki>$3<nowiki>]</nowiki>]]</sup>',
	'cite_references_link_one'                         => '<li id="$1">[[#$2|↑]] $3</li>',
	'cite_references_link_many'                        => '<li id="$1">↑ $2 $3</li>',
	'cite_references_link_many_format'                 => '[[#$1|<sup>$2</sup>]]',
	# An item from this set is passed as $3 in the message above
	'cite_references_link_many_format_backlink_labels' => 'a b c d e f g h i j k l m n o p q r s t u v w x y z',
	'cite_references_link_many_sep'                    => "\xc2\xa0", // &nbsp;
	'cite_references_link_many_and'                    => "\xc2\xa0", // &nbps;

	# Although I could just use # instead of <li> above and nothing here that
	# will break on input that contains linebreaks
	'cite_references_prefix' => '<ol class="references">',
	'cite_references_suffix' => '</ol>',
);
$wgCiteMessages['he'] = array(
	/*
		Debug and errors
	*/

	# Internal errors
	'cite_croak' => 'בהערה יש שגיאה; $1: $2',

	'cite_error_' . CITE_ERROR_STR_INVALID         => 'שגיאה פנימית; $str שגוי',
	'cite_error_' . CITE_ERROR_KEY_INVALID_1       => 'שגיאה פנימית; מפתח שגוי',
	'cite_error_' . CITE_ERROR_KEY_INVALID_2       => 'שגיאה פנימית; מפתח שגוי',
	'cite_error_' . CITE_ERROR_STACK_INVALID_INPUT => 'שגיאה פנימית; מפתח שגוי בערימה',

	# User errors
	'cite_error' => 'שגיאת ציטוט $1; $2',

	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => 'קריאה שגויה; מצפה למפתח שאינו מספר',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => 'קריאה שגויה; לא צוין מפתח',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => 'קריאה שגויה; מפתחות שגויים, למשל, רבים מדי או שמפתח שגוי צוין',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => 'קריאה שגויה; לא צוין קלט',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => 'קריאה שגויה; מצפה לכלום',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => 'פרמטרים שגויים; מצפה לכלום',
	'cite_error_' . CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL  => "נגמרו תוויות הקישורים המותאמים אישית, אנא הגדירו נוספים בהודעת המערכת \"''cite_references_link_many_format_backlink_labels''\"",

	/*
	   Output formatting
	*/
	'cite_reference_link_key_with_num' => '$1_$2',
	# Ids produced by <ref>
	'cite_reference_link_prefix'       => '_ref-',
	'cite_reference_link_suffix'       => '',
	# Ids produced by <references>
	'cite_references_link_prefix'      => '_note-',
	'cite_references_link_suffix'      => '',

	'cite_reference_link'                              => '<sup id="$1" class="reference">[[#$2|<nowiki>[</nowiki>$3<nowiki>]</nowiki>]]</sup>',
	'cite_references_link_one'                         => '<li id="$1">[[#$2|↑]] $3</li>',
	'cite_references_link_many'                        => '<li id="$1">↑ $2 $3</li>',
	'cite_references_link_many_format'                 => '[[#$1|<sup>$2</sup>]]',
	# An item from this set is passed as $3 in the message above
	'cite_references_link_many_format_backlink_labels' => 'a b c d e f g h i j k l m n o p q r s t u v w x y z',
	'cite_references_link_many_sep'                    => "\xc2\xa0", // &nbsp;
	'cite_references_link_many_and'                    => "\xc2\xa0", // &nbps;

	# Although I could just use # instead of <li> above and nothing here that
	# will break on input that contains linebreaks
	'cite_references_prefix' => '<ol class="references">',
	'cite_references_suffix' => '</ol>',
);
$wgCiteMessages['id'] = array(
	# Internal errors
	'cite_croak' => 'Kegagalan pengutipan; $1: $2',
	'cite_error_' . CITE_ERROR_STR_INVALID         => 'Kesalahan internal; $str tak sah',
	'cite_error_' . CITE_ERROR_KEY_INVALID_1       => 'Kesalahan internal; kunci tak sah',
	'cite_error_' . CITE_ERROR_KEY_INVALID_2       => 'Kesalahan internal; kunci tak sah',
	'cite_error_' . CITE_ERROR_STACK_INVALID_INPUT => 'Kesalahan internal; kunci stack tak sah',

	# User errors
	'cite_error' => 'Kesalahan pengutipan $1; $2',
	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => 'Kesalahan pemanggilan; diharapkan suatu kunci non-integer',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => 'Kesalahan pemanggilan; tidak ada kunci yang dispesifikasikan',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => 'Kesalahan pemanggilan; kunci tak sah, contohnya karena terlalu banyak atau tidak ada kunci yang dispesifikasikan',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => 'Kesalahan pemanggilan; tidak ada masukan yang dispesifikasikan',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => 'Kesalahan masukan; seharusnya tidak ada',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => 'Paramater tak sah; seharusnya tidak ada',
	'cite_error_' . CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL  => "Kehabisan label pralana balik, tambakan pada pesan sistem \"''cite_references_link_many_format_backlink_labels''\"",
);
?>
