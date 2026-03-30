# API Documentation - codesaur/template

**Last Updated:** 2026-03-30

---

## Table of Contents

- [Overview](#overview)
- [MemoryTemplate](#memorytemplate)
- [FileTemplate](#filetemplate)
- [Built-in Filters](#built-in-filters)
- [Supported Template Syntax](#supported-template-syntax)
- [Examples](#examples)
- [Exception Reference](#exception-reference)

---

## Overview

`codesaur/template` consists of 2 core classes:

1. **MemoryTemplate** - Full template engine (if, for, filter, function, macro, expression parser, 33 built-in filters)
2. **FileTemplate** - File-based template loader wrapper (extends MemoryTemplate)

**Inheritance Hierarchy:**
```
MemoryTemplate  (full engine)
    |-- FileTemplate  (file reader wrapper)
```

---

## MemoryTemplate

Full template engine. Includes if, for, macro, filter, function, and expression parser.

### Class Signature

```php
class MemoryTemplate
```

### Properties

#### `protected string $_html`
The main HTML or text source of the template.

#### `protected array<string, mixed> $_vars`
Array of variables to be inserted into the template.

#### `protected array<string, callable> $filters`
Registered filters.

#### `protected array<string, callable> $functions`
Registered functions.

---

### Constructor

#### `__construct(string $template = '', array $vars = [])`

Creates a MemoryTemplate object. Automatically registers built-in filters and functions.

**Parameters:**
- `string $template` - Initial template value (default: `''`)
- `array $vars` - Variables array (default: `[]`)

**Example:**
```php
$template = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
```

---

### Variable Management

#### `set(string $key, $value): void`

Add or update a variable.

```php
$template->set('name', 'John');
```

---

#### `setVars(array $values): void`

Add multiple variables at once.

```php
$template->setVars(['name' => 'John', 'age' => 30]);
```

---

#### `get(string $key): mixed`

Returns variable value by reference. Returns `null` if not found.

```php
$value = &$template->get('name');
```

---

#### `getVars(): array<string, mixed>`

Returns all variables as an array.

```php
$vars = $template->getVars();
```

---

### Template Source Management

#### `source(string $html): void`

Set the template source content.

```php
$template->source('<h1>{{ title }}</h1>');
```

---

#### `getSource(): string`

Returns the template source content.

```php
$source = $template->getSource();
```

---

### Output

#### `output(): string`

Compile the template and return final HTML.

```php
$html = $template->output();
```

---

#### `render(): void`

Compile the template and echo it.

```php
$template->render();
```

---

#### `__toString(): string`

Returns `output()` when the object is echoed.

```php
echo $template;
```

---

### Filter / Function Registration

#### `addFilter(string $name, callable $callback): void`

Add a custom filter. Used in templates as `{{ value|name }}`.

```php
$template->addFilter('truncate', fn($v, int $len = 100) => mb_substr((string) $v, 0, $len));
// {{ description|truncate(50) }}
```

---

#### `addFunction(string $name, callable $callback): void`

Add a custom function. Used in templates as `{{ name(args) }}`.

```php
$template->addFunction('link', fn($route) => "/app/$route");
// {{ link('home') }}
```

---

## FileTemplate

Extends MemoryTemplate to load templates from the file system. All engine logic lives in MemoryTemplate. FileTemplate only reads files and passes content to the engine.

### Class Signature

```php
class FileTemplate extends MemoryTemplate
```

### Properties

#### `protected string $_file`
Full path of the template file.

---

### Methods

#### `__construct(string $template = '', array $vars = [])`

FileTemplate constructor.

**Parameters:**
- `string $template` - Path to the template file (can be empty)
- `array $vars` - Variables array (default: `[]`)

```php
$template = new FileTemplate(__DIR__ . '/template.html', ['name' => 'World']);
```

---

#### `file(string $filepath): void`

Set the template file path.

**Throws:** `\InvalidArgumentException` - If file name is empty

```php
$template->file(__DIR__ . '/template.html');
```

---

#### `getFileName(): string`

Returns the template file path.

```php
$path = $template->getFileName();
```

---

#### `getFileSource(): string`

Reads and returns the file content.

**Throws:** `\RuntimeException` - If file not found or read error

```php
$content = $template->getFileSource();
```

---

#### `output(): string`

Reads the file and compiles it to final HTML.

**Throws:** `\RuntimeException` - If file read error

```php
$html = $template->output();
```

---

### Inherited Methods

FileTemplate inherits all MemoryTemplate public methods:

- Variable management: `set`, `setVars`, `get`, `getVars`
- Template source: `source`, `getSource`
- Output: `render`, `__toString`
- Filter/Function: `addFilter`, `addFunction`

---

## Built-in Filters

Automatically registered in the MemoryTemplate constructor:

| Filter | Description | Example |
|--------|-------------|---------|
| `int` | Cast to integer | `{{ value\|int }}` |
| `round` | Round number | `{{ price\|round(2) }}` |
| `number_format` | Format number | `{{ price\|number_format(2, '.', ',') }}` |
| `json_encode` | Encode to JSON | `{{ data\|json_encode }}` |
| `upper` | Uppercase | `{{ name\|upper }}` |
| `lower` | Lowercase | `{{ name\|lower }}` |
| `capitalize` | Capitalize first letter | `{{ name\|capitalize }}` |
| `nl2br` | Newlines to `<br>` | `{{ text\|nl2br }}` |
| `url_encode` | URL encode | `{{ url\|url_encode }}` |
| `raw` | No escaping | `{{ html\|raw }}` |
| `e` / `escape` | HTML escape | `{{ input\|e }}` |
| `date` | Format date | `{{ d\|date('Y-m-d') }}` |
| `length` | Get length | `{{ items\|length }}` |
| `keys` | Get array keys | `{{ data\|keys }}` |
| `first` | First element | `{{ items\|first }}` |
| `last` | Last element | `{{ items\|last }}` |
| `slice` | Extract portion | `{{ text\|slice(0, 5) }}` |
| `merge` | Merge arrays | `{{ arr\|merge([4, 5]) }}` |
| `split` | Split string | `{{ csv\|split(',') }}` |
| `default` | Default value | `{{ name\|default('Unknown') }}` |
| `format` | sprintf | `{{ 'Hi %s'\|format(name) }}` |
| `abs` | Absolute value | `{{ num\|abs }}` |
| `trim` | Strip whitespace | `{{ text\|trim }}` |
| `striptags` | Strip HTML tags | `{{ html\|striptags }}` |
| `title` | Title Case | `{{ name\|title }}` |
| `join` | Join array | `{{ items\|join(', ') }}` |
| `reverse` | Reverse | `{{ items\|reverse }}` |
| `sort` | Sort array | `{{ items\|sort }}` |
| `unique` | Remove duplicates | `{{ items\|unique }}` |
| `column` | Array column | `{{ users\|column('name') }}` |
| `batch` | Chunk array | `{{ items\|batch(3) }}` |
| `values` | Array values only | `{{ data\|values }}` |
| `replace` | Replace text | `{{ text\|replace({'a': 'b'}) }}` |
| `wordwrap` | Wrap lines | `{{ text\|wordwrap(80) }}` |
| `json_decode` | Decode JSON | `{{ json\|json_decode }}` |

### Built-in Functions

| Function | Description | Example |
|----------|-------------|---------|
| `attribute` | Get array element | `{{ attribute(obj, key) }}` |
| `range` | Number sequence | `{{ range(1, 10) }}` |
| `max` | Maximum value | `{{ max(a, b) }}` |
| `min` | Minimum value | `{{ min(a, b) }}` |

---

## Supported Template Syntax

### Output

- `{{ variable }}` - Variable
- `{{ variable|filter }}` - Filter chain
- `{{ function(args) }}` - Function call
- `{{ a ? b : c }}` - Ternary operator
- `{{ a ?? b }}` - Null coalescing
- `{{ a ~ b }}` - Concat operator

### Control Structures

- `{% if cond %}...{% elseif cond %}...{% else %}...{% endif %}`
- `{% for item in items %}...{% endfor %}`
- `{% for key, val in items %}...{% endfor %}`
- `{% set name = value %}`
- `{% macro name(params) %}...{% endmacro %}`

### Loop Variables

Inside `{% for %}`, the `loop` object is available:
- `loop.index` (starts from 1)
- `loop.index0` (starts from 0)
- `loop.first` (is this the first iteration?)
- `loop.last` (is this the last iteration?)
- `loop.length` (total count)

### Tests

- `is defined`, `is empty`, `is null`, `is iterable`
- `is not defined`, `is not empty`, etc.

### Operators

- Comparison: `==`, `!=`, `<`, `>`, `<=`, `>=`
- Logic: `and`, `or`, `not`
- String: `starts with`
- Arithmetic: `+`, `-`, `*`, `/`, `%`

### Literals

- String: `'hello'`, `"hello"`
- Number: `42`, `3.14`
- Boolean: `true`, `false`
- Null: `null`, `none`
- Array: `[1, 2, 3]`
- Hash: `{'key': 'value'}`

### Access

- Dot notation: `user.name`
- Bracket notation: `user['name']`
- Filter chain: `value|filter1|filter2(arg)`

---

## Examples

### MemoryTemplate -- full engine

```php
use codesaur\Template\MemoryTemplate;

$t = new MemoryTemplate(
    '{% for item in items %}{{ loop.index }}. {{ item|upper }} {% endfor %}',
    ['items' => ['php', 'js', 'go']]
);
echo $t; // 1. PHP 2. JS 3. GO

// Custom function
$t = new MemoryTemplate('{{ greet("World") }}');
$t->addFunction('greet', fn($name) => "Hello, $name!");
echo $t; // Hello, World!

// Custom filter
$t = new MemoryTemplate('{{ name|reverse }}', ['name' => 'hello']);
$t->addFilter('reverse', fn($v) => strrev((string) $v));
echo $t; // olleh
```

### FileTemplate

```php
use codesaur\Template\FileTemplate;

$template = new FileTemplate(__DIR__ . '/page.html', [
    'title' => 'My Page',
    'users' => [['name' => 'John'], ['name' => 'Jane']]
]);
$template->addFunction('link', fn($route, $params = []) => '/app/' . $route);
echo $template->output();
```

---

## Exception Reference

### `\InvalidArgumentException`

- `FileTemplate::file()` - If file name is empty

### `\RuntimeException`

- `FileTemplate::getFileSource()` - If file not found or read error
- `FileTemplate::output()` - If file read error

---

## Best Practices

1. **MemoryTemplate** - runs the full engine, sufficient for most use cases
2. **FileTemplate** - use only when loading templates from the file system
3. **Custom functions** - register value-producing logic (like `text()`, `link()`) as functions
4. **Custom filters** - register value-transforming logic (like `|reverse`, `|truncate`) as filters
5. **HTML comments** - use `<!-- comment -->` in templates (`{# #}` is not supported)
