<?php
declare( strict_types = 1 );

namespace Cite\Parsoid;

use Wikimedia\Parsoid\DOM\Element;

/**
 * Individual item in a {@see RefGroup}.
 *
 * @license GPL-2.0-or-later
 */
class RefGroupItem {

	/**
	 * Pointer to the contents of the ref, accessible with the
	 * {@see ParsoidExtensionAPI::getContentDOM}, to be used when serializing the references group.
	 * It gets set when extracting the ref from a node and not $missingContent.  Note that that
	 * might not be the first one for named refs.  Also, for named refs, it's used to detect
	 * multiple conflicting definitions.
	 */
	public ?string $contentId = null;

	/** Just used for comparison when we have multiples */
	public ?string $cachedHtml = null;

	public string $dir = '';
	/**
	 * Name of the group (or empty for the default group) which this <ref> belongs to.
	 */
	public string $group = '';
	/**
	 * Sequence number per {@see $group}, starting from 1. To be used in the footnote marker,
	 * e.g. "[1]".
	 */
	public int $numberInGroup = 1;
	/**
	 * Global, unique sequence number for each <ref>, no matter which group, starting from 0.
	 * Currently unused.
	 */
	public int $index = 0;
	/**
	 * Same as {@see id}, but without the "-0" suffix
	 */
	public string $key;
	/**
	 * The clickable href="#…" backlink and id="…" target to jump from the reference list back up
	 * to the corresponding footnote marker
	 */
	public string $id;
	/** @var array<int,string> */
	public array $linkbacks = [];
	/**
	 * The original name="…" attribute of a <ref>, or empty for anonymous references.
	 */
	public string $name = '';
	/**
	 * The clickable href="#…" link and id="…" target to jump down from a footnote marker to the
	 * item in the reference list
	 */
	public string $target;

	/** @var Element[] */
	public array $nodes = [];

	/** @var string[] */
	public array $embeddedNodes = [];

}
