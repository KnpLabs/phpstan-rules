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
 * It tracks either `new \DateTime()`, `new \DateTime('now')`, `new \DateTimeImmutable()` and `new \DateTimeImmutable('now')`.
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

        if ($firstArg instanceof Node\Scalar\String_ && 'now' === strtolower($firstArg->value)) {
            return [$this->buildError($node->class->toString(), '\'now\'')];
        }

        return [];
    }

    private function buildError(string $class, string $context): IdentifierRuleError
    {
        return RuleErrorBuilder::message("Avoid using new $class($context) directly. Prefer using \Psr\Clock\ClockInterface instead.")
            ->identifier('clock.disallowDateTimeNow')
            ->build();
    }
}
