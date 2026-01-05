# 📚 API Documentation - codesaur/template

**Version:** 3.0.2  
**PHP Version:** 8.2.1+  
**Last Updated:** 2026-01-05  

---

## Table of Contents

- [Overview](#overview)
- [MemoryTemplate](#memorytemplate)
- [FileTemplate](#filetemplate)
- [TwigTemplate](#twigtemplate)
- [Examples](#examples)

---

## Overview

`codesaur/template` consists of 3 core classes:

1. **MemoryTemplate** - Lightweight engine with simple placeholders
2. **FileTemplate** - File-based template loader (extends MemoryTemplate)
3. **TwigTemplate** - Advanced renderer fully integrated with Twig engine (extends FileTemplate)

**Inheritance Hierarchy:**
```
MemoryTemplate
    └── FileTemplate
        └── TwigTemplate
```

---

## MemoryTemplate

Lightweight template engine designed to process simple HTML or text-based templates.

### Class Signature

```php
class MemoryTemplate
```

### Properties

#### `protected string $_html`
The main HTML or text source of the template.

#### `protected array<string, mixed> $_vars`
Array of variables to be inserted into the template.

---

### Methods

#### `__construct(string $template = '', array $vars = [])`

Constructor to create a MemoryTemplate object.

**Parameters:**
- `string $template` - Initial HTML or text value of the template (optional, default: `''`)
- `array $vars` - Array of variables to use in the template (optional, default: `[]`)

**Returns:** `void`

**Example:**
```php
$template = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
```

---

#### `__toString(): string`

Returns the result of the `output()` function when the object is directly echoed.

**Returns:** `string` - Processed template

**Example:**
```php
$template = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
echo $template; // Output: "Hello, World!"
```

---

#### `source(string $html): void`

Sets the source HTML/text content of the template.

**Parameters:**
- `string $html` - HTML or text content

**Returns:** `void`

**Example:**
```php
$template = new MemoryTemplate();
$template->source('<h1>{{ title }}</h1>');
```

---

#### `has(string $key): bool`

Checks if a specific variable exists in the template.

**Parameters:**
- `string $key` - Variable key

**Returns:** `bool` - Returns `true` if the variable exists, `false` otherwise

**Example:**
```php
$template = new MemoryTemplate('', ['name' => 'John']);
$template->has('name'); // true
$template->has('email'); // false
```

---

#### `set(string $key, $value): void`

Adds or updates a variable to be used in the template in name=value format.

**Parameters:**
- `string $key` - Variable key
- `mixed $value` - Variable value (any type)

**Returns:** `void`

**Example:**
```php
$template = new MemoryTemplate('{{ name }}');
$template->set('name', 'John');
$template->set('age', 30);
```

---

#### `setVars(array $values): void`

Adds or updates multiple variables at once.

**Parameters:**
- `array<string, mixed> $values` - Array of variables

**Returns:** `void`

**Example:**
```php
$template = new MemoryTemplate('{{ name }} is {{ age }} years old');
$template->setVars([
    'name' => 'John',
    'age' => 30
]);
```

---

#### `get(string $key): mixed`

Returns the variable value by reference. Returns `null` if the variable is not found.

**Parameters:**
- `string $key` - Variable key

**Returns:** `mixed` - Variable value or `null`

**Example:**
```php
$template = new MemoryTemplate('', ['name' => 'John']);
$value = &$template->get('name'); // 'John'
$value = 'Jane'; // Variable is updated
```

---

#### `getVars(): array<string, mixed>`

Gets all variables used in the template as an array.

**Returns:** `array<string, mixed>` - Array of variables

**Example:**
```php
$template = new MemoryTemplate('', ['name' => 'John', 'age' => 30]);
$vars = $template->getVars(); // ['name' => 'John', 'age' => 30]
```

---

#### `getSource(): string`

Returns the source HTML/text of the template.

**Returns:** `string` - Source HTML/text of the template

**Example:**
```php
$template = new MemoryTemplate('Hello, {{ name }}!');
$source = $template->getSource(); // 'Hello, {{ name }}!'
```

---

#### `render(): void`

Renders the template and echoes it.

**Returns:** `void`

**Example:**
```php
$template = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
$template->render(); // Output: "Hello, World!"
```

---

#### `output(): string`

Returns the final processed HTML of the template.

**Returns:** `string` - Processed HTML

**Example:**
```php
$template = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
$html = $template->output(); // "Hello, World!"
```

---

#### `protected compile(string $html): string`

Processes template tags and produces final HTML.

**Supported Syntax:**
- `{{ key }}`
- `{{key}}`
- `{{   user.profile.email   }}`

If a variable is not found, the tag itself remains.

**Parameters:**
- `string $html` - Source HTML or text of the template

**Returns:** `string` - Processed final HTML or text

**Note:** Protected method - intended for internal use

---

#### `protected resolveValue(string $path): mixed|null`

Resolves the value of a multi-level key (like user.profile.email).

**Example:** `"user.profile.email"` → `$vars['user']['profile']['email']`

**Parameters:**
- `string $path` - Key path in "a.b.c" format (dot-separated)

**Returns:** `mixed|null` - Found value or `null` (if not found)

**Note:** Protected method - intended for internal use

---

#### `protected stringify($content): string`

Converts arrays or any type of data to text.

If it's an array, all elements are concatenated sequentially. For other types, it performs a string cast.

**Parameters:**
- `mixed $content` - Data to convert

**Returns:** `string` - Value converted to text format

**Note:** Protected method - intended for internal use

---

## FileTemplate

Extends `MemoryTemplate` to provide the ability to read and render templates from the file system.

### Class Signature

```php
class FileTemplate extends MemoryTemplate
```

### Properties

#### `protected string $_file`
Full path of the template file to be rendered.

---

### Methods

#### `__construct(string $template = '', array $vars = [])`

FileTemplate constructor.

**Parameters:**
- `string $template` - Path to the template file (can be empty, set later using the `file()` method)
- `array $vars` - Array of variables to pass to the template (optional, default: `[]`)

**Returns:** `void`

**Example:**
```php
$template = new FileTemplate(__DIR__ . '/template.html', ['name' => 'World']);
```

---

#### `file(string $filepath): void`

Sets the path of the template file to use.

**Parameters:**
- `string $filepath` - Path to the template file

**Returns:** `void`

**Throws:**
- `\InvalidArgumentException` - If the file name is empty

**Example:**
```php
$template = new FileTemplate();
$template->file(__DIR__ . '/template.html');
```

---

#### `getFileName(): string`

Returns the currently set template file path.

**Returns:** `string` - File path

**Example:**
```php
$template = new FileTemplate(__DIR__ . '/template.html');
$path = $template->getFileName(); // '/path/to/template.html'
```

---

#### `getFileSource(): string`

Reads and returns the content of the template file.

**Returns:** `string` - HTML/text content of the file

**Throws:**
- `\RuntimeException` - If file is not specified, file doesn't exist, or error occurs when reading

**Example:**
```php
$template = new FileTemplate(__DIR__ . '/template.html');
$content = $template->getFileSource(); // File content
```

---

#### `output(): string`

Reads the template file and returns final HTML using `MemoryTemplate`'s `compile()`.

This method overrides `MemoryTemplate`'s `output()`, reads the file content, and passes it to `compile()`.

**Returns:** `string` - Final processed HTML

**Throws:**
- `\RuntimeException` - If error occurs when reading file

**Example:**
```php
$template = new FileTemplate(__DIR__ . '/template.html', ['name' => 'World']);
$html = $template->output(); // Processed HTML
```

---

### Inherited Methods

`FileTemplate` inherits all public methods from `MemoryTemplate`:

- `__toString(): string`
- `source(string $html): void`
- `has(string $key): bool`
- `set(string $key, $value): void`
- `setVars(array $values): void`
- `get(string $key): mixed`
- `getVars(): array<string, mixed>`
- `getSource(): string`
- `render(): void`

---

## TwigTemplate

Extends `FileTemplate` to provide more powerful and flexible template processing using the Twig template engine.

### Class Signature

```php
class TwigTemplate extends FileTemplate
```

### Properties

#### `protected Environment $_environment`
TwigEnvironment object - contains the Twig engine with all configurations.

---

### Methods

#### `__construct(string $template = '', array $vars = [])`

TwigTemplate constructor.

**Parameters:**
- `string $template` - Path to the template file
- `array $vars` - Array of variables to pass to the template (optional, default: `[]`)

**Returns:** `void`

**Note:** Constructor creates Twig Environment with `autoescape=false` configuration. Also automatically adds `int` and `json_decode` filters.

**Example:**
```php
$template = new TwigTemplate(__DIR__ . '/template.twig', ['name' => 'World']);
```

---

#### `getEnvironment(): Environment`

Returns the Twig Environment object.

**Returns:** `\Twig\Environment` - Twig Environment instance

**Example:**
```php
$template = new TwigTemplate(__DIR__ . '/template.twig');
$env = $template->getEnvironment();
// Can work directly with Environment
```

---

#### `addGlobal(string $name, $value): void`

Adds an external global variable to the template.

**Parameters:**
- `string $name` - Variable name
- `mixed $value` - Variable value

**Returns:** `void`

**Example:**
```php
$template = new TwigTemplate(__DIR__ . '/template.twig');
$template->addGlobal('app_name', 'MyApp');
// Can use {{ app_name }} in template
```

---

#### `addFilter(TwigFilter $filter): void`

Adds a custom filter to Twig.

**Parameters:**
- `\Twig\TwigFilter $filter` - TwigFilter instance

**Returns:** `void`

**Example:**
```php
use Twig\TwigFilter;

$template = new TwigTemplate(__DIR__ . '/template.twig');
$template->addFilter(new TwigFilter('uppercase', function ($string) {
    return strtoupper($string);
}));
// Can use {{ value|uppercase }} in template
```

---

#### `addFunction(TwigFunction $function): void`

Adds a custom function to Twig.

**Parameters:**
- `\Twig\TwigFunction $function` - TwigFunction instance

**Returns:** `void`

**Example:**
```php
use Twig\TwigFunction;

$template = new TwigTemplate(__DIR__ . '/template.twig');
$template->addFunction(new TwigFunction('greet', function ($name) {
    return "Hello, $name!";
}));
// Can use {{ greet("World") }} in template
```

---

#### `protected compile(string $html): string`

Main compile function of TwigTemplate.

FileTemplate → reads file content, MemoryTemplate → `compile()` is overridden and passed to Twig.

This method creates a virtual template named "result" using ArrayLoader and processes it using Twig's `render()`.

**Parameters:**
- `string $html` - Source HTML of the template read from file

**Returns:** `string` - Final HTML processed by Twig engine

**Throws:**
- `\Twig\Error\LoaderError` - If template loader error occurs
- `\Twig\Error\RuntimeError` - If runtime error occurs
- `\Twig\Error\SyntaxError` - If template syntax error occurs

**Note:** Protected method - intended for internal use

---

### Built-in Filters

TwigTemplate automatically adds the following filters:

#### `int`
Converts to numeric value.

**Example:**
```twig
{{ value|int }}
```

#### `json_decode`
Converts JSON string to array or object.

**Example:**
```twig
{{ json|json_decode }}
```

---

### Inherited Methods

`TwigTemplate` inherits all public methods from `FileTemplate` and `MemoryTemplate`:

- `file(string $filepath): void`
- `getFileName(): string`
- `getFileSource(): string`
- `__toString(): string`
- `source(string $html): void`
- `has(string $key): bool`
- `set(string $key, $value): void`
- `setVars(array $values): void`
- `get(string $key): mixed`
- `getVars(): array<string, mixed>`
- `getSource(): string`
- `render(): void`
- `output(): string`

---

## Examples

### MemoryTemplate Example

```php
use codesaur\Template\MemoryTemplate;

// Simple variable
$template = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
echo $template; // Output: "Hello, World!"

// Multi-level variable
$template = new MemoryTemplate('Email: {{ user.email }}', [
    'user' => ['email' => 'test@example.com']
]);
echo $template; // Output: "Email: test@example.com"

// Add dynamic variables
$template = new MemoryTemplate('{{ name }} is {{ age }} years old');
$template->set('name', 'John');
$template->set('age', 30);
echo $template; // Output: "John is 30 years old"
```

### FileTemplate Example

```php
use codesaur\Template\FileTemplate;

// Use file template
$template = new FileTemplate(__DIR__ . '/page.html', [
    'title' => 'Hello Codesaur',
    'message' => 'This is file-based templating.'
]);

echo $template->output();

// Set file path later
$template = new FileTemplate();
$template->file(__DIR__ . '/page.html');
$template->set('title', 'New Title');
echo $template->output();
```

### TwigTemplate Example

```php
use codesaur\Template\TwigTemplate;
use Twig\TwigFilter;
use Twig\TwigFunction;

// Simple Twig template
$template = new TwigTemplate(__DIR__ . '/template.twig', [
    'title' => 'Template Example',
    'menu' => ['Home', 'About', 'Products'],
    'items' => [
        ['title' => 'Lightweight', 'text' => 'Fast template system.'],
        ['title' => 'Flexible', 'text' => 'Supports various types of templates.']
    ]
]);

$template->render();

// Add custom filter
$template->addFilter(new TwigFilter('uppercase', function ($string) {
    return strtoupper($string);
}));

// Add custom function
$template->addFunction(new TwigFunction('greet', function ($name) {
    return "Hello, $name!";
}));

// Add global variable
$template->addGlobal('app_name', 'MyApp');
```

### Template File Example (Twig)

`template.twig`:
```twig
<!doctype html>
<html>
<head>
    <title>{{ title }}</title>
</head>
<body>
    <nav>
        <ul>
            {% for item in menu %}
                <li>{{ item }}</li>
            {% endfor %}
        </ul>
    </nav>
    
    <div class="container">
        {% for box in items %}
            <div class="card">
                <h4>{{ box.title }}</h4>
                <p>{{ box.text }}</p>
            </div>
        {% endfor %}
    </div>
    
    <footer>
        <small>&copy; {{ "now"|date("Y") }} {{ app_name }}</small>
    </footer>
</body>
</html>
```

---

## Exception Reference

### `\InvalidArgumentException`

**Thrown by:**
- `FileTemplate::file()` - If file name is empty

**Example:**
```php
try {
    $template = new FileTemplate();
    $template->file(''); // Throws InvalidArgumentException
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage();
}
```

### `\RuntimeException`

**Thrown by:**
- `FileTemplate::getFileSource()` - If file is not specified, file doesn't exist, or error occurs when reading
- `FileTemplate::output()` - If error occurs when reading file

**Example:**
```php
try {
    $template = new FileTemplate('/nonexistent/file.html');
    $template->output(); // Throws RuntimeException
} catch (\RuntimeException $e) {
    echo $e->getMessage();
}
```

### `\Twig\Error\LoaderError`

**Thrown by:**
- `TwigTemplate::compile()` - If template loader error occurs

### `\Twig\Error\RuntimeError`

**Thrown by:**
- `TwigTemplate::compile()` - If runtime error occurs

### `\Twig\Error\SyntaxError`

**Thrown by:**
- `TwigTemplate::compile()` - If template syntax error occurs

**Example:**
```php
try {
    $template = new TwigTemplate(__DIR__ . '/invalid.twig');
    $template->output(); // Throws SyntaxError if template has syntax errors
} catch (\Twig\Error\SyntaxError $e) {
    echo $e->getMessage();
}
```

---

## Supported Template Syntax

### MemoryTemplate / FileTemplate

- `{{ key }}` - Simple variable
- `{{key}}` - Variable without whitespace
- `{{   key   }}` - Variable with whitespace
- `{{ user.profile.email }}` - Multi-level variable

### TwigTemplate

TwigTemplate supports all Twig syntax:

- Variables: `{{ variable }}`
- Filters: `{{ variable|filter }}`
- Functions: `{{ function() }}`
- Control Structures: `{% if %}`, `{% for %}`, `{% block %}`, etc.
- Comments: `{# comment #}`

For more information, see [Twig Documentation](https://twig.symfony.com/doc/).

---

## Best Practices

1. **MemoryTemplate** - Use for small, simple templates
2. **FileTemplate** - Use for file-based templates
3. **TwigTemplate** - Use when complex templates, loops, conditions are needed

4. **Exception handling** - Use try-catch when reading files and rendering templates
5. **Variable validation** - Check if variable exists using `has()` method
6. **Template caching** - Use template cache in production environment (for TwigTemplate)
7. **Type hints** - Use type hints on all methods (PHP 8.2+)
8. **PHPDoc** - Write detailed PHPDoc comments on all public methods
