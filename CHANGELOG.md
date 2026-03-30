# Changelog - codesaur/template

This file documents all notable changes to the `codesaur/template` package.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [4.0.0] - 2026-03-30
[4.0.0]: https://github.com/codesaur-php/Template/compare/v3.0.1...v4.0.0

### Changed

#### Twig dependency-г бүрэн хассан
- `twig/twig` package-г dependency-ээс бүрэн хассан
- Хөгжлийн явцад Twig-ийн синтакс, дизайн загвараас санаа авч хэрэгтэй чадамжуудыг
  өөрийн бие даасан engine болгон хэрэгжүүлсэн
- ext-mbstring шаардлагад нэмсэн (capitalize, upper, lower, length гм.)

#### MemoryTemplate руу бүрэн engine шилжүүлсэн
- Бүрэн template engine (tokenizer, parser, renderer, expression evaluator) FileTemplate-ээс
  MemoryTemplate руу шилжсэн. Одоо MemoryTemplate нь if/for/set/macro, filter chain,
  expression parser, ternary/null coalescing, loop variables бүгдийг дэмжинэ
- FileTemplate нь зөвхөн файл уншиж MemoryTemplate-ийн engine-д дамжуулах
  нимгэн wrapper болсон

#### MemoryTemplate-д шинэ method-ууд нэмэгдсэн
- `addFilter(string, callable)`, `addFunction(string, callable)` - custom filter/function бүртгэх
- 33 built-in filter (e, date, length, keys, slice, json_encode, json_decode, abs, trim, striptags, title, join, reverse, sort, unique, column, batch, values, replace, wordwrap гм.)
- Built-in function: `attribute`, `range`, `max`, `min`

### Removed
- **TwigTemplate** class устгасан (FileTemplate руу нэгтгэсэн)
- `twig/twig` dependency (15+ файл, ~50,000 мөр код хасагдсан)
- `addGlobal()`, `getEnvironment()` методууд (Twig-specific)
- `{# comment #}` template comment синтакс
- `stringify()` protected method устгасан - `(string)` cast-аар солигдсон

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
