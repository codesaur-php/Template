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

`codesaur/template` нь 2 үндсэн класс-аас бүрдэнэ:

1. **MemoryTemplate** - Бүрэн template engine (if, for, filter, function, macro, expression parser, 33 built-in filter)
2. **FileTemplate** - Файлын системээс template уншиж рэндэрлэх wrapper (MemoryTemplate-ийг өргөтгөнө)

**Inheritance Hierarchy:**
```
MemoryTemplate  (бүрэн engine)
    |-- FileTemplate  (файл уншигч wrapper)
```

---

## MemoryTemplate

Бүрэн template engine. If, for, macro, filter, function, expression parser бүгдийг агуулна.

### Class Signature

```php
class MemoryTemplate
```

### Properties

#### `protected string $_html`
Темплейтийн үндсэн HTML эсвэл текст эх.

#### `protected array<string, mixed> $_vars`
Темплейтэд оруулах хувьсагчдын массив.

#### `protected array<string, callable> $filters`
Бүртгэгдсэн filter-үүд.

#### `protected array<string, callable> $functions`
Бүртгэгдсэн function-үүд.

---

### Constructor

#### `__construct(string $template = '', array $vars = [])`

MemoryTemplate объект үүсгэх. Built-in filter, function-уудыг автоматаар бүртгэнэ.

**Parameters:**
- `string $template` - Темплейтийн эхлэл утга (default: `''`)
- `array $vars` - Хувьсагчдын массив (default: `[]`)

**Example:**
```php
$template = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
```

---

### Хувьсагч удирдлага (Variable Management)

#### `set(string $key, $value): void`

Хувьсагч нэмэх эсвэл шинэчлэх.

```php
$template->set('name', 'John');
```

---

#### `setVars(array $values): void`

Олон хувьсагч нэгэн зэрэг нэмэх.

```php
$template->setVars(['name' => 'John', 'age' => 30]);
```

---

#### `get(string $key): mixed`

Хувьсагчийн утгыг reference байдлаар буцаана. Олдохгүй бол `null`.

```php
$value = &$template->get('name');
```

---

#### `getVars(): array<string, mixed>`

Бүх хувьсагчдын массивыг буцаана.

```php
$vars = $template->getVars();
```

---

### Template source удирдлага

#### `source(string $html): void`

Темплейтийн эх агуулгыг тохируулна.

```php
$template->source('<h1>{{ title }}</h1>');
```

---

#### `getSource(): string`

Темплейтийн эх агуулгыг буцаана.

```php
$source = $template->getSource();
```

---

### Output

#### `output(): string`

Темплейтийг compile хийж финал HTML буцаана.

```php
$html = $template->output();
```

---

#### `render(): void`

Темплейтийг compile хийж echo хийнэ.

```php
$template->render();
```

---

#### `__toString(): string`

Объектыг echo хийх үед `output()` дуудагдана.

```php
echo $template;
```

---

### Filter / Function бүртгэх

#### `addFilter(string $name, callable $callback): void`

Custom filter нэмэх. Template дотор `{{ value|name }}` хэлбэрээр ашиглана.

```php
$template->addFilter('truncate', fn($v, int $len = 100) => mb_substr((string) $v, 0, $len));
// {{ description|truncate(50) }}
```

---

#### `addFunction(string $name, callable $callback): void`

Custom function нэмэх. Template дотор `{{ name(args) }}` хэлбэрээр ашиглана.

```php
$template->addFunction('link', fn($route) => "/app/$route");
// {{ link('home') }}
```

---

## FileTemplate

MemoryTemplate-ийг өргөтгөж, файлын системээс template уншиж рэндэрлэнэ. Бүх engine логик нь MemoryTemplate-д байдаг. FileTemplate зөвхөн файл уншиж дамжуулна.

### Class Signature

```php
class FileTemplate extends MemoryTemplate
```

### Properties

#### `protected string $_file`
Темплейт файлын бүрэн зам.

---

### Methods

#### `__construct(string $template = '', array $vars = [])`

FileTemplate конструктор.

**Parameters:**
- `string $template` - Темплейт файлын зам (хоосон байж болно)
- `array $vars` - Хувьсагчдын массив (default: `[]`)

```php
$template = new FileTemplate(__DIR__ . '/template.html', ['name' => 'World']);
```

---

#### `file(string $filepath): void`

Темплейт файлын замыг тохируулна.

**Throws:** `\InvalidArgumentException` - Файлын нэр хоосон байвал

```php
$template->file(__DIR__ . '/template.html');
```

---

#### `getFileName(): string`

Темплейт файлын замыг буцаана.

```php
$path = $template->getFileName();
```

---

#### `getFileSource(): string`

Файлын агуулгыг уншиж буцаана.

**Throws:** `\RuntimeException` - Файл олдохгүй эсвэл уншихад алдаа гарвал

```php
$content = $template->getFileSource();
```

---

#### `output(): string`

Файлыг уншиж compile хийж финал HTML буцаана.

**Throws:** `\RuntimeException` - Файл уншихад алдаа гарвал

```php
$html = $template->output();
```

---

### Inherited Methods

FileTemplate нь MemoryTemplate-ийн бүх public method-уудыг өвлөж авна:

- Хувьсагч удирдлага: `set`, `setVars`, `get`, `getVars`
- Template source: `source`, `getSource`
- Output: `render`, `__toString`
- Filter/Function: `addFilter`, `addFunction`

