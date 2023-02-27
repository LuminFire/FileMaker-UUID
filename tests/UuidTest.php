<?php

namespace Test;

use BrilliantPackages\FileMakerUuid\Uuid;
use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{

    public function testPhpUnit() : void
    {
        $this->assertTrue(true);
    }

    public function testGenerateBasicUuid() : void
    {
        $uuid = Uuid::numeric();
        $this->assertEquals(41, strlen($uuid));
    }

    public function testUserIdUuid() : void
    {
        $uuid = Uuid::numeric(12345);
        $this->assertStringContainsString('12345', $uuid->toString());
    }

    public function testRandomDigits() : void
    {
        $uuid1 = new publicUuid(123);
        $uuid2 = new publicUuid(12345);
        $uuid3 = new publicUuid(1234567890);

        $this->assertEquals('00123', $uuid1->getRandomDigits(5));
        $this->assertEquals('12345', $uuid2->getRandomDigits(5));
        $this->assertEquals('67890', $uuid3->getRandomDigits(5));
    }
}

/**
 * Extends Uuid for access to private methods.
 *
 * @since 1.0.0
 */
class publicUuid extends Uuid
{
    public function getRandomDigits(int $length): string
    {
        return parent::getRandomDigits($length);
    }
}
