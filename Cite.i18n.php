<?php
/**
 * Internationalisation file for Cite extension.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	/*
		Debug and errors
	*/

	# Internal errors
	'cite_croak'                     => 'Cite croaked; $1: $2',
	'cite_error_key_str_invalid'     => 'Internal error; invalid $str and/or $key.  This should never occur.',
	'cite_error_stack_invalid_input' => 'Internal error; invalid stack key.  This should never occur.',

	# User errors
	'cite_error'                               => 'Cite error: $1',
	'cite_error_ref_numeric_key'               => 'Invalid <code>&lt;ref&gt;</code> tag; name cannot be a simple integer, use a descriptive title',
	'cite_error_ref_no_key'                    => 'Invalid <code>&lt;ref&gt;</code> tag; refs with no content must have a name',
	'cite_error_ref_too_many_keys'             => 'Invalid <code>&lt;ref&gt;</code> tag; invalid names, e.g. too many',
	'cite_error_ref_no_input'                  => 'Invalid <code>&lt;ref&gt;</code> tag; refs with no name must have content',
	'cite_error_references_invalid_input'      => 'Invalid <code>&lt;references&gt;</code> tag; no input is allowed, use
<code>&lt;references /&gt;</code>',
	'cite_error_references_invalid_parameters' => 'Invalid <code>&lt;references&gt;</code> tag; no parameters are allowed, use <code>&lt;references /&gt;</code>',
	'cite_error_references_no_backlink_label'  => "Ran out of custom backlink labels, define more in the \"''cite_references_link_many_format_backlink_labels''\" message",
	'cite_error_references_no_text'            => 'No text given.',

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
	'cite_references_link_many_format'                 => '<sup>[[#$1|$2]]</sup>',
	# An item from this set is passed as $3 in the message above
	'cite_references_link_many_format_backlink_labels' => 'a b c d e f g h i j k l m n o p q r s t u v w x y z',
	'cite_references_link_many_sep'                    => " ",
	'cite_references_link_many_and'                    => " ",

	# Although I could just use # instead of <li> above and nothing here that
	# will break on input that contains linebreaks
	'cite_references_prefix' => '<ol class="references">',
	'cite_references_suffix' => '</ol>',
);

/** Czech (Česky) */
$messages['cs'] = array(
	'cite_croak'                              => 'Nefunkční citace; $1: $2',
	'cite_error_key_str_invalid'              => 'Vnitřní chyba; neplatný $str',
	'cite_error_stack_invalid_input'          => 'Vnitřní chyba; neplatný klíč zásobníku',
	'cite_error'                              => 'Chybná citace $1',
	'cite_error_ref_numeric_key'              => 'Chyba v tagu <code>&lt;ref&gt;</code>; názvem nesmí být prosté číslo, použijte popisné označení',
	'cite_error_ref_no_key'                   => 'Chyba v tagu <code>&lt;ref&gt;</code>; prázdné citace musí obsahovat název',
	'cite_error_ref_too_many_keys'            => 'Chyba v tagu <code>&lt;ref&gt;</code>; chybné názvy, např. je jich příliš mnoho',
	'cite_error_ref_no_input'                 => 'Chyba v tagu <code>&lt;ref&gt;</code>; citace bez názvu musí mít vlastní obsah',
	'cite_error_references_invalid_input'     => 'Chyba v tagu <code>&lt;references&gt;</code>; zde není dovolen vstup, použijte <code>&lt;references /&gt;</code>',
	'cite_error_references_no_backlink_label' => "Došla označení zpětných odkazů, přidejte jich několik do zprávy „''cite_references_link_many_format_backlink_labels''“",
);

