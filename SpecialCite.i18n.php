<?php
/**
 * Internationalisation file for Cite special page extension.
 *
 * @file
 * @ingroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'cite_article_desc'       => 'Adds a [[Special:Cite|citation]] special page and toolbox link',
	'cite_article_link'       => 'Cite this page',
	'tooltip-cite-article'    => 'Information on how to cite this page',
	'accesskey-cite-article'  => '', # Do not translate this
	'cite'                    => 'Cite',
	'cite-summary'            => '', # Do not translate this
	'cite_page'               => 'Page:',
	'cite_submit'             => 'Cite',
	'cite_text'               => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

== Bibliographic details for {{FULLPAGENAME}} ==

* Page name: {{FULLPAGENAME}}
* Author: {{SITENAME}} contributors
* Publisher: ''{{SITENAME}}, {{int:sitesubtitle}}''.
* Date of last revision: {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC
* Date retrieved: <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC</citation>
* Permanent URL: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* Page Version ID: {{REVISIONID}}

</div>
<div class=\"plainlinks mw-specialcite-styles\">

== Citation styles for {{FULLPAGENAME}} ==

=== [[APA style]] ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}). ''{{SITENAME}}, {{int:sitesubtitle}}''. Retrieved <citation>{{CURRENTTIME}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> from {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[The MLA style manual|MLA style]] ===
\"{{FULLPAGENAME}}.\" ''{{SITENAME}}, {{int:sitesubtitle}}''. {{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC. <citation>{{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;.

=== [[MHRA Style Guide|MHRA style]] ===
{{SITENAME}} contributors, '{{FULLPAGENAME}}', ''{{SITENAME}}, {{int:sitesubtitle}},'' {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC, &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [accessed <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}</citation>]

=== [[The Chicago Manual of Style|Chicago style]] ===
{{SITENAME}} contributors, \"{{FULLPAGENAME}},\" ''{{SITENAME}}, {{int:sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (accessed <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== [[Council of Science Editors|CBE/CSE style]] ===
{{SITENAME}} contributors. {{FULLPAGENAME}} [Internet]. {{SITENAME}}, {{int:sitesubtitle}}; {{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}, {{CURRENTTIME}} UTC [cited <citation>{{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}</citation>]. Available from:
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[Bluebook|Bluebook style]] ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (last visited <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== [[BibTeX]] entry ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }

When using the [[LaTeX]] package url (<code>\usepackage{url}</code> somewhere in the preamble) which tends to give much more nicely formatted web addresses, the following may be preferred:

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }


</div> <!--closing div for \"plainlinks\"-->",
);

/** Message documentation (Message documentation)
 * @author Jon Harald Søby
 * @author Lloffiwr
 * @author Siebrand
 * @author Tgr
 * @author Umherirrender
 */
$messages['qqq'] = array(
	'cite_article_desc' => '{{desc}}',
	'cite_article_link' => 'Text of link in toolbox',
	'tooltip-cite-article' => 'Tooltip',
	'cite' => '{{Identical|Cite}}',
	'cite-summary' => '{{notranslate}}',
	'cite_page' => '{{Identical|Page}}',
	'cite_submit' => '{{Identical|Cite}}',
);

/** Säggssch (Säggssch)
 * @author Thogo
 */
$messages['sxu'] = array(
	'cite_article_link' => 'Zidier dän ardiggl hier',
	'cite' => 'Zidierhilfe',
	'cite_submit' => 'Zidierhilfe',
);

/** Niuean (ko e vagahau Niuē)
 * @author Jose77
 */
$messages['niu'] = array(
	'cite_article_link' => 'Fakakite e tala nei',
);

/** Achinese (Acèh)
 * @author Si Gam Acèh
 */
$messages['ace'] = array(
	'cite_article_link' => 'Cok ôn nyoë',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 * @author SPQRobin
 */
$messages['af'] = array(
	'cite_article_desc' => "Maak 'n [[Special:Cite|spesiale bladsy vir sitasie]], en 'n skakel daarna in hulpmiddels beskikbaar",
	'cite_article_link' => 'Haal dié blad aan',
	'tooltip-cite-article' => 'Inligting oor hoe u hierdie bladsy kan citeer',
	'cite' => 'Aanhaling',
	'cite_page' => 'Bladsy:',
	'cite_submit' => 'Aanhaling',
);

/** Amharic (አማርኛ)
 * @author Codex Sinaiticus
 * @author Teferra
 */
$messages['am'] = array(
	'cite_article_link' => 'ይህንን ገጽ አጣቅስ',
	'cite' => 'መጥቀሻ',
	'cite_page' => 'አርዕስት፦',
	'cite_submit' => 'ዝርዝሮች ይታዩ',
);

/** Aragonese (Aragonés)
 * @author Juanpabl
 */
$messages['an'] = array(
	'cite_article_desc' => 'Adibe un vinclo y una pachina especial de [[Special:Cite|cita]]',
	'cite_article_link' => 'Citar ista pachina',
	'tooltip-cite-article' => 'Información de como citar ista pachina',
	'cite' => 'Citar',
	'cite_page' => 'Pachina:',
	'cite_submit' => 'Citar',
);

/** Arabic (العربية)
 * @author Meno25
 * @author OsamaK
 */
$messages['ar'] = array(
	'cite_article_desc' => 'يضيف صفحة [[Special:Cite|استشهاد]] خاصة ووصلة صندوق أدوات',
	'cite_article_link' => 'استشهد بهذه الصفحة',
	'tooltip-cite-article' => 'معلومات عن كيفية الاستشهاد بالصفحة',
	'cite' => 'استشهاد',
	'cite_page' => 'الصفحة:',
	'cite_submit' => 'استشهاد',
	'cite_text'               => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

== تفاصيل التأليف ل{{FULLPAGENAME}} ==

* اسم الصفحة: {{FULLPAGENAME}}
* المؤلف: مساهمو {{SITENAME}}
* الناشر: ''{{SITENAME}}, {{int:sitesubtitle}}''.
* تاريخ آخر مراجعة: {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC
* تاريخ الاسترجاع: <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC</citation>
* وصلة دائمة: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* رقم نسخة الصفحة: {{REVISIONID}}

</div>
<div class=\"plainlinks mw-specialcite-styles\">

== أنماط الاستشهاد ل{{FULLPAGENAME}} ==

=== [[APA style|نمط APA]] ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}). ''{{SITENAME}}, {{int:sitesubtitle}}''. Retrieved <citation>{{CURRENTTIME}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> from {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[The MLA style manual|نمط MLA]] ===
\"{{FULLPAGENAME}}.\" ''{{SITENAME}}, {{int:sitesubtitle}}''. {{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC. <citation>{{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;.

=== [[MHRA Style Guide|نمط MHRA]] ===
{{SITENAME}} contributors, '{{FULLPAGENAME}}', ''{{SITENAME}}, {{int:sitesubtitle}},'' {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC, &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [accessed <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}</citation>]

=== [[The Chicago Manual of Style|نمط شيكاغو]] ===
{{SITENAME}} contributors, \"{{FULLPAGENAME}},\" ''{{SITENAME}}, {{int:sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (accessed <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== [[Council of Science Editors|نمط CBE/CSE]] ===
{{SITENAME}} contributors. {{FULLPAGENAME}} [Internet]. {{SITENAME}}, {{int:sitesubtitle}}; {{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}, {{CURRENTTIME}} UTC [cited <citation>{{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}</citation>]. Available from:
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[Bluebook|نمط Bluebook]] ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (last visited <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== مدخلة [[BibTeX]] ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }

عند استخدام وصلة مجموعة [[LaTeX]] (<code>\usepackage{url}</code> في مكان ما) مما يؤدي إى إعطاء عناوين ويب مهيأة بشكل أفضل، التالي ربما يكون مفضلا:

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }


</div> <!--closing div for \"plainlinks\"-->"
);

/** Aramaic (ܐܪܡܝܐ)
 * @author Basharh
 */
$messages['arc'] = array(
	'cite_article_link' => 'ܡܣܗܕ ܥܠ ܗܕܐ ܦܐܬܐ',
	'tooltip-cite-article' => 'ܝܕ̈ܥܬܐ ܥܠ ܐܝܟܢܐ ܕܡܣܗܕ ܥܠ ܦܐܬܐ',
	'cite' => 'ܡܣܗܕ',
	'cite_page' => 'ܦܐܬܐ:',
	'cite_submit' => 'ܡܣܗܕ',
);

/** Araucanian (Mapudungun)
 * @author Kaniw
 * @author Remember the dot
 */
$messages['arn'] = array(
	'cite_article_desc' => 'Yomvmi kiñe wicu aztapvl ñi [[Special:Cite|konvmpan]] mew ka jasun kvzawpeyvm mew',
	'cite_article_link' => 'Konvmpape faci xoy',
	'tooltip-cite-article' => 'Cumley konvmpageay faci xoy',
	'cite' => 'Konvmpan',
	'cite_page' => 'Aztapvl:',
	'cite_submit' => 'Konvmpan',
);

/** Egyptian Spoken Arabic (مصرى)
 * @author Ghaly
 * @author Ramsis II
 */
$messages['arz'] = array(
	'cite_article_desc' => 'بيضيف [[Special:Cite|مرجع]] صفحة مخصوصة ولينك لصندوء أدوات',
	'cite_article_link' => 'استشهد بالصفحة دى',
	'cite' => 'مرجع',
	'cite_page' => 'الصفحه:',
	'cite_submit' => 'مرجع',
	'cite_text'               => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

== تفاصيل التأليف ل{{FULLPAGENAME}} ==

* اسم الصفحة: {{FULLPAGENAME}}
* المؤلف: مساهمو {{SITENAME}}
* الناشر: ''{{SITENAME}}, {{int:sitesubtitle}}''.
* تاريخ آخر مراجعة: {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC
* تاريخ الاسترجاع: <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC</citation>
* وصلة دائمة: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* رقم نسخة الصفحة: {{REVISIONID}}

</div>
<div class=\"plainlinks mw-specialcite-styles\">


== أنماط الاستشهاد ل{{FULLPAGENAME}} ==

=== [[APA style|نمط APA]] ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}). ''{{SITENAME}}, {{int:sitesubtitle}}''. Retrieved <citation>{{CURRENTTIME}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> from {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[The MLA style manual|نمط MLA]] ===
\"{{FULLPAGENAME}}.\" ''{{SITENAME}}, {{int:sitesubtitle}}''. {{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC. <citation>{{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;.

=== [[MHRA Style Guide|نمط MHRA]] ===
{{SITENAME}} contributors, '{{FULLPAGENAME}}', ''{{SITENAME}}, {{int:sitesubtitle}},'' {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC, &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [accessed <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}</citation>]

=== [[The Chicago Manual of Style|نمط شيكاغو]] ===
{{SITENAME}} contributors, \"{{FULLPAGENAME}},\" ''{{SITENAME}}, {{int:sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (accessed <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== [[Council of Science Editors|نمط CBE/CSE]] ===
{{SITENAME}} contributors. {{FULLPAGENAME}} [Internet]. {{SITENAME}}, {{int:sitesubtitle}}; {{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}, {{CURRENTTIME}} UTC [cited <citation>{{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}</citation>]. Available from:
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[Bluebook|نمط Bluebook]] ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (last visited <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== مدخلة [[BibTeX]] ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }

عند استخدام وصلة مجموعة [[LaTeX]] (<code>\usepackage{url}</code> في مكان ما) مما يؤدى إلى إعطاء عناوين ويب مهيأة بشكل أفضل، التالى ربما يكون مفضلا:

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }


</div> <!--closing div for \"plainlinks\"-->"
);

/** Asturian (Asturianu)
 * @author Esbardu
 * @author Xuacu
 */
$messages['ast'] = array(
	'cite_article_desc' => 'Añade una páxina especial de [[Special:Cite|cites]] y un enllaz a la caxa de ferramientes',
	'cite_article_link' => 'Citar esta páxina',
	'tooltip-cite-article' => 'Información tocante a cómo citar esta páxina',
	'cite' => 'Citar',
	'cite_page' => 'Páxina:',
	'cite_submit' => 'Citar',
);

/** Avaric (Авар)
 * @author Amikeco
 */
$messages['av'] = array(
	'cite_article_link' => 'Гьумер рехсезе',
);

/** Azerbaijani (Azərbaycanca)
 * @author Cekli829
 */
$messages['az'] = array(
	'cite' => 'Sayt',
	'cite_page' => 'Səhifə:',
	'cite_submit' => 'Sayt',
);

/** Bashkir (Башҡортса)
 * @author Assele
 * @author Haqmar
 */
$messages['ba'] = array(
	'cite_article_desc' => '[[Special:Cite|Өҙөмтә яһау]] махсус битен һәм ҡоралдарҙа һылтанма өҫтәй',
	'cite_article_link' => 'Биттән өҙөмтә яһарға',
	'tooltip-cite-article' => 'Был битте нисек өҙөмтәләргә кәрәклеге тураһында мәғлүмәт',
	'cite' => 'Өҙөмтәләү',
	'cite_page' => 'Бит:',
	'cite_submit' => 'Өҙөмтәләргә',
);

/** Bavarian (Boarisch)
 * @author Man77
 * @author Mucalexx
 */
$messages['bar'] = array(
	'cite_article_desc' => "Ergänzd d' [[Special:Cite|Zitirhüf]]-Speziaalseiten und an Link im Werkzeigkosten",
	'cite_article_link' => "d' Seiten zitirn",
	'tooltip-cite-article' => 'Hihweis, wia dé Seiten zitird wern kå',
	'cite' => 'Zitirhüf',
	'cite_page' => 'Seiten:',
	'cite_submit' => 'åzoang',
);

/** Southern Balochi (بلوچی مکرانی)
 * @author Mostafadaneshvar
 */
$messages['bcc'] = array(
	'cite_article_desc' => 'اضافه کن یک [[Special:Cite|citation]] صفحه حاص و لینک جعبه ابزار',
	'cite_article_link' => 'ای صفحه ی مرجع بل',
	'cite' => 'مرجع',
	'cite_page' => 'صفحه:',
	'cite_submit' => 'مرجع',
);

/** Bikol Central (Bikol Central)
 * @author Filipinayzd
 */
$messages['bcl'] = array(
	'cite_article_link' => 'Sambiton an artikulong ini',
	'cite' => 'Sambiton',
	'cite_page' => 'Pahina:',
	'cite_submit' => 'Sambiton',
);

/** Belarusian (Беларуская)
 * @author Хомелка
 */
$messages['be'] = array(
	'cite_article_desc' => 'Дадае [[Special:Cite|цытату]] адмысловых старонак і спасылку панэлі інструментаў',
	'cite_article_link' => 'Цытаваць гэту старонку',
	'tooltip-cite-article' => 'Інфармацыя пра тое, як цытаваць гэтую старонку',
	'cite' => 'Спаслацца',
	'cite_page' => 'Старонка:',
	'cite_submit' => 'Спаслацца',
);

/** Belarusian (Taraškievica orthography) (‪Беларуская (тарашкевіца)‬)
 * @author EugeneZelenko
 */
$messages['be-tarask'] = array(
	'cite_article_desc' => 'Дадае спэцыяльную старонку [[Special:Cite|цытаваньня]] і спасылку ў інструмэнтах',
	'cite_article_link' => 'Цытаваць гэтую старонку',
	'tooltip-cite-article' => 'Інфармацыя пра тое, як цытатаваць гэтую старонку',
	'cite' => 'Цытаваньне',
	'cite_page' => 'Старонка:',
	'cite_submit' => 'Цытаваць',
);

/** Bulgarian (Български)
 * @author DCLXVI
 * @author Turin
 */
$messages['bg'] = array(
	'cite_article_desc' => 'Добавя специална страница и препратка за [[Special:Cite|цитиране]]',
	'cite_article_link' => 'Цитиране на страницата',
	'tooltip-cite-article' => 'Данни за начин на цитиране на тази страница',
	'cite' => 'Цитиране',
	'cite_page' => 'Страница:',
	'cite_submit' => 'Цитиране',
);

/** Bengali (বাংলা)
 * @author Bellayet
 * @author Zaheen
 */
$messages['bn'] = array(
	'cite_article_desc' => 'একটি বিশেষ [[Special:Cite|উদ্ধৃতি]] পাতা ও টুলবক্স সংযোগ যোগ করে',
	'cite_article_link' => 'এ পাতাটি উদ্ধৃত করো',
	'cite' => 'উদ্ধৃত',
	'cite_page' => 'পাতা:',
	'cite_submit' => 'উদ্ধৃত করো',
);

/** Tibetan (བོད་ཡིག)
 * @author Freeyak
 */
$messages['bo'] = array(
	'cite' => '',
	'cite_page' => 'ཤོག་ངོས།',
);

/** Bishnupria Manipuri (ইমার ঠার/বিষ্ণুপ্রিয়া মণিপুরী) */
$messages['bpy'] = array(
	'cite_article_link' => 'নিবন্ধ এহানরে উদ্ধৃত করেদে',
	'cite' => 'উদ্ধৃত করেদে',
);

/** Breton (Brezhoneg)
 * @author Fulup
 */
$messages['br'] = array(
	'cite_article_desc' => 'Ouzhpennañ a ra ur bajenn dibar [[Special:Cite|arroud]] hag ul liamm er voest ostilhoù',
	'cite_article_link' => 'Menegiñ ar pennad-mañ',
	'tooltip-cite-article' => 'Titouroù war an doare da venegiñ ar bajenn-mañ',
	'cite' => 'Menegiñ',
	'cite_page' => 'Pajenn :',
	'cite_submit' => 'Menegiñ',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'cite_article_desc' => 'Dodaje posebnu stranicu za [[Special:Cite|citiranje]] i link u alatnoj kutiji',
	'cite_article_link' => 'Citiraj ovu stranicu',
	'tooltip-cite-article' => 'Informacije kako citirati ovu stranicu',
	'cite' => 'Citiranje',
	'cite_page' => 'Stranica:',
	'cite_submit' => 'Citiraj',
);

/** Catalan (Català)
 * @author Davidpar
 * @author SMP
 * @author Toniher
 */
$messages['ca'] = array(
	'cite_article_desc' => 'Afegeix un enllaç i una pàgina especial de [[Special:Cite|citació]]',
	'cite_article_link' => 'Cita aquesta pàgina',
	'tooltip-cite-article' => 'Informació sobre com citar aquesta pàgina.',
	'cite' => 'Citeu',
	'cite_page' => 'Pàgina:',
	'cite_submit' => 'Cita',
);

/** Min Dong Chinese (Mìng-dĕ̤ng-ngṳ̄) */
$messages['cdo'] = array(
	'cite_article_link' => 'Īng-ê̤ṳng cī piĕng ùng-ciŏng',
	'cite' => 'Īng-ê̤ṳng',
	'cite_page' => 'Hiĕk-miêng:',
	'cite_submit' => 'Īng-ê̤ṳng',
);

/** Chechen (Нохчийн)
 * @author Sasan700
 */
$messages['ce'] = array(
	'cite' => 'Далийнадош',
);

/** Cebuano (Cebuano)
 * @author Abastillas
 */
$messages['ceb'] = array(
	'cite' => 'Kutloa',
);

/** Sorani (کوردی)
 * @author Asoxor
 */
$messages['ckb'] = array(
	'cite_article_link' => 'ئەم پەڕە بکە بە ژێدەر',
	'tooltip-cite-article' => 'زانیاری سەبارەت بە چۆنیەتیی بە ژێدەر کردنی ئەم پەڕە',
	'cite' => 'بیکە بە ژێدەر',
	'cite_page' => 'پەڕە:',
	'cite_submit' => 'بیکە بە ژێدەر',
);

/** Corsican (Corsu) */
$messages['co'] = array(
	'cite_article_link' => 'Cità issu articulu',
	'cite' => 'Cità',
	'cite_page' => 'Pagina:',
);

/** Czech (Česky)
 * @author Beren
 * @author Li-sung
 * @author Martin Kozák
 * @author Mormegil
 */
$messages['cs'] = array(
	'cite_article_desc' => 'Přidává speciální stránku [[Special:Cite|Citace]] a odkaz v nabídce nástrojů',
	'cite_article_link' => 'Citovat stránku',
	'tooltip-cite-article' => 'Informace o tom, jak citovat tuto stránku',
	'cite' => 'Citace',
	'cite_page' => 'Článek:',
	'cite_submit' => 'Citovat',
);

/** Church Slavic (Словѣ́ньскъ / ⰔⰎⰑⰂⰡⰐⰠⰔⰍⰟ)
 * @author ОйЛ
 */
$messages['cu'] = array(
	'cite_article_link' => 'привєдєниѥ члѣна словєсъ',
	'cite_page' => 'страница :',
);

/** Welsh (Cymraeg)
 * @author Lloffiwr
 */
$messages['cy'] = array(
	'cite_article_desc' => 'Yn ychwanegu tudalen arbennig ar gyfer [[Special:Cite|cyfeirio at erthygl]] a chyswllt bocs offer',
	'cite_article_link' => 'Cyfeiriwch at yr erthygl hon',
	'tooltip-cite-article' => 'Gwybodaeth ar sut i gyfeirio at y dudalen hon',
	'cite' => 'Cyfeirio at erthygl',
	'cite_page' => 'Tudalen:',
	'cite_submit' => 'Cyfeirio',
);

/** Danish (Dansk)
 * @author Byrial
 * @author Morten LJ
 * @author Peter Alberti
 */
$messages['da'] = array(
	'cite_article_desc' => 'Tilføjer en [[Special:Cite|specialside til citering]] og en henvisning i værktøjsmenuen',
	'cite_article_link' => 'Citér denne artikel',
	'tooltip-cite-article' => 'Information om, hvordan man kan citere denne side',
	'cite' => 'Citér',
	'cite_page' => 'Side:',
	'cite_submit' => 'Citér',
);

/** German (Deutsch)
 * @author Kghbln
 */
$messages['de'] = array(
	'cite_article_desc' => 'Ergänzt eine [[Special:Cite|Spezialseite]] als Zitierhilfe sowie einen zugehörigen Link im Bereich Werkzeuge',
	'cite_article_link' => 'Seite zitieren',
	'tooltip-cite-article' => 'Hinweis, wie diese Seite zitiert werden kann',
	'cite' => 'Zitierhilfe',
	'cite_page' => 'Seite:',
	'cite_submit' => 'zitieren',
	'cite_text'               => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">
Diese Seite dient ausschließlich als Hilfe zum '''korrekten Zitieren von Daten, einzelnen Sätzen oder kürzeren Abschnitten''' aus {{SITENAME}} im Rahmen des Zitatrechts.

'''Wichtig:''' Zur Übernahme umfangreicher Absätze oder kompletter Seiten müssen die [[{{MediaWiki:Copyrightpage}}|Lizenzbestimmungen]] eingehalten werden.
</div>

<div class=\"mw-specialcite-bibliographic\">
== Bibliografische Angaben für „[[{{FULLPAGENAME}}]]“ ==
* Seitentitel: {{FULLPAGENAME}}
* Herausgeber: {{SITENAME}}, {{MediaWiki:Sitesubtitle}}. 
* Autor(en): siehe [{{canonicalurl:{{FULLPAGENAME}}|action=history}} Versionsgeschichte]
* Datum der letzten Bearbeitung: {{LOCALDAY}}. {{LOCALMONTHNAME}} {{LOCALYEAR}}, {{LOCALTIME}}
* Versions-ID der Seite: {{REVISIONID}}
* Permanentlink: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* Datum des Abrufs: <citation>{{LOCALDAY}}. {{LOCALMONTHNAME}} {{LOCALYEAR}}, {{LOCALTIME}}</citation>
</div>

<div class=\"plainlinks mw-specialcite-styles\">
==Zitatangabe zum Kopieren==
Seite ''{{FULLPAGENAME}}.'' In: {{SITENAME}}, {{MediaWiki:Sitesubtitle}}. Bearbeitungsstand: {{LOCALDAY}}. {{LOCALMONTHNAME}} {{LOCALYEAR}}, {{LOCALTIME}}. URL: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (Abgerufen: <citation>{{LOCALDAY}}. {{LOCALMONTHNAME}} {{LOCALYEAR}}, {{LOCALTIME}}</citation>)
</div>

<div class=\"plainlinks mw-specialcite-styles\">
== BibTeX-Eintrag ==

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{MediaWiki:Sitesubtitle}}\",
    year = \"{{LOCALYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[Online; Stand <citation>{{LOCALDAY}}. {{LOCALMONTHNAME}} {{LOCALYEAR}}</citation>]\"
  }

