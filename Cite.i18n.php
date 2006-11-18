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

	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => 'Invalid <code>&lt;ref&gt;</code> tag; name cannot be a simple integer, use a descriptive title',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => 'Invalid <code>&lt;ref&gt;</code> tag; refs with no content must have a name',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => 'Invalid <code>&lt;ref&gt;</code> tag; invalid names, e.g. too many',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => 'Invalid <code>&lt;ref&gt;</code> tag; refs with no name must have content',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => 'Invalid <code>&lt;references&gt;</code> tag; no input is allowed, use
<code>&lt;references /&gt;</code>',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => 'Invalid <code>&lt;references&gt;</code> tag; no parameters are allowed, use <code>&lt;references /&gt;</code>',
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
$wgCiteMessages['cs'] = array(
	/*
		Debug and errors
	*/

	# Internal errors
	'cite_croak' => 'Nefunkční citace; $1: $2',

	'cite_error_' . CITE_ERROR_STR_INVALID         => 'Vnitřní chyba; neplatný $str',
	'cite_error_' . CITE_ERROR_KEY_INVALID_1       => 'Vnitřní chyba; neplatný klíč',
	'cite_error_' . CITE_ERROR_KEY_INVALID_2       => 'Vnitřní chyba; neplatný klíč',
	'cite_error_' . CITE_ERROR_STACK_INVALID_INPUT => 'Vnitřní chyba; neplatný klíč zásobníku',

	# User errors
	'cite_error' => 'Chybná citace $1; $2',

	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => 'Chyba v tagu <code>&lt;ref&gt;</code>; názvem nesmí být prosté číslo, použijte popisné označení',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => 'Chyba v tagu <code>&lt;ref&gt;</code>; prázdné citace musí obsahovat název',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => 'Chyba v tagu <code>&lt;ref&gt;</code>; chybné názvy, např. je jich příliš mnoho',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => 'Chyba v tagu <code>&lt;ref&gt;</code>; citace bez názvu musí mít vlastní obsah',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => 'Chyba v tagu <code>&lt;references&gt;</code>; zde není dovolen vstup, použijte <code>&lt;references /&gt;</code>',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => 'Invalid <code>&lt;references&gt;</code> tag; no parameters are allowed, use <code>&lt;references /&gt;</code>',
	'cite_error_' . CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL  => "Došla označení zpětných odkazů, přidejte jich několik do zprávy „''cite_references_link_many_format_backlink_labels''“",

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
	'cite_references_link_many_and'                    => "\xc2\xa0", // &nbsp;

	# Although I could just use # instead of <li> above and nothing here that
	# will break on input that contains linebreaks
	'cite_references_prefix' => '<ol class="references">',
	'cite_references_suffix' => '</ol>',
);
$wgCiteMessages['fr'] = array(
	# Internal errors
	'cite_croak' => 'Citation corrompue; $1: $2',
	'cite_error_' . CITE_ERROR_STR_INVALID         => 'Erreur interne; $str attendue',
	'cite_error_' . CITE_ERROR_KEY_INVALID_1       => 'Erreur interne; clé invalide',
	'cite_error_' . CITE_ERROR_KEY_INVALID_2       => 'Erreur interne; clé invalide ',
	'cite_error_' . CITE_ERROR_STACK_INVALID_INPUT => 'Erreur interne; clé de pile invalide',

	# User errors
	'cite_error' => 'Kesalahan pengutipan $1; $2',
	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => 'Appel invalide; clé non-intégrale attendue',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => 'Appel invalide; aucune clé spécifiée',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => 'Appel invalide; clés invalides : clé erronée, ou trop de clés spécifiées',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => 'Appel invalide; aucune entrée spécifiée',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => 'Entrée invalides; entrée attendu',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => 'Arguments invalides; argument attendu',
	'cite_error_' . CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL  => "Exécution en dehors des étiquettes personnalisées, définissez plus dans le message \"''cite_references_link_many_format_backlink_labels''\"",
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

	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => 'תגית <code>&lt;ref&gt;</code> שגויה; שם לא יכול להיות מספר פשוט, יש להשתמש בכותרת תיאורית',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => 'תגית <code>&lt;ref&gt;</code> שגויה; להערות שוליים ללא תוכן חייב להיות שם',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => 'תגית <code>&lt;ref&gt;</code> שגויה; שמות שגויים, למשל, רבים מדי',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => 'תגית <code>&lt;ref&gt;</code> שגויה; להערות שוליים ללא שם חייב להיות תוכן',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => 'תגית <code>&lt;references&gt;</code> שגויה; לא ניתן לכתוב תוכן, יש להשתמש בקוד <code>&lt;references /&gt;</code>',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => 'תגית <code>&lt;references&gt;</code> שגויה; לא ניתן להשתמש בפרמטרים, יש להשתמש בקוד <code>&lt;references /&gt;</code>',
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
$wgCiteMessages['kk-kz'] = array(
	/*
		Debug and errors
	*/

	# Internal errors
	'cite_croak' => 'Дәйексөз алу сәтсіз бітті; $1: $2 ',

	'cite_error_' . CITE_ERROR_STR_INVALID         => 'Ішкі қате; жарамсыз $str ',
	'cite_error_' . CITE_ERROR_KEY_INVALID_1       => 'Ішкі қате; жарамсыз кілт',
	'cite_error_' . CITE_ERROR_KEY_INVALID_2       => 'Ішкі қате; жарамсыз кілт',
	'cite_error_' . CITE_ERROR_STACK_INVALID_INPUT => 'Ішкі қате; жарамсыз стек кілті',

	# User errors
	'cite_error' => 'Дәйексөз алу $1 қатесі; $2',

	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => 'Жарамсыз <code>&lt;ref&gt;</code> белгішесі; атау кәдімгі бүтін сан болуы мүмкін емес, сиппатауыш атау қолданыңыз',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => 'Жарамсыз <code>&lt;ref&gt;</code> белгішесі; мағлұматсыз түсініктемелерде атау болуы қажет',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => 'Жарамсыз <code>&lt;ref&gt;</code> белгіше; жарамсыз атаулар, мысалы, тым көп',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => 'Жарамсыз <code>&lt;ref&gt;</code> белгіше; атаусыз түсініктемелерде мағлұматы болуы қажет',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => 'Жарамсыз <code>&lt;references&gt;</code> белгіше; еш кіріс рұқсат етілмейді, былай <code>&lt;references /&gt;</code> қолданыңыз',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => 'Жарамсыз <code>&lt;references&gt;</code> белгіше; еш баптар рұқсат етілмейді, былай <code>&lt;references /&gt;</code> қолданыңыз',
	'cite_error_' . CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL  => 'Қосымша белгілердің саны бітті, одан әрі көбірек «\'\'cite_references_link_many_format_backlink_labels\'\'» жүйе хабарында белгілеңіз',
);
$wgCiteMessages['kk-tr'] = array(
	/*
		Debug and errors
	*/

	# Internal errors
	'cite_croak' => 'Däýeksöz alw sätsiz bitti; $1: $2 ',

	'cite_error_' . CITE_ERROR_STR_INVALID         => 'İşki qate; jaramsız $str ',
	'cite_error_' . CITE_ERROR_KEY_INVALID_1       => 'İşki qate; jaramsız kilt',
	'cite_error_' . CITE_ERROR_KEY_INVALID_2       => 'İşki qate; jaramsız kilt',
	'cite_error_' . CITE_ERROR_STACK_INVALID_INPUT => 'İşki qate; jaramsız stek kilti',

	# User errors
	'cite_error' => 'Däýeksöz alw $1 qatesi; $2',

	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => 'Jaramsız <code>&lt;ref&gt;</code> belgişesi; ataw kädimgi bütin san bolwı mümkin emes, sïppatawış ataw qoldanıñız',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => 'Jaramsız <code>&lt;ref&gt;</code> belgişesi; mağlumatsız tüsiniktemelerde ataw bolwı qajet',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => 'Jaramsız <code>&lt;ref&gt;</code> belgişe; jaramsız atawlar, mısalı, tım köp',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => 'Jaramsız <code>&lt;ref&gt;</code> belgişe; atawsız tüsiniktemelerde mağlumatı bolwı qajet',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => 'Jaramsız <code>&lt;references&gt;</code> belgişe; eş kiris ruqsat etilmeýdi, bılaý <code>&lt;references /&gt;</code> qoldanıñız',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => 'Jaramsız <code>&lt;references&gt;</code> belgişe; eş baptar ruqsat etilmeýdi, bılaý <code>&lt;references /&gt;</code> qoldanıñız',
	'cite_error_' . CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL  => 'Qosımşa belgilerdiñ sanı bitti, odan äri köbirek «\'\'cite_references_link_many_format_backlink_labels\'\'» jüýe xabarında belgileñiz',
);
$wgCiteMessages['kk-cn'] = array(
	/*
		Debug and errors
	*/

	# Internal errors
	'cite_croak' => 'دٴايەكسٴوز الۋ سٴاتسٴىز بٴىتتٴى; $1: $2 ',

	'cite_error_' . CITE_ERROR_STR_INVALID         => 'ٴىشكٴى قاتە; جارامسىز $str ',
	'cite_error_' . CITE_ERROR_KEY_INVALID_1       => 'ٴىشكٴى قاتە; جارامسىز كٴىلت',
	'cite_error_' . CITE_ERROR_KEY_INVALID_2       => 'ٴىشكٴى قاتە; جارامسىز كٴىلت',
	'cite_error_' . CITE_ERROR_STACK_INVALID_INPUT => 'ٴىشكٴى قاتە; جارامسىز ستەك كٴىلتٴى',

	# User errors
	'cite_error' => 'دٴايەكسٴوز الۋ $1 قاتەسٴى; $2',

	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => 'جارامسىز <code>&lt;ref&gt;</code> بەلگٴىشەسٴى; اتاۋ كٴادٴىمگٴى بٴۇتٴىن سان بولۋى مٴۇمكٴىن ەمەس, سيپپاتاۋىش اتاۋ قولدانىڭىز',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => 'جارامسىز <code>&lt;ref&gt;</code> بەلگٴىشەسٴى; ماعلۇماتسىز تٴۇسٴىنٴىكتەمەلەردە اتاۋ بولۋى قاجەت',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => 'جارامسىز <code>&lt;ref&gt;</code> بەلگٴىشە; جارامسىز اتاۋلار, مىسالى, تىم كٴوپ',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => 'جارامسىز <code>&lt;ref&gt;</code> بەلگٴىشە; اتاۋسىز تٴۇسٴىنٴىكتەمەلەردە ماعلۇماتى بولۋى قاجەت',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => 'جارامسىز <code>&lt;references&gt;</code> بەلگٴىشە; ەش كٴىرٴىس رۇقسات ەتٴىلمەيدٴى, بىلاي <code>&lt;references /&gt;</code> قولدانىڭىز',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => 'جارامسىز <code>&lt;references&gt;</code> بەلگٴىشە; ەش باپتار رۇقسات ەتٴىلمەيدٴى, بىلاي <code>&lt;references /&gt;</code> قولدانىڭىز',
	'cite_error_' . CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL  => 'قوسىمشا بەلگٴىلەردٴىڭ سانى بٴىتتٴى, ودان ٴارٴى كٴوبٴىرەك «\'\'cite_references_link_many_format_backlink_labels\'\'» جٴۇيە حابارىندا بەلگٴىلەڭٴىز',
);
$wgCiteMessages['kk'] = $wgCiteMessages['kk-kz'];
$wgCiteMessages['nl'] = array(
	/*
		Debug and errors
	*/

	# Internal errors
	'cite_croak' => 'Probleem met Cite; $1: $2',

	'cite_error_' . CITE_ERROR_STR_INVALID         => 'Interne fout; onjuiste $str',
	'cite_error_' . CITE_ERROR_KEY_INVALID_1       => 'Interne fout; onjuiste sleutel',
	'cite_error_' . CITE_ERROR_KEY_INVALID_2       => 'Interne fout; onjuiste sleutel',
	'cite_error_' . CITE_ERROR_STACK_INVALID_INPUT => 'Interne fout; onjuiste stacksleutel',

	# User errors
	'cite_error' => 'Citefout $1; $2',

	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => 'Onjuiste call; expecting a non-integer key',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => 'Onjuiste call; no key specified',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => 'Onjuiste call; invalid keys, e.g. too many or wrong key specified',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => 'Onjuiste call; no input specified',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => 'Onjuiste invoer; geen verwacht',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => 'Onjuiste parameters; geen verwacht',
	'cite_error_' . CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL  => "Voorraad terugverwijslabels was op, stel er meer in bij het bericht \"''cite_references_link_many_format_backlink_labels''\"",
);
$wgCiteMessages['ru'] = array(
	/*
		Debug and errors
	*/

	# Internal errors
	'cite_croak' => 'Цитата сдохла; $1: $2',

	'cite_error_' . CITE_ERROR_STR_INVALID         => 'Внутренняя ошибка: неверный $str',
	'cite_error_' . CITE_ERROR_KEY_INVALID_1       => 'Внутренняя ошибка: неверный ключ',
	'cite_error_' . CITE_ERROR_KEY_INVALID_2       => 'Внутренняя ошибка: неверный ключ',
	'cite_error_' . CITE_ERROR_STACK_INVALID_INPUT => 'Внутренняя ошибка: неверный ключ стека ',

	# User errors
	'cite_error' => 'Ошибка цитирования $1; $2',

	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => 'Неправильный вызов: ожидался нечисловой ключ',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => 'Неправильный вызов: ключ не был указан',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => 'Неправильный вызов: неверные ключи, например было указано слишком много ключей или ключ был неправильным',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => 'Неверный вызов: нет входных данных',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => 'Входные данные недействительны, так как не предполагаются',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => 'Переданы недействительные параметры; их вообще не предусмотрено.',
	'cite_error_' . CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL  => 'Не хватает символов для возвратных гиперссылок; следует расширить системную переменную «cite_references_link_many_format_backlink_labels».',

	/*
	   Output formatting
	*/
	'cite_references_link_many_format_backlink_labels' => 'а б в г д е ё ж з и й к л м н о п р с т у ф х ц ч ш щ ъ ы ь э ю я',
);
$wgCiteMessages['sk'] = array(
	/*
	    Debug and errors
	*/

	# Internal errors
	'cite_croak' => 'Citát je už neaktuálny; $1: $2',

	'cite_error_' . CITE_ERROR_STR_INVALID         => 'Vnútorná chyba; neplatný $str',
	'cite_error_' . CITE_ERROR_KEY_INVALID_1       => 'Vnútorná chyba; neplatný kľúč',
	'cite_error_' . CITE_ERROR_KEY_INVALID_2       => 'Vnútorná chyba; neplatný kľúč',
	'cite_error_' . CITE_ERROR_STACK_INVALID_INPUT => 'Vnútorná chyba; neplatný kľúč zásobníka',

	# User errors
	'cite_error' => 'Chyba citácie $1; $2',

	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => 'Neplatné volanie; očakáva sa neceločíselný typ kľúča',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => 'Neplatné volanie; nebol špecifikovaný kľúč',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => 'Neplatné volanie; neplatné kľúče, napr. príliš veľa alebo nesprávne špecifikovaný kľúč',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => 'Neplatné volanie; nebol špecifikovaný vstup',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => 'Neplatné volanie; neočakával sa vstup',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => 'Neplatné parametre; neočakávli sa žiadne',
	'cite_error_' . CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL  => "Minuli sa generované návestia spätných odkazov, definujte viac v správe \"''cite_references_link_many_format_backlink_labels''\"",

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
$wgCiteMessages['zh-cn'] = array(
	/*
		Debug and errors
	*/

	# Internal errors
	'cite_croak' => '引用阻塞; $1: $2',

	'cite_error_' . CITE_ERROR_STR_INVALID         => '内部错误；非法的 $str',
	'cite_error_' . CITE_ERROR_KEY_INVALID_1       => '内部错误；非法键值',
	'cite_error_' . CITE_ERROR_KEY_INVALID_2       => '内部错误；非法键值',
	'cite_error_' . CITE_ERROR_STACK_INVALID_INPUT => '内部错误；非法堆栈键值',

	# User errors
	'cite_error' => '引用错误 $1; $2',

	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => '无效呼叫；需要一个非整数的键值',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => '无效呼叫；没有指定键值',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => '无效呼叫；非法键值，例如：过多或错误的指定键值',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => '无效呼叫；没有指定的输入',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => '无效输入；需求为空',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => '非法参数；需求为空',
	'cite_error_' . CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL  => "自定义后退标签已经用完了，现在可在标签 \"''cite_references_link_many_format_backlink_labels''\" 定义更多信息",

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
$wgCiteMessages['zh-tw'] = array(
	/*
		Debug and errors
	*/

	# Internal errors
	'cite_croak' => '引用阻塞; $1: $2',

	'cite_error_' . CITE_ERROR_STR_INVALID         => '內部錯誤；非法的 $str',
	'cite_error_' . CITE_ERROR_KEY_INVALID_1       => '內部錯誤；非法鍵',
	'cite_error_' . CITE_ERROR_KEY_INVALID_2       => '內部錯誤；非法鍵',
	'cite_error_' . CITE_ERROR_STACK_INVALID_INPUT => '內部錯誤；非法堆疊鍵值',

	# User errors
	'cite_error' => '引用錯誤 $1; $2',

	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => '無效呼叫；需要一個非整數的鍵',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => '無效呼叫；沒有指定鍵',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => '無效呼叫；非法鍵值，例如：過多或錯誤的指定鍵',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => '無效呼叫；沒有指定的輸入',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => '無效輸入；需求為空',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => '非法參數；需求為空',
	'cite_error_' . CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL  => "自訂後退標籤已經用完了，現在可在標籤 \"''cite_references_link_many_format_backlink_labels''\" 定義更多信息",

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
$wgCiteMessages['zh-yue'] = array(
	/*
		Debug and errors
	*/

	# Internal errors
	'cite_croak' => '引用阻塞咗; $1: $2',

	'cite_error_' . CITE_ERROR_STR_INVALID         => '內部錯誤; 無效嘅 $str',
	'cite_error_' . CITE_ERROR_KEY_INVALID_1       => '內部錯誤; 無效嘅匙',
	'cite_error_' . CITE_ERROR_KEY_INVALID_2       => '內部錯誤; 無效嘅匙',
	'cite_error_' . CITE_ERROR_STACK_INVALID_INPUT => '內部錯誤; 無效嘅堆疊匙',

	# User errors
	'cite_error' => '引用錯誤 $1; $2',

	'cite_error_' . CITE_ERROR_REF_NUMERIC_KEY               => '無效嘅呼叫; 需要一個非整數嘅匙',
	'cite_error_' . CITE_ERROR_REF_NO_KEY                    => '無效嘅呼叫; 未指定匙',
	'cite_error_' . CITE_ERROR_REF_TOO_MANY_KEYS             => '無效嘅呼叫; 無效嘅匙, 例如: 太多或者指定咗一個錯咗嘅匙',
	'cite_error_' . CITE_ERROR_REF_NO_INPUT                  => '無效嘅呼叫; 未指定輸入',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_INPUT      => '無效嘅輸入; 唔需要有嘢',
	'cite_error_' . CITE_ERROR_REFERENCES_INVALID_PARAMETERS => '無效嘅參數; 唔需要有嘢',
	'cite_error_' . CITE_ERROR_REFERENCES_NO_BACKLINK_LABEL  => "用晒啲自定返回標籤, 響 \"''cite_references_link_many_format_backlink_labels''\" 信息再整多啲",

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
$wgCiteMessages['zh-hk'] = $wgCiteMessages['zh-tw'];
$wgCiteMessages['zh-sg'] = $wgCiteMessages['zh-cn'];
?>