/** German (Deutsch) */
$messages['de'] = array(
	'cite_croak'                               => 'Fehler im Referenz-System. $1: $2',
	'cite_error_key_str_invalid'               => 'Interner Fehler: ungültiger $str',
	'cite_error_stack_invalid_input'           => 'Interner Fehler: ungültiger „name“-stack',
	'cite_error'                               => 'Referenz-Fehler $1',
	'cite_error_ref_numeric_key'               => 'Ungültige <code><nowiki><ref></nowiki></code>-Verwendung: „name“ darf kein reiner Zahlenwert sein, benutze einen beschreibenden Namen.',
	'cite_error_ref_no_key'                    => 'Ungültige <code><nowiki><ref></nowiki></code>-Verwendung: „ref“ ohne Inhalt muss einen Namen haben.',
	'cite_error_ref_too_many_keys'             => 'Ungültige <code><nowiki><ref></nowiki></code>-Verwendung: „name“ ist ungültig oder zu lang.',
	'cite_error_ref_no_input'                  => 'Ungültige <code><nowiki><ref></nowiki></code>-Verwendung: „ref“ ohne Namen muss einen Inhalt haben.',
	'cite_error_references_invalid_input'      => 'Ungültige <code><nowiki><references></nowiki></code>-Verwendung: Es ist kein zusätzlicher Text erlaubt, verwende ausschließlich <code><nowiki><references /></nowiki></code>.',
	'cite_error_references_invalid_parameters' => 'Ungültige <code><nowiki><reference></nowiki></code>-Verwendung: Es sind keine zusätzlichen Parameter erlaubt, verwende ausschließlich <code><nowiki><reference /></nowiki></code>.',
	'cite_error_references_no_backlink_label'  => 'Eine Referenz der Form <code><nowiki><ref name="…"/></nowiki></code> wird öfter benutzt als Buchstaben vorhanden sind. Ein Administrator muss <nowiki>[[MediaWiki:cite references link many format backlink labels]]</nowiki> um weitere Buchstaben/Zeichen ergänzen.',
	'cite_error_references_no_text'            => 'Eine Referenz der Form <code><nowiki><ref name="…"/></nowiki></code> wird verwendet, ohne definiert worden zu sein.',
);

/** French (Français) */
$messages['fr'] = array(
	'cite_croak'                               => 'Citation corrompue ; $1 : $2',
	'cite_error_key_str_invalid'               => 'Erreur interne ; $str attendue',
	'cite_error_stack_invalid_input'           => 'Erreur interne ; clé de pile invalide',
	'cite_error'                               => 'Erreur de citation $1',
	'cite_error_ref_numeric_key'               => 'Appel invalide ; clé non-intégrale attendue',
	'cite_error_ref_no_key'                    => 'Appel invalide ; aucune clé spécifiée',
	'cite_error_ref_too_many_keys'             => 'Appel invalide ; clés invalides, par exemple, trop de clés spécifiées ou clé erronée',
	'cite_error_ref_no_input'                  => 'Appel invalide ; aucune entrée spécifiée',
	'cite_error_references_invalid_input'      => 'Entrée invalide ; entrée attendue',
	'cite_error_references_invalid_parameters' => 'Arguments invalides ; argument attendu',
	'cite_error_references_no_backlink_label'  => 'Exécution hors des étiquettes personnalisées, définissez plus dans le message « cite_references_link_many_format_backlink_labels »',
	'cite_error_references_no_text'            => 'Aucun texte indiqué.',
);

/** Galician (Galego)
 * @author Alma
 */
$messages['gl'] = array(
	'cite_error' => 'Citar erro: $1',
);

