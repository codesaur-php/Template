# Changelog - codesaur/template

This file documents all notable changes to the `codesaur/template` package.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [4.0.2] - 2026-06-09
[4.0.2]: https://github.com/codesaur-php/Template/compare/v4.0.1...v4.0.2

### Changed

- **Property names no longer use a leading underscore** (PSR-12 compliance)
  - `MemoryTemplate`: `$_html` -> `$html`, `$_vars` -> `$vars`
  - `FileTemplate`: `$_file` -> `$filepath`
  - Internal only - all properties are `protected`, so the public API is unchanged.

---

## [4.0.1] - 2026-04-27
[4.0.1]: https://github.com/codesaur-php/Template/compare/v4.0.0...v4.0.1

### Fixed

#### `{% for %}{% else %}{% endfor %}` empty-iterable branch
- **Bug:** Templates using the Twig-style `{% for %}{% else %}{% endfor %}`
  construct silently dropped *everything* after the `{% endfor %}` from the
  rendered output. The parser hit `{% else %}`, returned the loop body it had
  collected so far, but never consumed the `else`/`endfor` tokens - so the
  outer `buildTree` saw the orphan `else` as an end-marker and returned
  immediately. Both the `else` branch and any content following the loop were
  lost.
- **Root cause:** `buildFor()` only looked for `{% endfor %}` and had no
  knowledge of the `{% else %}` separator. The for-node had no `else` slot,
  and `renderFor()` returned an empty string for non-iterable / empty
  collections instead of falling back to the alternate branch.
- **Fix:** `buildFor()` now optionally consumes an `{% else %}` block and
  attaches its body as a new `else` key on the for-node. `renderFor()` renders
  that else body when the iterable is missing, non-iterable, or empty -
  matching Twig's documented behavior.

#### Object method calls in expressions
- **Bug:** Expressions like `{{ user.can('edit') }}` and `{% if auth.is('admin') %}`
  silently returned `null`, causing permission-gated UI to be hidden even from
  authorized users.
- **Root cause:** The `pPostfix()` parser only handled the `_self.macroName(...)`
  (macro invocation) case when it encountered a method call with arguments, and
  silently set the result to `null` for all other cases - completely skipping
  object and array-of-callables method dispatch.
- **Fix:** `pPostfix()` now handles three distinct cases when a `.name(args)`
  postfix is parsed:
  - `_self.macro(...)` -> `callMacro` (unchanged)
  - `object.method(...)` -> `$val->$method(...$args)` (guarded by `method_exists`)
  - `array['callable'](...)` -> invokes the callable array element directly
  - Otherwise -> `null` (unchanged)

### Added

#### Documentation
- Added "Method calls" line to the `MemoryTemplate` class docblock describing the
  newly supported `object.method(args)` and `array_of_callables.name(args)` forms.

#### Tests
- `testForElseWithItems` - `{% else %}` is skipped when the loop iterates
- `testForElseWithEmptyItems` - `{% else %}` renders for empty arrays
- `testForElseWithNonIterable` - `{% else %}` renders for null / non-iterable
- `testObjectMethodCall` - calling public methods on an object from expressions
- `testObjectMethodCallInIfBlock` - using a method call as an `{% if %}` condition
- `testArrayOfCallablesMethodCall` - calling closures stored as array values
- `testMissingMethodReturnsNull` - non-existent methods safely return `null`

---

## [4.0.0] - 2026-03-30
[4.0.0]: https://github.com/codesaur-php/Template/compare/v3.0.1...v4.0.0

### Changed

#### Completely removed Twig dependency
- Removed `twig/twig` package entirely from dependencies
- During development, the necessary capabilities inspired by Twig's syntax and design patterns
  were reimplemented as our own standalone engine
- Added ext-mbstring to requirements (for capitalize, upper, lower, length, etc.)

#### Migrated full engine into MemoryTemplate
- The complete template engine (tokenizer, parser, renderer, expression evaluator) has been
  moved from FileTemplate into MemoryTemplate. MemoryTemplate now supports if/for/set/macro,
  filter chains, expression parser, ternary/null coalescing, and loop variables
- FileTemplate is now a thin wrapper that only reads files and passes them to MemoryTemplate's
  engine

#### New methods added to MemoryTemplate
- `addFilter(string, callable)`, `addFunction(string, callable)` - register custom filters/functions
- 33 built-in filters (e, date, length, keys, slice, json_encode, json_decode, abs, trim, striptags, title, join, reverse, sort, unique, column, batch, values, replace, wordwrap, etc.)
- Built-in functions: `attribute`, `range`, `max`, `min`

### Removed
- Removed the **TwigTemplate** class (merged into FileTemplate)
- `twig/twig` dependency (15+ files, ~50,000 lines of code removed)
- `addGlobal()`, `getEnvironment()` methods (Twig-specific)
- `{# comment #}` template comment syntax
- Removed the `stringify()` protected method - replaced with `(string)` cast

---

## [3.0.1] - 2026-03-05
[3.0.1]: https://github.com/codesaur-php/Template/compare/v3.0.0...v3.0.1

### Changed

#### Documentation Cleanup
- Removed all Unicode emoji characters from documentation files
- Replaced Unicode arrow with ASCII arrow

---

## [3.0.0] - 2026-01-08
[3.0.0]: https://github.com/codesaur-php/Template/compare/v2.0.0...v3.0.0

### Stable Release

This version is the stable release of the `codesaur/template` package with complete features, full test coverage, and comprehensive documentation.

### Added

#### Core Functionality
- **MemoryTemplate** - Lightweight template engine with simple {{key}} placeholders
- **FileTemplate** - File-based template loader (extends MemoryTemplate)
- **TwigTemplate** - Advanced renderer fully integrated with Twig engine (extends FileTemplate)

#### Testing
- Unit, Integration, Performance, Memory tests (70+ tests, 1200+ assertions)
- 100% line coverage, 100% method coverage

#### CI/CD
- GitHub Actions CI/CD pipeline (PHP 8.2)

#### Documentation
- README.md, API.md, REVIEW.md (Mongolian and English)

---

## [2.0.0] - 2025-11-28
[2.0.0]: https://github.com/codesaur-php/Template/compare/v1.0...v2.0.0

### Added

#### Core Improvements
- Enhanced template processing capabilities
- Improved error handling
- Better file template support

---

## [1.0] - 2021-03-09
[1.0]: https://github.com/codesaur-php/Template/releases/tag/v1.0

### Initial Release

- **MemoryTemplate** - Basic template engine with {{key}} placeholders
- **FileTemplate** - File-based template loader
- **TwigTemplate** - Twig engine integration
