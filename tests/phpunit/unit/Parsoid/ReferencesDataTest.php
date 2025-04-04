<?php

namespace Cite\Tests\Unit;

use Cite\Cite;
use Cite\Parsoid\ReferencesData;
use Cite\Parsoid\RefGroupItem;
use MediaWikiUnitTestCase;

/**
 * @covers \Cite\Parsoid\ReferencesData
 * @license GPL-2.0-or-later
 */
class ReferencesDataTest extends MediaWikiUnitTestCase {

	public function testMinimalSetup() {
		$data = new ReferencesData();
		$this->assertSame( [], $data->embeddedErrors );
		$this->assertSame( Cite::DEFAULT_GROUP, $data->referencesGroup );
		$this->assertFalse( $data->inReferencesContent() );
		$this->assertFalse( $data->inEmbeddedContent() );
		$this->assertNull( $data->lookupRefGroup( Cite::DEFAULT_GROUP ) );
		$this->assertSame( [], $data->getRefGroups() );
	}

	public function testGetOrCreateRefGroup() {
		$data = new ReferencesData();
		$group = $data->getOrCreateRefGroup( 'note', true );
		$this->assertSame( 'note', $group->name );
		$data->removeRefGroup( 'note' );
		$this->assertNull( $data->lookupRefGroup( 'note' ) );
	}

	public function testEmbeddedInAnyContent() {
		$data = new ReferencesData();
		$data->pushEmbeddedContentFlag();
		$this->assertTrue( $data->inEmbeddedContent() );
		$this->assertFalse( $data->inReferencesContent() );
		$data->popEmbeddedContentFlag();
		$this->assertFalse( $data->inEmbeddedContent() );
	}

	public function testEmbeddedInReferencesContent() {
		$data = new ReferencesData();
		$data->pushEmbeddedContentFlag( 'references' );
		$this->assertTrue( $data->inEmbeddedContent() );
		$this->assertTrue( $data->inReferencesContent() );
		$data->popEmbeddedContentFlag();
		$this->assertFalse( $data->inReferencesContent() );
	}

	public function testAddUnnamedRef() {
		$data = new ReferencesData();
		$ref = $data->add(
			Cite::DEFAULT_GROUP,
			'',
			''
		);

		$expected = new RefGroupItem();
		$expected->globalId = 1;
		$expected->backLinkIdBase = 'cite_ref-1';
		$expected->noteId = 'cite_note-1';
		$this->assertEquals( $expected, $ref );

		$group = $data->lookupRefGroup( Cite::DEFAULT_GROUP );
		$this->assertEquals( [ $expected ], $group->refs );
		$this->assertSame( [], $group->indexByName );
	}

	public function testAddNamedRef() {
		$data = new ReferencesData();
		$ref = $data->add(
			'note',
			'wales',
			'rtl'
		);

		$expected = new RefGroupItem();
		$expected->dir = 'rtl';
		$expected->group = 'note';
		$expected->globalId = 1;
		$expected->backLinkIdBase = 'cite_ref-wales_1';
		$expected->name = 'wales';
		$expected->noteId = 'cite_note-wales-1';
		$this->assertEquals( $expected, $ref );

		$group = $data->lookupRefGroup( 'note' );
		$this->assertEquals( [ $expected ], $group->refs );
		$this->assertEquals( [ 'wales' => $expected ], $group->indexByName );
	}

}
