# codesaur/template

A self-contained PHP template engine. During its evolution, adopted syntax and design patterns inspired by Twig.

`codesaur/template` is part of the **codesaur ecosystem** and provides everything from simple text-based templates to powerful templates with control structures, filters, functions, macros, and expression parsing - all without any external dependencies.

The package consists of 2 core classes:

- **MemoryTemplate** - full template engine (if, for, filter, function, macro, expression parser, 33 built-in filters)
- **FileTemplate** - file-based template loader wrapper (extends MemoryTemplate)

---

## CI/CD

This project is automatically tested using GitHub Actions. The CI/CD pipeline checks the following:

- Syntax check on PHP 8.2
- Composer dependencies installation
- PHP file syntax error check
- PHPUnit unit tests execution
- PHPUnit integration tests execution

CI/CD status can be viewed on the [GitHub Actions](https://github.com/codesaur-php/Template/actions) page.

---

## Features

- Supports all whitespace/no-whitespace formats such as `{{ key }}`, `{{key}}`, `{{ user.profile.email }}`
- Nested variable support (multi-level arrays)
- Full template syntax: if/elseif/else, for loops, macros, filters, functions, expressions
- 33 built-in filters (e, escape, date, length, keys, slice, split, merge, json_encode, number_format, capitalize, upper, lower, default, round, nl2br, url_encode, format, first, last, etc.)
- Custom filter/function registration: `addFilter()`, `addFunction()`
- MemoryTemplate runs the full engine. FileTemplate is just a file-reading wrapper
- Framework-agnostic, fully compatible with codesaur, Laravel, Symfony, Slim and all other PHP frameworks
- Complete PHPDoc documentation
- Unit, Integration, Performance and Memory tests (95 tests, 1267 assertions)

---

## Installation

Install via Composer:

```
composer require codesaur/template
```

---

## Usage Example 1 - MemoryTemplate

MemoryTemplate now includes the full engine - if, for, filter, function all work directly:

```php
use codesaur\Template\MemoryTemplate;

$template = new MemoryTemplate(
    '{% for user in users %}{{ user.name|upper }}, {% endfor %}',
    ['users' => [
        ['name' => 'Narankhuu'],
        ['name' => 'Erdenebat'],
    ]]
);

echo $template; // NARANKHUU, ERDENEBAT,
```

Simple placeholder example:

```php
$template = new MemoryTemplate(
    'Hello, {{ user.name }}!',
    ['user' => ['name' => 'Narankhuu']]
);

echo $template; // Hello, Narankhuu!
```

Register custom function:

```php
$template = new MemoryTemplate('{{ link("home") }}');
$template->addFunction('link', fn($route) => "/app/$route");
echo $template; // /app/home
```

---

## Usage Example 2 - FileTemplate

FileTemplate reads from files and processes through MemoryTemplate's engine:

```php
use codesaur\Template\FileTemplate;

$template = new FileTemplate(__DIR__ . '/page.html', [
    'title' => 'Hello Codesaur',
    'items' => ['Home', 'About', 'Contact']
]);
$template->addFunction('text', fn($key) => $translations[$key] ?? $key);

echo $template->output();
```

---

## Usage Example 3 - FileTemplate (Bootstrap example)

`example/index.php`:

```php
use codesaur\Template\FileTemplate;

$template = new FileTemplate(__DIR__ . '/example.html', [
    'title' => 'Template Example',
    'menu'  => ['Home', 'About', 'Products', 'Contact'],
    'items' => [
        ['title' => 'Lightweight', 'text' => 'Fast, simple template system.'],
        ['title' => 'Flexible', 'text' => 'Supports Plain and File-based Templates.'],
        ['title' => 'Powerful', 'text' => 'Can use nested variables, filters, functions.'],
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

</body>
</html>
```

---

## Running Tests

This package includes complete tests using PHPUnit. All tests can be run using Composer:

### 1. Install Dependencies

```bash
composer install
```

### 2. Run All Tests

```bash
composer test
```

### 3. Run Specific Test File

```bash
vendor/bin/phpunit tests/MemoryTemplateTest.php
```

**Windows users:** Replace `vendor/bin/phpunit` with `vendor\bin\phpunit.bat`

### Test Files

- `tests/MemoryTemplateTest.php` - MemoryTemplate class unit tests
- `tests/EngineTest.php` - Template engine unit tests (if, for, filter, macro)
- `tests/Integration/TemplateIntegrationTest.php` - Integration tests
- `tests/PerformanceTest.php` - Performance tests
- `tests/MemoryTest.php` - Memory usage tests

### Test Statistics

- **Total Tests:** 100+ tests
- **Assertions:** 1300+ assertions

---

## API Overview

### MemoryTemplate

Full template engine - includes if, for, filter, function, macro, and expression parser:

- `__construct(string $template = '', array $vars = [])`
- `set(string $key, $value)` / `setVars(array $values)` / `get(string $key)` / `getVars(): array`
- `source(string $html)` / `getSource(): string`
- `output(): string` / `render()` / `__toString()`
- `addFilter(string, callable)` / `addFunction(string, callable)`

### FileTemplate

Inherits all MemoryTemplate API + file management:

- `file(string $filepath)` / `getFileName(): string`
- `getFileSource(): string`
- `output(): string` (reads file and compiles)

---

## Documentation

- **[API](api.md)** - Complete API documentation
- **[REVIEW](review.md)** - Code review report

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
