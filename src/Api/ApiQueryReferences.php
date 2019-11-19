<?php
/**
 * Expose reference information for a page via prop=references API.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @see https://www.mediawiki.org/wiki/Extension:Cite#API
 */

namespace Cite\Api;

use ApiBase;
use ApiQuery;
use ApiResult;
use Cite\Cite;
use MediaWiki\MediaWikiServices;
use Wikimedia\Rdbms\Database;
use Wikimedia\Rdbms\IDatabase;

class ApiQueryReferences extends \ApiQueryBase {

	/**
	 * Cache duration when fetching references from the database, in seconds. 18,000 seconds = 5
	 * hours.
	 */
	private const CACHE_DURATION_ONFETCH = 18000;

	/**
	 * @param ApiQuery $query
	 * @param string $moduleName
	 */
	public function __construct( ApiQuery $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'rf' );
	}

	/**
	 * @inheritDoc
	 */
	public function getAllowedParams() {
		return [
		   'continue' => [
			   ApiBase::PARAM_HELP_MSG => 'api-help-param-continue',
		   ],
		];
	}

	public function execute() {
		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'cite' );
		if ( !$config->get( 'CiteStoreReferencesData' ) ) {
			$this->dieWithError( 'apierror-citestoragedisabled' );
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
			$storedRefs = $this->getStoredReferences( $pageId );
			$allReferences = [];
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

	/**
	 * Fetch references stored for the given title in page_props
	 * For performance, results are cached
	 *
	 * @param int $pageId
	 * @return array|false
	 */
	private function getStoredReferences( $pageId ) {
		global $wgCiteStoreReferencesData;
		if ( !$wgCiteStoreReferencesData ) {
			return false;
		}

		$cache = MediaWikiServices::getInstance()->getMainWANObjectCache();
		$key = $cache->makeKey( Cite::EXT_DATA_KEY, $pageId );
		return $cache->getWithSetCallback(
			$key,
			self::CACHE_DURATION_ONFETCH,
			function ( $oldValue, &$ttl, array &$setOpts ) use ( $pageId ) {
				$dbr = wfGetDB( DB_REPLICA );
				$setOpts += Database::getCacheSetOptions( $dbr );
				return $this->recursiveFetchRefsFromDB( $pageId, $dbr );
			},
			[
				'checkKeys' => [ $key ],
				'lockTSE' => 30,
			]
		);
	}

	/**
	 * Reconstructs compressed json by successively retrieving the properties references-1, -2, etc
	 * It attempts the next step when a decoding error occurs.
	 * Returns json_decoded uncompressed string, with validation of json
	 *
	 * @param int $pageId
	 * @param IDatabase $dbr
	 * @param string $string
	 * @param int $i
	 * @return array|false
	 */
	private function recursiveFetchRefsFromDB(
		$pageId,
		IDatabase $dbr,
		$string = '',
		$i = 1
	) {
		$result = $dbr->selectField(
			'page_props',
			'pp_value',
			[
				'pp_page' => $pageId,
				'pp_propname' => 'references-' . $i
			],
			__METHOD__
		);
		if ( $result === false ) {
			// no refs stored in page_props at this index
			if ( $i > 1 ) {
				// shouldn't happen
				wfDebug( "Failed to retrieve stored references for title id $pageId" );
			}
			return false;
		}

		$string .= $result;
		$decodedString = gzdecode( $string );
		if ( $decodedString !== false ) {
			$json = json_decode( $decodedString, true );
			if ( json_last_error() === JSON_ERROR_NONE ) {
				return $json;
			}
			// corrupted json ?
			// shouldn't happen since when string is truncated, gzdecode should fail
			wfDebug( "Corrupted json detected when retrieving stored references for title id $pageId" );
		}
		// if gzdecode fails, try to fetch next references- property value
		return $this->recursiveFetchRefsFromDB( $pageId, $dbr, $string, ++$i );
	}

	/**
	 * Get the cache mode for the data generated by this module.
	 *
	 * @param array $params
	 * @return string
	 */
	public function getCacheMode( $params ) {
		return 'public';
	}

	/**
	 * @inheritDoc
	 */
	protected function getExamplesMessages() {
		return [
			'action=query&prop=references&titles=Albert%20Einstein' =>
				'apihelp-query+references-example-1',
		];
	}

}
