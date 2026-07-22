# Data Model: PSR-20 Relative Date Enforcement

This feature introduces no persistent data. The key design entity is the **relative date pattern set** — a static, in-code catalogue of patterns the rule tests against.

---

## Entity: RelativeDatePattern

**What it represents**: A single rule that classifies a PHP datetime string literal as "relative" (i.e., clock-dependent at runtime).

| Attribute | Type | Description |
|-----------|------|-------------|
| `group` | string | Human-readable category (e.g., `keyword`, `signed-offset`) |
| `pattern` | regex | Case-insensitive PCRE pattern applied to the trimmed literal value |
| `examples` | string[] | Representative string literals that this pattern matches |

**Relationships**: The full set of `RelativeDatePattern` instances together forms the detection catalogue used by `Psr20Rule::isRelativeDateString()`.

---

## Catalogue: Relative Date Patterns

The complete set of patterns, grouped for clarity:

### Keywords

| Pattern | Matches |
|---------|---------|
| `/^(now\|yesterday\|today\|tomorrow\|noon\|midnight)\b/i` | `now`, `tomorrow`, `noon`, `TOMORROW`, `yesterday noon`, `today midnight` |

> **Why `\b` instead of `$`**: Using a word boundary rather than end-of-string allows the pattern
> to also catch compound modifier strings such as `'yesterday noon'` or `'tomorrow 12:00'`, where
> a relative keyword is followed by a time component. PHP's datetime parser treats these as
> relative expressions. The six keywords are consolidated into one pattern for efficiency.

### Standalone Day Names

| Pattern | Matches |
|---------|---------|
| `/^(monday\|tuesday\|wednesday\|thursday\|friday\|saturday\|sunday)\b/i` | `Monday`, `friday`, `TUESDAY`, `monday 14:00:00` |

> **Why `\b` instead of `$`**: Same rationale as Keywords — day names followed by a time
> component (e.g., `'monday 14:00:00'`) are relative and must be caught.

### Relative Qualifiers

| Pattern | Matches |
|---------|---------|
| `/^(next\|last\|previous\|this)\s+/i` | `next Monday`, `last week`, `previous year`, `this month` |

### Offset Formats

| Pattern | Matches |
|---------|---------|
| `/^[+-]\d+\s+(second\|minute\|hour\|day\|week\|month\|year)s?/i` | `+1 day`, `-2 weeks`, `+3 months`, `-1 second` |
| `/^\d+\s+(second\|minute\|hour\|day\|week\|month\|year)s?\s*$/i` | `1 day`, `2 weeks`, `3 months` |

### Ago / In

| Pattern | Matches |
|---------|---------|
| `/\bago\b/i` | `2 days ago`, `1 week ago`, `3 months ago` |
| `/^in\s+\d+\s+(second\|minute\|hour\|day\|week\|month\|year)s?/i` | `in 2 days`, `in 1 week`, `in 3 months` |

### Day-of-Month Selectors

| Pattern | Matches |
|---------|---------|
| `/^(first\|last)\s+day\s+of\b/i` | `first day of this month`, `last day of January 2023` |

### Clock-Relative

| Pattern | Matches |
|---------|---------|
| `/^(back\|front)\s+of\b/i` | `back of 10`, `front of 11` |

---

## Non-Entity: AbsoluteDateString

An absolute date string is defined negatively — any string literal that matches none of the patterns above is treated as absolute and allowed. Common absolute formats that the rule correctly allows:

| Format | Example |
|--------|---------|
| ISO 8601 date | `2023-01-15` |
| ISO 8601 date-time | `2023-01-15 12:00:00` |
| ISO 8601 with timezone | `2023-01-15T12:00:00+00:00` |
| Time only | `12:00:00` |
| US format | `01/15/2023` |

---

## State Transitions

None — the rule is stateless. Each constructor invocation is evaluated independently.