Bei Benutzung der LaTeX-Paketes „url“ (<tt>\usepackage{url}</tt> im Bereich der Einleitung), welches eine schöner formatierte Internetadresse ausgibt, oder „hyperref“ (<tt>\usepackage{hyperref}</tt>, nur bei Erzeugung von PDF-Dokumenten), welches diese zusätzlich noch verlinkt, kann die folgende Ausgabe genommen werden:

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{MediaWiki:Sitesubtitle}}\",
    year = \"{{LOCALYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[Online; Stand <citation>{{LOCALDAY}}. {{LOCALMONTHNAME}} {{LOCALYEAR}}</citation>]\"
  }
</div><!--closing div for \"plainlinks\"-->"
);

/** Zazaki (Zazaki)
 * @author Mirzali
 * @author Xoser
 */
$messages['diq'] = array(
	'cite_article_desc' => 'Yew pelê [[Special:Cite|citation]] u lînkê toolboxî de keno',
	'cite_article_link' => 'Na pele çime bimocne',
	'tooltip-cite-article' => 'Melumato ke ena pele çıtewr iqtıbas keno',
	'cite' => 'Çıme bımocne',
	'cite_page' => 'Pel:',
	'cite_submit' => 'Çime',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'cite_article_desc' => 'Pśidawa specialny bok [[Special:Cite|Citěrowańska pomoc]] a link w kašćiku źěłowe rědy',
	'cite_article_link' => 'Toś ten bok citěrowaś',
	'tooltip-cite-article' => 'Informacije wó tom, kak toś ten bok dajo se citěrowaś',
	'cite' => 'Citěrowańska pomoc',
	'cite_page' => 'Bok:',
	'cite_submit' => 'pokazaś',
);

/** Ewe (Eʋegbe) */
$messages['ee'] = array(
	'cite_page' => 'Nuŋɔŋlɔ:',
);

/** Greek (Ελληνικά)
 * @author Consta
 * @author Omnipaedista
 */
$messages['el'] = array(
	'cite_article_desc' => 'Προσθέτει μία ειδική σελίδα [[Special:Cite|παραθέσεων]] καί έναν σύνδεσμο προς την εργαλειοθήκη',
	'cite_article_link' => 'Αναφέρεται αυτή τη σελίδα',
	'tooltip-cite-article' => 'Πληροφορίες για το πως να παραπέμψετε σε αυτήν την σελίδα',
	'cite' => 'Αναφορά',
	'cite_page' => 'Σελίδα:',
	'cite_submit' => 'Προσθήκη παραθέσεων',
);

/** Esperanto (Esperanto)
 * @author Michawiki
 * @author Tlustulimu
 * @author Yekrats
 */
$messages['eo'] = array(
	'cite_article_desc' => 'Aldonas specialan paĝon por [[Special:Cite|citado]] kaj ligilo al ilaro',
	'cite_article_link' => 'Citi ĉi tiun paĝon',
	'tooltip-cite-article' => 'Informoj pri tio, kiel oni citu ĉi tiun paĝon',
	'cite' => 'Citado',
	'cite_page' => 'Paĝo:',
	'cite_submit' => 'Citi',
);

/** Spanish (Español)
 * @author Crazymadlover
 * @author Icvav
 * @author Jatrobat
 * @author Muro de Aguas
 * @author Sanbec
 */
$messages['es'] = array(
	'cite_article_desc' => 'Añade una página especial para [[Special:Cite|citar la página]] y un enlace en la caja de herramientas.',
	'cite_article_link' => 'Citar este artículo',
	'tooltip-cite-article' => 'Información de como citar esta página',
	'cite' => 'Citar',
	'cite_page' => 'Página:',
	'cite_submit' => 'Citar',
);

/** Estonian (Eesti)
 * @author Pikne
 * @author WikedKentaur
 */
$messages['et'] = array(
	'cite_article_desc' => 'Lisab [[Special:Cite|tsiteerimise]] erilehekülje ja lingi külgmenüü tööriistakasti.',
	'cite_article_link' => 'Tsiteeri seda artiklit',
	'tooltip-cite-article' => 'Teave tsiteerimisviiside kohta',
	'cite' => 'Tsiteerimine',
	'cite_page' => 'Leht:',
	'cite_submit' => 'Tsiteeri',
);

/** Basque (Euskara)
 * @author An13sa
 * @author Theklan
 * @author Xabier Armendaritz
 */
$messages['eu'] = array(
	'cite_article_desc' => '[[Special:Cite|Aipatu]] orrialde berezia gehitzen du tresna-kutxaren loturetan',
	'cite_article_link' => 'Orrialde hau aipatu',
	'tooltip-cite-article' => 'Orri honen aipua egiteko moduari buruzko informazioa',
	'cite' => 'Aipamenak',
	'cite_page' => 'Orrialdea:',
	'cite_submit' => 'Aipatu',
);

/** Extremaduran (Estremeñu)
 * @author Better
 */
$messages['ext'] = array(
	'cite_article_link' => 'Almiental esti artículu',
	'cite' => 'Almiental',
	'cite_page' => 'Páhina:',
	'cite_submit' => 'Almiental',
);

/** Persian (فارسی)
 * @author Huji
 * @author Wayiran
 */
$messages['fa'] = array(
	'cite_article_desc' => 'صفحهٔ ویژه‌ای برای [[Special:Cite|یادکرد]] اضافه می‌کند و پیوندی به جعبه ابزار می‌افزاید',
	'cite_article_link' => 'یادکرد پیوند این مقاله',
	'tooltip-cite-article' => 'اطلاعات در خصوص چگونگی یادکرد این صفحه',
	'cite' => 'یادکرد این مقاله',
	'cite_page' => 'صفحه:',
	'cite_submit' => 'یادکرد',
);

/** Finnish (Suomi)
 * @author Nike
 * @author ZeiP
 */
$messages['fi'] = array(
	'cite_article_desc' => 'Lisää työkaluihin toimintosivun, joka neuvoo [[Special:Cite|viittaamaan]] oikeaoppisesti.',
	'cite_article_link' => 'Viitetiedot',
	'tooltip-cite-article' => 'Tietoa tämän sivun lainaamisesta',
	'cite' => 'Viitetiedot',
	'cite_page' => 'Sivu:',
	'cite_submit' => 'Viittaa',
	'cite_text'               => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

== Bibliografiset tiedot artikkelille {{FULLPAGENAME}} ==

* Sivun nimi: {{FULLPAGENAME}}
* Tekijä: {{SITENAME}}-projektin osanottajat
* Julkaisija: ''{{SITENAME}}, {{int:sitesubtitle}}''.
* Viimeisimmän version päivämäärä: {{CURRENTDAY}}. {{CURRENTMONTHNAME}}ta {{CURRENTYEAR}}, kello {{CURRENTTIME}} (UTC)
* Sivu haettu: <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}}ta {{CURRENTYEAR}}, kello {{CURRENTTIME}} (UTC)</citation>
* Pysyvä osoite: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* Sivun version tunniste: {{REVISIONID}}

</div>
<div class=\"plainlinks mw-specialcite-styles\">

== Viittaustyylit artikkelille {{FULLPAGENAME}} ==

=== APA-tyyli ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}}, {{CURRENTMONTHNAME}}n {{CURRENTDAY}}). ''{{SITENAME}}, {{int:sitesubtitle}}''. Haettu <citation>{{CURRENTTIME}}, {{CURRENTMONTHNAME}}n {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> osoitteesta {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== MLA-tyyli ===
\"{{FULLPAGENAME}}.\" ''{{SITENAME}}, {{int:sitesubtitle}}''. {{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC. <citation>{{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;.

=== MHRA-tyyli ===
{{SITENAME}} contributors, '{{FULLPAGENAME}}', ''{{SITENAME}}, {{int:sitesubtitle}},'' {{CURRENTDAY}} {{CURRENTMONTHNAME}}ta {{CURRENTYEAR}}, {{CURRENTTIME}} UTC, &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [haettu <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}}ta {{CURRENTYEAR}}</citation>]

