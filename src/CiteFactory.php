<?php

namespace Cite;

use MediaWiki\Config\Config;
use MediaWiki\Extension\CommunityConfiguration\Provider\ConfigurationProviderFactory;
use MediaWiki\Parser\Parser;

/**
 * @license GPL-2.0-or-later
 */
class CiteFactory {
	public function __construct(
		private readonly Config $config,
		private readonly AlphabetsProvider $alphabetsProvider,
		private readonly ?ConfigurationProviderFactory $configurationProviderFactory,
	) {
	}

	public function newCite( Parser $parser ): Cite {
		return new Cite(
			$parser,
			$this->config,
			$this->alphabetsProvider,
			$this->configurationProviderFactory,
		);
	}
}
