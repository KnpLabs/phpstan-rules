# AI Agents Guidelines

## Commands

All commands must run inside the PHP container:

```bash
docker compose run --rm php composer install
docker compose run --rm php composer test
docker compose run --rm php composer phpstan
docker compose run --rm php composer cs-fix
docker compose run --rm php composer cs-check
```

Run a single test file:

```bash
docker compose run --rm php vendor/bin/phpunit tests/Rules/Psr/Psr20RuleTest.php
```

CI runs all three checks (`test`, `phpstan`, `cs-check`) on PHP 8.2, 8.3, 8.4 and 8.5.

## Architecture

PHPStan extension package. Each rule lives in `src/Rules/<Namespace>/` and implements `PHPStan\Rules\Rule<NodeType>`.

Rules are registered as services in `extension.neon` with tag `phpstan.rules.rule`. This file is the extension entry point declared in `composer.json` under `extra.phpstan.includes`.

### Adding a new rule

1. Create `src/Rules/<Namespace>/<RuleName>.php` — implement `Rule<T>` where `T` is the AST node type from `PhpParser\Node`.
2. Register the class as a service in `extension.neon`.
3. Create `tests/Rules/<Namespace>/<RuleName>Test.php` — extend `PHPStan\Testing\RuleTestCase<YourRule>`.
4. Add a fixture file at `tests/Rules/<Namespace>/fixtures/<name>.php` with PHP code that triggers (and doesn't trigger) the rule.
5. Call `$this->analyse([__DIR__ . '/fixtures/<name>.php'], [...expected errors with message + line...])` in the test.

### Code style

php-cs-fixer enforces PSR-12 + `declare_strict_types`, `strict_param`, `no_unused_imports`, `ordered_imports` (alpha). All source files must have `declare(strict_types=1)`.
