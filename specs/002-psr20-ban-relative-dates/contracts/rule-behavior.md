# Contract: Psr20Rule Behavioral Contract

**Rule identifier**: `clock.disallowDateTimeNow`
**Applies to**: `new DateTime(...)` and `new DateTimeImmutable(...)` constructor expressions.

---

## Input

A PHP AST node representing `new DateTime` or `new DateTimeImmutable` with zero or more arguments.

---

## Output Contract

### Violations (error reported)

The rule MUST report an error for each of the following cases:

| Case | Example | Error message |
|------|---------|---------------|
| No argument | `new DateTimeImmutable()` | `Avoid using new DateTimeImmutable() directly. Prefer using \Psr\Clock\ClockInterface instead.` |
| String literal `now` (any case) | `new DateTimeImmutable('now')` | `Avoid using new DateTimeImmutable('now') directly. Prefer using \Psr\Clock\ClockInterface instead.` |
| Relative date keyword | `new DateTimeImmutable('tomorrow')` | `Avoid using new DateTimeImmutable('tomorrow') directly. Prefer using \Psr\Clock\ClockInterface instead.` |
| Relative day name | `new DateTimeImmutable('monday')` | `Avoid using new DateTimeImmutable('monday') directly. Prefer using \Psr\Clock\ClockInterface instead.` |
| Signed offset | `new DateTimeImmutable('+1 day')` | `Avoid using new DateTimeImmutable('+1 day') directly. Prefer using \Psr\Clock\ClockInterface instead.` |
| Unsigned offset | `new DateTimeImmutable('1 day')` | `Avoid using new DateTimeImmutable('1 day') directly. Prefer using \Psr\Clock\ClockInterface instead.` |
| Ago expression | `new DateTimeImmutable('2 days ago')` | `Avoid using new DateTimeImmutable('2 days ago') directly. Prefer using \Psr\Clock\ClockInterface instead.` |
| In expression | `new DateTimeImmutable('in 3 weeks')` | `Avoid using new DateTimeImmutable('in 3 weeks') directly. Prefer using \Psr\Clock\ClockInterface instead.` |
| Next/last qualifier | `new DateTimeImmutable('next Monday')` | `Avoid using new DateTimeImmutable('next Monday') directly. Prefer using \Psr\Clock\ClockInterface instead.` |
| Day-of-month selector | `new DateTimeImmutable('last day of this month')` | `Avoid using new DateTimeImmutable('last day of this month') directly. Prefer using \Psr\Clock\ClockInterface instead.` |
| Uppercase relative string | `new DateTimeImmutable('TOMORROW')` | `Avoid using new DateTimeImmutable('TOMORROW') directly. Prefer using \Psr\Clock\ClockInterface instead.` |

### No Violation (passes silently)

The rule MUST NOT report an error for:

| Case | Example |
|------|---------|
| Variable argument | `new DateTimeImmutable($dateString)` |
| ISO date literal | `new DateTimeImmutable('2023-01-15')` |
| ISO date-time literal | `new DateTimeImmutable('2023-12-31 23:59:59')` |
| ISO 8601 with timezone | `new DateTimeImmutable('2023-01-15T12:00:00+00:00')` |
| Time-only literal | `new DateTimeImmutable('12:00:00')` |
| Variadic placeholder | `new DateTimeImmutable(...$args)` |
| Non-`DateTime`/`DateTimeImmutable` class | `new Carbon('tomorrow')` |

---

## Error Message Template

```
Avoid using new {ClassName}({QuotedArgument}) directly. Prefer using \Psr\Clock\ClockInterface instead.
```

Where:
- `{ClassName}` is `DateTime` or `DateTimeImmutable`
- `{QuotedArgument}` is the quoted string literal (e.g., `'tomorrow'`) or empty string for no-argument case

---

## Unchanged Behaviors (regression surface)

The following behaviors from the pre-existing rule MUST be preserved:

| Case | Expected outcome |
|------|-----------------|
| `new DateTime()` | Error: `Avoid using new DateTime() directly...` |
| `new DateTime('now')` | Error: `Avoid using new DateTime('now') directly...` |
| `new DateTimeImmutable()` | Error: `Avoid using new DateTimeImmutable() directly...` |
| `new DateTimeImmutable('now')` | Error: `Avoid using new DateTimeImmutable('now') directly...` |
| `new DateTime('2023-01-01')` | No error |
| `new DateTime($variable)` | No error |
| `new SomeOtherClass('tomorrow')` | No error |
