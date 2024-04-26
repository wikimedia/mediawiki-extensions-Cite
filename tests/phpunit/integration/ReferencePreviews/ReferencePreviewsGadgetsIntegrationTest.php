<?php

namespace Cite\Tests\Integration\ReferencePreviews;

use Cite\ReferencePreviews\ReferencePreviewsGadgetsIntegration;
use InvalidArgumentException;
use MediaWiki\Config\Config;
use MediaWiki\Config\HashConfig;
use MediaWiki\Extension\Gadgets\Gadget;
use MediaWiki\Extension\Gadgets\GadgetRepo;
use MediaWiki\User\User;
use MediaWikiIntegrationTestCase;

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

	private function getConfig( ?string $gadgetName = self::NAV_POPUPS_GADGET_NAME ): Config {
		return new HashConfig( [
			ReferencePreviewsGadgetsIntegration::CONFIG_NAVIGATION_POPUPS_NAME => $gadgetName,
			ReferencePreviewsGadgetsIntegration::CONFIG_REFERENCE_TOOLTIPS_NAME => $gadgetName,
		] );
	}

	/**
	 * @covers ::isNavPopupsGadgetEnabled
	 * @covers ::__construct
	 * @covers ::sanitizeGadgetName
	 */
	public function testConflictsWithNavPopupsGadgetIfGadgetsExtensionIsNotLoaded() {
		$integration = new ReferencePreviewsGadgetsIntegration( $this->getConfig() );
		$this->assertFalse(
			$integration->isNavPopupsGadgetEnabled( $this->createNoOpMock( User::class ) ),
			'No conflict is identified.'
		);
	}

	/**
	 * @covers ::isNavPopupsGadgetEnabled
	 */
	public function testConflictsWithNavPopupsGadgetIfGadgetNotExists() {
		$gadgetRepoMock = $this->createMock( GadgetRepo::class );
		$gadgetRepoMock->expects( $this->once() )
			->method( 'getGadgetIds' )
			->willReturn( [] );

		$this->executeIsNavPopupsGadgetEnabled(
			$this->createNoOpMock( User::class ),
			$this->getConfig(),
			$gadgetRepoMock,
			self::GADGET_DISABLED
		);
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

		$this->executeIsNavPopupsGadgetEnabled(
			$user,
			$this->getConfig(),
			$gadgetRepoMock,
			self::GADGET_ENABLED
		);
	}

	/**
	 * Test the edge case when GadgetsRepo::getGadget throws an exception
	 * @covers ::isNavPopupsGadgetEnabled
	 */
	public function testConflictsWithNavPopupsGadgetWhenGadgetNotExists() {
		$gadgetRepoMock = $this->createMock( GadgetRepo::class );
		$gadgetRepoMock->expects( $this->once() )
			->method( 'getGadgetIds' )
			->willReturn( [ self::NAV_POPUPS_GADGET_NAME ] );
		$gadgetRepoMock->expects( $this->once() )
			->method( 'getGadget' )
			->with( self::NAV_POPUPS_GADGET_NAME )
			->willThrowException( new InvalidArgumentException() );

		$this->executeIsNavPopupsGadgetEnabled(
			$this->createNoOpMock( User::class ),
			$this->getConfig(),
			$gadgetRepoMock,
			self::GADGET_DISABLED
		);
	}

	/**
	 * @covers ::sanitizeGadgetName
	 * @dataProvider provideGadgetNamesWithSanitizedVersion
	 */
	public function testConflictsWithNavPopupsGadgetNameSanitization( string $gadgetName, string $sanitized ) {
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

		$this->executeIsNavPopupsGadgetEnabled(
			$this->createNoOpMock( User::class ),
			$this->getConfig( $gadgetName ),
			$gadgetRepoMock,
			self::GADGET_ENABLED
		);
	}

	public static function provideGadgetNamesWithSanitizedVersion() {
		yield [ ' Popups ', 'Popups' ];
		yield [ 'Navigation_popups-API', 'Navigation_popups-API' ];
		yield [ 'Navigation popups ', 'Navigation_popups' ];
	}

	private function executeIsNavPopupsGadgetEnabled(
		User $user,
		Config $config,
		GadgetRepo $repoMock,
		bool $expected
	): void {
		$this->setService( 'GadgetsRepo', $repoMock );

		$integration = new ReferencePreviewsGadgetsIntegration( $config );
		$this->assertSame(
			$expected,
			$integration->isNavPopupsGadgetEnabled( $user ),
			( $expected ? 'A' : 'No' ) . ' conflict is identified.'
		);
	}
}
