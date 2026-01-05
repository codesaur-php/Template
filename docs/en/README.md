# 🧩 codesaur/template  

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
- 🟢 Unit, Integration, Performance and Memory tests (70+ tests, 1200+ assertions)

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

## Running Tests

This package includes complete tests using PHPUnit. All tests can be run using Composer (works the same on any OS):

### 1. Install Dependencies

```bash
composer install
```

### 2. Run All Tests

```bash
composer test
```

### 3. Generate Test Coverage

Generate coverage report (Xdebug required):

```bash
composer test-coverage
```

**Note:** Xdebug must be installed to generate coverage reports. Add the following configuration to your `php.ini` file:

```ini
[xdebug]
zend_extension=xdebug
xdebug.mode=coverage,debug
```

Coverage report will be generated in the `coverage/` folder. You can open the HTML file in a browser.

### 4. Run Specific Test File or Method

Run specific test file:

```bash
vendor/bin/phpunit tests/MemoryTemplateTest.php
```

Run specific test method:

```bash
vendor/bin/phpunit --filter testSimpleVariableReplacement tests/MemoryTemplateTest.php
```

**Windows users:** Replace `vendor/bin/phpunit` with `vendor\bin\phpunit.bat`

### Test Files

#### Unit Tests
- `tests/MemoryTemplateTest.php` - MemoryTemplate class unit tests
- `tests/FileTemplateTest.php` - FileTemplate class unit tests (100% method coverage)
- `tests/TwigTemplateTest.php` - TwigTemplate class unit tests

#### Integration Tests
- `tests/Integration/TemplateIntegrationTest.php` - Template classes integration tests
  - Tests working with real file system
  - Tests working with multiple template files together
  - Real-world scenarios tests
  - Template inheritance chain tests

#### Performance Tests
- `tests/PerformanceTest.php` - Performance tests
  - Performance tests with large templates
  - Performance tests with many variables
  - Performance tests with deeply nested variables
  - Performance tests with multiple sequential renders

#### Memory Tests
- `tests/MemoryTest.php` - Memory usage tests
  - Memory usage tests with large templates
  - Memory usage tests with multiple template instances
  - Memory usage tests with deeply nested variables
  - Memory usage tests with multiple sequential renders

### Test Statistics

- **Total Tests:** 70+ tests
- **Assertions:** 1200+ assertions
- **Coverage:** 98.72% line coverage, 100% method coverage (FileTemplate)

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

---

## Documentation

This package includes the following documentation:

- **[API](api.md)** - Complete API documentation (automatically generated from PHPDoc using Cursor AI)
  - Detailed description of all classes, methods, parameters, return types
  - Exception reference
  - Usage examples
  - Best practices
  
- **[REVIEW](review.md)** - Review report (generated using Cursor AI)
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
    PerformanceTest.php
    MemoryTest.php
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

## Changelog

For version history and changes, see [CHANGELOG](../../CHANGELOG.md) file.

---

## License

This project is licensed under the MIT license.

---

## Author

**Narankhuu**  
https://github.com/codesaur  