=== Chicago-tyyli ===
{{SITENAME}}-projektin osanottajat, \"{{FULLPAGENAME}},\" ''{{SITENAME}}, {{int:sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (haettu <citation>{{CURRENTMONTHNAME}}n {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== CBE/CSE-tyyli ===
{{SITENAME}}-projektin osanottajat. {{FULLPAGENAME}} [Internet]. {{SITENAME}}, {{int:sitesubtitle}}; {{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}, {{CURRENTTIME}} UTC [cited <citation>{{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}</citation>]. Saatavilla osoitteesta: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== Bluebook-tyyli ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (vierailtu viimeksi <citation>{{CURRENTMONTHNAME}}n {{CURRENTDAY}}., {{CURRENTYEAR}}</citation>).

=== BibTeX-muoto ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[Online; haettu <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }

Käytettäessä [[LaTeX]]-pakettia url, (<code>\usepackage{url}</code> jossain alussa) joka tapaa antaa paremmin muotoiltuja osoitteita, seuraavaa muotoa voidaan käyttää:

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[Online; haettu <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }


</div> <!--closing div for \"plainlinks\"-->"
);

/** Fijian (Na Vosa Vakaviti) */
$messages['fj'] = array(
	'cite_article_link' => 'Vola dau vaqarai',
);

/** Faroese (Føroyskt)
 * @author Diupwijk
 * @author Spacebirdy
 */
$messages['fo'] = array(
	'cite_article_link' => 'Sitera hesa síðuna',
	'cite' => 'Sitera',
	'cite_page' => 'Síða:',
	'cite_submit' => 'Sitera',
);

/** French (Français)
 * @author Grondin
 * @author Hégésippe Cormier
 * @author PieRRoMaN
 * @author Urhixidur
 */
$messages['fr'] = array(
	'cite_article_desc' => 'Ajoute une page spéciale [[Special:Cite|citation]] et un lien dans la boîte à outils',
	'cite_article_link' => 'Citer cette page',
	'tooltip-cite-article' => 'Informations sur comment citer cette page',
	'cite' => 'Citation',
	'cite_page' => 'Page :',
	'cite_submit' => 'Citer',
	'cite_text'               => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

== Détails bibliographiques pour {{FULLPAGENAME}} ==

* Nom de la page : {{FULLPAGENAME}} 
* Auteur : {{SITENAME}} contributors
* Éditeur : ''{{SITENAME}}, {{int:sitesubtitle}}''. 
* Dernière modification : {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC
* Récupéré : <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC</citation>
* URL permanente : {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* Identifiant de cette version : {{REVISIONID}}

</div>
<div class=\"plainlinks mw-specialcite-styles\">

== Styles de citations pour {{FULLPAGENAME}} ==

=== [[Style APA]] ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}). ''{{SITENAME}}, {{int:sitesubtitle}}''. Retrieved <citation>{{CURRENTTIME}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> from {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[Style MLA]] ===
\"{{FULLPAGENAME}}.\" ''{{SITENAME}}, {{int:sitesubtitle}}''. {{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC. <citation>{{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;.

=== [[Style MHRA]] ===
{{SITENAME}} contributors, '{{FULLPAGENAME}}',  ''{{SITENAME}}, {{int:sitesubtitle}},'' {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC, &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [accessed <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}</citation>]

=== [[Style Chicago]] ===
{{SITENAME}} contributors, \"{{FULLPAGENAME}},\"  ''{{SITENAME}}, {{int:sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (accessed <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== [[Style CBE/CSE]] ===
{{SITENAME}} contributors. {{FULLPAGENAME}} [Internet].  {{SITENAME}}, {{int:sitesubtitle}};  {{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}},   {{CURRENTTIME}} UTC [cited <citation>{{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}</citation>].  Available from: 
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[Style Bluebook]] ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (last visited   <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== Entrée [[BibTeX]] ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }

Si vous utilisez le package URL dans [[LaTeX]] (<code>\usepackage{url}</code> quelquepart dans le préambule), qui donne des addresses webs mieux formatées, utilisez le format suivant :

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }


</div> <!--closing div for \"plainlinks\"-->"
);

/** Franco-Provençal (Arpetan)
 * @author ChrisPtDe
 */
$messages['frp'] = array(
	'cite_article_desc' => 'Apond una pâge spèciâla [[Special:Cite|citacion]] et un lim dens la bouèta d’outils.',
	'cite_article_link' => 'Citar ceta pâge',
	'tooltip-cite-article' => 'Enformacions sur coment citar ceta pâge',
	'cite' => 'Citacion',
	'cite_page' => 'Pâge :',
	'cite_submit' => 'Citar',
);

/** Friulian (Furlan)
 * @author Klenje
 * @author MF-Warburg
 */
$messages['fur'] = array(
	'cite_article_link' => 'Cite cheste vôs',
	'cite' => 'Citazion',
	'cite_page' => 'Pagjine:',
	'cite_submit' => 'Cree la citazion',
);

/** Western Frisian (Frysk)
 * @author SK-luuut
 * @author Snakesteuben
 */
$messages['fy'] = array(
	'cite_article_desc' => 'Foeget in [[Special:Cite|spesjale side]] om te sitearjen, lykas in ferwizing nei de helpmiddels, ta.',
	'cite_article_link' => 'Sitearje dizze side',
	'cite' => 'Sitearje',
	'cite_page' => 'Side:',
	'cite_submit' => 'Sitearje',
);

/** Irish (Gaeilge)
 * @author Alison
 */
$messages['ga'] = array(
	'cite_article_desc' => 'Cuir [[Special:Cite|deismireacht]] leathanach speisíalta agus nasc bosca uirlisí',
	'cite_article_link' => 'Luaigh an lch seo',
	'cite' => 'Luaigh',
	'cite_page' => 'Leathanach:',
	'cite_submit' => 'Luaigh',
);

/** Galician (Galego)
 * @author Toliño
 * @author Xosé
 */
$messages['gl'] = array(
	'cite_article_desc' => 'Engade unha páxina especial de [[Special:Cite|citas]] e unha ligazón na caixa de ferramentas',
	'cite_article_link' => 'Citar esta páxina',
	'tooltip-cite-article' => 'Información sobre como citar esta páxina',
	'cite' => 'Citar un artigo',
	'cite_page' => 'Páxina:',
	'cite_submit' => 'Citar',
);

/** Ancient Greek (Ἀρχαία ἑλληνικὴ)
 * @author AndreasJS
 * @author LeighvsOptimvsMaximvs
 * @author Omnipaedista
 */
$messages['grc'] = array(
	'cite_article_desc' => 'Προσθέτει εἰδικὴν δἐλτον [[Special:Cite|ἀναφορῶν]] τινὰ καὶ σύνδεσμον τινὰ ἐν τῷ ἐργαλειοκάδῳ',
	'cite_article_link' => 'Άναφέρειν τήνδε τὴν δέλτον',
	'cite' => 'Μνημονεύειν',
	'cite_page' => 'Δέλτος:',
	'cite_submit' => 'Μνημονεύειν',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 * @author Strommops
 */
$messages['gsw'] = array(
	'cite_article_desc' => 'Ergänzt d [[Special:Cite|Zitierhilf]]-Spezialsyte un e Gleich im Chaschte Wärchzyyg',
	'cite_article_link' => 'Die Site zitiere',
	'tooltip-cite-article' => 'Informatione driber, wie mer die Syte cha zitiere',
	'cite' => 'Zitierhilf',
	'cite_page' => 'Syte:',
	'cite_submit' => 'aazeige',
);

/** Gujarati (ગુજરાતી)
 * @author Dsvyas
 * @author KartikMistry
 * @author Sushant savla
 */
$messages['gu'] = array(
	'cite_article_desc' => '[[Special:Cite|સંદર્ભ]] ખાસ પાનું અને સાધન પેટીની કડી ઉમેરે છે',
	'cite_article_link' => 'આ પાનું ટાંકો',
	'tooltip-cite-article' => 'આ પાનાંને સમર્થન કઈ રીતે આપવું તેની માહિતી',
	'cite' => 'ટાંકો',
	'cite_page' => 'પાનું:',
	'cite_submit' => 'ટાંકો',
);

/** Manx (Gaelg)
 * @author MacTire02
 */
$messages['gv'] = array(
	'cite_article_desc' => 'Cur duillag [[Special:Cite|symney]] er lheh as kiangley kishtey greie',
	'cite_article_link' => 'Symney yn duillag shoh',
	'cite' => 'Symney',
	'cite_page' => 'Duillag:',
	'cite_submit' => 'Symney',
);

/** Hausa (هَوُسَ) */
$messages['ha'] = array(
	'cite_page' => 'Shafi:',
);

/** Hawaiian (Hawai`i)
 * @author Singularity
 */
$messages['haw'] = array(
	'cite_article_link' => "E ho'ōia i kēia mea",
	'cite_page' => '‘Ao‘ao:',
);

/** Hebrew (עברית)
 * @author Rotem Liss
 */
$messages['he'] = array(
	'cite_article_desc' => 'הוספת דף מיוחד וקישור בתיבת הכלים ל[[Special:Cite|ציטוט]]',
	'cite_article_link' => 'ציטוט דף זה',
	'tooltip-cite-article' => 'מידע כיצד לצטט דף זה',
	'cite' => 'ציטוט',
	'cite_page' => 'דף:',
	'cite_submit' => 'ציטוט',
	'cite_text'               => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

== מידע ביבליוגרפי על {{FULLPAGENAME}} ==

* שם הדף: {{FULLPAGENAME}}
* מחבר: תורמי {{SITENAME}}
* מוציא לאור: ''{{SITENAME}}, {{int:sitesubtitle}}''.
* תאריך השינוי האחרון: {{CURRENTDAY}} {{CURRENTMONTHNAMEGEN}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC
* תאריך האחזור: <citation>{{CURRENTDAY}} {{CURRENTMONTHNAMEGEN}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC</citation>
* קישור קבוע: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* קוד זיהוי גרסה: {{REVISIONID}}

</div>
<div class=\"plainlinks mw-specialcite-styles\">

== סגנונות ציטוט עבור {{FULLPAGENAME}} ==

=== [[APA style]] ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}). ''{{SITENAME}}, {{int:sitesubtitle}}''. אוחזר <citation>{{CURRENTTIME}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> מתוך {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[The MLA style manual|MLA style]] ===
\"{{FULLPAGENAME}}.\" ''{{SITENAME}}, {{int:sitesubtitle}}''. {{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC. <citation>{{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;.

=== [[MHRA Style Guide|MHRA style]] ===
תורמי {{SITENAME}}, '{{FULLPAGENAME}}', ''{{SITENAME}}, {{int:sitesubtitle}},'' {{CURRENTDAY}} {{CURRENTMONTHNAMEGEN}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC, &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [אוחזר <citation>{{CURRENTDAY}} {{CURRENTMONTHNAMEGEN}} {{CURRENTYEAR}}</citation>]

=== [[The Chicago Manual of Style|Chicago style]] ===
תורמי {{SITENAME}}, \"{{FULLPAGENAME}},\" ''{{SITENAME}}, {{int:sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (אוחזר <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== [[Council of Science Editors|CBE/CSE style]] ===
תורמי {{SITENAME}}. {{FULLPAGENAME}} [אינטרנט]. {{SITENAME}}, {{int:sitesubtitle}}; {{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}, {{CURRENTTIME}} UTC [צוטט <citation>{{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}</citation>]. זמין ב:
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[Bluebook|Bluebook style]] ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (ביקור אחרון <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== ערך [[BibTeX]] ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[מקוון; אוחזר <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }

כאשר משתמשים ב־URL מחבילת [[LaTeX]] (באמצעות כתיבת \usepackage{url} במקום כלשהו במבוא), המניבה כתובות אינטרנט המעוצבות טוב יותר, יש להעדיף את דרך הכתיבה הבאה:

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[מקוון; אוחזר <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }


</div> <!--closing div for \"plainlinks\"-->"
);

/** Hindi (हिन्दी)
 * @author Ansumang
 * @author Kaustubh
 */
$messages['hi'] = array(
	'cite_article_desc' => 'एक विशेष [[Special:Cite|बाह्यकड़ियां]] देनेवाला पन्ना और टूलबॉक्सका लिंक बनाईयें',
	'cite_article_link' => 'इस पन्ने को उद्घृत करें',
	'tooltip-cite-article' => 'तथ्य कैसे इस पृष्ठ में संदर्भ जोड़ें',
	'cite' => 'उद्घॄत करें',
	'cite_page' => 'पन्ना:',
	'cite_submit' => 'उद्घृत करें',
);

/** Hiligaynon (Ilonggo)
 * @author Jose77
 */
$messages['hil'] = array(
	'cite_article_link' => 'Tumuron ining artikulo',
);

/** Croatian (Hrvatski)
 * @author Dalibor Bosits
 * @author Excaliboor
 * @author SpeedyGonsales
 */
$messages['hr'] = array(
	'cite_article_desc' => 'Dodaje posebnu stranicu za [[Special:Cite|citiranje]] i link u okvir za alate',
	'cite_article_link' => 'Citiraj ovaj članak',
	'tooltip-cite-article' => 'Informacije o tome kako citirati ovu stranicu',
	'cite' => 'Citiranje',
	'cite_page' => 'Stranica:',
	'cite_submit' => 'Citiraj',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'cite_article_desc' => 'Přidawa specialnu stronu [[Special:Cite|Citowanska pomoc]] a wotkaz w gratowym kašćiku',
	'cite_article_link' => 'Nastawk citować',
	'tooltip-cite-article' => 'Informacije wo tym, kak tuta strona hodźi so citować',
	'cite' => 'Citowanska pomoc',
	'cite_page' => 'Strona:',
	'cite_submit' => 'pokazać',
);

/** Haitian (Kreyòl ayisyen)
 * @author Masterches
 */
$messages['ht'] = array(
	'cite_article_desc' => 'Ajoute yon paj espesyal [[Special:Cite|sitasyon]] epitou yon lyen nan bwat zouti yo',
	'cite_article_link' => 'Site paj sa',
	'cite' => 'Sitasyon',
	'cite_page' => 'Paj:',
	'cite_submit' => 'Site',
);

/** Hungarian (Magyar)
 * @author Dani
 * @author Glanthor Reviol
 * @author Tgr
 */
$messages['hu'] = array(
	'cite_article_desc' => '[[Special:Cite|Hivatkozás-készítő]] speciális lap és link az eszközdobozba',
	'cite_article_link' => 'Hogyan hivatkozz erre a lapra',
	'tooltip-cite-article' => 'Információk a lap idézésével kapcsolatban',
	'cite' => 'Hivatkozás',
	'cite_page' => 'Lap neve:',
	'cite_submit' => 'Mehet',
	'cite_text' => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

'''FONTOS MEGJEGYZÉS:''' A legtöbb tanár és szakember nem tartja helyesnek a [[harmadlagos forrás]]ok – mint a lexikonok – kizárólagos forrásként való felhasználását. A Wiki cikkeket háttérinformációnak, vagy a további kutatómunka kiindulásaként érdemes használni.

Mint minden [[{{ns:project}}:Ki írja a Wikipédiát|közösség által készített]] hivatkozásnál, a wiki tartalmában is lehetségesek hibák vagy pontatlanságok: kérjük, több független forrásból ellenőrizd a tényeket és ismerd meg a [[{{ns:project}}:Jogi nyilatkozat|jogi nyilatkozatunkat]], mielőtt a wiki adatait felhasználod.

<div style=\"border: 1px solid grey; background: #E6E8FA; width: 90%; padding: 15px 30px 15px 30px; margin: 10px auto;\">

== {{FULLPAGENAME}} lap adatai ==

* Lap neve: {{FULLPAGENAME}} 
* Szerző: Wiki szerkesztők
* Kiadó: ''{{SITENAME}}, {{MediaWiki:Sitesubtitle}}''. 
* A legutóbbi változat dátuma: {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC
* Letöltés dátuma: <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC</citation>
* Állandó hivatkozás: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* Lapváltozat-azonosító: {{REVISIONID}}

Légy szíves, ellenőrizd, hogy ezek az adatok megfelelnek-e a kívánalmaidnak.  További információhoz lásd az '''[[{{ns:project}}:Idézés a Wikipédiából|Idézés a Wikipédiából]]''' lapot.

</div>
<div class=\"plainlinks mw-specialcite-styles\">

== Idézési stílusok a(z) {{FULLPAGENAME}} laphoz ==

=== APA stílus ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}}. {{CURRENTMONTHNAME}} {{CURRENTDAY}}). ''{{SITENAME}}, {{MediaWiki:Sitesubtitle}}''. Retrieved <citation>{{CURRENTYEAR}}. {{CURRENTMONTHNAME}} {{CURRENTDAY}}. {{CURRENTTIME}}</citation> from {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== MLA stílus ===
\"{{FULLPAGENAME}}.\" ''{{SITENAME}}, {{MediaWiki:Sitesubtitle}}''. {{CURRENTYEAR}}. {{CURRENTMONTHABBREV}}. {{CURRENTDAY}}. {{CURRENTTIME}} UTC. <citation>{{CURRENTYEAR}}. {{CURRENTMONTHABBREV}}. {{CURRENTDAY}}. {{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;.

=== MHRA stílus ===
Wiki szerkesztők, '{{FULLPAGENAME}}',  ''{{SITENAME}}, {{MediaWiki:Sitesubtitle}},'' {{CURRENTYEAR}}. {{CURRENTMONTHNAME}} {{CURRENTDAY}}. {{CURRENTTIME}} UTC, &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [accessed <citation>{{CURRENTYEAR}}. {{CURRENTMONTHNAME}} {{CURRENTDAY}}.</citation>]

=== Chicago stílus ===
Wiki szerkesztők, \"{{FULLPAGENAME}},\"  ''{{SITENAME}}, {{MediaWiki:Sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (accessed <citation>{{CURRENTYEAR}}. {{CURRENTMONTHNAME}} {{CURRENTDAY}}.</citation>).

=== CBE/CSE stílus ===
wiki szerkesztők. {{FULLPAGENAME}} [Internet].  {{SITENAME}}, {{MediaWiki:Sitesubtitle}};  {{CURRENTYEAR}}. {{CURRENTMONTHABBREV}}. {{CURRENTDAY}}.  {{CURRENTTIME}} UTC [cited <citation>{{CURRENTYEAR}}. {{CURRENTMONTHABBREV}}. {{CURRENTDAY}}.</citation>].  Elérhető: 
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== Bluebook stílus ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (last visited <citation>{{CURRENTYEAR}}. {{CURRENTMONTHNAME}} {{CURRENTDAY}}.</citation>).

=== [[BibTeX]] bejegyzés ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{MediaWiki:Sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }

Az <code>url</code> nevű [[LaTeX]] csomag használata esetén (<code>\\usepackage{url}</code> a preambulumban), amely a webes hivatkozások formázásában nyújt segítséget, a következő forma ajánlott:

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{MediaWiki:Sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }

</div> <!--closing \"Citation styles\" div-->"
);

/** Armenian (Հայերեն)
 * @author Teak
 */
$messages['hy'] = array(
	'cite_article_link' => 'Քաղվածել հոդվածը',
	'cite' => 'Քաղվածում',
	'cite_page' => 'Էջ.',
	'cite_submit' => 'Քաղվածել',
);

/** Interlingua (Interlingua)
 * @author Malafaya
 * @author McDutchie
 */
$messages['ia'] = array(
	'cite_article_desc' => 'Adde un pagina special de [[Special:Cite|citation]] e un ligamine verso le instrumentario',
	'cite_article_link' => 'Citar iste pagina',
	'tooltip-cite-article' => 'Informationes super como citar iste pagina',
	'cite' => 'Citation',
	'cite_page' => 'Pagina:',
	'cite_submit' => 'Citar',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 * @author IvanLanin
 */
$messages['id'] = array(
	'cite_article_desc' => 'Menambahkan halaman istimewa [[Special:Cite|kutipan]] dan pranala pada kotak peralatan',
	'cite_article_link' => 'Kutip halaman ini',
	'tooltip-cite-article' => 'Informasi tentang bagaimana mengutip halaman ini',
	'cite' => 'Kutip',
	'cite_page' => 'Halaman:',
	'cite_submit' => 'Kutip',
	'cite_text' => "__NOTOC__
<div style=\"border: 1px solid grey; background: #E6E8FA; width: 90%; padding: 15px 30px 15px 30px; margin: 10px auto;\">

== Rincian bibliografis untuk {{FULLPAGENAME}} ==

* Nama halaman: {{FULLPAGENAME}} 
* Pengarang: Para kontributor {{SITENAME}}
* Penerbit: ''{{SITENAME}}, {{int:sitesubtitle}}''. 
* Tanggal revisi terakhir: {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC
* Tanggal akses: <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC</citation>
* Pranala permanen: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* ID versi halaman: {{REVISIONID}}

</div>
<div class=\"plainlinks\" style=\"border: 1px solid grey; width: 90%; padding: 15px 30px 15px 30px; margin: 10px auto;\">

== Format pengutipan untuk {{FULLPAGENAME}} ==

=== [[Format APA]] ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}). ''{{SITENAME}}, {{int:sitesubtitle}}''. Diakses pada <citation>{{CURRENTTIME}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> dari {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[Format MLA]] ===
\"{{FULLPAGENAME}}.\" ''{{SITENAME}}, {{int:sitesubtitle}}''. {{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC. <citation>{{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;.

=== [[Format MHRA]] ===
Para kontributor {{SITENAME}}, '{{FULLPAGENAME}}',  ''{{SITENAME}}, {{int:sitesubtitle}},'' {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC, &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [diakses pada <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}</citation>]

=== [[Format Chicago]] ===
Para kontributor {{SITENAME}}, \"{{FULLPAGENAME}},\"  ''{{SITENAME}}, {{int:sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (diakses pada <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== [[Format CBE/CSE]] ===
Para kontributor {{SITENAME}}. {{FULLPAGENAME}} [Internet].  {{SITENAME}}, {{int:sitesubtitle}};  {{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}},   {{CURRENTTIME}} UTC [dikutip pada <citation>{{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}</citation>].  Tersedia dari: 
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[Format Bluebook]] ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (terakhir dikunjungi pada <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== Entri [[BibTeX]] ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }

Saat menggunakan url paket [[LaTeX]] (<code>\usepackage{url}</code> di manapun di bagian pembuka) yang biasanya menghasilkan alamat-alamat web yang diformat dengan lebih baik, cara berikut ini lebih disarankan:

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }


</div> <!--closing div for \"plainlinks\"-->"
);

/** Interlingue (Interlingue)
 * @author Malafaya
 */
$messages['ie'] = array(
	'cite_page' => 'Págine:',
);

/** Igbo (Igbo)
 * @author Ukabia
 */
$messages['ig'] = array(
	'cite_article_desc' => 'Nè tí [[Special:Cite|ndéputà]] ihü kárírí na jikodo ngwa ọru',
	'cite_article_link' => 'Députà ihüa',
	'tooltip-cite-article' => 'Ùmà màkà otụ ha shi députà ihe na ihüa',
	'cite' => 'Ndéputà',
	'cite_page' => 'Ihü:',
	'cite_submit' => 'Ndéputà',
);

/** Iloko (Ilokano)
 * @author Lam-ang
 */
$messages['ilo'] = array(
	'cite_article_desc' => 'Nayunan na ti [[Special:Cite|dakamat]] ti naipangpangruna a panid ken panilpo iti ramramit',
	'cite_article_link' => 'Dakamaten daytoy a panid',
	'tooltip-cite-article' => 'Pakaammo no kasanu ti panagdakamat daytoy a panid',
	'cite' => 'Dakamaten',
	'cite_page' => 'Panid:',
	'cite_submit' => 'Dakamaten',
);

/** Ido (Ido)
 * @author Malafaya
 */
$messages['io'] = array(
	'cite_article_desc' => 'Ico adjuntas specala pagino e ligilo por [[Special:Cite|citaji]] en utensilo-buxo',
	'cite_article_link' => 'Citar ca pagino',
	'cite' => 'Citar',
	'cite_page' => 'Pagino:',
	'cite_submit' => 'Citar',
);

/** Icelandic (Íslenska)
 * @author S.Örvarr.S
 * @author לערי ריינהארט
 */
$messages['is'] = array(
	'cite_article_link' => 'Vitna í þessa síðu',
	'cite' => 'Vitna í síðu',
	'cite_page' => 'Síða:',
	'cite_submit' => 'Vitna í',
);

/** Italian (Italiano)
 * @author Beta16
 * @author BrokenArrow
 */
$messages['it'] = array(
	'cite_article_desc' => 'Aggiunge una pagina speciale per le [[Special:Cite|citazioni]] e un collegamento negli strumenti',
	'cite_article_link' => 'Cita questa pagina',
	'tooltip-cite-article' => 'Informazioni su come citare questa pagina',
	'cite' => 'Citazione',
	'cite_page' => 'Pagina da citare:',
	'cite_submit' => 'Crea la citazione',
);

/** Japanese (日本語)
 * @author Aotake
 * @author Fryed-peach
 * @author JtFuruhata
 * @author Shirayuki
 * @author Suisui
 */
$messages['ja'] = array(
	'cite_article_desc' => '[[Special:Cite|引用情報]]の特別ページとツールボックスのリンクを追加',
	'cite_article_link' => 'この項目を引用',
	'tooltip-cite-article' => 'このページの引用の仕方',
	'cite' => '引用',
	'cite_page' => 'ページ:',
	'cite_submit' => '引用',
	'cite_text' => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

== 「{{FULLPAGENAME}}」の書誌情報 ==

* ページ名: {{FULLPAGENAME}}
* 著者: {{SITENAME}}への寄稿者ら
* 発行者: {{int:sitesubtitle}}『{{SITENAME}}』
* 更新日時: {{CURRENTYEAR}}年{{CURRENTMONTHNAME}}{{CURRENTDAY}}日 {{CURRENTTIME}} (UTC)
* 取得日時: <citation>{{CURRENTYEAR}}年{{CURRENTMONTHNAME}}{{CURRENTDAY}}日 {{CURRENTTIME}} (UTC)</citation>
* 恒久的なURI: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* ページの版番号: {{REVISIONID}}

</div>
<div class=\"plainlinks mw-specialcite-styles\">

== 各種方式による「{{FULLPAGENAME}}」の引用の仕方 ==

=== [[科学技術情報流通技術基準|SIST02方式]] ===
{{SITENAME}}への寄稿者ら. “{{FULLPAGENAME}}”. {{SITENAME}}, {{int:sitesubtitle}}. {{CURRENTYEAR}}-{{CURRENTMONTH}}-{{CURRENTDAY}}. {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}, (参照 <citation>{{CURRENTYEAR}}-{{CURRENTMONTH}}-{{CURRENTDAY}}</citation>).

=== [[APA方式]] ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}}年{{CURRENTMONTHNAME}}{{CURRENTDAY}}日{{CURRENTTIME}}). ''{{SITENAME}}, {{int:sitesubtitle}}''. <citation>{{CURRENTYEAR}}年{{CURRENTMONTHNAME}}{{CURRENTDAY}}日{{CURRENTTIME}}</citation> {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} にて閲覧.

=== [[The MLA style manual|MLA方式]] ===
\"{{FULLPAGENAME}}.\" ''{{SITENAME}}, {{int:sitesubtitle}}''. {{CURRENTYEAR}}年{{CURRENTMONTHABBREV}}{{CURRENTDAY}}日{{CURRENTTIME}} (UTC). <citation>{{CURRENTYEAR}}年{{CURRENTMONTHABBREV}}{{CURRENTDAY}}日{{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;.

=== [[MHRA Style Guide|MHRA方式]] ===
{{SITENAME}}への寄稿者ら, '{{FULLPAGENAME}}', ''{{SITENAME}}, {{int:sitesubtitle}},''{{CURRENTYEAR}}年{{CURRENTMONTHABBREV}}{{CURRENTDAY}}日 (UTC), &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [<citation>{{CURRENTYEAR}}年{{CURRENTMONTHABBREV}}{{CURRENTDAY}}日</citation>閲覧]

=== [[The Chicago Manual of Style|Chicago方式]] ===
{{SITENAME}}への寄稿者ら, \"{{FULLPAGENAME}},\" ''{{SITENAME}}, {{int:sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (<citation>{{CURRENTYEAR}}年{{CURRENTMONTHABBREV}}{{CURRENTDAY}}日</citation>閲覧).

=== [[Council of Science Editors|CBE/CSE方式]] ===
{{SITENAME}}への寄稿者ら. {{FULLPAGENAME}} [Internet]. {{SITENAME}}, {{int:sitesubtitle}}; {{CURRENTYEAR}}年{{CURRENTMONTHABBREV}}{{CURRENTDAY}}日{{CURRENTTIME}} (UTC) [<citation>{{CURRENTYEAR}}年{{CURRENTMONTHABBREV}}{{CURRENTDAY}}日</citation>現在で引用]. 入手元：
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[Bluebook|Bluebook方式]] ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (<citation>{{CURRENTYEAR}}年{{CURRENTMONTHNAME}}{{CURRENTDAY}}日</citation>最終訪問).

=== [[BibTeX]]エントリ ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[オンライン; 閲覧日時 <citation>{{CURRENTYEAR}}-{{CURRENTDAY}}-{{CURRENTMONTH}}</citation>]\"
  }

ウェブアドレスを見た目良く整形するために[[LaTeX]]パッケージ url を用いる場合（プリアンブルのどこかに<code>\usepackage{url}</code>がある場合）は、下記の方が良いかもしれません：

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[オンライン; 閲覧日時 <citation>{{CURRENTYEAR}}-{{CURRENTDAY}}-{{CURRENTMONTH}}</citation>]\"
  }


