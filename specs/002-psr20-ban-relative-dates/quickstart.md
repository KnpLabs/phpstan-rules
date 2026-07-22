# Quickstart Validation Guide: PSR-20 Relative Date Enforcement

This guide describes how to validate the updated `Psr20Rule` end-to-end after implementation.

All commands must be run inside the PHP container:

```bash
docker compose run --rm php <command>
```

---

## Prerequisites

- Docker and Docker Compose installed and running
- Project dependencies installed:

```bash
docker compose run --rm php composer install
```

---

## Validation Scenarios

### Scenario 1: Relative date strings are flagged

**Setup**: The updated test fixture (`tests/Rules/Psr/fixtures/psr20.php`) must include PHP code with relative date string literals such as `'tomorrow'`, `'+1 day'`, `'yesterday'`, `'next Monday'`.

**Run**:

```bash
docker compose run --rm php vendor/bin/phpunit tests/Rules/Psr/Psr20RuleTest.php
```

**Expected outcome**: The test passes, confirming that each relative date literal in the fixture maps to an expected error at the correct line.

---

### Scenario 2: Absolute date strings are not flagged

**Setup**: The fixture must also include lines using absolute date strings such as `'2023-01-15'`, `'2023-12-31 23:59:59'`, and ISO 8601 strings. These lines must NOT appear in the expected-errors array in the test.

**Run**:

```bash
docker compose run --rm php vendor/bin/phpunit tests/Rules/Psr/Psr20RuleTest.php
```

**Expected outcome**: Test passes with no unexpected errors for absolute date lines.

---

### Scenario 3: Variable arguments are not flagged

**Setup**: The fixture must include lines using variables as constructor arguments (e.g., `new DateTimeImmutable($someVar)`). These must NOT appear in the expected-errors array.

**Run**:

```bash
docker compose run --rm php vendor/bin/phpunit tests/Rules/Psr/Psr20RuleTest.php
```

**Expected outcome**: Test passes with no errors for variable-argument lines.

---

### Scenario 4: Existing behaviors are preserved

**Setup**: The fixture retains the original lines (`new DateTime()`, `new DateTime('now')`, `new DateTimeImmutable()`, `new DateTimeImmutable('now')`). Expected errors for these lines must still be present in the test.

**Run**:

```bash
docker compose run --rm php vendor/bin/phpunit tests/Rules/Psr/Psr20RuleTest.php
```

**Expected outcome**: All four existing error assertions still pass.

---

### Scenario 5: Full CI pipeline passes

**Run all three CI checks**:

```bash
docker compose run --rm php composer test
docker compose run --rm php composer phpstan
docker compose run --rm php composer cs-check
```

**Expected outcome**: All three commands exit with code 0 on all supported PHP versions (8.2, 8.3, 8.4, 8.5).

---

## Key Files Touched

| File | Purpose |
|------|---------|
| `src/Rules/Psr/Psr20Rule.php` | Core rule logic — relative-date detection added |
| `tests/Rules/Psr/Psr20RuleTest.php` | Test — expected errors updated to include relative-date cases |
| `tests/Rules/Psr/fixtures/psr20.php` | Fixture — new lines for relative date and absolute date cases added |
| `README.md` | Rule documentation — "Triggers on" and "Does not trigger on" sections updated |

See [rule behavior contract](contracts/rule-behavior.md) for the complete list of triggering and non-triggering cases.
