# Tasks: PSR-20 Relative Date Enforcement

**Input**: Design documents from `specs/002-psr20-ban-relative-dates/`

**Prerequisites**: plan.md ✓, spec.md ✓, research.md ✓, data-model.md ✓, contracts/ ✓

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (US1, US2, US3)

---

## Phase 1: Setup

**Purpose**: Verify development environment is ready. No project initialization needed — all structure exists.

- [x] T001 Verify project dependencies are installed: `docker compose run --rm php composer install`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Add the relative-date detection method to the rule class. All user stories depend on this.

**⚠️ CRITICAL**: No user story work can begin until this phase is complete.

- [x] T002 Add private static method `isRelativeDateString(string $value): bool` to `src/Rules/Psr/Psr20Rule.php` — implement all 10 PCRE pattern groups from `specs/002-psr20-ban-relative-dates/data-model.md` (keywords, day names, relative qualifiers, signed/unsigned offsets, ago, in, day-of-month selectors, clock-relative); matching must be case-insensitive on the trimmed string value
- [x] T003 Update `processNode()` in `src/Rules/Psr/Psr20Rule.php` — replace the `'now' === strtolower($firstArg->value)` check with `self::isRelativeDateString($firstArg->value)` and pass the raw string value (not the quoted form) as `$context` to `buildError()` wrapped in single-quotes; the `'now'` case is now subsumed by the keyword pattern

**Checkpoint**: Rule logic is updated. Run `docker compose run --rm php composer phpstan` — must pass.

---

## Phase 3: User Story 1 — Relative date strings are flagged (Priority: P1) 🎯 MVP

**Goal**: All PHP relative date string literals passed to `new DateTime`/`new DateTimeImmutable` are reported as violations.

**Independent Test**: Run `docker compose run --rm php vendor/bin/phpunit tests/Rules/Psr/Psr20RuleTest.php` — all expected errors for relative-date lines must be present.

### Implementation for User Story 1

- [x] T004 [US1] Extend `tests/Rules/Psr/fixtures/psr20.php` — append lines using relative date string literals that must trigger errors: `new DateTimeImmutable('yesterday')` is already on line 10 (leave it); add `new DateTimeImmutable('tomorrow')`, `new DateTime('+1 day')`, `new DateTimeImmutable('next Monday')`, `new DateTimeImmutable('-2 weeks')`, `new DateTimeImmutable('2 days ago')`, `new DateTimeImmutable('last day of this month')`; also cover remaining pattern groups from data-model.md: `new DateTimeImmutable('today')` (keyword), `new DateTimeImmutable('friday')` (standalone day name), `new DateTimeImmutable('in 3 weeks')` (in-expression), `new DateTimeImmutable('noon')` (clock keyword); and edge cases: `new DateTimeImmutable('+0 seconds')` and `new DateTimeImmutable('0 days')` (zero-offset must still flag)
- [x] T005 [US1] Update `tests/Rules/Psr/Psr20RuleTest.php` — add expected error assertions for every relative-date line in the fixture, including the pre-existing `'yesterday'` line (currently line 10, now expected to error); each assertion must match the exact error message format `"Avoid using new {Class}('{arg}') directly. Prefer using \Psr\Clock\ClockInterface instead."` and the correct line number
- [x] T006 [US1] Run `docker compose run --rm php vendor/bin/phpunit tests/Rules/Psr/Psr20RuleTest.php` and confirm all assertions pass

**Checkpoint**: User Story 1 complete. Every relative date literal in the fixture produces the expected error. Existing errors for empty-arg and `'now'` cases still pass.

---

## Phase 4: User Story 2 — Absolute date strings are permitted (Priority: P2)

**Goal**: String literals representing absolute dates (e.g., `'2023-01-15'`) produce no violation.

**Independent Test**: Run `docker compose run --rm php vendor/bin/phpunit tests/Rules/Psr/Psr20RuleTest.php` — no errors for absolute-date lines.

### Implementation for User Story 2

- [x] T007 [US2] Extend `tests/Rules/Psr/fixtures/psr20.php` — add absolute date lines that must NOT trigger errors: `new DateTime('2023-01-01')` is already present (leave it); add `new DateTimeImmutable('2023-12-31 23:59:59')`, `new DateTimeImmutable('2023-01-15T12:00:00+00:00')`, and `new DateTimeImmutable('12:00:00')` (time-only string — must not flag)
- [x] T008 [US2] Confirm those lines are absent from the expected-error array in `tests/Rules/Psr/Psr20RuleTest.php` (no assertion added for them) and run `docker compose run --rm php vendor/bin/phpunit tests/Rules/Psr/Psr20RuleTest.php`

**Checkpoint**: User Stories 1 and 2 both pass. Absolute date lines produce no errors; relative date lines all produce errors.

---

## Phase 5: User Story 3 — Variable arguments are permitted (Priority: P2)