</div> <!--closing div for \"plainlinks\"-->"
);

/** Jutish (Jysk)
 * @author Huslåke
 */
$messages['jut'] = array(
	'cite_article_link' => 'Fodnåter denne ertikel',
	'cite' => 'Fodnåt',
	'cite_page' => 'Side:',
	'cite_submit' => 'Fodnåt',
);

/** Javanese (Basa Jawa)
 * @author Meursault2004
 */
$messages['jv'] = array(
	'cite_article_desc' => 'Nambahaké kaca astaméwa [[Special:Cite|sitat (kutipan)]] lan pranala ing kothak piranti',
	'cite_article_link' => 'Kutip (sitir) kaca iki',
	'cite' => 'Kutip (sitir)',
	'cite_page' => 'Kaca:',
	'cite_submit' => 'Kutip (sitir)',
);

/** Georgian (ქართული)
 * @author BRUTE
 * @author Malafaya
 * @author გიორგიმელა
 */
$messages['ka'] = array(
	'cite_article_desc' => 'ამატებს [[Special:Cite|ციტირების]] სპეციალურ გვერდს ხელსაწყოებში',
	'cite_article_link' => 'ამ გვერდის ციტირება',
	'tooltip-cite-article' => 'ინფორმაცია ამ გვერდის ციტირების შესახებ',
	'cite' => 'ციტირება',
	'cite_page' => 'გვერდი:',
	'cite_submit' => 'ციტირება',
);