/** Hebrew (עברית) */
$messages['he'] = array(
	'cite_croak'                               => 'בהערה יש שגיאה; $1: $2',
	'cite_error_key_str_invalid'               => 'שגיאה פנימית; $str שגוי',
	'cite_error_stack_invalid_input'           => 'שגיאה פנימית; מפתח שגוי בערימה',
	'cite_error'                               => 'שגיאת ציטוט $1',
	'cite_error_ref_numeric_key'               => 'תגית <code>&lt;ref&gt;</code> שגויה; שם לא יכול להיות מספר פשוט, יש להשתמש בכותרת תיאורית',
	'cite_error_ref_no_key'                    => 'תגית <code>&lt;ref&gt;</code> שגויה; להערות שוליים ללא תוכן חייב להיות שם',
	'cite_error_ref_too_many_keys'             => 'תגית <code>&lt;ref&gt;</code> שגויה; שמות שגויים, למשל, רבים מדי',
	'cite_error_ref_no_input'                  => 'תגית <code>&lt;ref&gt;</code> שגויה; להערות שוליים ללא שם חייב להיות תוכן',
	'cite_error_references_invalid_input'      => 'תגית <code>&lt;references&gt;</code> שגויה; לא ניתן לכתוב תוכן, יש להשתמש בקוד <code>&lt;references /&gt;</code>',
	'cite_error_references_invalid_parameters' => 'תגית <code>&lt;references&gt;</code> שגויה; לא ניתן להשתמש בפרמטרים, יש להשתמש בקוד <code>&lt;references /&gt;</code>',
	'cite_error_references_no_backlink_label'  => "נגמרו תוויות הקישורים המותאמים אישית, אנא הגדירו נוספים בהודעת המערכת \"''cite_references_link_many_format_backlink_labels''\"",
	'cite_error_references_no_text'            => 'לא נכתב טקסט.',
);

/** Indonesian (Bahasa Indonesia) */
$messages['id'] = array(
	'cite_croak'                               => 'Kegagalan pengutipan; $1: $2',
	'cite_error_key_str_invalid'               => 'Kesalahan internal; $str tak sah',
	'cite_error_stack_invalid_input'           => 'Kesalahan internal; kunci stack tak sah',
	'cite_error'                               => 'Kesalahan pengutipan $1',
	'cite_error_ref_numeric_key'               => 'Kesalahan pemanggilan; diharapkan suatu kunci non-integer',
	'cite_error_ref_no_key'                    => 'Kesalahan pemanggilan; tidak ada kunci yang dispesifikasikan',
	'cite_error_ref_too_many_keys'             => 'Kesalahan pemanggilan; kunci tak sah, contohnya karena terlalu banyak atau tidak ada kunci yang dispesifikasikan',
	'cite_error_ref_no_input'                  => 'Kesalahan pemanggilan; tidak ada masukan yang dispesifikasikan',
	'cite_error_references_invalid_input'      => 'Kesalahan masukan; seharusnya tidak ada',
	'cite_error_references_invalid_parameters' => 'Paramater tak sah; seharusnya tidak ada',
	'cite_error_references_no_backlink_label'  => "Kehabisan label pralana balik, tambakan pada pesan sistem \"''cite_references_link_many_format_backlink_labels''\"",
);

/** Italian (Italiano) */
$messages['it'] = array(
	'cite_croak'                               => 'Errore nella citazione: $1: $2',
	'cite_error_key_str_invalid'               => 'Errore interno: $str errato',
	'cite_error_stack_invalid_input'           => 'Errore interno: chiave di stack errata',
	'cite_error'                               => 'Errore nella funzione Cite $1',
	'cite_error_ref_numeric_key'               => "Errore nell'uso del marcatore <code>&lt;ref&gt;</code>: il nome non può essere un numero intero. Usare un titolo esteso",
	'cite_error_ref_no_key'                    => "Errore nell'uso del marcatore <code>&lt;ref&gt;</code>: i ref vuoti non possono essere privi di nome",
	'cite_error_ref_too_many_keys'             => "Errore nell'uso del marcatore <code>&lt;ref&gt;</code>: nomi non validi (ad es. numero troppo elevato)",
	'cite_error_ref_no_input'                  => "Errore nell'uso del marcatore <code>&lt;ref&gt;</code>: i ref privi di nome non possono essere vuoti",
	'cite_error_references_invalid_input'      => "Errore nell'uso del marcatoree <code>&lt;references&gt;</code>: input non ammesso, usare il marcatore
<code>&lt;references /&gt;</code>",
	'cite_error_references_invalid_parameters' => "Errore nell'uso del marcatore <code>&lt;references&gt;</code>: parametri non ammessi, usare il marcatore <code>&lt;references /&gt;</code>",
	'cite_error_references_no_backlink_label'  => "Etichette di rimando personalizzate esaurite, aumentarne il numero nel messaggio \"''cite_references_link_many_format_backlink_labels''\"",
);

