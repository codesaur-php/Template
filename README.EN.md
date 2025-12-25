# 🧩 codesaur/template  

[![CI](https://github.com/codesaur-php/Template/actions/workflows/ci.yml/badge.svg)](https://github.com/codesaur-php/Template/actions/workflows/ci.yml)
[![PHP Version](https://img.shields.io/badge/php-%5E8.2.1-777BB4.svg?logo=php)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

**Language:** [🇲🇳 Монгол](README.md) | **🇬🇧 English**

A minimal, extensible template engine that supports everything from simple text-based templates to powerful templates written with Twig.

`codesaur/template` is part of the codesaur Framework and is a minimal, extensible PHP template engine that supports everything from simple text-based templates to powerful templates written with Twig.

The package consists of the following 3 core classes:

- **MemoryTemplate** - lightweight engine with simple {{key}} placeholders  
- **FileTemplate** - file-based template loader  
- **TwigTemplate** - advanced renderer fully integrated with Twig engine  

---

## CI/CD

This project is automatically tested using GitHub Actions. The CI/CD pipeline checks the following:

- ✅ Syntax check on PHP 8.2, 8.3, 8.4
- ✅ Composer dependencies installation
- ✅ PHP file syntax error check
- ✅ PHPUnit unit tests execution
- ✅ PHPUnit integration tests execution

CI/CD status can be viewed on the [GitHub Actions](https://github.com/codesaur-php/Template/actions) page.

---

## Features

- 🟢 Supports all whitespace/no-whitespace formats such as `{{ key }}`, `{{key}}`, `{{ user.profile.email }}`  
- 🟢 Nested variable support (multi-level arrays)  
- 🟢 Perfect override structure - Memory → File → Twig  
- 🟢 Full support for Twig filters, functions, globals  
- 🟢 Zero external dependencies (Twig is only required when using TwigTemplate)  
- 🟢 Framework-agnostic, fully compatible with codesaur, Laravel, Symfony, Slim and all other PHP frameworks
- 🟢 Complete PHPDoc documentation (all methods, parameters, return types are clear)
- 🟢 Unit and Integration tests (45 unit + 10 integration tests)

---

## Installation

Install via Composer:

```
composer require codesaur/template
```

---

## Usage Example 1 - MemoryTemplate (simple)

```
use codesaur\Template\MemoryTemplate;

$template = new MemoryTemplate(
    'Hello, {{ user.name }}!',
    ['user' => ['name' => 'Narankhuu']]
);

echo $template;
```

Output:

```
Hello, Narankhuu!
```

---

## Usage Example 2 - FileTemplate

```
use codesaur\Template\FileTemplate;

$template = new FileTemplate(__DIR__ . '/page.html', [
    'title' => 'Hello Codesaur',
    'message' => 'This is file-based templating.'
]);

echo $template->output();
```

---

## Usage Example 3 - TwigTemplate (Bootstrap example)

`example/index.php`:

```
use codesaur\Template\TwigTemplate;

$template = new TwigTemplate(__DIR__ . '/example.html', [
    'title' => 'Template Example',
    'menu'  => ['Home', 'About', 'Products', 'Contact'],
    'items' => [
        ['title' => 'Lightweight', 'text' => 'Fast, simple template system.'],
        ['title' => 'Flexible', 'text' => 'Supports Plain, File-based and Twig Templates.'],
        ['title' => 'Powerful', 'text' => 'Can use nested variables, Twig filters, functions.'],
    ]
]);

$template->render();
```

`example/example.html`:

```html
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ title }}</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">codesaur/template</a>
        <ul class="navbar-nav ms-auto">
            {% for item in menu %}
                <li class="nav-item"><a href="#" class="nav-link text-uppercase">{{ item }}</a></li>
            {% endfor %}
        </ul>
    </div>
</nav>

<div class="container">
    <div class="text-center mb-5">
        <h1>{{ title }}</h1>
        <p class="text-muted">A simple, clean example using TwigTemplate and Bootstrap.</p>
    </div>

    <div class="row g-4">
        {% for box in items %}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 text-center">
                <div class="card-body">
                    <h4>{{ box.title }}</h4>
                    <p class="text-muted">{{ box.text }}</p>
                </div>
            </div>
        </div>
        {% endfor %}
    </div>
</div>

<footer class="text-center text-muted mt-5 py-4">
    <small>&copy; {{ "now"|date("Y") }} codesaur framework</small>
</footer>

</body>
</html>
```

---

## Running Unit Tests

This package includes complete unit tests using PHPUnit. To run the tests:

### 1. Install Dependencies

```bash
composer install
```

Or only dev dependencies:

```bash
composer install --dev
```

### 2. Run Tests

#### Windows (Command Prompt):

```cmd
vendor\bin\phpunit.bat
```

#### Linux / macOS:

```bash
./vendor/bin/phpunit
```

#### On Any OS (using Composer):

```bash
composer test
```

Generate coverage report:

```bash
composer test-coverage
```

### 3. View Test Coverage

Xdebug must be installed before generating coverage reports. To configure Xdebug coverage mode:

#### Windows (Command Prompt):

```cmd
REM Set Xdebug mode
set XDEBUG_MODE=coverage

REM Generate coverage report
vendor\bin\phpunit.bat --coverage-html coverage
```

#### Linux / macOS:

```bash
# Set Xdebug mode
export XDEBUG_MODE=coverage

# Generate coverage report
./vendor/bin/phpunit --coverage-html coverage

# Or use Composer script
composer test-coverage
```

**Note:** If Xdebug is installed, add the following configuration to your `php.ini` file:

```ini
[xdebug]
zend_extension=xdebug
xdebug.mode=coverage,debug
```

Coverage report will be generated in the `coverage/` folder. You can open the HTML file in a browser.

### 4. Run Specific Test File

#### Windows:

```cmd
vendor\bin\phpunit.bat tests/MemoryTemplateTest.php
```

#### Linux / macOS:

```bash
./vendor/bin/phpunit tests/MemoryTemplateTest.php
```

### 5. Run Specific Test Method

#### Windows:

```cmd
vendor\bin\phpunit.bat --filter testSimpleVariableReplacement tests/MemoryTemplateTest.php
```

#### Linux / macOS:

```bash
./vendor/bin/phpunit --filter testSimpleVariableReplacement tests/MemoryTemplateTest.php
```

### Test Files

#### Unit Tests
- `tests/MemoryTemplateTest.php` - MemoryTemplate class unit tests
- `tests/FileTemplateTest.php` - FileTemplate class unit tests
- `tests/TwigTemplateTest.php` - TwigTemplate class unit tests

#### Integration Tests
- `tests/Integration/TemplateIntegrationTest.php` - Template classes integration tests
  - Tests working with real file system
  - Tests working with multiple template files together
  - Real-world scenarios tests
  - Template inheritance chain tests

### 6. Run Integration Tests

Integration tests check real use cases such as working with real file systems and multiple templates together.

#### Windows:

```cmd
vendor\bin\phpunit.bat tests/Integration/
```

#### Linux / macOS:

```bash
./vendor/bin/phpunit tests/Integration/
```

#### Run Specific Integration Test Method:

```bash
# Windows
vendor\bin\phpunit.bat --filter testRealWorldScenario tests/Integration/

# Linux / macOS
./vendor/bin/phpunit --filter testRealWorldScenario tests/Integration/
```

---

## API Overview

### MemoryTemplate
- `__construct(string $template = '', array $vars = [])`
- `set(string $key, $value)`
- `setVars(array $values)`
- `get(string $key)`
- `getVars(): array`
- `output(): string`

### FileTemplate
- Inherits all MemoryTemplate API
- `file(string $filepath)`
- `getFileSource(): string`
- `output(): string`

### TwigTemplate
- Extends FileTemplate
- Additional API:
  - `getEnvironment(): Environment`
  - `addGlobal(string $name, $value)`
  - `addFilter(TwigFilter $filter)`
  - `addFunction(TwigFunction $function)`

**Detailed API Documentation:** See [API.EN.md](API.EN.md) file.

---

## Documentation

This package includes the following documentation:

- **[API.EN.md](API.EN.md)** - Complete API documentation (automatically generated from PHPDoc using Cursor AI)
  - Detailed description of all classes, methods, parameters, return types
  - Exception reference
  - Usage examples
  - Best practices
  
- **[REVIEW.EN.md](REVIEW.EN.md)** - Review report (generated using Cursor AI)
  - Code improvement descriptions
  - Test coverage report
  - Code quality assessment
  - Metrics and conclusions

---

## Example Folder Structure

```
/example
    index.php
    example.html
    .htaccess
/src
    MemoryTemplate.php
    FileTemplate.php
    TwigTemplate.php
/tests
    MemoryTemplateTest.php
    FileTemplateTest.php
    TwigTemplateTest.php
    /Integration
        TemplateIntegrationTest.php
README.md
README.EN.md
API.md
API.EN.md
REVIEW.md
REVIEW.EN.md
CHANGELOG.md
CHANGELOG.EN.md
composer.json
phpunit.xml.dist
.github/workflows/ci.yml
LICENSE
```

---

## License

This project is licensed under the MIT license.

---

## Author

**Narankhuu**  
📧 codesaur@gmail.com  
📲 [+976 99000287](https://wa.me/97699000287)  
🌐 https://github.com/codesaur  