---

## Built-in Filters

MemoryTemplate конструктор дотор автоматаар бүртгэгддэг filter-үүд:

| Filter | Тайлбар | Жишээ |
|--------|---------|-------|
| `int` | Тоон хөрвүүлэг | `{{ value\|int }}` |
| `round` | Тоймлох | `{{ price\|round(2) }}` |
| `number_format` | Тоон формат | `{{ price\|number_format(2, '.', ',') }}` |
| `json_encode` | JSON болгох | `{{ data\|json_encode }}` |
| `upper` | Том үсэг | `{{ name\|upper }}` |
| `lower` | Жижиг үсэг | `{{ name\|lower }}` |
| `capitalize` | Эхний үсэг том | `{{ name\|capitalize }}` |
| `nl2br` | Мөр таслал -> `<br>` | `{{ text\|nl2br }}` |
| `url_encode` | URL encode | `{{ url\|url_encode }}` |
| `raw` | Escape хийхгүй | `{{ html\|raw }}` |
| `e` / `escape` | HTML escape | `{{ input\|e }}` |
| `date` | Огноо формат | `{{ d\|date('Y-m-d') }}` |
| `length` | Урт | `{{ items\|length }}` |
| `keys` | Массивын түлхүүрүүд | `{{ data\|keys }}` |
| `first` | Эхний элемент | `{{ items\|first }}` |
| `last` | Сүүлийн элемент | `{{ items\|last }}` |
| `slice` | Хэсэг авах | `{{ text\|slice(0, 5) }}` |
| `merge` | Массив нэгтгэх | `{{ arr\|merge([4, 5]) }}` |
| `split` | Тэмдэгтээр хуваах | `{{ csv\|split(',') }}` |
| `default` | Өгөгдмөл утга | `{{ name\|default('Unknown') }}` |
| `format` | sprintf | `{{ 'Hi %s'\|format(name) }}` |
| `abs` | Абсолют утга | `{{ num\|abs }}` |
| `trim` | Хоосон зай арилгах | `{{ text\|trim }}` |
| `striptags` | HTML tag арилгах | `{{ html\|striptags }}` |
| `title` | Title Case | `{{ name\|title }}` |
| `join` | Массив нэгтгэх | `{{ items\|join(', ') }}` |
| `reverse` | Эргүүлэх | `{{ items\|reverse }}` |
| `sort` | Эрэмбэлэх | `{{ items\|sort }}` |
| `unique` | Давхардал арилгах | `{{ items\|unique }}` |
| `column` | Массивын нэг багана | `{{ users\|column('name') }}` |
| `batch` | Хэсэгчлэх | `{{ items\|batch(3) }}` |
| `values` | Зөвхөн утгууд | `{{ data\|values }}` |
| `replace` | Текст солих | `{{ text\|replace({'a': 'b'}) }}` |
| `wordwrap` | Мөр таслах | `{{ text\|wordwrap(80) }}` |
| `json_decode` | JSON задлах | `{{ json\|json_decode }}` |

### Built-in Functions

| Function | Тайлбар | Жишээ |
|----------|---------|-------|
| `attribute` | Массивын элемент авах | `{{ attribute(obj, key) }}` |
| `range` | Тоон цуваа | `{{ range(1, 10) }}` |
| `max` | Хамгийн их | `{{ max(a, b) }}` |
| `min` | Хамгийн бага | `{{ min(a, b) }}` |

---

## Supported Template Syntax

### Output

- `{{ variable }}` - Хувьсагч
- `{{ variable|filter }}` - Filter chain
- `{{ function(args) }}` - Function дуудалт
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

`{% for %}` дотор `loop` объект ашиглах боломжтой:
- `loop.index` (1-ээс эхэлнэ)
- `loop.index0` (0-ээс эхэлнэ)
- `loop.first` (эхний давталт уу?)
- `loop.last` (сүүлийн давталт уу?)
- `loop.length` (нийт тоо)

### Tests

- `is defined`, `is empty`, `is null`, `is iterable`
- `is not defined`, `is not empty` гэх мэт

### Operators

- Харьцуулах: `==`, `!=`, `<`, `>`, `<=`, `>=`
- Логик: `and`, `or`, `not`
- Тэмдэгт: `starts with`
- Тооцоолол: `+`, `-`, `*`, `/`, `%`

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

### MemoryTemplate -- бүрэн engine

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

- `FileTemplate::file()` - Файлын нэр хоосон байвал

### `\RuntimeException`

- `FileTemplate::getFileSource()` - Файл олдохгүй эсвэл уншихад алдаа гарвал
- `FileTemplate::output()` - Файл уншихад алдаа гарвал

---

## Best Practices

1. **MemoryTemplate** - бүрэн engine тул ихэнх тохиолдолд хангалттай
2. **FileTemplate** - зөвхөн файлын системээс template уншихад ашиглана
3. **Custom function** - `text()`, `link()` гэх мэт утга үүсгэгч логикийг function-ээр бүртгэ
4. **Custom filter** - `|reverse`, `|truncate` гэх мэт утга хувиргагчийг filter-ээр бүртгэ
5. **HTML comments** - Template дотор `<!-- comment -->` ашиглана (`{# #}` дэмжигдэхгүй)