/** Japanese (日本語) */
$messages['ja'] = array(
	'cite_croak'                               => '引用タグ機能の重大なエラー; $1: $2',
	'cite_error_key_str_invalid'               => '内部エラー; 無効な $str',
	'cite_error_stack_invalid_input'           => '内部エラー; 無効なスタックキー',
	'cite_error'                               => '引用エラー $1',
	'cite_error_ref_numeric_key'               => '無効な <code>&lt;ref&gt;</code> タグ: 名前に単純な数値は使用できません。',
	'cite_error_ref_no_key'                    => '無効な <code>&lt;ref&gt;</code> タグ: 引用句の内容がない場合には名前 （<code>name</code> 属性）が必要です',
	'cite_error_ref_too_many_keys'             => '無効な <code>&lt;ref&gt;</code> タグ: 無効な名前（多すぎる、もしくは誤った指定）',
	'cite_error_ref_no_input'                  => '無効な <code>&lt;ref&gt;</code> タグ: 名前 （<code>name</code> 属性）がない場合には引用句の内容が必要です',
	'cite_error_references_invalid_input'      => '無効な <code>&lt;references&gt;</code> タグ: 内容のあるタグは使用できません。 <code>&lt;references /&gt;</code> を用いてください。',
	'cite_error_references_invalid_parameters' => '無効な <code>&lt;references&gt;</code> タグ: 引数のあるタグは使用できません。 <code>&lt;references /&gt;</code> を用いてください。',
	'cite_error_references_no_backlink_label'  => "バックリンクラベルが使用できる個数を超えました。\"''cite_references_link_many_format_backlink_labels''\" メッセージでの定義を増やしてください。",
);

/** ‪Қазақша (Қазақстан)‬ (‪Қазақша (Қазақстан)‬) */
$messages['kk-kz'] = array(
	'cite_croak'                               => 'Дәйексөз алу сәтсіз бітті; $1: $2 ',
	'cite_error_key_str_invalid'               => 'Ішкі қате; жарамсыз $str ',
	'cite_error_stack_invalid_input'           => 'Ішкі қате; жарамсыз стек кілті',
	'cite_error'                               => 'Дәйексөз алу $1 қатесі',
	'cite_error_ref_numeric_key'               => 'Жарамсыз <code>&lt;ref&gt;</code> белгішесі; атау кәдімгі бүтін сан болуы мүмкін емес, сиппатауыш атау қолданыңыз',
	'cite_error_ref_no_key'                    => 'Жарамсыз <code>&lt;ref&gt;</code> белгішесі; мағлұматсыз түсініктемелерде атау болуы қажет',
	'cite_error_ref_too_many_keys'             => 'Жарамсыз <code>&lt;ref&gt;</code> белгіше; жарамсыз атаулар, мысалы, тым көп',
	'cite_error_ref_no_input'                  => 'Жарамсыз <code>&lt;ref&gt;</code> белгіше; атаусыз түсініктемелерде мағлұматы болуы қажет',
	'cite_error_references_invalid_input'      => 'Жарамсыз <code>&lt;references&gt;</code> белгіше; еш кіріс рұқсат етілмейді, былай <code>&lt;references /&gt;</code> қолданыңыз',
	'cite_error_references_invalid_parameters' => 'Жарамсыз <code>&lt;references&gt;</code> белгіше; еш баптар рұқсат етілмейді, былай <code>&lt;references /&gt;</code> қолданыңыз',
	'cite_error_references_no_backlink_label'  => "Қосымша белгілердің саны бітті, одан әрі көбірек «''cite_references_link_many_format_backlink_labels''» жүйе хабарында белгілеңіз",
);

