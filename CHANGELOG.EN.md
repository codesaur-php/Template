# 📝 Changelog - codesaur/template

**Language:** [🇲🇳 Монгол](CHANGELOG.md) | **🇬🇧 English**

This file documents all notable changes to the `codesaur/template` package.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [3.0.1] - 2025-12-25

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

### 🎉 Stable Release

This version is the stable release of the `codesaur/template` package.

### ✨ Added

#### Core Functionality
- ✅ **MemoryTemplate** - Lightweight template engine with simple {{key}} placeholders
  - Support for whitespace/no-whitespace formats (`{{ key }}`, `{{key}}`, `{{   key   }}`)
  - Nested variable support (multi-level arrays, e.g., `{{ user.profile.email }}`)
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

### 🔧 Technical Details

#### PHP Requirements
- PHP 8.2.1+
- ext-json extension

#### Dependencies
- twig/twig: ^3.22.2 (optional, only required when using TwigTemplate)

#### Dev Dependencies
- phpunit/phpunit: ^10.0

### 📊 Metrics

- **Total Classes:** 3
- **Total Methods:** 25
- **Test Cases:** 45 unit tests + 10 integration tests
- **Test Assertions:** 59+
- **Line Coverage:** 98.72%
- **Method Coverage:** 96.00%
- **Class Coverage:** 66.67%

### 👥 Contributors

- **Narankhuu** - Original Author
- **AI Code Assistant** - Code Review, Documentation

---

## [Unreleased]

### Planned

- Improve FileTemplate coverage (add tests for remaining 1 method)
- Add performance tests (performance tests with large templates)
- Add memory usage tests

---

**Changelog Format:** [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)  
**Versioning:** [Semantic Versioning](https://semver.org/spec/v2.0.0.html)