/** Kazakh (Arabic script) (‫قازاقشا (تٴوتە)‬) */
$messages['kk-arab'] = array(
	'cite_article_link' => 'بەتتەن دايەكسوز الۋ',
	'cite' => 'دايەكسوز الۋ',
	'cite_page' => 'بەت اتاۋى:',
	'cite_submit' => 'دايەكسوز ال!',
	'cite_text' => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

== «{{FULLPAGENAME}}» اتاۋىلى بەتىنىڭ كىتاپنامالىق ەگجەي-تەگجەيلەرى ==

* بەتتىڭ اتاۋى: {{FULLPAGENAME}}
* اۋتورى: {{SITENAME}} ۇلەسكەرلەرى
* باسپاگەرى: ''{{SITENAME}}, {{int:sitesubtitle}}''.
* سوڭعى نۇسقاسىنىڭ كەزى: {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC
* الىنعان كەزى: <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC</citation>
* تۇراقتى سىلتەمەسى: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* بەت نۇسقاسىنىڭ تەڭدەستىرۋ ٴنومىرى: {{REVISIONID}}

</div>
<div class=\"plainlinks mw-specialcite-styles\">

== «{{FULLPAGENAME}}» بەتىنىڭ دايەكسوز مانەرلەرى ==

=== [[گوست مانەرى]] ===
<!-- ([[گوست 7.1|گوست 7.1—2003]] جانە [[گوست 7.82|گوست 7.82—2001]]) -->
{{SITENAME}}, {{int:sitesubtitle}} [ەلەكتروندى قاينار] : {{FULLPAGENAME}}, نۇسقاسىنىڭ ٴنومىرى {{REVISIONID}}, سوڭعى تۇزەتۋى {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC / ۋىيكىيپەدىييا اۋتورلارى. — ەلەكتروندى دەرەك. — فلورىيدا شتاتى. : ۋىيكىيمەدىييا قورى, {{CURRENTYEAR}}. — قاتىناۋ رەتى: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}

=== [[APA مانەرى]] ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}). ''{{SITENAME}}, {{int:sitesubtitle}}'' ماعلۇماتى. {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} بەتىنەن <citation>{{CURRENTTIME}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> كەزىندە الىنعان.

=== [[MLA مانەرى]] ===
«{{FULLPAGENAME}}». ''{{SITENAME}}, {{int:sitesubtitle}}''. {{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC. <citation>{{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}}</citation> <{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}>.

=== [[MHRA مانەرى]] ===
{{SITENAME}} ۇلەسكەرلەرى, '{{FULLPAGENAME}}', ''{{SITENAME}}, {{int:sitesubtitle}},'' {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC, <{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}> [<citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}</citation> كەزىندە قاتىنالدى]

=== [[شىيكاگو مانەرى]] ===
{{SITENAME}} ۇلەسكەرى, «{{FULLPAGENAME}}», ''{{SITENAME}}, {{int:sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (<citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> كەزىندە قاتىنالدى).

=== [[CBE/CSE مانەرى]] ===
{{SITENAME}} ۇلەسكەرلەرى. {{FULLPAGENAME}} [ىينتەرنەت]. {{SITENAME}}, {{int:sitesubtitle}}; {{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}, {{CURRENTTIME}} UTC [<citation>{{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}</citation> كەزىندە دايەكسوز الىندى]. قاتىناۋى:
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[كوك كىتاپ|كوك كىتاپ مانەرى]] ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (سوڭعى قارالعانى <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> كەزىندە).

=== [[BibTeX]] جازباسى ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[جەلىدەن; <citation>{{CURRENTDAY}}-{CURRENTMONTHNAME}}-{CURRENTYEAR}}</citation> كەزىندە قاتىنالدى]\"
  }

[[LaTeX]] بۋماسىنىڭ URL جايىن (<code>\usepackage{url}</code> كىرىسپەنىڭ قايبىر ورنىندا) قولدانعاندا (ۆەب جايلارىن ونەرلەۋ پىشىمدەۋىن كەلتىرەدى) كەلەسىسىن قالاۋعا بولادى:

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[جەلىدەن; <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation> كەزىندە قاتىنالدى]\"
  }


</div> <!--closing div for \"plainlinks\"-->"
);

/** Kazakh (Cyrillic script) (‪Қазақша (кирил)‬)
 * @author Kaztrans
 */
$messages['kk-cyrl'] = array(
	'cite_article_desc' => '[[Special:Cite|Дәйексөз]] арнайы бетін және құрал сілтемесін қосады',
	'cite_article_link' => 'Беттен дәйексоз алу',
	'cite' => 'Дәйексөз алу',
	'cite_page' => 'Бет атауы:',
	'cite_submit' => 'Дәйексөз ал!',
	'cite_text' => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

== «{{FULLPAGENAME}}» атауылы бетінің кітапнамалық егжей-тегжейлері ==

* Беттің атауы: {{FULLPAGENAME}}
* Ауторы: {{SITENAME}} үлескерлері
* Баспагері: ''{{SITENAME}}, {{int:sitesubtitle}}''.
* Соңғы нұсқасының кезі: {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC
* Алынған кезі: <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC</citation>
* Тұрақты сілтемесі: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* Бет нұсқасының теңдестіру номірі: {{REVISIONID}}

</div>
<div class=\"plainlinks mw-specialcite-styles\">

== «{{FULLPAGENAME}}» бетінің дәйексөз мәнерлері ==

=== [[ГОСТ мәнері]] ===
<!-- ([[ГОСТ 7.1|ГОСТ 7.1—2003]] және [[ГОСТ 7.82|ГОСТ 7.82—2001]]) -->
{{SITENAME}}, {{int:sitesubtitle}} [Электронды қайнар] : {{FULLPAGENAME}}, нұсқасының нөмірі {{REVISIONID}}, соңғы түзетуі {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC / Уикипедия ауторлары. — Электронды дерек. — Флорида штаты. : Уикимедия Қоры, {{CURRENTYEAR}}. — Қатынау реті: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}

=== [[APA мәнері]] ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}). ''{{SITENAME}}, {{int:sitesubtitle}}'' мағлұматы. {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} бетінен <citation>{{CURRENTTIME}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> кезінде алынған.

=== [[MLA мәнері]] ===
«{{FULLPAGENAME}}». ''{{SITENAME}}, {{int:sitesubtitle}}''. {{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC. <citation>{{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;.

=== [[MHRA мәнері]] ===
{{SITENAME}} үлескерлері, '{{FULLPAGENAME}}', ''{{SITENAME}}, {{int:sitesubtitle}},'' {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC, &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [<citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}</citation> кезінде қатыналды]

=== [[Шикаго мәнері]] ===
{{SITENAME}} үлескері, «{{FULLPAGENAME}}», ''{{SITENAME}}, {{int:sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (<citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> кезінде қатыналды).

=== [[CBE/CSE мәнері]] ===
{{SITENAME}} үлескерлері. {{FULLPAGENAME}} [Интернет]. {{SITENAME}}, {{int:sitesubtitle}}; {{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}, {{CURRENTTIME}} UTC [<citation>{{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}</citation> кезінде дәйексөз алынды]. Қатынауы:
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[Көк кітап|Көк кітап мәнері]] ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (соңғы қаралғаны <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> кезінде).

=== [[BibTeX]] жазбасы ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[Желіден; <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation> кезінде қатыналды]\"
  }

[[LaTeX]] бумасының URL жайын (<code>\usepackage{url}</code> кіріспенің қайбір орнында) қолданғанда (веб жайларын өнерлеу пішімдеуін келтіреді) келесісін қалауға болады:

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[Желіден; <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation> кезінде қатыналды]\"
  }


</div> <!--closing div for \"plainlinks\"-->"
);

/** Kazakh (Latin script) (‪Qazaqşa (latın)‬) */
$messages['kk-latn'] = array(
	'cite_article_link' => 'Betten däýeksoz alw',
	'cite' => 'Däýeksöz alw',
	'cite_page' => 'Bet atawı:',
	'cite_submit' => 'Däýeksöz al!',
	'cite_text' => "__NOTOC__
<div style=\"border: 1px solid grey; background: #E6E8FA; width: 90%; padding: 15px 30px 15px 30px; margin: 10px auto;\">

== «{{FULLPAGENAME}}» atawılı betiniñ kitapnamalıq egjeý-tegjeýleri ==

* Bettiñ atawı: {{FULLPAGENAME}}
* Awtorı: {{SITENAME}} üleskerleri
* Baspageri: ''{{SITENAME}}, {{int:sitesubtitle}}''.
* Soñğı nusqasınıñ kezi: {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC
* Alınğan kezi: <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC</citation>
* Turaqtı siltemesi: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* Bet nusqasınıñ teñdestirw nomiri: {{REVISIONID}}

</div>
<div class=\"plainlinks\" style=\"border: 1px solid grey; width: 90%; padding: 15px 30px 15px 30px; margin: 10px auto;\">

== «{{FULLPAGENAME}}» betiniñ däýeksöz mänerleri ==

=== [[GOST mäneri]] ===
<!-- ([[GOST 7.1|GOST 7.1—2003]] jäne [[GOST 7.82|GOST 7.82—2001]]) -->
{{SITENAME}}, {{int:sitesubtitle}} [Élektrondı qaýnar] : {{FULLPAGENAME}}, nusqasınıñ nömiri {{REVISIONID}}, soñğı tüzetwi {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC / Wïkïpedïya awtorları. — Élektrondı derek. — Florïda ştatı. : Wïkïmedïya Qorı, {{CURRENTYEAR}}. — Qatınaw reti: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}

=== [[APA mäneri]] ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}). ''{{SITENAME}}, {{int:sitesubtitle}}'' mağlumatı. {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} betinen <citation>{{CURRENTTIME}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> kezinde alınğan.

=== [[MLA mäneri]] ===
«{{FULLPAGENAME}}». ''{{SITENAME}}, {{int:sitesubtitle}}''. {{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC. <citation>{{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;.

=== [[MHRA mäneri]] ===
{{SITENAME}} üleskerleri, '{{FULLPAGENAME}}', ''{{SITENAME}}, {{int:sitesubtitle}},'' {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC, &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [<citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}</citation> kezinde qatınaldı]

=== [[Şïkago mäneri]] ===
{{SITENAME}} üleskeri, «{{FULLPAGENAME}}», ''{{SITENAME}}, {{int:sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (<citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> kezinde qatınaldı).

=== [[CBE/CSE mäneri]] ===
{{SITENAME}} üleskerleri. {{FULLPAGENAME}} [Ïnternet]. {{SITENAME}}, {{int:sitesubtitle}}; {{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}, {{CURRENTTIME}} UTC [<citation>{{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}</citation> kezinde däýeksöz alındı]. Qatınawı:
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[Kök kitap|Kök kitap mäneri]] ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (soñğı qaralğanı <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> kezinde).

=== [[BibTeX]] jazbası ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[Jeliden; <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation> kezinde qatınaldı]\"
  }

[[LaTeX]] bwmasınıñ URL jaýın (<code>\usepackage{url}</code> kirispeniñ qaýbir ornında) qoldanğanda (veb jaýların önerlew pişimdewin keltiredi) kelesisin qalawğa boladı:

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[Jeliden; <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation> kezinde qatınaldı]\"
  }


</div> <!--closing div for \"plainlinks\"-->"
);

/** Kalaallisut (Kalaallisut)
 * @author Qaqqalik
 */
$messages['kl'] = array(
	'cite_article_link' => 'Una qupperneq issuaruk',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Chhorran
 * @author Lovekhmer
 * @author គីមស៊្រុន
 */
$messages['km'] = array(
	'cite_article_link' => 'ប្រភពនៃទំព័រនេះ',
	'tooltip-cite-article' => 'ព័ត៌មានអំពីការយោងមកអត្ថបទនេះ',
	'cite' => 'ការយោង',
	'cite_page' => 'ទំព័រ ៖',
	'cite_submit' => 'ដាក់ការយោង',
);

/** Kannada (ಕನ್ನಡ)
 * @author Nayvik
 * @author Shushruth
 */
$messages['kn'] = array(
	'cite_article_link' => 'ಈ ಪುಟವನ್ನು ಉಲ್ಲೇಖಿಸಿ',
	'cite' => 'ಉಲ್ಲೇಖಿಸಿ',
	'cite_page' => 'ಪುಟ:',
);

/** Korean (한국어)
 * @author Kwj2772
 * @author ToePeu
 */
$messages['ko'] = array(
	'cite_article_desc' => '[[Special:Cite|인용]] 특수문서와 도구상자 고리를 더함',
	'cite_article_link' => '이 문서 인용하기',
	'tooltip-cite-article' => '이 문서를 인용하는 방법에 대한 정보',
	'cite' => '인용',
	'cite_page' => '문서:',
	'cite_submit' => '인용',
);

/** Karachay-Balkar (Къарачай-Малкъар)
 * @author Iltever
 */
$messages['krc'] = array(
	'cite_article_link' => 'Бетни цитата эт',
	'cite' => 'Цитата этиу',
);

/** Kinaray-a (Kinaray-a)
 * @author Jose77
 */
$messages['krj'] = array(
	'cite_page' => 'Pahina:',
);

/** Colognian (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'cite_article_desc' => 'Brenk de Sondersigg „[[Special:Cite|Ziteere]]“ un ene Link onger „{{int:toolbox}}“.',
	'cite_article_link' => 'Di Sigk Zitteere',
	'tooltip-cite-article' => 'Enfommazjuhne doh drövver, wi mer heh di Sigg zitteere sullt.',
	'cite' => 'Zittiere',
	'cite_page' => 'Sigk:',
	'cite_submit' => 'Zittėere',
);

/** Kurdish (Latin script) (‪Kurdî (latînî)‬)
 * @author George Animal
 */
$messages['ku-latn'] = array(
	'cite_page' => 'Rûpel:',
);

/** Cornish (Kernowek)
 * @author Kernoweger
 * @author Kw-Moon
 */
$messages['kw'] = array(
	'cite_article_link' => 'Devydna an erthygel-ma',
	'cite' => 'Devydna',
);

/** Latin (Latina)
 * @author MissPetticoats
 * @author SPQRobin
 * @author UV
 */
$messages['la'] = array(
	'cite_article_desc' => ' Addet [[Special:Cite|citation]] specialem paginam et arcam instrumenti',
	'cite_article_link' => 'Hanc paginam citare',
	'cite' => 'Paginam citare',
	'cite_page' => 'Pagina:',
	'cite_submit' => 'Citare',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Kaffi
 * @author Robby
 */
$messages['lb'] = array(
	'cite_article_desc' => "Setzt eng [[Special:Cite|Zitatioun op dëser Spezialsäit]] bäi an e Link an d'Geschiirkëscht",
	'cite_article_link' => 'Dës Säit zitéieren',
	'tooltip-cite-article' => 'Informatioune wéi een dës Säit zitéiere kann',
	'cite' => 'Zitéierhëllef',
	'cite_page' => 'Säit:',
	'cite_submit' => 'weisen',
);

/** Lingua Franca Nova (Lingua Franca Nova)
 * @author Malafaya
 */
$messages['lfn'] = array(
	'cite_page' => 'Paje:',
);

/** Ganda (Luganda)
 * @author Kizito
 */
$messages['lg'] = array(
	'cite_article_link' => 'Juliza olupapula luno',
	'tooltip-cite-article' => "Amagezi agakwata ku ngeri ey'okujuliz'olupapula luno",
	'cite' => 'Juliza',
	'cite_page' => 'Lupapula:',
	'cite_submit' => 'Kakasa okujuliza',
);

/** Limburgish (Limburgs)
 * @author Ooswesthoesbes
 * @author Pahles
 */
$messages['li'] = array(
	'cite_article_desc' => "Voog 'n [[Special:Cite|speciaal pagina óm te citere]] toe en 'ne link derhaer in de gereidsjapskis",
	'cite_article_link' => 'Citeer dees pagina',
	'tooltip-cite-article' => 'Informatie euver wie se dees pazjena kins citere',
	'cite' => 'Citere',
	'cite_page' => 'Pagina:',
	'cite_submit' => 'Citere',
);

/** Lumbaart (Lumbaart)
 * @author Dakrismeno
 */
$messages['lmo'] = array(
	'cite_article_link' => 'Cita quela vus chì',
	'cite' => 'Cita una vus',
);

/** Lao (ລາວ) */
$messages['lo'] = array(
	'cite_article_link' => 'ອ້າງອີງບົດຄວາມນີ້',
	'cite' => 'ອ້າງອີງ',
	'cite_page' => 'ໜ້າ:',
);

/** Lithuanian (Lietuvių)
 * @author Garas
 */
$messages['lt'] = array(
	'cite_article_desc' => 'Prideda [[Special:Cite|citavimo]] specialųjį puslapį ir įrankių juostos nuorodą',
	'cite_article_link' => 'Cituoti šį puslapį',
	'tooltip-cite-article' => 'Informacija kaip cituoti šį puslapį',
	'cite' => 'Cituoti',
	'cite_page' => 'Puslapis:',
	'cite_submit' => 'Cituoti',
	'cite_text' => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

== Bibliografinės \"{{FULLPAGENAME}}\" detalės==

* Puslapio pavadinimas: {{FULLPAGENAME}} 
* Autorius: Projekto \"{{SITENAME}}\" naudotojai
* Leidėjas: ''{{SITENAME}}''. 
* Paskutinės versijos data: {{CURRENTYEAR}} {{CURRENTMONTHNAME}} {{CURRENTDAY}} {{CURRENTTIME}} UTC
* Puslapis gautas: <citation>{{CURRENTYEAR}} {{CURRENTMONTHNAME}} {{CURRENTDAY}} {{CURRENTTIME}} UTC</citation>
* Nuolatinė nuoroda: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* Puslapio versijos Nr.: {{REVISIONID}}

</div>
<div class=\"plainlinks mw-specialcite-styles\">

== Citatų stiliai puslapiui \"{{FULLPAGENAME}}\" ==

=== APA stilius ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}} {{CURRENTMONTHNAME}} {{CURRENTDAY}}). ''{{SITENAME}}''. Gautas <citation>{{CURRENTYEAR}} {{CURRENTMONTHNAME}} {{CURRENTDAY}} {{CURRENTTIME}}</citation> iš {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== MLA stilius ===
\"{{FULLPAGENAME}}.\" ''{{SITENAME}}''. {{CURRENTYEAR}} {{CURRENTMONTHNAME}} {{CURRENTDAY}} {{CURRENTTIME}} UTC. <citation>{{CURRENTYEAR}} {{CURRENTMONTHNAME}} {{CURRENTDAY}} {{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;.

=== MHRA stilius ===
{{SITENAME}} naudotojai, '{{FULLPAGENAME}}', ''{{SITENAME}},'' {{CURRENTYEAR}} {{CURRENTMONTHNAME}} {{CURRENTDAY}} {{CURRENTTIME}} UTC, &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [žiūrėta <citation>{{CURRENTYEAR}} {{CURRENTMONTHNAME}} {{CURRENTDAY}}</citation>]

=== Čikagos stilius ===
{{SITENAME}} naudotojai, \"{{FULLPAGENAME}}\",  ''{{SITENAME}}'', {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (žiūrėta <citation>{{CURRENTYEAR}} {{CURRENTMONTHNAME}} {{CURRENTDAY}}</citation>).

=== CBE/CSE stilius ===
{{SITENAME}} naudotojai. {{FULLPAGENAME}} [internete].  {{SITENAME}},  {{CURRENTYEAR}} {{CURRENTMONTHNAME}} {{CURRENTDAY}} {{CURRENTTIME}} UTC [cituota <citation>{{CURRENTYEAR}}-{{CURRENTMONTH}}-{{CURRENTDAY2}}</citation>]. Galima rasti: 
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== Bluebook stilius ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (paskutinį kartą žiūrėta <citation>{{CURRENTYEAR}} {{CURRENTMONTHNAME}} {{CURRENTDAY}}</citation>).

=== BibTeX įrašas ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[Žiniatinklyje; žiūrėta <citation>{{CURRENTYEAR}} {{CURRENTMONTHNAME}} {{CURRENTDAY}}</citation>]\"
  }

Kai naudojate LaTeX paketą ''url'' (<code>\usepackage{url}</code> kur nors pradžioje), kuris skirtas duoti daug gražiau suformuotus žiniatinklio adresus, patartina naudoti šitaip:

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[Žiniatinklyje; žiūrėta <citation>{{CURRENTYEAR}} {{CURRENTMONTHNAME}} {{CURRENTDAY}}</citation>]\"
  }


</div>"
);

/** Latvian (Latviešu)
 * @author Xil
 */
$messages['lv'] = array(
	'cite_article_link' => 'Atsauce uz šo lapu',
	'cite' => 'Citēšana',
	'cite_page' => 'Raksts:',
	'cite_submit' => 'Parādīt atsauci',
);

/** Literary Chinese (文言) */
$messages['lzh'] = array(
	'cite_article_link' => '引文',
	'cite' => '引文',
);

/** Eastern Mari (Олык Марий)
 * @author Сай
 */
$messages['mhr'] = array(
	'cite_page' => 'Лаштык:',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 * @author Brest
 * @author Misos
 */
$messages['mk'] = array(
	'cite_article_desc' => 'Додава специјална страница за [[Special:Cite|цитирање]] и врска кон алатникот',
	'cite_article_link' => 'Цитирање на страницава',
	'tooltip-cite-article' => 'Информации како да ја цитирате оваа страница',
	'cite' => 'Цитат',
	'cite_page' => 'Страница:',
	'cite_submit' => 'Цитат',
);

/** Malayalam (മലയാളം)
 * @author Praveenp
 * @author Shijualex
 */
$messages['ml'] = array(
	'cite_article_desc' => '[[Special:Cite|സൈറ്റേഷൻ]] എന്ന പ്രത്യേക താളും, പണി സഞ്ചി  കണ്ണിയും ചേർക്കുന്നു',
	'cite_article_link' => 'ഈ താൾ ഉദ്ധരിക്കുക',
	'tooltip-cite-article' => 'ഈ താളിനെ എങ്ങനെ അവലംബിതമാക്കാം എന്ന വിവരങ്ങൾ',
	'cite' => 'ഉദ്ധരിക്കുക',
	'cite_page' => 'താൾ:',
	'cite_submit' => 'ഉദ്ധരിക്കുക',
);

/** Mongolian (Монгол)
 * @author Chinneeb
 */
$messages['mn'] = array(
	'cite_article_link' => 'Энэ хуудаснаас иш татах',
	'cite' => 'Иш татах',
	'cite_page' => 'Хуудас:',
	'cite_submit' => 'Иш татах',
);

/** Marathi (मराठी)
 * @author Kaustubh
 * @author Mahitgar
 * @author V.narsikar
 */
$messages['mr'] = array(
	'cite_article_desc' => 'एक विशेष [[Special:Cite|बाह्यदुवे]] देणारे पान व टूलबॉक्सची लिंक तयार करा',
	'cite_article_link' => 'हे पान उधृत करा',
	'tooltip-cite-article' => 'हे पृष्ठ बघण्यासाठीची माहिती',
	'cite' => 'उधृत करा',
	'cite_page' => 'पान',
	'cite_submit' => 'उधृत करा',
);

/** Hill Mari (Кырык мары)
 * @author Amdf
 */
$messages['mrj'] = array(
	'cite_article_link' => 'Ӹлӹшташӹм цитируяш',
);

/** Malay (Bahasa Melayu)
 * @author Aurora
 * @author Aviator
 */
$messages['ms'] = array(
	'cite_article_desc' => 'Menambah laman khas dan pautan kotak alatan untuk [[Special:Cite|pemetikan]]',
	'cite_article_link' => 'Petik laman ini',
	'tooltip-cite-article' => 'Maklumat tentang cara memetik laman ini',
	'cite' => 'Petik',
	'cite_page' => 'Laman:',
	'cite_submit' => 'Petik',
);

/** Maltese (Malti)
 * @author Chrisportelli
 * @author Giangian15
 */
$messages['mt'] = array(
	'cite_article_desc' => 'Iżżid paġna speċjali għaċ-[[Special:Cite|ċitazzjonijiet]] u ħolqa mal-istrumenti',
	'cite_article_link' => 'Iċċita din il-paġna',
	'tooltip-cite-article' => 'Informazzjoni fuq kif tiċċita din il-paġna',
	'cite' => 'Ċitazzjoni',
	'cite_page' => 'Paġna:',
	'cite_submit' => 'Oħloq ċitazzjoni',
);

/** Mirandese (Mirandés)
 * @author Malafaya
 */
$messages['mwl'] = array(
	'cite_page' => 'Páigina:',
);

/** Erzya (Эрзянь)
 * @author Amdf
 * @author Botuzhaleny-sodamo
 */
$messages['myv'] = array(
	'cite_page' => 'Лопась:',
);

/** Nahuatl (Nāhuatl)
 * @author Fluence
 * @author Ricardo gs
 */
$messages['nah'] = array(
	'cite_article_link' => 'Tlahtoa inīn tlahcuilōltechcopa',
	'cite' => 'Titēnōtzaz',
	'cite_page' => 'Zāzanilli:',
	'cite_submit' => 'Titēnōtzaz',
);

/** Min Nan Chinese (Bân-lâm-gú) */
$messages['nan'] = array(
	'cite_article_link' => 'Ín-iōng chit phiⁿ bûn-chiuⁿ',
	'cite' => 'Ín-iōng',
	'cite_page' => 'Ia̍h:',
	'cite_submit' => 'Ín-iōng',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 * @author Nghtwlkr
 */
$messages['nb'] = array(
	'cite_article_desc' => 'Legger til en [[Special:Cite|siteringsside]] og lenke i verktøy-menyen',
	'cite_article_link' => 'Siter denne siden',
	'tooltip-cite-article' => 'Informasjon om hvordan denne siden kan siteres',
	'cite' => 'Siter',
	'cite_page' => 'Side:',
	'cite_submit' => 'Siter',
);

/** Low German (Plattdüütsch)
 * @author Slomox
 */
$messages['nds'] = array(
	'cite_article_desc' => 'Föögt en [[Special:Cite|Spezialsied för Zitaten]] un en Lenk dorop in’n Kasten Warktüüch to',
	'cite_article_link' => 'Disse Siet ziteren',
	'cite' => 'Ziteerhelp',
	'cite_page' => 'Siet:',
	'cite_submit' => 'Ziteren',
);

/** Nedersaksisch (Nedersaksisch)
 * @author Servien
 */
$messages['nds-nl'] = array(
	'cite_article_desc' => 'Zet n [[Special:Cite|spesiale pagina]] derbie um te siteren, en n verwiezing dernaor in de hulpmiddels',
	'cite_article_link' => 'Disse pagina siteren',
	'tooltip-cite-article' => "Informasie over ho of da'j disse pagina kunnen siteren",
	'cite' => 'Siteerhulpe',
	'cite_page' => 'Pagina:',
	'cite_submit' => 'Siteren',
);

/** Nepali (नेपाली) */
$messages['ne'] = array(
	'cite_article_link' => 'लेख उद्दरण गर्नुहोस्',
	'cite' => 'उद्दरण गर्नु',
	'cite_page' => 'पृष्ठ:',
);

/** Dutch (Nederlands)
 * @author Effeietsanders
 * @author SPQRobin
 * @author Siebrand
 */
$messages['nl'] = array(
	'cite_article_desc' => 'Voegt een [[Special:Cite|speciale pagina]] toe om te citeren, en een verwijzing ernaar in de hulpmiddelen',
	'cite_article_link' => 'Deze pagina citeren',
	'tooltip-cite-article' => 'Informatie over hoe u deze pagina kunt citeren',
	'cite' => 'Citeren',
	'cite_page' => 'Pagina:',
	'cite_submit' => 'Citeren',
	'cite_text' => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

== Bibliografische gegevens voor {{FULLPAGENAME}} ==

* Artikel: {{FULLPAGENAME}} 
* Auteur: {{SITENAME}}-medewerkers
* Uitgever: ''{{SITENAME}}, {{int:sitesubtitle}}''. 
* Tijdstip laatste bewerking: {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} (UTC)
* Tijdstip opgehaald: <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} (UTC)</citation>
* Permalink: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* Paginaversienummer: {{REVISIONID}}

</div>
<div class=\"plainlinks mw-specialcite-styles\">

== Citatiestijlen voor {{FULLPAGENAME}} ==

=== APA-stijl ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}). ''{{SITENAME}}, {{int:sitesubtitle}}''. Opgehaald <citation>{{CURRENTTIME}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> van {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== MLA-stijl ===
\"{{FULLPAGENAME}}.\" ''{{SITENAME}}, {{int:sitesubtitle}}''. {{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}} (UTC). <citation>{{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;.

=== MHRA-stijl ===
{{SITENAME}}-medewerkers, '{{FULLPAGENAME}}',  ''{{SITENAME}}, {{int:sitesubtitle}},'' {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} (UTC), &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [accessed <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}</citation>]

=== Chicago-stijl ===
{{SITENAME}}-medewerkers, \"{{FULLPAGENAME}},\"  ''{{SITENAME}}, {{int:sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (accessed <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== CBE/CSE-stijl ===
{{SITENAME}}-medewerkers. {{FULLPAGENAME}} [Internet].  {{SITENAME}}, {{int:sitesubtitle}};  {{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}},   {{CURRENTTIME}} (UTC) [cited <citation>{{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}</citation>].  Available from: 
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== Bluebook-stijl ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (het laatst bezocht <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== [[BibTeX]]-entry ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }

Bij gebruik van het LaTeX-package url (<code>\usepackage{url}</code> ergens vooraan in het bestand) dat webaddressen mooier vormgeeft, heeft het volgende de voorkeur:

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }


</div> <!--closing div for \"plainlinks\"-->"
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Gunnernett
 * @author Harald Khan
 * @author Jon Harald Søby
 */
$messages['nn'] = array(
	'cite_article_desc' => 'Legg til ei [[Special:Cite|siteringssida]] og lenkja i verktøy-menyen',
	'cite_article_link' => 'Siter denne sida',
	'tooltip-cite-article' => 'Informasjon om korleis ein siterer denne sida',
	'cite' => 'Siter',
	'cite_page' => 'Side:',
	'cite_submit' => 'Siter',
);

/** Novial (Novial)
 * @author MF-Warburg
 */
$messages['nov'] = array(
	'cite_article_link' => 'Sita disi artikle',
	'cite' => 'Sita',
);

/** Northern Sotho (Sesotho sa Leboa)
 * @author Mohau
 */
$messages['nso'] = array(
	'cite_page' => 'Letlakala:',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'cite_article_desc' => "Apond una pagina especiala [[Special:Cite|citacion]] e un ligam dins la bóstia d'espleches",
	'cite_article_link' => 'Citar aqueste article',
	'tooltip-cite-article' => 'Informacions sus cossí citar aquesta pagina',
	'cite' => 'Citacion',
	'cite_page' => 'Pagina :',
	'cite_submit' => 'Citar',
	'cite_text' => "__NOTOC__ 
<div class=\"mw-specialcite-bibliographic\">

== Informacions bibliograficas sus {{FULLPAGENAME}} == 
* Nom de la pagina : {{FULLPAGENAME}} 
* Autors : {{canonicalurl:{{FULLPAGENAME}}|action=history}} 
* Editor : {{SITENAME}} 
* Darrièra revision : {{CURRENTDAY}} de {{CURRENTMONTHNAME}} de {{CURRENTYEAR}} a {{CURRENTTIME}} UTC 
* Pagina consultada lo : <citation>{{CURRENTDAY}} de {{CURRENTMONTHNAME}} de {{CURRENTYEAR}} a {{CURRENTTIME}} UTC</citation> 
* Ligam vèrs la version citada : {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} 
* Numerò de version : {{REVISIONID}} </div> 
<div class=\"plainlinks mw-specialcite-styles\">

== Cossí citar la pagina {{FULLPAGENAME}} ==
=== [[APA style]] === 
{{FULLPAGENAME}}. ({{CURRENTYEAR}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}). ''{{SITENAME}}, {{int:sitesubtitle}}''. Pagina consultada lo <citation>{{CURRENTTIME}}, de {{CURRENTMONTHNAME}}  {{CURRENTDAY}}, de {{CURRENTYEAR}}</citation> a partir de {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}. 

=== [[The MLA style manual|MLA style]] === 
\"{{FULLPAGENAME}}.\" ''{{SITENAME}}, {{int:sitesubtitle}}''. {{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC. <citation>{{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;. 

=== [[MHRA Style Guide|MHRA style]] === 
Contributors a {{SITENAME}}, '{{FULLPAGENAME}}', ''{{SITENAME}}, {{int:sitesubtitle}},'' {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC, &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [Pagina consultada lo <citation>{{CURRENTDAY}} de {{CURRENTMONTHNAME}} de {{CURRENTYEAR}}</citation>]

=== [[The Chicago Manual of Style|Chicago style]] === Contributors a {{SITENAME}}, \"{{FULLPAGENAME}},\" ''{{SITENAME}}, {{int:sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (Pagina consultada lo <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, de {{CURRENTYEAR}}</citation>).

=== [[Council of Science Editors|CBE/CSE style]] === Contributors a {{SITENAME}}. {{FULLPAGENAME}} [Internet]. {{SITENAME}}, {{int:sitesubtitle}}; {{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}, a {{CURRENTTIME}} UTC [cited <citation>{{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}</citation>]. Disponible a l'adreça: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[Bluebook|Bluebook style]] === {{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (Pagina consultada lo <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, de {{CURRENTYEAR}}</citation>). 

=== [[BibTeX]] === 
@misc{ wiki:xxx, 
 author = \"{{SITENAME}}\",
 title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
 year = \"{{CURRENTYEAR}}\",
 url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
 note = \"[En linha; Pagina disponibla lo <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\" }

Quand utilisatz lo modul url jos [[LaTeX]] (<code>\usepackage{url}</code> endacòm dins lo preambul) qu'amelhora l'afichatge de las adreças internet, utilisatz de preferéncia aqueste format: 

@misc{ wiki:xxx, author = \"{{SITENAME}}\",
 title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
 year = \"{{CURRENTYEAR}}\",
 url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
 note = \"[En linha; Pagina disponibla lo <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\" }

</div> <!--closing div for \"plainlinks\"-->"
);

/** Oriya (ଓଡ଼ିଆ)
 * @author Psubhashish
 */
$messages['or'] = array(
	'cite_article_desc' => 'ଏକ [[Special:Cite|ଆଧାର]] ବିଶେଷ ପୃଷ୍ଠା ଓ ଉପକରଣ ପେଡ଼ିର ଲିଙ୍କ ଯୋଡ଼ିଥାଏ',
	'cite_article_link' => 'ଏହି ପୃଷ୍ଠାଟିରେ ପ୍ରମାଣ ଯୋଡ଼ିବେ',
	'tooltip-cite-article' => 'ଏକ ଆଧାର ଦେବା ଉପରେ ଅଧିକ ବିବରଣୀ',
	'cite' => 'ଆଧାର ଦେବେ',
	'cite_page' => 'ପୃଷ୍ଠା:',
	'cite_submit' => 'ଆଧାର ଦେବେ',
);

/** Ossetic (Ирон)
 * @author Amikeco
 */
$messages['os'] = array(
	'cite_page' => 'Фарс:',
);

/** Pangasinan (Pangasinan) */
$messages['pag'] = array(
	'cite_article_link' => 'Bitlaen yan article',
	'cite' => 'Bitlaen',
	'cite_page' => 'Bolong:',
	'cite_submit' => 'Bitlaen',
);

/** Pampanga (Kapampangan) */
$messages['pam'] = array(
	'cite_article_link' => 'Banggitan ya ing articulung ini',
	'cite' => 'Banggitan ya',
	'cite_page' => 'Bulung:',
	'cite_submit' => 'Banggitan me',
);

/** Deitsch (Deitsch)
 * @author Xqt
 */
$messages['pdc'] = array(
	'cite_page' => 'Blatt:',
);

/** Pälzisch (Pälzisch)
 * @author Manuae
 * @author SPS
 */
$messages['pfl'] = array(
	'cite_article_link' => 'Die Said zidiere',
	'cite' => 'Hilf zum Zidiere',
	'cite_submit' => 'Schbaischere',
);

/** Polish (Polski)
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'cite_article_desc' => 'Dodaje stronę specjalną i guzik w toolbarze edycyjnym do obsługi [[Special:Cite|cytowania]]',
	'cite_article_link' => 'Cytowanie tego artykułu',
	'tooltip-cite-article' => 'Informacja o tym jak należy cytować tę stronę',
	'cite' => 'Bibliografia',
	'cite_page' => 'Strona:',
	'cite_submit' => 'stwórz wpis bibliograficzny',
);

/** Piedmontese (Piemontèis)
 * @author Bèrto 'd Sèra
 * @author Dragonòt
 */
$messages['pms'] = array(
	'cite_article_desc' => "A gionta na pàgina special [[Special:Cite|citassion]] e n'anliura dj'utiss",
	'cite_article_link' => 'Sita sta pàgina-sì',
	'tooltip-cite-article' => 'Anformassion ëd com sité sta pàgina-sì.',
	'cite' => 'Citassion',
	'cite_page' => 'Pàgina da cité:',
	'cite_submit' => 'Pronta la citassion',
);

/** Western Punjabi (پنجابی)
 * @author Khalid Mahmood
 */
$messages['pnb'] = array(
	'cite_article_desc' => 'جوڑدا اے اک [[Special:Cite|اتہ پتہ]] خاص صفہ تے اوزار ڈبہ جوڑ۔',
	'cite_article_link' => 'ایس صفے دا اتہ پتہ دیو',
	'tooltip-cite-article' => 'ایس صفے دا کنج اتہ پتہ دیوو دی دس۔',
	'cite' => 'اتہ پتہ',
	'cite_page' => 'صفہ:',
	'cite_submit' => 'اتہ پتہ',
);

/** Pontic (Ποντιακά)
 * @author Sinopeus
 */
$messages['pnt'] = array(
	'cite_page' => 'Σελίδα:',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'cite_article_link' => 'د دې مخ درک',
	'tooltip-cite-article' => 'د دې مخ د درک لګولو مالومات',
	'cite' => 'درک',
	'cite_page' => 'مخ:',
	'cite_submit' => 'درک لګول',
);

/** Portuguese (Português)
 * @author 555
 * @author Hamilton Abreu
 * @author Lijealso
 * @author Malafaya
 */
$messages['pt'] = array(
	'cite_article_desc' => '[[Special:Cite|Página especial]] que produz uma citação de qualquer outra página na wiki (em vários formatos) e adiciona um link na barra de ferramentas',
	'cite_article_link' => 'Citar esta página',
	'tooltip-cite-article' => 'Informação sobre como citar esta página',
	'cite' => 'Citar',
	'cite_page' => 'Página:',
	'cite_submit' => 'Citar',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Carla404
 * @author Giro720
 */
$messages['pt-br'] = array(
	'cite_article_desc' => 'Adiciona uma página especial de [[Special:Cite|citação]] e link para a caixa de ferramentas',
	'cite_article_link' => 'Citar esta página',
	'tooltip-cite-article' => 'Informação sobre como citar esta página',
	'cite' => 'Citar',
	'cite_page' => 'Página:',
	'cite_submit' => 'Citar',
);

/** Quechua (Runa Simi)
 * @author AlimanRuna
 */
$messages['qu'] = array(
	'cite_article_desc' => "[[Special:Cite|Pukyumanta willanapaq]] sapaq p'anqatam llamk'ana t'asrapi t'inkitapas yapan",
	'cite_article_link' => 'Kay qillqamanta willay',
	'tooltip-cite-article' => "Ima hinam kay p'anqamanta willay",
	'cite' => 'Qillqamanta willay',
	'cite_page' => "P'anqa:",
	'cite_submit' => 'Qillqamanta willay',
);

/** Romansh (Rumantsch)
 * @author Kazu89
 */
$messages['rm'] = array(
	'cite_article_link' => 'Citar questa pagina',
	'cite_page' => 'Pagina:',
);

/** Romani (Romani)
 * @author Desiphral
 */
$messages['rmy'] = array(
	'cite_article_link' => 'Prinjardo phandipen ko lekh',
	'cite' => 'Kana trebul phandipen',
	'cite_submit' => 'Ja',
);

/** Romanian (Română)
 * @author Danutz
 * @author Emily
 * @author Firilacroco
 * @author KlaudiuMihaila
 * @author Mihai
 * @author Minisarm
 * @author Stelistcristi
 */
$messages['ro'] = array(
	'cite_article_desc' => 'Adaugă o pagină specială [[Special:Cite|citare]] și o legătură cutie unelte',
	'cite_article_link' => 'Citați acest articol',
	'tooltip-cite-article' => 'Informații cu privire la modul de citare a acestei pagini',
	'cite' => 'Citare',
	'cite_page' => 'Pagină:',
	'cite_submit' => 'Deschide informații',
);

/** Tarandíne (Tarandíne)
 * @author Joetaras
 */
$messages['roa-tara'] = array(
	'cite_article_desc' => "Aggiunge 'na pàgena speciele de [[Special:Cite|citaziune]] e collegamende a scatele de le struminde",
	'cite_article_link' => 'Cite sta pàgene',
	'tooltip-cite-article' => "'Mbormaziune sus a cumme se cite sta pàgene",
	'cite' => 'Cite',
	'cite_page' => 'Pàgene:',
	'cite_submit' => 'Cite',
);

/** Russian (Русский)
 * @author Huuchin
 * @author Александр Сигачёв
 * @author Ильнар
 */
$messages['ru'] = array(
	'cite_article_desc' => 'Добавляет служебную страницу [[Special:Cite|цитирования]] и ссылку в инструментах',
	'cite_article_link' => 'Цитировать страницу',
	'tooltip-cite-article' => 'Информация о том, как цитировать эту страницу',
	'cite' => 'Цитирование',
	'cite_page' => 'Страница:',
	'cite_submit' => 'Процитировать',
	'cite_text' => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

== Библиографические данные статьи {{FULLPAGENAME}} ==

* Статья: {{FULLPAGENAME}}
* Автор: {{SITENAME}} авторы
* Опубликовано: ''{{SITENAME}}, {{int:sitesubtitle}}''.
* Дата последнего изменения: {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC
* Дата загрузки: <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC</citation>
* Постоянная ссылка: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* Идентификатор версии страницы: {{REVISIONID}}

</div>
<div class=\"plainlinks mw-specialcite-styles\">

== Варианты оформления ссылок на статью «{{FULLPAGENAME}}» ==

=== Описание по [[ГОСТ 7.1|ГОСТ 7.1—2003]] и [[ГОСТ 7.82|ГОСТ 7.82—2001]] ===
{{SITENAME}}, {{int:sitesubtitle}} [Электронный ресурс] : {{FULLPAGENAME}}, вариант {{REVISIONID}}, последняя правка {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC / Авторы Википедии. — Электрон. дан. — Штат Флорида. : Фонд Викимедиа, {{CURRENTYEAR}}. — Режим доступа: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}

=== [[APA style|Стиль APA]] ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}). ''{{SITENAME}}, {{int:sitesubtitle}}''. Retrieved <citation>{{CURRENTTIME}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> from {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[The MLA style manual|Стиль MLA]] ===
\"{{FULLPAGENAME}}.\" ''{{SITENAME}}, {{int:sitesubtitle}}''. {{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC. <citation>{{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;.

=== [[MHRA Style Guide|Стиль MHRA]] ===
{{SITENAME}} contributors, '{{FULLPAGENAME}}',  ''{{SITENAME}}, {{int:sitesubtitle}},'' {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC, &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [accessed <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}</citation>]

=== [[The Chicago Manual of Style|Чикагский стиль]] ===
{{SITENAME}} contributors, \"{{FULLPAGENAME}},\"  ''{{SITENAME}}, {{int:sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (accessed <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== [[Council of Science Editors|Стиль CBE/CSE]] ===
{{SITENAME}} contributors. {{FULLPAGENAME}} [Internet].  {{SITENAME}}, {{int:sitesubtitle}};  {{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}},   {{CURRENTTIME}} UTC [cited <citation>{{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}</citation>].  Available from: 
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[Bluebook|Bluebook style]] ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (last visited   <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== Запись в [[BibTeX]] ===
  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }

При использовании [[LaTeX]]-пакета url для более наглядного представления веб-адресов (<code>\usepackage{url}</code> в преамбуле), вероятно, лучше будет указать:

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
  }

</div> <!--closing div for \"plainlinks\"-->"
);

/** Rusyn (Русиньскый)
 * @author Gazeb
 */
$messages['rue'] = array(
	'cite_article_desc' => 'Придасть шпеціалну сторінку [[Special:Cite|Цітації]] і одказ в понуцї інштрументів',
	'cite_article_link' => 'Цітовати сторінку',
	'tooltip-cite-article' => 'Інформації о тім, як цітовати тоту сторінку',
	'cite' => 'Цітованя',
	'cite_page' => 'Сторінка:',
	'cite_submit' => 'Цітовати',
);

/** Aromanian (Armãneashce) */
$messages['rup'] = array(
	'cite_article_link' => 'Bagã articlu aistu ca tsitat',
);

/** Sanskrit (संस्कृतम्)
 * @author Ansumang
 */
$messages['sa'] = array(
	'cite' => 'उदाहरति',
	'cite_page' => 'पृष्ठ:',
	'cite_submit' => 'उदाहरति',
);

/** Sakha (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'cite_article_desc' => 'Аналлаах [[Special:Cite|быһа тардыы]] сирэйин уонна үнүстүрүмүөннэргэ ыйынньык эбэн биэрэр',
	'cite_article_link' => 'Сирэйи цитируйдааһын',
	'tooltip-cite-article' => 'Бу сирэйи хайдах цитируйдуур туһунан',
	'cite' => 'Цитата',
	'cite_page' => 'Сирэй:',
	'cite_submit' => 'Цитаата',
);

/** Sicilian (Sicilianu)
 * @author Santu
 */
$messages['scn'] = array(
	'cite_article_desc' => 'Junci na pàggina spiciali pi li [[Special:Cite|cosi di muntuari]] e nu lijami ntê strumenti',
	'cite_article_link' => 'Muntùa sta pàggina',
	'cite' => 'Muntuazzioni',
	'cite_page' => 'Pàggina di muntari',
	'cite_submit' => 'Cria la cosa di muntuari',
);

/** Sindhi (سنڌي) */
$messages['sd'] = array(
	'cite' => 'حواليو',
);

/** Samogitian (Žemaitėška)
 * @author Hugo.arg
 */
$messages['sgs'] = array(
	'cite' => 'Citoutė',
	'cite_page' => 'Poslapis:',
);

/** Sinhala (සිංහල)
 * @author Budhajeewa
 * @author නන්දිමිතුරු
 */
$messages['si'] = array(
	'cite_article_desc' => '[[Special:Cite|උපහරණ]] විශේෂ පිටුවක් හා මෙවලම්ගොන්න සබැඳියක් එක්කරයි',
	'cite_article_link' => 'මෙම පිටුව උපන්‍යාස කරන්න',
	'tooltip-cite-article' => 'මෙම පිටුව උපුටා දක්වන්නේ කෙසේද යන්න පිළිබඳ තොරතුරු.',
	'cite' => 'උපන්‍යාසය',
	'cite_page' => 'පිටුව:',
	'cite_submit' => 'උපන්‍යාසය',
);

/** Slovak (Slovenčina)
 * @author Helix84
 * @author Martin Kozák
 */
$messages['sk'] = array(
	'cite_article_desc' => 'Pridáva špeciálnu stránku [[Special:Cite|Citovať]] a odkaz v nástrojoch',
	'cite_article_link' => 'Citovať túto stránku',
	'tooltip-cite-article' => 'Ako citovať túto stránku',
	'cite' => 'Citovať',
	'cite_page' => 'Stránka:',
	'cite_submit' => 'Citovať',
);

/** Slovenian (Slovenščina)
 * @author Dbc334
 * @author Smihael
 */
$messages['sl'] = array(
	'cite_article_desc' => 'Doda [[Special:Cite|posebno stran za navedbo vira]] in povezavo v orodno vrstico',
	'cite_article_link' => 'Navedba strani',
	'tooltip-cite-article' => 'Informacije o tem, kako navajati to stran',
	'cite' => 'Navedi',
	'cite_page' => 'Stran:',
	'cite_submit' => 'Navedi',
);

/** Southern Sami (Åarjelsaemien)
 * @author M.M.S.
 */
$messages['sma'] = array(
	'cite_page' => 'Bielie:',
);

/** Shona (chiShona) */
$messages['sn'] = array(
	'cite_article_link' => 'Ita cite nyaya iyi',
);

/** Albanian (Shqip)
 * @author Olsi
 */
$messages['sq'] = array(
	'cite_article_desc' => 'Shton një faqe speciale [[Special:Cite|citimi]] dhe një lidhje veglash.',
	'cite_article_link' => 'Cito artikullin',
	'tooltip-cite-article' => 'Informacion mbi mënyrën e citimit të kësaj faqeje',
	'cite' => 'Citate',
	'cite_page' => 'Faqja:',
	'cite_submit' => 'Citoje',
	'cite_text' => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

== Të dhënat bibliografike për «{{FULLPAGENAME}}» ==
* Emri i faqes: {{FULLPAGENAME}}
* Autori: Redaktorët e {{SITENAME}}-s
* Publikuesi: ''{{SITENAME}}, {{int:sitesubtitle}}''.
* Data e versionit të fundit: {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC
* E marrë më: <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} {{CURRENTTIME}} UTC</citation>
* Lidhja e përhershme: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* Nr i versionit të faqes: {{REVISIONID}}
</div>

<div class=\"plainlinks mw-specialcite-styles\">

== Stile të ndryshme citimi për «{{FULLPAGENAME}}» ==

=== [[Stili citimit APA|APA]] ===
{{FULLPAGENAME}}. ({{CURRENTYEAR}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}). ''{{SITENAME}}, {{int:sitesubtitle}}''. Retrieved <citation>{{CURRENTTIME}}, {{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation> from {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[Stili citimit MLA|MLA]] ===
\"{{FULLPAGENAME}}.\" ''{{SITENAME}}, {{int:sitesubtitle}}''. {{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC. <citation>{{CURRENTDAY}} {{CURRENTMONTHABBREV}} {{CURRENTYEAR}}, {{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;.

=== [[Stili citimit MHRA|MHRA]] ===
{{SITENAME}} contributors, '{{FULLPAGENAME}}', ''{{SITENAME}}, {{int:sitesubtitle}},'' {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}, {{CURRENTTIME}} UTC, &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt; [accessed <citation>{{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}</citation>]

=== [[Stili i citimit Chicago|Chicago]] ===
{{SITENAME}} contributors, \"{{FULLPAGENAME}},\" ''{{SITENAME}}, {{int:sitesubtitle}},'' {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (accessed <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== [[Stili i citimit CBE/CSE|CBE/CSE]] ===
{{SITENAME}} contributors. {{FULLPAGENAME}} [Internet]. {{SITENAME}}, {{int:sitesubtitle}}; {{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}, {{CURRENTTIME}} UTC [cited <citation>{{CURRENTYEAR}} {{CURRENTMONTHABBREV}} {{CURRENTDAY}}</citation>]. Available from: {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}.

=== [[Stili i citimit Bluebook|Bluebook]] ===
{{FULLPAGENAME}}, {{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}} (last visited <citation>{{CURRENTMONTHNAME}} {{CURRENTDAY}}, {{CURRENTYEAR}}</citation>).

=== [[Stili i citimit BibTeX|BibTeX]] ===
@misc{ wiki:xxx,
	author = \"{{SITENAME}}\",
	title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
	year = \"{{CURRENTYEAR}}\",
	url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
	note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
} 

When using the [[LaTeX]] package url (<code>\\usepackage{url}</code> somewhere in the preamble) which tends to give much more nicely formatted web addresses, the following may preferred:

@misc{ wiki:xxx,
	author = \"{{SITENAME}}\",
	title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
	year = \"{{CURRENTYEAR}}\",
	url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
	note = \"[Online; accessed <citation>{{CURRENTDAY}}-{{CURRENTMONTHNAME}}-{{CURRENTYEAR}}</citation>]\"
}
</div><!--closing div for \"plainlinks\"-->"
);

/** Serbian (Cyrillic script) (‪Српски (ћирилица)‬)
 * @author Millosh
 * @author Rancher
 * @author Sasa Stefanovic
 * @author Жељко Тодоровић
 * @author Михајло Анђелковић
 */
$messages['sr-ec'] = array(
	'cite_article_desc' => 'Додаје посебну страницу за [[Special:Cite|цитирање]] и везу с алаткама',
	'cite_article_link' => 'Цитирање странице',
	'tooltip-cite-article' => 'Информације о томе како цитирати ову страну',
	'cite' => 'цитат',
	'cite_page' => 'Страница:',
	'cite_submit' => 'цитат',
);

/** Serbian (Latin script) (‪Srpski (latinica)‬)
 * @author Liangent
 * @author Michaello
 * @author Жељко Тодоровић
 */
$messages['sr-el'] = array(
	'cite_article_desc' => 'Dodaje specijalnu stranu za [[Special:Cite|citiranje]] i vezu ka oruđima.',
	'cite_article_link' => 'citiranje ove strane',
	'tooltip-cite-article' => 'Informacije o tome kako citirati ovu stranu',
	'cite' => 'citat',
	'cite_page' => 'Stranica:',
	'cite_submit' => 'citat',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'cite_article_desc' => 'Föiget ju [[Special:Cite|Zitierhilfe]]-Spezioalsiede un n Link in dän Kasten Reewen bietou',
	'cite_article_link' => 'Disse Siede zitierje',
	'cite' => 'Zitierhälpe',
	'cite_page' => 'Siede:',
	'cite_submit' => 'anwiese',
);

/** Sundanese (Basa Sunda)
 * @author Kandar
 */
$messages['su'] = array(
	'cite_article_desc' => 'Nambahkeun kaca husus [[Special:Cite|cutatan]] & tumbu toolbox',
	'cite_article_link' => 'Cutat kaca ieu',
	'tooltip-cite-article' => 'Émbaran ngeunaan cara ngarujuk ieu kaca',
	'cite' => 'Cutat',
	'cite_page' => 'Kaca:',
	'cite_submit' => 'Cutat',
);

/** Swedish (Svenska)
 * @author Lejonel
 * @author Per
 * @author Sannab
 */
$messages['sv'] = array(
	'cite_article_desc' => 'Lägger till en specialsida för [[Special:Cite|källhänvisning]] och en länk i verktygslådan',
	'cite_article_link' => 'Citera denna artikel',
	'tooltip-cite-article' => 'Information om hur denna sida kan citeras',
	'cite' => 'Citera',
	'cite_page' => 'Sida:',
	'cite_submit' => 'Citera',
);

/** Swahili (Kiswahili)
 * @author Lloffiwr
 */
$messages['sw'] = array(
	'cite_article_link' => 'Taja ukurasa huu',
	'tooltip-cite-article' => 'Taarifa juu ya njia ya kutaja ukurasa huu',
	'cite' => 'Taja',
	'cite_page' => 'Ukurasa:',
	'cite_submit' => 'Taja',
);

/** Silesian (Ślůnski)
 * @author Herr Kriss
 * @author Timpul
 */
$messages['szl'] = array(
	'cite_article_link' => 'Cytuj ta zajta',
	'cite_page' => 'Zajta:',
);

/** Tamil (தமிழ்)
 * @author TRYPPN
 * @author Trengarasu
 */
$messages['ta'] = array(
	'cite_article_desc' => 'கருவிப் பெட்டியில் [[Special:Cite|மேற்கோள்]] காடுவதற்கான இணைப்பை ஏற்படுத்துகிறது',
	'cite_article_link' => 'இப்பக்க்த்தை மேற்கோள் காட்டு',
	'tooltip-cite-article' => 'இப்பக்கத்தை எப்படி மேற்கோளாகக் காட்டுவது என்பது பற்றிய விவரம்',
	'cite' => 'மேற்கோள் காட்டு',
	'cite_page' => 'பக்கம்:',
	'cite_submit' => 'மேற்கோள் காட்டு',
);

/** Telugu (తెలుగు)
 * @author Mpradeep
 * @author Veeven
 */
$messages['te'] = array(
	'cite_article_desc' => '[[Special:Cite|ఉదహరింపు]] అనే ప్రత్యేక పేజీని & పరికర పెట్టె లింకునీ చేరుస్తుంది',
	'cite_article_link' => 'ఈ వ్యాసాన్ని ఉదహరించండి',
	'tooltip-cite-article' => 'ఈ పేజీని ఎలా ఉదహరించాలి అన్నదానిపై సమాచారం',
	'cite' => 'ఉదహరించు',
	'cite_page' => 'పేజీ:',
	'cite_submit' => 'ఉదహరించు',
);

/** Tetum (Tetun)
 * @author MF-Warburg
 */
$messages['tet'] = array(
	'cite_article_desc' => 'Kria pájina espesíal ba [[Special:Cite|sitasaun]] ho ligasaun iha kaixa besi nian',
	'cite_article_link' => "Sita pájina ne'e",
	'tooltip-cite-article' => "Informasaun kona-ba sita pájina ne'e",
	'cite' => 'Sita',
	'cite_page' => 'Pájina:',
	'cite_submit' => 'Sita',
);

/** Tajik (Cyrillic script) (Тоҷикӣ)
 * @author Ibrahim
 */
$messages['tg-cyrl'] = array(
	'cite_article_desc' => 'Саҳифаи вижае барои [[Special:Cite|ёдкард]] изофа мекунад ва пайванде ба ҷаъбаи абзор меафзояд',
	'cite_article_link' => 'Ёд кардани пайванди ин мақола',
	'cite' => 'Ёд кардани ин мақола',
	'cite_page' => 'Саҳифа:',
	'cite_submit' => 'Ёд кардан',
);

/** Tajik (Latin script) (tojikī)
 * @author Liangent
 */
$messages['tg-latn'] = array(
	'cite_article_desc' => "Sahifai viƶae baroi [[Special:Cite|jodkard]] izofa mekunad va pajvande ba ça'bai abzor meafzojad",
	'cite_article_link' => 'Jod kardani pajvandi in maqola',
	'cite' => 'Jod kardani in maqola',
	'cite_page' => 'Sahifa:',
	'cite_submit' => 'Jod kardan',
);

/** Thai (ไทย)
 * @author Octahedron80
 * @author Passawuth
 */
$messages['th'] = array(
	'cite_article_desc' => 'เพิ่มหน้า[[Special:Cite|อ้างอิง]]พิเศษและลิงก์บนกล่องเครื่องมือ',
	'cite_article_link' => 'อ้างอิงหน้านี้',
	'tooltip-cite-article' => 'ข้อมูลเกี่ยวกับวิธีการอ้างอิงหน้านี้',
	'cite' => 'อ้างอิง',
	'cite_page' => 'หน้า:',
	'cite_submit' => 'อ้างอิง',
);

/** Turkmen (Türkmençe)
 * @author Hanberke
 */
$messages['tk'] = array(
	'cite_article_desc' => '[[Special:Cite|Sitirle]] ýörite sahypasyny we gural sandygy çykgydyny goşýar',
	'cite_article_link' => 'Sahypany sitirle',
	'tooltip-cite-article' => 'Bu sahypany nähili sitirlemelidigi hakda maglumat',
	'cite' => 'Sitirle',
	'cite_page' => 'Sahypa:',
	'cite_submit' => 'Sitirle',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'cite_article_desc' => 'Nagdaragdag ng isang natatanging pahinang [[Special:Cite|pampagtutukoy]] at kawing sa kahon (lalagyan) ng kagamitan',
	'cite_article_link' => 'Tukuyin ang pahinang ito',
	'tooltip-cite-article' => 'Kabatiran kung paano tutukuyin ang pahinang ito',
	'cite' => 'Tukuyin',
	'cite_page' => 'Pahina:',
	'cite_submit' => 'Tukuyin',
);

/** Tswana (Setswana) */
$messages['tn'] = array(
	'cite_article_link' => 'Nopola mokwalo o',
);

/** Tongan (lea faka-Tonga) */
$messages['to'] = array(
	'cite_article_link' => 'Lau ki he kupú ni',
	'cite' => 'Lau ki he',
);

/** Turkish (Türkçe)
 * @author Erkan Yilmaz
 * @author Joseph
 * @author Srhat
 * @author Uğur Başak
 */
$messages['tr'] = array(
	'cite_article_desc' => '[[Special:Cite|Alıntı]] özel sayfa ve araç kutusu linkini ekler',
	'cite_article_link' => 'Sayfayı kaynak göster',
	'tooltip-cite-article' => 'Bu sayfanın nasıl alıntı yapılacağı hakkında bilgi',
	'cite' => 'Kaynak göster',
	'cite_page' => 'Sayfa:',
	'cite_submit' => 'Belirt',
);

/** Tsonga (Xitsonga)
 * @author Thuvack
 */
$messages['ts'] = array(
	'cite_page' => 'Tluka:',
);

/** Tatar (Cyrillic script) (Татарча)
 * @author Ильнар
 */
$messages['tt-cyrl'] = array(
	'cite_article_desc' => 'Махсус [[Special:Cite|күчермәләү]] битен һәм җиһазларга сылтамалар өсти',
	'cite_article_link' => 'Бу битне күчермәләү',
	'tooltip-cite-article' => 'Бу битне ничек күчермәләү турындагы мәгълүмат',
	'cite' => 'Күчермәләү',
	'cite_page' => 'Бит:',
	'cite_submit' => 'Күчермәләү',
);

/** Udmurt (Удмурт)
 * @author ОйЛ
 */
$messages['udm'] = array(
	'cite_article_link' => 'Кызьы со статьяез цитировать кароно',
);

/** Uyghur (Latin script) (Uyghurche‎)
 * @author Jose77
 */
$messages['ug-latn'] = array(
	'cite_article_link' => 'Bu maqalini ishliting',
	'cite_page' => 'Bet:',
);

/** Ukrainian (Українська)
 * @author Ahonc
 * @author Prima klasy4na
 */
$messages['uk'] = array(
	'cite_article_desc' => 'Додає спеціальну сторінку [[Special:Cite|цитування]] і посилання в інструментах',
	'cite_article_link' => 'Цитувати сторінку',
	'tooltip-cite-article' => 'Інформація про те, як цитувати цю сторінку',
	'cite' => 'Цитування',
	'cite_page' => 'Сторінка:',
	'cite_submit' => 'Процитувати',
);

/** Urdu (اردو) */
$messages['ur'] = array(
	'cite_article_link' => 'مضمون کا حوالہ دیں',
	'cite' => 'حوالہ',
	'cite_page' => 'صفحہ:',
);

/** Vèneto (Vèneto)
 * @author Candalua
 */
$messages['vec'] = array(
	'cite_article_desc' => 'Zonta na pagina speciale par le [[Special:Cite|citazion]] e un colegamento nei strumenti',
	'cite_article_link' => 'Cita sta pagina',
	'tooltip-cite-article' => 'Informassion su come citar sta pagina',
	'cite' => 'Citazion',
	'cite_page' => 'Pagina da citar:',
	'cite_submit' => 'Crea la citazion',
);

/** Veps (Vepsän kel’)
 * @author Triple-ADHD-AS
 * @author Игорь Бродский
 */
$messages['vep'] = array(
	'cite_article_desc' => 'Ližadab [[Special:Cite|citiruindan]] specialižen lehtpolen da kosketusen azegištos',
	'cite_article_link' => "Citiruida nece lehtpol'",
	'tooltip-cite-article' => "Informacii siš, kut pidab citiruida nece lehtpol'.",
	'cite' => 'Citiruind',
	'cite_page' => 'Lehtpol’:',
	'cite_submit' => 'Citiruida',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'cite_article_desc' => 'Thêm trang đặc biệt để [[Special:Cite|trích dẫn bài viết]] và đặt liên kết trong thanh công cụ',
	'cite_article_link' => 'Trích dẫn trang này',
	'tooltip-cite-article' => 'Hướng dẫn cách trích dẫn trang này',
	'cite' => 'Trích dẫn',
	'cite_page' => 'Trang:',
	'cite_submit' => 'Trích dẫn',
);

/** Volapük (Volapük)
 * @author Malafaya
 * @author Smeira
 */
$messages['vo'] = array(
	'cite_article_desc' => 'Läükon padi patik [[Special:Cite|saitama]] sa yüm ad stumem',
	'cite_article_link' => 'Saitön padi at',
	'cite' => 'Saitön',
	'cite_page' => 'Pad:',
	'cite_submit' => 'Saitön',
);

/** Walloon (Walon)
 * @author Srtxg
 */
$messages['wa'] = array(
	'cite_page' => 'Pådje:',
);

/** Wu (吴语) */
$messages['wuu'] = array(
	'cite_article_link' => '引用该篇文章',
	'cite' => '引用',
	'cite_page' => '页面:',
	'cite_submit' => '引用',
);

/** Kalmyk (Хальмг)
 * @author Huuchin
 */
$messages['xal'] = array(
	'cite_article_link' => 'Тер халхиг эшллх',
);

/** Yiddish (ייִדיש)
 * @author פוילישער
 */
$messages['yi'] = array(
	'cite_article_desc' => 'לייגט צו א [[Special:Cite|ציטיר]] באַזונדערן בלאַט און געצייגקאַסן לינק',
	'cite_article_link' => 'ציטירן דעם דאזיגן בלאט',
	'tooltip-cite-article' => 'אינפֿאָרמאַציע ווי אַזוי צו ציטירן דעם בלאַט',
	'cite' => 'ציטירן',
	'cite_page' => 'בלאט:',
	'cite_submit' => 'ציטירן',
);

/** Yoruba (Yorùbá)
 * @author Demmy
 */
$messages['yo'] = array(
	'cite_page' => 'Ojúewé:',
);

/** Cantonese (粵語) */
$messages['yue'] = array(
	'cite_article_desc' => '加一個[[Special:Cite|引用]]特別頁同埋一個工具箱連結',
	'cite_article_link' => '引用呢篇文',
	'cite' => '引用文章',
	'cite_page' => '版：',
	'cite_submit' => '引用',
);

/** Simplified Chinese (‪中文(简体)‬)
 * @author Xiaomingyan
 */
$messages['zh-hans'] = array(
	'cite_article_desc' => '增加[[Special:Cite|引用]]特殊页面以及工具箱链接',
	'cite_article_link' => '引用此文',
	'tooltip-cite-article' => '关于如何引用此页的资讯',
	'cite' => '引用页面',
	'cite_page' => '页面：',
	'cite_submit' => '引用',
	'cite_text' => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

== {{FULLPAGENAME}}的文献详细信息 ==

* 页面名称：{{FULLPAGENAME}}
* 作者：{{SITENAME}}编者
* 出版者：{{SITENAME}}，{{int:sitesubtitle}}．
* 最新版本日期：{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}（协调世界时）
* 查阅日期：<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}（协调世界时）</citation>
* 永久链接：{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* 页面版本号：{{REVISIONID}}

</div>
<div class=\"plainlinks mw-specialcite-styles\">

== {{FULLPAGENAME}}的参考文献格式 ==

=== GB7714格式 ===
{{SITENAME}}编者．{{FULLPAGENAME}}[G/OL]．{{SITENAME}}，{{int:sitesubtitle}}，{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}［<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}</citation>］．{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}．

=== APA格式 ===
{{FULLPAGENAME}}．（{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日）．''{{SITENAME}}，{{int:sitesubtitle}}''．于<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}</citation>查阅自{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}．

=== MLA格式 ===
“{{FULLPAGENAME}}．”''{{SITENAME}}，{{int:sitesubtitle}}''．{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}（协调世界时）．<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;．

=== MHRA格式 ===
{{SITENAME}}编者，‘{{FULLPAGENAME}}’，''{{SITENAME}}，{{int:sitesubtitle}}''，{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}（协调世界时），&lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;［于<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日</citation>查阅］

=== 芝加哥格式 ===
{{SITENAME}}编者，“{{FULLPAGENAME}}，”''{{SITENAME}}，{{int:sitesubtitle}}''，{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}（于<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日</citation>查阅）．

=== CBE/CSE格式 ===
{{SITENAME}}编者．{{FULLPAGENAME}}［互联网］．{{SITENAME}}，{{int:sitesubtitle}}；{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}（协调世界时）［引用于<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日</citation>］．可访问自：
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}．

=== Bluebook格式 ===
{{FULLPAGENAME}}，{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}（最新访问于<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日</citation>）．

=== BibTeX记录 ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[在线资源；访问于<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日</citation>]\"
  }

使用LaTeX包装的链接（开头某处的<code>\usepackage{url}</code>）将提供更好的网址格式，推荐选用下列格式：
  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[在线资源；访问于<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日</citation>]\"
  }


</div> <!--closing div for \"plainlinks\"-->"
);

/** Traditional Chinese (‪中文(繁體)‬)
 * @author Frankou
 */
$messages['zh-hant'] = array(
	'cite_article_desc' => '增加[[Special:Cite|引用]]特殊頁面以及工具箱連結',
	'cite_article_link' => '引用此文',
	'tooltip-cite-article' => '關於如何引用此頁的資訊',
	'cite' => '引用文章',
	'cite_page' => '頁面：',
	'cite_submit' => '引用',
	'cite_text' => "__NOTOC__
<div class=\"mw-specialcite-bibliographic\">

== {{FULLPAGENAME}}的文獻詳細資訊 ==

* 頁面名稱：{{FULLPAGENAME}}
* 作者：{{SITENAME}}編者
* 出版者：{{SITENAME}}，{{int:sitesubtitle}}．
* 最新版本日期：{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}（協調世界時）
* 查閲日期：<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}（協調世界時）</citation>
* 永久連結：{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}
* 頁面版本號：{{REVISIONID}}

</div>
<div class=\"plainlinks mw-specialcite-styles\">

== {{FULLPAGENAME}}的參考文獻格式 ==

=== GB7714格式 ===
{{SITENAME}}編者．{{FULLPAGENAME}}[G/OL]．{{SITENAME}}，{{int:sitesubtitle}}，{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}［<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}</citation>］．{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}．

=== APA格式 ===
{{FULLPAGENAME}}．（{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日）．''{{SITENAME}}，{{int:sitesubtitle}}''．於<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}</citation>查閲自{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}．

=== MLA格式 ===
「{{FULLPAGENAME}}」．''{{SITENAME}}，{{int:sitesubtitle}}''．{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}（協調世界時）．<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}</citation> &lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;．

=== MHRA格式 ===
{{SITENAME}}編者，『{{FULLPAGENAME}}』，''{{SITENAME}}，{{int:sitesubtitle}}''，{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}（協調世界時），&lt;{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}&gt;［於<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日</citation>查閲］

=== 芝加哥格式 ===
{{SITENAME}}編者，「{{FULLPAGENAME}}」，''{{SITENAME}}，{{int:sitesubtitle}}''，{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}（於<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日</citation>查閲）．

=== CBE/CSE格式 ===
{{SITENAME}}編者．{{FULLPAGENAME}}［網際網絡］．{{SITENAME}}，{{int:sitesubtitle}}；{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日{{CURRENTTIME}}（協調世界時）［引用於<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日</citation>］．可訪問自：
{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}．

=== Bluebook格式 ===
{{FULLPAGENAME}}，{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}（最新訪問於<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日</citation>）．

=== BibTeX記錄 ===

  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}\",
    note = \"[線上資源；訪問於<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日</citation>]\"
  }

使用LaTeX包裝的連結（開頭某處的<code>\usepackage{url}</code>）將提供更好的網址格式，推薦選用下列格式：
  @misc{ wiki:xxx,
    author = \"{{SITENAME}}\",
    title = \"{{FULLPAGENAME}} --- {{SITENAME}}{,} {{int:sitesubtitle}}\",
    year = \"{{CURRENTYEAR}}\",
    url = \"'''\url{'''{{canonicalurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'''}'''\",
    note = \"[線上資源；訪問於<citation>{{CURRENTYEAR}}年{{CURRENTMONTH}}月{{CURRENTDAY}}日</citation>]\"
  }


</div> <!--closing div for \"plainlinks\"-->"
);

