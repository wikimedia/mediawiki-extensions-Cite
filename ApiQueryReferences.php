<?php

/**
 * Expose reference information for a page via prop=references API.
 *
 * @see https://www.mediawiki.org/wiki/Extension:Cite#API
 *
 * @license WTFPL 2.0
 */
class ApiQueryReferences extends ApiQueryBase {

	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'rf' );
	}

	public function getAllowedParams() {
		return [
		   'continue' => [
		       ApiBase::PARAM_HELP_MSG => 'api-help-param-continue',
		   ],
		];
	}

	public function execute() {
		$config = ConfigFactory::getDefaultInstance()->makeConfig( 'cite' );
		if ( !$config->get( 'CiteStoreReferencesData' ) ) {
			$this->dieUsage( 'Cite extension reference storage is not enabled', 'citestoragedisabled' );
		}
		$params = $this->extractRequestParams();
		$titles = $this->getPageSet()->getGoodTitles();
		ksort( $titles );
		if ( !is_null( $params['continue'] ) ) {
			$startId = (int)$params['continue'];
			// check it is definitely an int
			$this->dieContinueUsageIf( strval( $startId ) !== $params['continue'] );
		} else {
			$startId = false;
		}

		foreach ( $titles as $pageId => $title ) {
			// Skip until you have the correct starting point
			if ( $startId !== false && $startId !== $pageId ) {
				continue;
			} else {
				$startId = false;
			}
			$storedRefs = Cite::getStoredReferences( $title );
			$allReferences = array();
			// some pages may not have references stored
			if ( $storedRefs !== false ) {
				// a page can have multiple <references> tags but they all have unique keys
				foreach ( $storedRefs['refs'] as $index => $grouping ) {
					foreach ( $grouping as $group => $members ) {
						foreach ( $members as $name => $ref ) {
							$ref['name'] = $name;
							$key = $ref['key'];
							if ( is_string( $name ) ) {
								$id = Cite::getReferencesKey( $name . '-' . $key );
							} else {
								$id = Cite::getReferencesKey( $key );
							}
							$ref['group'] = $group;
							$ref['reflist'] = $index;
							$allReferences[$id] = $ref;
						}
					}
				}
			}
			// set some metadata since its an assoc data structure
			ApiResult::setArrayType( $allReferences, 'kvp', 'id' );
			// Ship a data representation of the combined references.
			$fit = $this->addPageSubItems( $pageId, $allReferences );
			if ( !$fit ) {
				$this->setContinueEnumParameter( 'continue', $pageId );
				break;
			}
		}
	}

	public function getCacheMode( $params ) {
		return 'public';
	}

	/**
	 * @see ApiBase::getExamplesMessages()
	 */
	protected function getExamplesMessages() {
		return array(
			'action=query&prop=references&titles=Albert%20Einstein' =>
				'apihelp-query+references-example-1',
		);
	}

}
