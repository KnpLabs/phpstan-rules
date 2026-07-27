<?php

declare(strict_types=1);

namespace KnpLabs\PHPStan\Rules\Psr;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\VariadicPlaceholder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Enforce the PSR-20 recommendation to avoid using `new \DateTime()` or `new \DateTimeImmutable()` directly in your code.
 *
 * Flags empty constructors and any relative date string literal (e.g. `'now'`, `'tomorrow'`, `'+1 day'`, `'next Monday'`).
 * Absolute date strings (e.g. `'2023-01-15'`) and variable arguments are allowed.
 *
 * @implements Rule<New_>
 */
final readonly class Psr20Rule implements Rule
{
    public function getNodeType(): string
    {
        return New_::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (
            !$node->class instanceof Node\Name
            || !in_array($node->class->toString(), ['DateTime', 'DateTimeImmutable'], true)
        ) {
            return [];
        }

        if (0 === count($node->args)) {
            return [$this->buildError($node->class->toString(), '')];
        }

        $firstArg = $node->args[0];

        if ($firstArg instanceof VariadicPlaceholder) {
            return [];
        }

        $firstArg = $firstArg->value;

        if ($firstArg instanceof Node\Scalar\String_ && self::isRelativeDateString($firstArg->value)) {
            return [$this->buildError($node->class->toString(), sprintf("'%s'", $firstArg->value))];
        }

        return [];
    }

    private static function isRelativeDateString(string $value): bool
    {
        $trimmed = trim($value);

        $patterns = [
            '/^(now|yesterday|today|tomorrow|noon|midnight)\b/i',
            '/^(monday|tuesday|wednesday|thursday|friday|saturday|sunday)\b/i',
            '/^(mon|tue|wed|thu|fri|sat|sun)\b/i',
            '/^(next|last|previous|this)\s+/i',
            '/^[+-]\d+\s+(second|minute|hour|day|week|month|year)s?/i',
            '/^\d+\s+(second|minute|hour|day|week|month|year)s?\s*$/i',
            '/\bago\b/i',
            '/^in\s+\d+\s+(second|minute|hour|day|week|month|year)s?/i',
            '/^(first|last)\s+day\s+of\b/i',
            '/^(back|front)\s+of\b/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $trimmed)) {
                return true;
            }
        }

        return false;
    }

    private function buildError(string $class, string $context): IdentifierRuleError
    {
        return RuleErrorBuilder::message("Avoid using new $class($context) directly. Prefer using \Psr\Clock\ClockInterface instead.")
            ->identifier('clock.disallowDateTimeNow')
            ->build();
    }
}
