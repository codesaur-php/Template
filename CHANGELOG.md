# 📝 Changelog - codesaur/template

This file documents all notable changes to the `codesaur/template` package.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [3.0.0] - 2026-01-08
[3.0.0]: https://github.com/codesaur-php/Template/compare/v2.0.0...v3.0.0

### 🎉 Stable Release

This version is the stable release of the `codesaur/template` package with complete features, full test coverage, and comprehensive documentation.

### ✨ Added

#### Core Functionality
- ✅ **MemoryTemplate** - Lightweight template engine with simple {{key}} placeholders
  - Support for whitespace/no-whitespace formats (`{{ key }}`, `{{key}}`, `{{   key   }}`)
  - Nested variable support (multi-level arrays, e.g., `{{ user.profile.email }}`)
  - Template source and variable management (set, get, has, setVars, getVars)
  - Render and output functions
  - String conversion for all data types (arrays, objects, primitives)

- ✅ **FileTemplate** - File-based template loader (extends MemoryTemplate)
  - Read templates from file system
  - File path management (`file()`, `getFileName()`)
  - File source reading (`getFileSource()`)
  - Error handling for missing files and file reading errors
  - All FileTemplate functionality + MemoryTemplate functionality

- ✅ **TwigTemplate** - Advanced renderer fully integrated with Twig engine (extends FileTemplate)
  - Twig Environment integration
  - Add custom filters (`addFilter`)
  - Add custom functions (`addFunction`)
  - Add global variables (`addGlobal`)
  - Built-in filters: `int`, `json_decode`
  - Full support for all Twig syntax (variables, filters, functions, control structures, comments)
  - Autoescape disabled for flexibility

#### Testing
- ✅ **Unit Tests** (11 tests for FileTemplate, full coverage for all classes)
  - FileTemplate rendering tests
  - FileTemplate file management tests
  - FileTemplate error handling tests
  - MemoryTemplate comprehensive tests
  - TwigTemplate comprehensive tests

- ✅ **Integration Tests** (10 tests)
  - Template inheritance chain tests
  - Multiple template files tests
  - Nested template structure tests
  - TwigTemplate advanced features tests
  - Dynamic variable updates tests
  - Template file content changes tests
  - Custom filter/function integration tests
  - Real-world scenario tests
  - Template caching simulation tests
  - Tests working with real file system

- ✅ **Performance Tests** (6 tests)
  - Performance test with large MemoryTemplate (1000 variables)
  - Performance test with large FileTemplate (500 variables)
  - Performance test with deeply nested variables (3-level nesting)
  - Performance test with very long template content (~100KB)
  - Performance test with multiple sequential renders (100 renders)
  - Performance test with large FileTemplate file (~500KB)

- ✅ **Memory Usage Tests** (8 tests)
  - Memory usage test with large MemoryTemplate (1000 variables)
  - Memory usage test with large FileTemplate (500 variables)
  - Memory usage test with very long template content (~100KB)
  - Memory usage test with multiple template instances (100 instances)
  - Memory usage test with deeply nested variables
  - Memory usage test with large FileTemplate file (~500KB)
  - Memory usage test with repeated renders (1000 renders)
  - Memory usage comparison between MemoryTemplate and FileTemplate

#### Test Statistics
- ✅ Total tests: 70+ tests
- ✅ Total assertions: 1200+ assertions
- ✅ Test coverage: 100% class coverage (3/3)
- ✅ Test coverage: 100% method coverage (25/25)
- ✅ Test coverage: 100% line coverage (78/78)
- ✅ FileTemplate: 100% method coverage (5/5 methods)
- ✅ FileTemplate: 100% line coverage (21/21 lines)
- ✅ All tests passing

#### CI/CD
- ✅ GitHub Actions CI/CD pipeline configured
- ✅ Automatic tests on PHP 8.2, 8.3, 8.4
- ✅ Composer dependencies installation
- ✅ PHP syntax check
- ✅ PHPUnit unit, integration, performance and memory tests
- ✅ Automatic test execution on push/pull request

#### Documentation
- ✅ Complete PHPDoc documentation (all methods, parameters, return types are clear)
- ✅ Detailed `@param` descriptions added to all methods
- ✅ `@return` descriptions added to all methods
- ✅ Return type declarations added (`void`, etc.)
- ✅ Array type annotations added (`array<string, mixed>`)
- ✅ Exception descriptions enhanced
- ✅ Detailed method descriptions and examples added

#### Documentation Files
- ✅ README.md - Detailed usage guide (Mongolian and English)
- ✅ API.md - Complete API documentation (Mongolian and English)
  - All classes, methods, parameters, return types documented
  - Exception reference
  - Usage examples
  - Best practices
- ✅ REVIEW.md - Code review report (Mongolian and English)
  - Code quality assessment
  - Test coverage report
  - Metrics and conclusions
- ✅ CHANGELOG.md - Complete version history

#### Documentation Structure
- ✅ Documentation organized in `docs/` folder
  - `docs/mn/` - Mongolian documentation
  - `docs/en/` - English documentation
- ✅ All documentation files include examples and usage guides
- ✅ Test running instructions (OS-agnostic using Composer)

---

## [2.0.0] - 2025-11-28
[2.0.0]: https://github.com/codesaur-php/Template/compare/v1.0...v2.0.0

### ✨ Added

#### Core Improvements
- ✅ Enhanced template processing capabilities
- ✅ Improved error handling
- ✅ Better file template support

---

## [1.0] - 2021-03-09
[1.0]: https://github.com/codesaur-php/Template/releases/tag/v1.0

### 🎉 Initial Release

First stable version of the `codesaur/template` package.

### ✨ Added

#### Core Functionality
- ✅ **MemoryTemplate** - Basic template engine with {{key}} placeholders
  - Simple variable replacement
  - Template source and variable management

- ✅ **FileTemplate** - File-based template loader
  - Read templates from file system
  - Basic file path management

- ✅ **TwigTemplate** - Twig engine integration
  - Basic Twig Environment integration
  - Support for Twig syntax
