<?php

namespace Cite;

/**
 * Internal data container for a single <ref> tag and all information about it.
 *
 * @license GPL-2.0-or-later
 */
class ReferenceStackItem {
	/**
	 * @var int How often a reference footnote mark appears.  Can be 0 in the case
	 * of not-yet-used or unused list-defined references, or sub-ref parents.
	 */
	public int $count = 0;
	/**
	 * @var ?string Direction of the text. Should either be "ltr", "rtl" or null
	 * if unspecified.
	 */
	public ?string $dir = null;
	/**
	 * @var ?string Marks a "follow" ref which continues the ref text from a
	 * previous page, e.g. in the Page:… namespace on Wikisource.
	 */
	public ?string $follow = null;
	/**
	 * @var string Name of the group (or empty for the default group) which this ref
	 * belongs to.
	 */
	public string $group;
	/**
	 * @var int Sequence number for all references, no matter which group, starting
	 * from 1. Used to generate IDs and anchors.
	 */
	public int $key;
	/**
	 * @var ?string The original name attribute of a reference, or null for anonymous
	 * references.
	 */
	public ?string $name = null;
	/**
	 * Sequence number per {@see $group}, starting from 1. To be used in the footnote marker,
	 * e.g. "[1]". Potentially unset when {@see $follow} is used.
	 */
	public ?int $numberInGroup;
	/**
	 * @var ?string The content inside the <ref>…</ref> tag. Null for a
	 * self-closing <ref /> without content. Also null for <ref></ref> without any
	 * non-whitespace content.
	 */
	public ?string $text = null;
	/**
	 * @var ?int Subreference pointer to parent `$key`, or null for top-level refs.
	 */
	public ?int $parentRefKey = null;
	/**
	 * @var ?int Count how many subreferences point to a parent.  Corresponds to
	 *   the last {@see subrefIndex} but this field belongs to the parent.
	 */
	public ?int $subrefCount = null;
	/**
	 * @var ?int Sequence number for sub-references with the same details
	 * attribute, starting from 1. {@see $numberInGroup} and this details index are
	 * combined to render a footnote marker like "[1.1]".
	 */
	public ?int $subrefIndex = null;
	/**
	 * @var array Error messages attached to this reference.
	 */
	public array $warnings = [];
}
