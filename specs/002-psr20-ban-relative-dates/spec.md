# Feature Specification: PSR-20 Relative Date Enforcement

**Feature Branch**: `002-psr20-ban-relative-dates`

**Created**: 2026-07-22

**Status**: Draft

**Input**: User description: "@src/Rules/Psr/Psr20Rule.php currently act only against current timestamp. It needs to be updated to forbid any relative date (tomorrow, +1 day, etc...) but keep possible to instanciate a DateTimeImmutable via its constructor if the date is a variable or an absolute date represented as a string."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Relative Date Strings Are Flagged (Priority: P1)

A developer writes code that constructs a `DateTime` or `DateTimeImmutable` object using a relative date string such as `'tomorrow'`, `'+1 day'`, `'next Monday'`, or `'last week'`. The rule detects this as a violation and directs the developer to use a clock abstraction instead.

**Why this priority**: This is the core new capability. The existing rule only blocks `'now'` and empty constructors, leaving many implicit time-dependency patterns undetected. Catching all relative strings is the fundamental goal of this feature.

**Independent Test**: Can be fully tested by running static analysis on a file containing `new DateTimeImmutable('tomorrow')` and verifying that a violation is reported.

**Acceptance Scenarios**:

1. **Given** code containing `new DateTimeImmutable('tomorrow')`, **When** static analysis runs, **Then** a violation is reported directing the developer to use a clock interface.
2. **Given** code containing `new DateTime('+1 day')`, **When** static analysis runs, **Then** a violation is reported.
3. **Given** code containing `new DateTimeImmutable('next Monday')`, **When** static analysis runs, **Then** a violation is reported.
4. **Given** code containing `new DateTimeImmutable('-2 weeks')`, **When** static analysis runs, **Then** a violation is reported.
5. **Given** code containing `new DateTimeImmutable('yesterday')`, **When** static analysis runs, **Then** a violation is reported.
6. **Given** code containing `new DateTimeImmutable('TOMORROW')` (uppercase), **When** static analysis runs, **Then** a violation is reported.
7. **Given** code containing `new DateTimeImmutable('last day of this month')`, **When** static analysis runs, **Then** a violation is reported.

---

### User Story 2 - Absolute Date Strings Are Permitted (Priority: P2)

A developer constructs a `DateTime` or `DateTimeImmutable` object using a hard-coded absolute date string (e.g., `'2023-01-15'` or `'2023-01-15 12:00:00'`). This is a valid pattern — for example, when setting a known fixed date in a test or configuration — and the rule must not report a violation.

**Why this priority**: Without this allowance the rule would block legitimate uses of explicitly known dates, making it unnecessarily obstructive. Distinguishing relative from absolute is what makes the rule precise and trustworthy.

**Independent Test**: Can be fully tested by running static analysis on a file containing `new DateTimeImmutable('2023-01-15')` and verifying no violation is reported.

**Acceptance Scenarios**:

1. **Given** code containing `new DateTimeImmutable('2023-01-15')`, **When** static analysis runs, **Then** no violation is reported.
2. **Given** code containing `new DateTime('2023-12-31 23:59:59')`, **When** static analysis runs, **Then** no violation is reported.
3. **Given** code containing `new DateTimeImmutable('2023-01-15T12:00:00+00:00')`, **When** static analysis runs, **Then** no violation is reported.

---

### User Story 3 - Variable Arguments Are Permitted (Priority: P2)

A developer constructs a `DateTime` or `DateTimeImmutable` object using a variable as the date argument. Because the value is not known at analysis time, the rule allows this usage to avoid false positives.

**Why this priority**: Blocking variable-based construction would produce false positives in legitimate dynamic scenarios, undermining developer trust in the rule.

**Independent Test**: Can be fully tested by running static analysis on a file containing `new DateTimeImmutable($dateString)` and verifying no violation is reported.

**Acceptance Scenarios**:

1. **Given** code containing `new DateTimeImmutable($dateString)`, **When** static analysis runs, **Then** no violation is reported.
2. **Given** code containing `new DateTime($someVariable)`, **When** static analysis runs, **Then** no violation is reported.

---

### Edge Cases

- What happens with `'now'` (existing behavior)? → Must still be flagged.
- What happens with an empty constructor `new DateTimeImmutable()` (existing behavior)? → Must still be flagged.
- What happens with a variadic placeholder argument (`...`)? → Must not be flagged (cannot be statically evaluated).
- What happens with relative strings in mixed case (e.g., `'TOMORROW'`, `'Next Monday'`)? → Must be flagged (case-insensitive matching).
- What happens with `'+0 seconds'` or `'0 days'`? → Must be flagged (still a relative expression, even if the offset is zero).
- What happens with time-only strings like `'12:00:00'`? → Must not be flagged (represents an absolute time, not a relative date).
- What happens with strings that combine a fixed month/year and a relative selector (e.g., `'last day of January 2023'`)? → Must be flagged (contains a relative component).

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The rule MUST report a violation when `new DateTime` or `new DateTimeImmutable` is called with no arguments.
- **FR-002**: The rule MUST report a violation when `new DateTime` or `new DateTimeImmutable` is called with the string literal `'now'` (case-insensitive).
- **FR-003**: The rule MUST report a violation when `new DateTime` or `new DateTimeImmutable` is called with a string literal that represents a relative date or time.
- **FR-004**: Relative date string detection MUST cover at minimum: `yesterday`, `today`, `tomorrow`, `next <weekday>`, `last <weekday>`, `+N <unit>`, `-N <unit>`, `first day of`, `last day of`, `ago`, and other English-language relative specifiers recognized by the PHP datetime parser.
- **FR-005**: Relative date matching MUST be case-insensitive.
- **FR-006**: The rule MUST NOT report a violation when the first constructor argument is a variable (value not known at analysis time).
- **FR-007**: The rule MUST NOT report a violation when the first constructor argument is a string literal representing an absolute date or date-time (e.g., `'2023-01-15'`, `'2023-12-31 23:59:59'`, ISO 8601 strings).
- **FR-008**: The rule MUST NOT report a violation when the first constructor argument is a variadic placeholder.
- **FR-009**: The error message MUST direct the developer to use `\Psr\Clock\ClockInterface` instead.

### Key Entities

- **Relative date string**: A string literal whose value is computed relative to the current moment at runtime (e.g., `'tomorrow'`, `'+1 day'`, `'next Monday'`, `'last week'`, `'yesterday noon'`).
- **Absolute date string**: A string literal that denotes a fixed, explicitly stated date or date-time whose value does not depend on when the code executes (e.g., `'2023-01-15'`, `'2023-01-15 12:00:00'`).

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% of relative date string patterns in the test fixture are detected as violations by the updated rule.
- **SC-002**: 0 false positives are produced when the rule analyzes code using absolute date string literals or variable arguments.
- **SC-003**: All previously existing passing tests for the rule continue to pass without modification.
- **SC-004**: The full CI pipeline (tests, static analysis, code style) passes on all declared supported PHP versions.

## Assumptions

- The rule targets `DateTime` and `DateTimeImmutable` by their unqualified names, consistent with the current behavior. Fully qualified names (e.g., `\DateTimeImmutable`) are also covered.
- The scope does not extend to subclasses, interfaces, or third-party date libraries.
- The definition of "relative date string" is derived from the set of relative formats recognized by the PHP datetime parser, not from a custom arbitrary list.
- Strings that are ambiguous but whose most natural reading is absolute (e.g., pure time strings like `'12:00:00'`) are treated as absolute to minimize false positives.
- The existing rule error identifier may need to be broadened in scope; this is deferred to the planning phase.
- No new dependencies are introduced to implement the detection logic.
