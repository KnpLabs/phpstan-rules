# knplabs/phpstan-rules

[![CI](https://github.com/KnpLabs/phpstan-rules/actions/workflows/ci.yml/badge.svg)](https://github.com/KnpLabs/phpstan-rules/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/badge/PHP-8.2%20|%208.3%20|%208.4%20|%208.5-blue)](https://www.php.net/)
[![Packagist](https://img.shields.io/packagist/v/knplabs/phpstan-rules)](https://packagist.org/packages/knplabs/phpstan-rules)
[![License](https://img.shields.io/github/license/KnpLabs/phpstan-rules)](LICENSE)

PHPStan rules shared across KnpLabs organization projects.

## Requirements

- PHP 8.2 or higher
- PHPStan ^2.0

## Installation

### Using phpstan/extension-installer (recommended)

```bash
composer require --dev knplabs/phpstan-rules phpstan/extension-installer
```

The extension is loaded automatically.

### Manual configuration

```bash
composer require --dev knplabs/phpstan-rules
```

Then include the extension in your `phpstan.neon`:

```neon
includes:
    - vendor/knplabs/phpstan-rules/extension.neon
```

## Rules

### `clock.disallowDateTimeNow` — PSR-20 Clock Abstraction

Enforces the [PSR-20](https://www.php-fig.org/psr/psr-20/) recommendation to avoid instantiating `DateTime` or `DateTimeImmutable` with the current time directly. This makes code that depends on the current time testable and respects the clock abstraction.

**Triggers on:**

```php
$a = new DateTime();
$b = new DateTime('now');
$c = new DateTimeImmutable();
$d = new DateTimeImmutable('now');
```

**Does not trigger on** (explicit non-"now" timestamps are fine):

```php
$a = new DateTime('2023-01-01');
$b = new DateTimeImmutable('yesterday');
```

**Recommended fix:** inject `Psr\Clock\ClockInterface` and call `$clock->now()`:

```php
use Psr\Clock\ClockInterface;

final class MyService
{
    public function __construct(private readonly ClockInterface $clock) {}

    public function doSomething(): void
    {
        $now = $this->clock->now();
        // ...
    }
}
```

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for the human contributor guide.

If you are working with an AI agent (Claude Code), refer to [CLAUDE.md](CLAUDE.md) — it contains the AI-facing instructions for this repository.

## License

MIT — see [LICENSE](LICENSE).
