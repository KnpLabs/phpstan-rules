# Research: PSR-20 Relative Date Enforcement

## Decision 1: How to detect "relative" vs "absolute" date strings

**Decision**: Use a compiled allowlist of regex patterns matching PHP's documented relative date formats. Flag a string literal if it matches any pattern; allow it otherwise.

**Rationale**: PHP's datetime parser accepts a large set of relative specifiers (documented at https://www.php.net/manual/en/datetime.formats.relative.php). Rather than trying to parse what is "absolute" (very wide set), it is simpler and more predictable to enumerate what is "relative" and flag exactly those patterns. False negatives (missing an obscure relative string) are acceptable; false positives (blocking a legitimate absolute date) are not.

**Alternatives considered**:
- **Call `DateTimeImmutable::createFromFormat`** at rule-evaluation time to test if the string matches a known absolute format: rejected because it introduces runtime evaluation inside a static analysis rule and requires a known list of absolute formats that may be incomplete.
- **Attempt full parsing with PHP's date parser**: rejected because the parser is highly permissive and many absolute strings would still parse as "relative" in certain contexts.

---

## Decision 2: Relative date patterns to detect

The following pattern groups cover the PHP-documented relative date formats:

| Group | Examples | Pattern strategy |
|-------|----------|-----------------|
| Keywords | `now`, `yesterday`, `today`, `tomorrow` | Exact keyword match (case-insensitive) |
| Day of day | `noon`, `midnight` | Exact keyword match |
| Day names (standalone) | `monday`, `friday` | Exact weekday name match |
| Relative qualifiers | `next monday`, `last friday`, `previous year`, `this week` | `^(next\|last\|previous\|this)\s+` prefix |
| Signed offsets | `+1 day`, `-2 weeks`, `+3 months` | `^[+-]\d+\s+<unit>` |
| Unsigned offsets with unit | `1 day`, `2 weeks` (when followed by a known time unit) | `^\d+\s+(second\|minute\|hour\|day\|week\|month\|year)s?` |
| Ago | `2 days ago`, `1 week ago` | `\bago\b` anywhere in string |
| In N units | `in 2 days`, `in 1 week` | `^in\s+\d+` prefix |
| Day-of-month selectors | `first day of`, `last day of` | `^(first\|last)\s+day\s+of\b` |
| Clock-relative | `back of 10`, `front of 11` | `^(back\|front)\s+of\b` |

All matching is case-insensitive. Matching is applied to the trimmed string value.

**Edge case: `today`** — PHP treats `today` as midnight of the current day, making it relative. The rule flags it.

**Edge case: time-only strings** (e.g., `'12:00:00'`) — These are absolute time references with no date component. They match none of the relative patterns and are correctly allowed.

**Edge case: `'+0 seconds'`** — Matches the signed-offset pattern; flagged correctly.

**Edge case: `'last day of January 2023'`** — Contains `last day of` prefix; flagged (has a relative component).

---

## Decision 3: Rule identifier — keep or replace `clock.disallowDateTimeNow`

**Decision**: Keep the existing `clock.disallowDateTimeNow` identifier.

**Rationale**: Any user who has suppressed this error with `// @phpstan-ignore clock.disallowDateTimeNow` would face a breaking change if the identifier changed. The identifier's semantic scope is "PSR-20 clock abstraction compliance", not narrowly "only the `now` literal". Keeping it backward-compatible is the right trade-off.

**Alternatives considered**:
- **New identifier `clock.disallowRelativeDateTime`**: provides clearer semantics but breaks existing suppressions without a migration path. Rejected in favor of backward compatibility.
- **Emit both old and new identifiers**: PHPStan `RuleErrorBuilder` supports a single identifier per error. Not feasible without a refactor. Rejected.

---

## Decision 4: Error message format

**Decision**: Extend the existing error message format to name the offending literal. The current format `"Avoid using new $class($context) directly. Prefer using \Psr\Clock\ClockInterface instead."` already supports variable `$context`, so `'tomorrow'`, `'+1 day'`, etc. are naturally substituted in. No structural change needed.

**Rationale**: The message naturally extends without modification. For empty args, `$context` remains `''` (current behavior). For string literals, `$context` is the quoted string value (e.g., `'tomorrow'`). Consistent with the existing pattern.

---

## Decision 5: Handling the `DateTime` class alongside `DateTimeImmutable`

**Decision**: Apply the same relative-date detection to both `DateTime` and `DateTimeImmutable`, consistent with the existing rule behavior.

**Rationale**: Both classes share the same constructor signature and accept the same date formats. The PSR-20 concern (clock dependency) applies equally to both.

---

## Summary of Unknowns Resolved

| Unknown | Resolution |
|---------|-----------|
| How to classify relative vs absolute strings | Regex allowlist of relative patterns |
| Which relative patterns to cover | PHP documented relative formats (10 pattern groups) |
| Rule identifier strategy | Keep `clock.disallowDateTimeNow` for backward compat |
| Error message changes | None required — existing format already handles it |
| Scope: both `DateTime` and `DateTimeImmutable` | Yes, consistent with existing behavior |
