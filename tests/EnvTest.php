<?php

use App\Support\Env;
use PHPUnit\Framework\TestCase;

final class EnvTest extends TestCase
{
    public function testEnvGetReturnsDefaultWhenMissing(): void
    {
        putenv('UNIT_TEST_MISSING');

        $this->assertSame('fallback', Env::get('UNIT_TEST_MISSING', 'fallback'));
    }

    public function testEnvGetReturnsValue(): void
    {
        putenv('UNIT_TEST_VALUE=hello');

        $this->assertSame('hello', Env::get('UNIT_TEST_VALUE', 'fallback'));
    }
}