/** ‪Qazaqşa (Türkïya)‬ (‪Qazaqşa (Türkïya)‬) */
$messages['kk-tr'] = array(
	'cite_croak'                               => 'Däýeksöz alw sätsiz bitti; $1: $2 ',
	'cite_error_key_str_invalid'               => 'İşki qate; jaramsız $str ',
	'cite_error_stack_invalid_input'           => 'İşki qate; jaramsız stek kilti',
	'cite_error'                               => 'Däýeksöz alw $1 qatesi',
	'cite_error_ref_numeric_key'               => 'Jaramsız <code>&lt;ref&gt;</code> belgişesi; ataw kädimgi bütin san bolwı mümkin emes, sïppatawış ataw qoldanıñız',
	'cite_error_ref_no_key'                    => 'Jaramsız <code>&lt;ref&gt;</code> belgişesi; mağlumatsız tüsiniktemelerde ataw bolwı qajet',
	'cite_error_ref_too_many_keys'             => 'Jaramsız <code>&lt;ref&gt;</code> belgişe; jaramsız atawlar, mısalı, tım köp',
	'cite_error_ref_no_input'                  => 'Jaramsız <code>&lt;ref&gt;</code> belgişe; atawsız tüsiniktemelerde mağlumatı bolwı qajet',
	'cite_error_references_invalid_input'      => 'Jaramsız <code>&lt;references&gt;</code> belgişe; eş kiris ruqsat etilmeýdi, bılaý <code>&lt;references /&gt;</code> qoldanıñız',
	'cite_error_references_invalid_parameters' => 'Jaramsız <code>&lt;references&gt;</code> belgişe; eş baptar ruqsat etilmeýdi, bılaý <code>&lt;references /&gt;</code> qoldanıñız',
	'cite_error_references_no_backlink_label'  => "Qosımşa belgilerdiñ sanı bitti, odan äri köbirek «''cite_references_link_many_format_backlink_labels''» jüýe xabarında belgileñiz",
);

/** ‫قازاقشا (جۇنگو)‬ (‫قازاقشا (جۇنگو)‬) */
$messages['kk-cn'] = array(
	'cite_croak'                               => 'دٵيەكسٶز الۋ سٵتسٸز بٸتتٸ; $1: $2 ',
	'cite_error_key_str_invalid'               => 'ٸشكٸ قاتە; جارامسىز $str ',
	'cite_error_stack_invalid_input'           => 'ٸشكٸ قاتە; جارامسىز ستەك كٸلتٸ',
	'cite_error'                               => 'دٵيەكسٶز الۋ $1 قاتەسٸ',
	'cite_error_ref_numeric_key'               => 'جارامسىز <code>&lt;ref&gt;</code> بەلگٸشەسٸ; اتاۋ كٵدٸمگٸ بٷتٸن سان بولۋى مٷمكٸن ەمەس, سيپپاتاۋىش اتاۋ قولدانىڭىز',
	'cite_error_ref_no_key'                    => 'جارامسىز <code>&lt;ref&gt;</code> بەلگٸشەسٸ; ماعلۇماتسىز تٷسٸنٸكتەمەلەردە اتاۋ بولۋى قاجەت',
	'cite_error_ref_too_many_keys'             => 'جارامسىز <code>&lt;ref&gt;</code> بەلگٸشە; جارامسىز اتاۋلار, مىسالى, تىم كٶپ',
	'cite_error_ref_no_input'                  => 'جارامسىز <code>&lt;ref&gt;</code> بەلگٸشە; اتاۋسىز تٷسٸنٸكتەمەلەردە ماعلۇماتى بولۋى قاجەت',
	'cite_error_references_invalid_input'      => 'جارامسىز <code>&lt;references&gt;</code> بەلگٸشە; ەش كٸرٸس رۇقسات ەتٸلمەيدٸ, بىلاي <code>&lt;references /&gt;</code> قولدانىڭىز',
	'cite_error_references_invalid_parameters' => 'جارامسىز <code>&lt;references&gt;</code> بەلگٸشە; ەش باپتار رۇقسات ەتٸلمەيدٸ, بىلاي <code>&lt;references /&gt;</code> قولدانىڭىز',
	'cite_error_references_no_backlink_label'  => "قوسىمشا بەلگٸلەردٸڭ سانى بٸتتٸ, ودان ٵرٸ كٶبٸرەك «''cite_references_link_many_format_backlink_labels''» جٷيە حابارىندا بەلگٸلەڭٸز",
);

