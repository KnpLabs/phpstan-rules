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

## Release & Publishing

Releases are published to [Packagist](https://packagist.org/packages/knplabs/phpstan-rules) automatically when a GitHub release is created.

### Prerequisites (one-time setup)

1. **Register the package on Packagist** — submit the repository once at [packagist.org/packages/submit](https://packagist.org/packages/submit).

2. **Add repository secrets** — in GitHub → Settings → Secrets and variables → Actions, create two repository secrets:

   | Secret name | Value |
   |-------------|-------|
   | `PACKAGIST_USERNAME` | Your Packagist account username |
   | `PACKAGIST_API_TOKEN` | An API token generated on your [Packagist profile page](https://packagist.org/profile/) |

### Publishing a release

1. Create a new release in GitHub (Releases → Draft a new release).
2. Set the tag (e.g. `v1.2.0`), fill in the title and description, then click **Publish release**.
3. The `Publish` workflow triggers automatically and notifies Packagist via its REST API.
4. The new version appears on Packagist within a few minutes.

> **Draft and pre-releases** — the workflow only fires on published releases. Saving a draft or marking a release as a pre-release does **not** trigger the automation.

### Manual re-trigger

If the workflow fails or you need to re-sync without creating a new release:

1. Go to Actions → **Publish** → **Run workflow**.
2. Click **Run workflow** (no inputs required).

## License

MIT — see [LICENSE](LICENSE).
