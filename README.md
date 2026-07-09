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

> [!WARNING]
> The package is not yet available on Packagist. It will be release with the v1.0.0 once internal tests has been done.

During testing phase, install via VCS repository pointing at `dev-main`. Add repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/KnpLabs/phpstan-rules"
        }
    ]
}
```

Then require dev-main version:

```bash
composer require --dev knplabs/phpstan-rules:dev-main
```

If you don't use `phpstan/extension-installer`, include the extension in your `phpstan.neon`:

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

### `clock.disallowTimeFunctions` — PSR-20 Clock Abstraction (functions)

Enforces the [PSR-20](https://www.php-fig.org/psr/psr-20/) recommendation to avoid using `time()` or `date()` directly. This makes code that depends on the current time testable and respects the clock abstraction.

**Triggers on:**

```php
$a = time();
$b = date('Y-m-d');
$c = date('Y-m-d', time());
$d = date('Y-m-d', 'now');
```

**Does not trigger on** (explicit non-"now" timestamps are fine):

```php
$a = date('Y-m-d', 1672531200);
$b = date('Y-m-d', $someTimestamp);
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

If you are working with an AI agent, refer to [AGENTS.md](AGENTS.md) — it contains the AI-facing instructions for this repository.

## License

MIT — see [LICENSE](LICENSE).