**Goal**: Variable arguments (`$var`) passed to `new DateTime`/`new DateTimeImmutable` produce no violation.

**Independent Test**: Run `docker compose run --rm php vendor/bin/phpunit tests/Rules/Psr/Psr20RuleTest.php` — no errors for variable-arg lines.

### Implementation for User Story 3

- [x] T009 [US3] Extend `tests/Rules/Psr/fixtures/psr20.php` — add variable argument lines that must NOT trigger errors: `new DateTimeImmutable($dateString)` and `new DateTime($someVar)` (declare `$dateString` and `$someVar` as `string` variables above them); also add `new DateTimeImmutable(...$args)` (variadic placeholder — covers FR-008; declare `$args` as `string[]` above it)
- [x] T010 [US3] Confirm those lines are absent from the expected-error array in `tests/Rules/Psr/Psr20RuleTest.php` and run `docker compose run --rm php vendor/bin/phpunit tests/Rules/Psr/Psr20RuleTest.php`

**Checkpoint**: All three user stories pass. Full test suite is green.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Documentation update and full CI validation.

- [x] T011 [P] Update `README.md` section for `clock.disallowDateTimeNow` — add `'tomorrow'`, `'+1 day'`, `'next Monday'` to the "Triggers on" code block; remove `'yesterday'` from the "Does not trigger on" block (it now triggers); add `new DateTimeImmutable($dateVariable)` as an allowed example in "Does not trigger on"
- [x] T012 Run full CI pipeline: `docker compose run --rm php composer test && docker compose run --rm php composer phpstan && docker compose run --rm php composer cs-check` — all three must exit 0

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies — can start immediately
- **Foundational (Phase 2)**: Depends on Phase 1 — **BLOCKS all user stories**
- **User Stories (Phases 3–5)**: All depend on Foundational (Phase 2)
  - US1 (Phase 3) must complete first — US2 and US3 fixture tasks append to the same file
  - US2 (Phase 4) depends on Phase 3 completing (fixture file must be stable)
  - US3 (Phase 5) depends on Phase 4 completing (same fixture file)
- **Polish (Phase 6)**: Depends on all user stories complete

### User Story Dependencies

- **US1 (P1)**: Can start after Phase 2 — no dependency on US2/US3
- **US2 (P2)**: Logically independent but shares the fixture file with US1 — sequence after US1 to avoid conflicts
- **US3 (P2)**: Logically independent but shares the fixture file — sequence after US2

### Within Each User Story

- Fixture extension before test update (need line numbers to write assertions)
- Test update before test run
- Logic change (Phase 2) before any fixture/test work

### Parallel Opportunities

- T002 and T003 in Phase 2 must be sequential (T003 depends on T002's change)
- T004, T007, T009 are NOT marked [P] — they all modify the same fixture file and must be sequenced by phase
- T011 (README) is [P] relative to the test run (T012) — can be done while T012 runs

---

## Parallel Example: Foundational Phase

```bash
# Phase 2 must be sequential — T003 uses the method added in T002:
Task T002: "Add isRelativeDateString() to src/Rules/Psr/Psr20Rule.php"
  → then →
Task T003: "Update processNode() in src/Rules/Psr/Psr20Rule.php"
```

## Parallel Example: Polish Phase

```bash
# T011 and T012 can run in parallel:
Task T011: "Update README.md documentation"
Task T012: "Run full CI pipeline"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup (verify env)
2. Complete Phase 2: Foundational (add detection logic — **required**)
3. Complete Phase 3: User Story 1 (relative dates flagged)
4. **STOP and VALIDATE**: Run `docker compose run --rm php vendor/bin/phpunit tests/Rules/Psr/Psr20RuleTest.php`
5. Core capability delivered

### Incremental Delivery

1. Phase 1 + Phase 2 → Detection logic ready
2. Phase 3 (US1) → Relative dates flagged → MVP delivered
3. Phase 4 (US2) → Absolute dates confirmed passing → Extra test coverage
4. Phase 5 (US3) → Variable args confirmed passing → Full test coverage
5. Phase 6 → README updated + CI green → Ready to merge

### Single-Developer Strategy

Work strictly in phase order (1 → 2 → 3 → 4 → 5 → 6). No parallel work needed — this is a single-developer feature touching a small number of files.

---

## Notes

- [P] tasks = different files or logically independent, no file conflicts at that phase
- [Story] label maps each task to its spec user story for traceability
- The fixture file (`psr20.php`) is extended in multiple phases — always append, never rewrite earlier lines, to preserve line numbers from prior assertions
- Verify line numbers in the fixture match expected-error line numbers in the test before each run
- `'yesterday'` is on line 10 of the existing fixture and currently does NOT error — after T003 (Foundational), it will error; T005 must add this to the expected-error array
- Run `composer cs-check` after any PHP file change to catch formatting issues early
