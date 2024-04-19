<?php

namespace Cite\Tests\Integration\ReferencePreviews;

use Cite\ReferencePreviews\ReferencePreviewsGadgetsIntegration;
use InvalidArgumentException;
use MediaWiki\Config\Config;
use MediaWiki\Extension\Gadgets\Gadget;
use MediaWiki\Extension\Gadgets\GadgetRepo;
use MediaWiki\User\User;
use MediaWikiIntegrationTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Cite\ReferencePreviews\ReferencePreviewsGadgetsIntegration
 * @license GPL-2.0-or-later
 */
class ReferencePreviewsGadgetsIntegrationTest extends MediaWikiIntegrationTestCase {
	/**
	 * Gadget name for testing
	 */
	private const NAV_POPUPS_GADGET_NAME = 'navigation-test';

	/**
	 * Helper constants for easier reading
	 */
	private const GADGET_ENABLED = true;

	/**
	 * Helper constants for easier reading
	 */
	private const GADGET_DISABLED = false;

	/**
	 * @return MockObject|Config
	 */
	private function getConfigMock() {
		$mock = $this->createMock( Config::class );
		$mock->expects( $this->atLeastOnce() )
			->method( 'get' )
			->willReturnMap( [
				[ ReferencePreviewsGadgetsIntegration::CONFIG_NAVIGATION_POPUPS_NAME, self::NAV_POPUPS_GADGET_NAME ],
				[ ReferencePreviewsGadgetsIntegration::CONFIG_REFERENCE_TOOLTIPS_NAME, self::NAV_POPUPS_GADGET_NAME ],
			] );
		return $mock;
	}

	/**
	 * @covers ::isNavPopupsGadgetEnabled
	 * @covers ::__construct
	 * @covers ::sanitizeGadgetName
	 */
	public function testConflictsWithNavPopupsGadgetIfGadgetsExtensionIsNotLoaded() {
		$user = $this->createMock( User::class );
		$integration = new ReferencePreviewsGadgetsIntegration( $this->getConfigMock() );
		$this->assertFalse(
			$integration->isNavPopupsGadgetEnabled( $user ),
			'No conflict is identified.' );
	}

	/**
	 * @covers ::isNavPopupsGadgetEnabled
	 */
	public function testConflictsWithNavPopupsGadgetIfGadgetNotExists() {
		$user = $this->createMock( User::class );

		$gadgetRepoMock = $this->createMock( GadgetRepo::class );
		$gadgetRepoMock->expects( $this->once() )
			->method( 'getGadgetIds' )
			->willReturn( [] );

		$this->executeConflictsWithNavPopupsGadgetSafeCheck( $user, $this->getConfigMock(),
			$gadgetRepoMock, self::GADGET_DISABLED );
	}

	/**
	 * @covers ::isNavPopupsGadgetEnabled
	 */
	public function testConflictsWithNavPopupsGadgetIfGadgetExists() {
		$user = $this->createMock( User::class );

		$gadgetMock = $this->createMock( Gadget::class );
		$gadgetMock->expects( $this->once() )
			->method( 'isEnabled' )
			->with( $user )
			->willReturn( self::GADGET_ENABLED );

		$gadgetRepoMock = $this->createMock( GadgetRepo::class );
		$gadgetRepoMock->expects( $this->once() )
			->method( 'getGadgetIds' )
			->willReturn( [ self::NAV_POPUPS_GADGET_NAME ] );
		$gadgetRepoMock->expects( $this->once() )
			->method( 'getGadget' )
			->with( self::NAV_POPUPS_GADGET_NAME )
			->willReturn( $gadgetMock );

		$this->executeConflictsWithNavPopupsGadgetSafeCheck( $user, $this->getConfigMock(),
			$gadgetRepoMock, self::GADGET_ENABLED );
	}

	/**
	 * Test the edge case when GadgetsRepo::getGadget throws an exception
	 * @covers ::isNavPopupsGadgetEnabled
	 */
	public function testConflictsWithNavPopupsGadgetWhenGadgetNotExists() {
		$user = $this->createMock( User::class );

		$gadgetRepoMock = $this->createMock( GadgetRepo::class );
		$gadgetRepoMock->expects( $this->once() )
			->method( 'getGadgetIds' )
			->willReturn( [ self::NAV_POPUPS_GADGET_NAME ] );
		$gadgetRepoMock->expects( $this->once() )
			->method( 'getGadget' )
			->with( self::NAV_POPUPS_GADGET_NAME )
			->willThrowException( new InvalidArgumentException() );

		$this->executeConflictsWithNavPopupsGadgetSafeCheck( $user, $this->getConfigMock(),
			$gadgetRepoMock, self::GADGET_DISABLED );
	}

	/**
	 * @covers ::sanitizeGadgetName
	 * @dataProvider provideGadgetNamesWithSanitizedVersion
	 */
	public function testConflictsWithNavPopupsGadgetNameSanitization( $name, $sanitized ) {
		$user = $this->createMock( User::class );

		$configMock = $this->createMock( Config::class );
		$configMock->expects( $this->atLeastOnce() )
			->method( 'get' )
			->willReturnMap( [
				[ ReferencePreviewsGadgetsIntegration::CONFIG_NAVIGATION_POPUPS_NAME, $name ],
				[ ReferencePreviewsGadgetsIntegration::CONFIG_REFERENCE_TOOLTIPS_NAME, $name ]
			] );

		$gadgetMock = $this->createMock( Gadget::class );
		$gadgetMock->expects( $this->once() )
			->method( 'isEnabled' )
			->willReturn( self::GADGET_ENABLED );

		$gadgetRepoMock = $this->createMock( GadgetRepo::class );
		$gadgetRepoMock->expects( $this->once() )
			->method( 'getGadgetIds' )
			->willReturn( [ $sanitized ] );
		$gadgetRepoMock->expects( $this->once() )
			->method( 'getGadget' )
			->with( $sanitized )
			->willReturn( $gadgetMock );

		$this->executeConflictsWithNavPopupsGadgetSafeCheck( $user, $configMock, $gadgetRepoMock,
			self::GADGET_ENABLED );
	}

	public static function provideGadgetNamesWithSanitizedVersion() {
		return [
			[ ' Popups ', 'Popups' ],
			[ 'Navigation_popups-API', 'Navigation_popups-API' ],
			[ 'Navigation popups ', 'Navigation_popups' ]
		];
	}

	/**
	 * Execute test and restore GadgetRepo
	 *
	 * @param User $user
	 * @param Config $config
	 * @param GadgetRepo $repoMock
	 * @param bool $expected
	 */
	private function executeConflictsWithNavPopupsGadgetSafeCheck(
		User $user,
		Config $config,
		GadgetRepo $repoMock,
		$expected
	) {
		$this->setService( 'GadgetsRepo', $repoMock );

		$integration = new ReferencePreviewsGadgetsIntegration( $config );
		$this->assertSame( $expected,
			$integration->isNavPopupsGadgetEnabled( $user ),
			( $expected ? 'A' : 'No' ) . ' conflict is identified.' );
	}
}