$messages['kk'] = $messages['kk-kz'];

/** Lietuvių (Lietuvių) */
$messages['lt'] = array(
	'cite_croak'                               => 'Cituoti nepavyko; $1: $2',
	'cite_error_key_str_invalid'               => 'Vidinė klaida; neleistinas $str',
	'cite_error_stack_invalid_input'           => 'Vidinė klaida; neleistinas steko raktas',
	'cite_error'                               => 'Citavimo klaida $1',
	'cite_error_ref_numeric_key'               => 'Neleistina <code>&lt;ref&gt;</code> gairė; vardas negali būti tiesiog skaičius, naudokite tekstinį pavadinimą',
	'cite_error_ref_no_key'                    => 'Neleistina <code>&lt;ref&gt;</code> gairė; nuorodos be turinio turi turėti vardą',
	'cite_error_ref_too_many_keys'             => 'Neleistina <code>&lt;ref&gt;</code> gairė; neleistini vardai, pvz., per daug',
	'cite_error_ref_no_input'                  => 'Neleistina <code>&lt;ref&gt;</code> gairė; nuorodos be vardo turi turėti turinį',
	'cite_error_references_invalid_input'      => 'Neleistina <code>&lt;references&gt;</code> gairė; neleistina jokia įvestis, naudokite <code>&lt;references /&gt;</code>',
	'cite_error_references_invalid_parameters' => 'Neleistina <code>&lt;references&gt;</code> gairė; neleidžiami jokie parametrai, naudokite <code>&lt;references /&gt;</code>',
	'cite_error_references_no_backlink_label'  => "Baigėsi antraštės, nurodykite daugiau \"''cite_references_link_many_format_backlink_labels''\" sisteminiame tekste",
);

/** Dutch (Nederlands) */
$messages['nl'] = array(
	'cite_croak'                               => 'Probleem met Cite; $1: $2',
	'cite_error_key_str_invalid'               => 'Interne fout; onjuiste $str',
	'cite_error_stack_invalid_input'           => 'Interne fout; onjuiste stacksleutel',
	'cite_error'                               => 'Citefout $1',
	'cite_error_ref_numeric_key'               => 'Onjuiste tag <code>&lt;ref&gt;</code>; de naam kan geen simplele integer zijn, gebruik een beschrijvende titel',
	'cite_error_ref_no_key'                    => 'Onjuiste tag <code>&lt;ref&gt;</code>; refs zonder inhoud moeten een naam hebben',
	'cite_error_ref_too_many_keys'             => 'Onjuiste tag <code>&lt;ref&gt;</code>; onjuiste namen, bijvoorbeeld te veel',
	'cite_error_ref_no_input'                  => 'Onjuiste tag <code>&lt;ref&gt;</code>; refs zonder naam moeten inhoud hebben',
	'cite_error_references_invalid_input'      => 'Onjuiste tag <code>&lt;references&gt;</code>; invoer is niet toegestaan, gebruik <code>&lt;references /&gt;</code>',
	'cite_error_references_invalid_parameters' => 'Onjuiste tag <code>&lt;references&gt;</code>; parameters zijn niet toegestaan, gebruik <code>&lt;references /&gt;</code>',
);

