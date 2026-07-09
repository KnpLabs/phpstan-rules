# Contributing

Thank you for your interest in contributing to `knplabs/phpstan-rules`.

## Reporting issues or requesting new rules

- **To request a new rule or ask for help:** [open an issue](https://github.com/KnpLabs/phpstan-rules/issues/new) describing the rule you'd like to see, why it matters, and ideally an example of code it should catch.
- **To submit a rule directly:** open a pull request following the steps below. Include the motivation, the code pattern it targets, and any edge cases you considered.

## Adding a new rule

### 1. Create the rule class

Create `src/Rules/<Namespace>/<RuleName>.php`. Implement `PHPStan\Rules\Rule<T>` where `T` is the AST node type from `PhpParser\Node` that your rule inspects.

```php
<?php

declare(strict_types=1);

namespace KnpLabs\PHPStan\Rules\<Namespace>;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/** @implements Rule<Node\<NodeType>> */
final readonly class <RuleName> implements Rule
{
    public function getNodeType(): string
    {
        return Node\<NodeType>::class;
    }

    /** @return list<IdentifierRuleError> */
    public function processNode(Node $node, Scope $scope): array
    {
        // return [] if no violation, or a RuleErrorBuilder error otherwise
    }
}
```

### 2. Register the rule

Add the class as a service in `extension.neon`:

```neon
services:
    -
        class: KnpLabs\PHPStan\Rules\<Namespace>\<RuleName>
        tags:
            - phpstan.rules.rule
```

### 3. Write the test

Create `tests/Rules/<Namespace>/<RuleName>Test.php`:

```php
<?php

declare(strict_types=1);

namespace KnpLabs\PHPStan\Rules\Tests\Rules\<Namespace>;

use KnpLabs\PHPStan\Rules\<Namespace>\<RuleName>;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/** @extends RuleTestCase<<RuleName>> */
final class <RuleName>Test extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new <RuleName>();
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/fixtures/<name>.php'], [
            ['Expected error message.', /* line */ 5],
        ]);
    }
}
```

### 4. Add a fixture file

Create `tests/Rules/<Namespace>/fixtures/<name>.php` with PHP code that both **triggers** and **does not trigger** the rule. Keep it minimal and clearly commented.

### 5. Run the test suite

All commands run inside the PHP container:

```bash
# Run your single test file
docker compose run --rm php vendor/bin/phpunit tests/Rules/<Namespace>/<RuleName>Test.php

# Run the full suite
docker compose run --rm php composer test

# Run PHPStan on the extension itself
docker compose run --rm php composer phpstan

# Check code style
docker compose run --rm php composer cs-check

# Fix code style automatically
docker compose run --rm php composer cs-fix
```

CI runs `test`, `phpstan`, and `cs-check` against PHP 8.2, 8.3, 8.4, and 8.5. All must pass before a PR can be merged.

## AI agents

If you are contributing via an AI coding agent, refer to [AGENTS.md](AGENTS.md) — it contains the machine-readable instructions for this repository.

## Code style

This project follows PSR-12 enforced by `php-cs-fixer`. All source files must declare `strict_types=1`. Run `composer cs-fix` before pushing.
