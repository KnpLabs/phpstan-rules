<?php

declare(strict_types=1);

namespace KnpLabs\PHPStan\Rules\Tests\Rules\Psr;

use KnpLabs\PHPStan\Rules\Psr\Psr20FunctionsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<Psr20FunctionsRule>
 */
final class Psr20FunctionsRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new Psr20FunctionsRule();
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/fixtures/psr20_functions.php'], [
            ["Avoid using time() directly. Prefer using \Psr\Clock\ClockInterface instead.", 5],
            ["Avoid using time() directly. Prefer using \Psr\Clock\ClockInterface instead.", 6],
            ["Avoid using date() directly. Prefer using \Psr\Clock\ClockInterface instead.", 7],
            ["Avoid using date() directly. Prefer using \Psr\Clock\ClockInterface instead.", 8],
            ["Avoid using date() directly. Prefer using \Psr\Clock\ClockInterface instead.", 9],
            ["Avoid using date() directly. Prefer using \Psr\Clock\ClockInterface instead.", 10],
        ]);
    }
}