/** Portuguese (Português) */
$messages['pt'] = array(
	'cite_croak'                               => 'Citação com problemas; $1: $2',
	'cite_error_key_str_invalid'               => 'Erro interno; $str inválido',
	'cite_error_stack_invalid_input'           => 'Erro interno; chave fixa inválida',
	'cite_error'                               => 'Erro de citação $1',
	'cite_error_ref_numeric_key'               => 'Código <code>&lt;ref&gt;</code> inválido; o nome não pode ser um número. Utilize um nome descritivo',
	'cite_error_ref_no_key'                    => 'Código <code>&lt;ref&gt;</code> inválido; refs sem conteúdo devem ter um parâmetro de nome',
	'cite_error_ref_too_many_keys'             => 'Código <code>&lt;ref&gt;</code> inválido; nomes inválidos (por exemplo, nome muito extenso)',
	'cite_error_ref_no_input'                  => 'Código <code>&lt;ref&gt;</code> inválido; refs sem parâmetro de nome devem possuir conteúdo a elas associado',
	'cite_error_references_invalid_input'      => 'Código <code>&lt;references&gt;</code> inválido; no input is allowed, use
<code>&lt;references /&gt;</code>',
	'cite_error_references_invalid_parameters' => 'Código <code>&lt;references&gt;</code> inválido; não são permitidos parâmetros. Utilize como <code>&lt;references /&gt;</code>',
);

/** Russian (Русский) */
$messages['ru'] = array(
	'cite_croak'                                       => 'Цитата сдохла; $1: $2',
	'cite_error_key_str_invalid'                       => 'Внутренняя ошибка: неверный $str',
	'cite_error_stack_invalid_input'                   => 'Внутренняя ошибка: неверный ключ стека ',
	'cite_error'                                       => 'Ошибка цитирования $1',
	'cite_error_ref_numeric_key'                       => 'Неправильный вызов: ожидался нечисловой ключ',
	'cite_error_ref_no_key'                            => 'Неправильный вызов: ключ не был указан',
	'cite_error_ref_too_many_keys'                     => 'Неправильный вызов: неверные ключи, например было указано слишком много ключей или ключ был неправильным',
	'cite_error_ref_no_input'                          => 'Неверный вызов: нет входных данных',
	'cite_error_references_invalid_input'              => 'Входные данные недействительны, так как не предполагаются',
	'cite_error_references_invalid_parameters'         => 'Переданы недействительные параметры; их вообще не предусмотрено.',
	'cite_error_references_no_backlink_label'          => 'Не хватает символов для возвратных гиперссылок; следует расширить системную переменную «cite_references_link_many_format_backlink_labels».',
	'cite_references_link_many_format_backlink_labels' => 'а б в г д е ё ж з и й к л м н о п р с т у ф х ц ч ш щ ъ ы ь э ю я',
);

/** Slovak (Slovenčina) */
$messages['sk'] = array(
	'cite_croak'                               => 'Citát je už neaktuálny; $1: $2',
	'cite_error_key_str_invalid'               => 'Vnútorná chyba; neplatný $str',
	'cite_error_stack_invalid_input'           => 'Vnútorná chyba; neplatný kľúč zásobníka',
	'cite_error'                               => 'Chyba citácie $1',
	'cite_error_ref_numeric_key'               => 'Neplatné volanie; očakáva sa neceločíselný typ kľúča',
	'cite_error_ref_no_key'                    => 'Neplatné volanie; nebol špecifikovaný kľúč',
	'cite_error_ref_too_many_keys'             => 'Neplatné volanie; neplatné kľúče, napr. príliš veľa alebo nesprávne špecifikovaný kľúč',
	'cite_error_ref_no_input'                  => 'Neplatné volanie; nebol špecifikovaný vstup',
	'cite_error_references_invalid_input'      => 'Neplatné volanie; neočakával sa vstup',
	'cite_error_references_invalid_parameters' => 'Neplatné parametre; neočakávli sa žiadne',
	'cite_error_references_no_backlink_label'  => "Minuli sa generované návestia spätných odkazov, definujte viac v správe \"''cite_references_link_many_format_backlink_labels''\"",
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'cite_error_key_str_invalid'    => 'Internen Failer: ungultigen $str',
	'cite_error_ref_too_many_keys'  => 'Uungultige <code><nowiki><ref></nowiki></code>-Ferweendenge: „name“ is uungultich of tou loang.',
	'cite_error_ref_no_input'       => 'Uungultige <code><nowiki><ref></nowiki></code>-Ferweendenge: „ref“ sunner Noome mout n Inhoold hääbe.',
	'cite_error_references_no_text' => 'Ne Referenz fon ju Foarm <code><nowiki><ref name="…"/></nowiki></code> wäd ferwoand, sunner definierd wuuden tou weesen.',
);

