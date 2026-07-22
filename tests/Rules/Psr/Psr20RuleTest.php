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
            ["Avoid using new DateTimeImmutable('yesterday') directly. Prefer using \Psr\Clock\ClockInterface instead.", 10],
            ["Avoid using new DateTimeImmutable('tomorrow') directly. Prefer using \Psr\Clock\ClockInterface instead.", 11],
            ["Avoid using new DateTime('+1 day') directly. Prefer using \Psr\Clock\ClockInterface instead.", 12],
            ["Avoid using new DateTimeImmutable('next Monday') directly. Prefer using \Psr\Clock\ClockInterface instead.", 13],
            ["Avoid using new DateTimeImmutable('-2 weeks') directly. Prefer using \Psr\Clock\ClockInterface instead.", 14],
            ["Avoid using new DateTimeImmutable('2 days ago') directly. Prefer using \Psr\Clock\ClockInterface instead.", 15],
            ["Avoid using new DateTimeImmutable('last day of this month') directly. Prefer using \Psr\Clock\ClockInterface instead.", 16],
            ["Avoid using new DateTimeImmutable('today') directly. Prefer using \Psr\Clock\ClockInterface instead.", 17],
            ["Avoid using new DateTimeImmutable('friday') directly. Prefer using \Psr\Clock\ClockInterface instead.", 18],
            ["Avoid using new DateTimeImmutable('in 3 weeks') directly. Prefer using \Psr\Clock\ClockInterface instead.", 19],
            ["Avoid using new DateTimeImmutable('noon') directly. Prefer using \Psr\Clock\ClockInterface instead.", 20],
            ["Avoid using new DateTimeImmutable('+0 seconds') directly. Prefer using \Psr\Clock\ClockInterface instead.", 21],
            ["Avoid using new DateTimeImmutable('0 days') directly. Prefer using \Psr\Clock\ClockInterface instead.", 22],
            ["Avoid using new DateTimeImmutable('yesterday noon') directly. Prefer using \Psr\Clock\ClockInterface instead.", 32],
            ["Avoid using new DateTimeImmutable('today midnight') directly. Prefer using \Psr\Clock\ClockInterface instead.", 33],
            ["Avoid using new DateTimeImmutable('tomorrow 12:00') directly. Prefer using \Psr\Clock\ClockInterface instead.", 34],
            ["Avoid using new DateTimeImmutable('monday 14:00:00') directly. Prefer using \Psr\Clock\ClockInterface instead.", 35],
        ]);
    }
}
