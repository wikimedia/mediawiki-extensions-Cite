<?php

namespace Cite\Tests\Unit;

use ApiMain;
use ApiQuery;
use ApiQueryReferences;
use IContextSource;
use Title;
use Wikimedia\AtEase\AtEase;
use Wikimedia\Rdbms\IDatabase;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \ApiQueryReferences
 */
class ApiQueryReferencesTest extends \MediaWikiUnitTestCase {

	/**
	 * @covers ::__construct
	 * @covers ::getAllowedParams
	 * @covers ::getCacheMode
	 * @covers ::getExamplesMessages
	 */
	public function testBasics() {
		$api = $this->newApiQueryReferences();
		$this->assertInternalType( 'array', $api->getAllowedParams() );
		$this->assertSame( 'public', $api->getCacheMode( [] ) );
		$this->assertContainsOnly( 'string', $api->getExamplesMessages() );
	}

	/**
	 * @covers ::getStoredReferences
	 */
	public function testGetStoredReferences_storeDisabled() {
		$api = $this->newApiQueryReferences();
		$title = $this->createMock( Title::class );
		$this->assertFalse( $api->getStoredReferences( $title ) );
	}

	/**
	 * @covers ::recursiveFetchRefsFromDB
	 */
	public function testRecursiveFetchRefsFromDB_fails() {
		$api = $this->newApiQueryReferences();
		$title = $this->createMock( Title::class );

		$dbr = $this->createMock( IDatabase::class );
		$dbr->method( 'selectField' )
			->willReturn( false );

		$this->assertFalse( $api->recursiveFetchRefsFromDB( $title, $dbr ) );
	}

	/**
	 * @covers ::recursiveFetchRefsFromDB
	 */
	public function testRecursiveFetchRefsFromDB_firstTry() {
		$api = $this->newApiQueryReferences();
		$title = $this->createMock( Title::class );

		$dbr = $this->createMock( IDatabase::class );
		$dbr->method( 'selectField' )
			->willReturn( gzencode( '{"refs":{}}' ) );

		$this->assertSame( [ 'refs' => [] ], $api->recursiveFetchRefsFromDB( $title, $dbr ) );
	}

	/**
	 * @covers ::recursiveFetchRefsFromDB
	 */
	public function testRecursiveFetchRefsFromDB_secondTry() {
		$api = $this->newApiQueryReferences();
		$title = $this->createMock( Title::class );

		$dbr = $this->createMock( IDatabase::class );
		$dbr->expects( $this->exactly( 2 ) )
			->method( 'selectField' )
			->willReturnOnConsecutiveCalls( '', gzencode( '{"refs":{}}' ) );

		// Code relies on gzdecode() returning false, but that reports an error now
		AtEase::suppressWarnings();
		$refs = $api->recursiveFetchRefsFromDB( $title, $dbr );
		AtEase::restoreWarnings();

		$this->assertSame( [ 'refs' => [] ], $refs );
	}

	/**
	 * @return ApiQueryReferences
	 */
	private function newApiQueryReferences() {
		$main = $this->createMock( ApiMain::class );
		$main->method( 'getContext' )
			->willReturn( $this->createMock( IContextSource::class ) );

		$query = $this->createMock( ApiQuery::class );
		$query->method( 'getMain' )
			->willReturn( $main );

		$api = new ApiQueryReferences( $query, '' );
		return TestingAccessWrapper::newFromObject( $api );
	}

}
