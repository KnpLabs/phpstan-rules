<?php

declare(strict_types=1);

namespace KnpLabs\PHPStan\Rules\Tests\Rules\Psr;

use KnpLabs\PHPStan\Rules\Psr\Psr20Rule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<Psr20Rule>
 */
final class Psr20RuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new Psr20Rule();
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/fixtures/psr20.php'], [
            ["Avoid using new DateTime() directly. Prefer using \Psr\Clock\ClockInterface instead.", 5],
            ["Avoid using new DateTime('now') directly. Prefer using \Psr\Clock\ClockInterface instead.", 6],
            ["Avoid using new DateTimeImmutable() directly. Prefer using \Psr\Clock\ClockInterface instead.", 7],
            ["Avoid using new DateTimeImmutable('now') directly. Prefer using \Psr\Clock\ClockInterface instead.", 8],
        ]);
    }
}
