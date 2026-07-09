<?php

declare(strict_types=1);

namespace KnpLabs\PHPStan\Rules\Psr;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\VariadicPlaceholder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Enforce the PSR-20 recommendation to avoid using `time()` or `date()` directly in your code.
 *
 * It tracks `time()`, and `date()` called without a timestamp argument or with the string `'now'`.
 *
 * @implements Rule<FuncCall>
 */
final readonly class Psr20FunctionsRule implements Rule
{
    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Node\Name) {
            return [];
        }

        $name = $node->name->toString();

        if ('time' === $name) {
            return [$this->buildError('time')];
        }

        if ('date' === $name) {
            return $this->processDateCall($node);
        }

        return [];
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function processDateCall(FuncCall $node): array
    {
        if (count($node->args) <= 1) {
            return [$this->buildError('date')];
        }

        $secondArg = $node->args[1];

        if ($secondArg instanceof VariadicPlaceholder) {
            return [];
        }

        if ($secondArg->value instanceof Node\Scalar\String_ && 'now' === strtolower($secondArg->value->value)) {
            return [$this->buildError('date')];
        }

        return [];
    }

    private function buildError(string $function): IdentifierRuleError
    {
        return RuleErrorBuilder::message("Avoid using $function() directly. Prefer using \Psr\Clock\ClockInterface instead.")
            ->identifier('clock.disallowTimeFunctions')
            ->build();
    }
}