/** Kantonese (粵語) */
$messages['yue'] = array(
	'cite_croak'                               => '引用阻塞咗; $1: $2',
	'cite_error_key_str_invalid'               => '內部錯誤; 無效嘅 $str',
	'cite_error_stack_invalid_input'           => '內部錯誤; 無效嘅堆疊匙',
	'cite_error'                               => '引用錯誤 $1',
	'cite_error_ref_numeric_key'               => '無效嘅呼叫; 需要一個非整數嘅匙',
	'cite_error_ref_no_key'                    => '無效嘅呼叫; 未指定匙',
	'cite_error_ref_too_many_keys'             => '無效嘅呼叫; 無效嘅匙, 例如: 太多或者指定咗一個錯咗嘅匙',
	'cite_error_ref_no_input'                  => '無效嘅呼叫; 未指定輸入',
	'cite_error_references_invalid_input'      => '無效嘅輸入; 唔需要有嘢',
	'cite_error_references_invalid_parameters' => '無效嘅參數; 唔需要有嘢',
	'cite_error_references_no_backlink_label'  => "用晒啲自定返回標籤, 響 \"''cite_references_link_many_format_backlink_labels''\" 信息再整多啲",
);

/** Simplified Chinese (‪中文(简体)‬) */
$messages['zh-hans'] = array(
	'cite_croak'                               => '引用阻塞; $1: $2',
	'cite_error_key_str_invalid'               => '内部错误；非法的 $str',
	'cite_error_stack_invalid_input'           => '内部错误；非法堆栈键值',
	'cite_error'                               => '引用错误 $1',
	'cite_error_ref_numeric_key'               => '无效呼叫；需要一个非整数的键值',
	'cite_error_ref_no_key'                    => '无效呼叫；没有指定键值',
	'cite_error_ref_too_many_keys'             => '无效呼叫；非法键值，例如：过多或错误的指定键值',
	'cite_error_ref_no_input'                  => '无效呼叫；没有指定的输入',
	'cite_error_references_invalid_input'      => '无效输入；需求为空',
	'cite_error_references_invalid_parameters' => '非法参数；需求为空',
	'cite_error_references_no_backlink_label'  => "自定义后退标签已经用完了，现在可在标签 \"''cite_references_link_many_format_backlink_labels''\" 定义更多信息",
);

/** ‪Traditional Chinese (‪中文(繁體)‬) */
$messages['zh-hant'] = array(
	'cite_croak'                               => '引用阻塞; $1: $2',
	'cite_error_key_str_invalid'               => '內部錯誤；非法的 $str',
	'cite_error_stack_invalid_input'           => '內部錯誤；非法堆疊鍵值',
	'cite_error'                               => '引用錯誤 $1',
	'cite_error_ref_numeric_key'               => '無效呼叫；需要一個非整數的鍵',
	'cite_error_ref_no_key'                    => '無效呼叫；沒有指定鍵',
	'cite_error_ref_too_many_keys'             => '無效呼叫；非法鍵值，例如：過多或錯誤的指定鍵',
	'cite_error_ref_no_input'                  => '無效呼叫；沒有指定的輸入',
	'cite_error_references_invalid_input'      => '無效輸入；需求為空',
	'cite_error_references_invalid_parameters' => '非法參數；需求為空',
	'cite_error_references_no_backlink_label'  => "自訂後退標籤已經用完了，現在可在標籤 \"''cite_references_link_many_format_backlink_labels''\" 定義更多信息",
);

$messages['zh'] = $messages['zh-hans'];
$messages['zh-cn'] = $messages['zh-hans'];
$messages['zh-hk'] = $messages['zh-hant'];
$messages['zh-sg'] = $messages['zh-hans'];
$messages['zh-tw'] = $messages['zh-hant'];
$messages['zh-yue'] = $messages['yue'];
