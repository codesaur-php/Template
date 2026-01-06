# 📝 Changelog - codesaur/template

This file documents all notable changes to the `codesaur/template` package.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [3.0.3] - 2026-01-06
[3.0.3]: https://github.com/codesaur-php/Template/compare/v3.0.2...v3.0.3

### 🔧 Changed

#### Documentation
- ✅ Fixed minor typos and errors in documentation files
  - Improved consistency in documentation formatting

---

## [3.0.2] - 2026-01-05
[3.0.2]: https://github.com/codesaur-php/Template/compare/v3.0.1...v3.0.2

### ✨ Added

#### Testing
- ✅ **FileTemplate 100% method coverage achieved**
  - Added test for `getFileSource()` method when filename is empty
  - All 5 FileTemplate methods now fully tested

- ✅ **Performance Tests** (6 new tests)
  - Performance test with large MemoryTemplate (1000 variables)
  - Performance test with large FileTemplate (500 variables)
  - Performance test with deeply nested variables (3-level nesting)
  - Performance test with very long template content (~100KB)
  - Performance test with multiple sequential renders (100 renders)
  - Performance test with large FileTemplate file (~500KB)

- ✅ **Memory Usage Tests** (8 new tests)
  - Memory usage test with large MemoryTemplate (1000 variables)
  - Memory usage test with large FileTemplate (500 variables)
  - Memory usage test with very long template content (~100KB)
  - Memory usage test with multiple template instances (100 instances)
  - Memory usage test with deeply nested variables
  - Memory usage test with large FileTemplate file (~500KB)
  - Memory usage test with repeated renders (1000 renders)
  - Memory usage comparison between MemoryTemplate and FileTemplate

#### Test Statistics
- ✅ Total tests: 70+ tests (up from 55)
- ✅ Total assertions: 1200+ assertions (up from 59+)
- ✅ FileTemplate: 100% method coverage (5/5 methods)
- ✅ FileTemplate: 100% line coverage (21/21 lines)

### 🔧 Changed

#### Documentation
- ✅ Refactored test instructions in all README files
  - Replaced OS-specific commands with Composer commands (`composer test`, `composer test-coverage`)
  - Updated test file structure to include PerformanceTest.php and MemoryTest.php
  - Added Windows-specific note for phpunit commands
  - Simplified test running instructions (OS-agnostic)

- ✅ Updated test statistics across all documentation files
  - Updated test counts in README.md files (both Mongolian and English)
  - Updated test counts in API.md files
  - Updated test counts in REVIEW.md files
  - Updated file structure examples

- ✅ Updated API documentation version to 3.0.2
- ✅ Updated last updated dates in documentation files

### 📊 Test Coverage Improvements

- **FileTemplate Coverage:**
  - Methods: 80.00% (4/5) → 100.00% (5/5) ✅
  - Lines: 95.24% (20/21) → 100.00% (21/21) ✅

- **Overall Coverage:**
  - Classes: 66.67% (2/3) → 100.00% (3/3) ✅
  - Methods: 96.00% (24/25) → 100.00% (25/25) ✅
  - Lines: 98.72% (77/78) → 100.00% (78/78) ✅

---

## [3.0.1] - 2025-12-25
[3.0.1]: https://github.com/codesaur-php/Template/compare/v3.0.0...v3.0.1

### ✨ Added

#### Documentation
- ✅ English documentation files added
  - ✅ README.EN.md - English translation of README.md
  - ✅ API.EN.md - English translation of API.md
  - ✅ REVIEW.EN.md - English translation of REVIEW.md
  - ✅ CHANGELOG.EN.md - English translation of CHANGELOG.md
  - ✅ Language switching links added to all documentation files (Mongolian ↔ English)

---

## [3.0.0] - 2025-12-17
[3.0.0]: https://github.com/codesaur-php/Template/compare/v1.0...v3.0.0

### ✨ Added

#### Core Functionality
- ✅ Support for whitespace/no-whitespace formats (`{{ key }}`, `{{key}}`, `{{   key   }}`)
- ✅ Nested variable support (multi-level arrays, e.g., `{{ user.profile.email }}`)

#### Testing
- ✅ 45 unit tests, 59 assertions
- ✅ 10 integration tests
- ✅ Test coverage: 98.72% line coverage, 96.00% method coverage
- ✅ Real-world scenarios tests
- ✅ Template inheritance chain tests
- ✅ Tests working with real file system

#### CI/CD
- ✅ GitHub Actions CI/CD pipeline configured
- ✅ Automatic tests on PHP 8.2, 8.3, 8.4
- ✅ Composer dependencies installation
- ✅ PHP syntax check
- ✅ PHPUnit unit and integration tests

#### Documentation
- ✅ Complete PHPDoc documentation (all methods, parameters, return types are clear)
- ✅ API.md - Complete API documentation
- ✅ REVIEW.md - Code review report
- ✅ README.md - Detailed usage guide
- ✅ README.EN.md, API.EN.md, REVIEW.EN.md, CHANGELOG.EN.md - Documentation in English

#### PHPDoc Enhancements (2025-12-17)
- ✅ Detailed `@param` descriptions added to all methods
- ✅ `@return` descriptions added to all methods
- ✅ Return type declarations added (`void`, etc.)
- ✅ Array type annotations added (`array<string, mixed>`)
- ✅ Exception descriptions enhanced
- ✅ Detailed method descriptions and examples added

#### Integration Tests (2025-12-17)
- ✅ 10 integration tests added
- ✅ Tests working with real file system
- ✅ Real-world scenarios tests
- ✅ Template inheritance chain tests
- ✅ Multiple template files tests
- ✅ Nested template structure tests
- ✅ TwigTemplate advanced features tests
- ✅ Dynamic variable updates tests
- ✅ Template file content changes tests
- ✅ Custom filter/function integration tests
- ✅ Template caching simulation tests

---

## [1.0] - 2021-03-09
[1.0]: https://github.com/codesaur-php/Template/releases/tag/v1.0

### 🎉 Stable Release

This version is the stable release of the `codesaur/template` package.

### ✨ Added

#### Core Functionality
- ✅ **MemoryTemplate** - Lightweight template engine with simple {{key}} placeholders
  - Template source and variable management (set, get, has, setVars, getVars)
  - Render and output functions

- ✅ **FileTemplate** - File-based template loader (extends MemoryTemplate)
  - Read templates from file system
  - File path management
  - All FileTemplate functionality + MemoryTemplate functionality

- ✅ **TwigTemplate** - Advanced renderer fully integrated with Twig engine (extends FileTemplate)
  - Twig Environment integration
  - Add custom filters (`addFilter`)
  - Add custom functions (`addFunction`)
  - Add global variables (`addGlobal`)
  - Built-in filters: `int`, `json_decode`
  - Full support for all Twig syntax (variables, filters, functions, control structures, comments)
