# API Documentation - codesaur/template

**Last Updated:** 2026-01-08  

---

## Table of Contents

- [Overview](#overview)
- [MemoryTemplate](#memorytemplate)
- [FileTemplate](#filetemplate)
- [TwigTemplate](#twigtemplate)
- [Examples](#examples)

---

## Overview

`codesaur/template` нь 3 үндсэн класс-аас бүрдэнэ:

1. **MemoryTemplate** - Энгийн placeholder-той lightweight engine
2. **FileTemplate** - Файл суурьтай template loader (MemoryTemplate-ийг өргөтгөнө)
3. **TwigTemplate** - Twig engine-тэй бүрэн интеграцлагдсан advanced renderer (FileTemplate-ийг өргөтгөнө)

**Inheritance Hierarchy:**
```
MemoryTemplate
    |-- FileTemplate
        |-- TwigTemplate
```

---

## MemoryTemplate

Энгийн HTML эсвэл текст суурьтай темплейт боловсруулах зориулалттай lightweight template engine.

### Class Signature

```php
class MemoryTemplate
```

### Properties

#### `protected string $_html`
Темплейтийн үндсэн HTML эсвэл текст эх сурвалч.

#### `protected array<string, mixed> $_vars`
Темплейтэд оруулах хувьсагчдын массив.

---

### Methods

#### `__construct(string $template = '', array $vars = [])`

MemoryTemplate объект үүсгэх конструктор.

**Parameters:**
- `string $template` - Темплейтийн HTML эсвэл текст эхлэл утга (optional, default: `''`)
- `array $vars` - Темплейтэд ашиглах хувьсагчдын массив (optional, default: `[]`)

**Returns:** `void`

**Example:**
```php
$template = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
```

---

#### `__toString(): string`

Объектыг шууд echo хийх үед `output()` функцийн үр дүнг буцаана.

**Returns:** `string` - Боловсруулсан template

**Example:**
```php
$template = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
echo $template; // Output: "Hello, World!"
```

---

#### `source(string $html): void`

Темплейтийн эх HTML/текст агуулгыг тохируулна.

**Parameters:**
- `string $html` - HTML эсвэл текст агуулга

**Returns:** `void`

**Example:**
```php
$template = new MemoryTemplate();
$template->source('<h1>{{ title }}</h1>');
```

---

#### `has(string $key): bool`

Тодорхой хувьсагч template-д байгаа эсэхийг шалгана.

**Parameters:**
- `string $key` - Хувьсагчийн түлхүүр

**Returns:** `bool` - Хувьсагч байвал `true`, байхгүй бол `false`

**Example:**
```php
$template = new MemoryTemplate('', ['name' => 'John']);
$template->has('name'); // true
$template->has('email'); // false
```

---

#### `set(string $key, $value): void`

Темплейтэд ашиглах хувьсагчийг name=value хэлбэрээр нэмэх эсвэл шинэчлэх.

**Parameters:**
- `string $key` - Хувьсагчийн түлхүүр (key)
- `mixed $value` - Хувьсагчийн утга (ямар ч төрөл)

**Returns:** `void`

**Example:**
```php
$template = new MemoryTemplate('{{ name }}');
$template->set('name', 'John');
$template->set('age', 30);
```

---

#### `setVars(array $values): void`

Нэгэн зэрэг олон хувьсагч нэмэх эсвэл шинэчлэх.

**Parameters:**
- `array<string, mixed> $values` - Хувьсагчдын массив

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

Хувьсагчийн утгыг reference байдлаар буцаана. Хувьсагч олдохгүй бол `null` буцаана.

**Parameters:**
- `string $key` - Хувьсагчийн түлхүүр

**Returns:** `mixed` - Хувьсагчийн утга эсвэл `null`

**Example:**
```php
$template = new MemoryTemplate('', ['name' => 'John']);
$value = &$template->get('name'); // 'John'
$value = 'Jane'; // Хувьсагч шинэчлэгдэнэ
```

---

#### `getVars(): array<string, mixed>`

Темплейтэд ашиглаж буй бүх хувьсагчдын массивыг авах.

**Returns:** `array<string, mixed>` - Хувьсагчдын массив

**Example:**
```php
$template = new MemoryTemplate('', ['name' => 'John', 'age' => 30]);
$vars = $template->getVars(); // ['name' => 'John', 'age' => 30]
```

---

#### `getSource(): string`

Темплейтийн эх HTML/текстийг буцаах.

**Returns:** `string` - Темплейтийн эх HTML/текст

**Example:**
```php
$template = new MemoryTemplate('Hello, {{ name }}!');
$source = $template->getSource(); // 'Hello, {{ name }}!'
```

---

#### `render(): void`

Темплейтийг рэндэрлэж echo хийнэ.

**Returns:** `void`

**Example:**
```php
$template = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
$template->render(); // Output: "Hello, World!"
```

---

#### `output(): string`

Темплейтийн финал боловсруулсан HTML-г буцаана.

**Returns:** `string` - Боловсруулсан HTML

**Example:**
```php
$template = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
$html = $template->output(); // "Hello, World!"
```

---

#### `protected compile(string $html): string`

Темплейтийн tag-уудыг боловсруулж финал HTML гаргана.

**Supported Syntax:**
- `{{ key }}`
- `{{key}}`
- `{{   user.profile.email   }}`

Хувьсагч олдохгүй бол tag өөрөө үлдэнэ.

**Parameters:**
- `string $html` - Темплейтийн эх HTML эсвэл текст

**Returns:** `string` - Боловсруулсан финал HTML эсвэл текст

**Note:** Protected method - дотоод ашиглалтад зориулагдсан

---

#### `protected resolveValue(string $path): mixed|null`

Олон түвшний key (user.profile.email гэх мэт)-ийн утгыг мөрдөж авах.

**Example:** `"user.profile.email"` -> `$vars['user']['profile']['email']`

**Parameters:**
- `string $path` - "a.b.c" хэлбэртэй key path (цэгээр тусгаарлагдсан)

**Returns:** `mixed|null` - Олдсон утга эсвэл `null` (олдохгүй бол)

**Note:** Protected method - дотоод ашиглалтад зориулагдсан

---

#### `protected stringify($content): string`

Массив эсвэл дурын төрлийн өгөгдлийг текст болгон хөрвүүлэх.

Массив бол бүх элементүүдийг дараалан нэгтгэнэ. Бусад төрөл бол string cast хийнэ.

**Parameters:**
- `mixed $content` - Хөрвүүлэх өгөгдөл

**Returns:** `string` - Текст хэлбэрт хөрвүүлсэн утга

**Note:** Protected method - дотоод ашиглалтад зориулагдсан

---

## FileTemplate

`MemoryTemplate`-ийг өргөтгөж, темплейтийг файлын системээс уншиж рэндэрлэх боломж олгоно.

### Class Signature

```php
class FileTemplate extends MemoryTemplate
```

### Properties

#### `protected string $_file`
Рэндэрлэх гэж буй темплейт файлын бүрэн зам.

---

### Methods

#### `__construct(string $template = '', array $vars = [])`

FileTemplate конструктор.

**Parameters:**
- `string $template` - Темплейт файлын зам (хоосон байж болно, дараа `file()` методоор тохируулна)
- `array $vars` - Темплейтэд дамжуулах хувьсагчдын массив (optional, default: `[]`)

**Returns:** `void`

**Example:**
```php
$template = new FileTemplate(__DIR__ . '/template.html', ['name' => 'World']);
```

---

#### `file(string $filepath): void`

Ашиглах темплейт файлын замыг тохируулна.

**Parameters:**
- `string $filepath` - Темплейт файлын зам

**Returns:** `void`

**Throws:**
- `\InvalidArgumentException` - Файлын нэр хоосон байвал

**Example:**
```php
$template = new FileTemplate();
$template->file(__DIR__ . '/template.html');
```

---

#### `getFileName(): string`

Одоогоор тохируулсан темплейт файлын замыг буцаана.

**Returns:** `string` - Файлын зам

**Example:**
```php
$template = new FileTemplate(__DIR__ . '/template.html');
$path = $template->getFileName(); // '/path/to/template.html'
```

---

#### `getFileSource(): string`

Темплейт файлын агуулгыг уншиж буцаана.

**Returns:** `string` - Файлын HTML/текст агуулга

**Throws:**
- `\RuntimeException` - Файл заагаагүй, файл олдохгүй эсвэл уншихад алдаа гарвал

**Example:**
```php
$template = new FileTemplate(__DIR__ . '/template.html');
$content = $template->getFileSource(); // Файлын агуулга
```

---

#### `output(): string`

Темплейт файлыг уншиж, `MemoryTemplate`-ийн `compile()` ашиглан финал HTML буцаана.

Энэ метод нь `MemoryTemplate`-ийн `output()` override хийж, файлын агуулгыг уншиж `compile()` руу дамжуулна.

**Returns:** `string` - Финал боловсруулсан HTML

**Throws:**
- `\RuntimeException` - Файл уншихад алдаа гарвал

**Example:**
```php
$template = new FileTemplate(__DIR__ . '/template.html', ['name' => 'World']);
$html = $template->output(); // Боловсруулсан HTML
```

---

### Inherited Methods

`FileTemplate` нь `MemoryTemplate`-ийн бүх public method-уудыг өвлөж авна:

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

`FileTemplate`-г өргөтгөж, Twig template engine ашиглан илүү хүчин чадалтай, уян хатан темплейт боловсруулах боломжийг олгоно.

### Class Signature

```php
class TwigTemplate extends FileTemplate
```

### Properties

#### `protected Environment $_environment`
TwigEnvironment объект - Twig engine-г бүх тохиргоотой нь агуулна.

---

### Methods

#### `__construct(string $template = '', array $vars = [])`

TwigTemplate конструктор.

**Parameters:**
- `string $template` - Темплейтийн файлын зам
- `array $vars` - Темплейтэд дамжуулах хувьсагчдын массив (optional, default: `[]`)

**Returns:** `void`

**Note:** Constructor нь Twig Environment-ийг `autoescape=false` тохиргоотой үүсгэнэ. Мөн `int` болон `json_decode` filter-үүдийг автоматаар нэмнэ.

**Example:**
```php
$template = new TwigTemplate(__DIR__ . '/template.twig', ['name' => 'World']);
```

---

#### `getEnvironment(): Environment`

Twig Environment объект буцаах.

**Returns:** `\Twig\Environment` - Twig Environment instance

**Example:**
```php
$template = new TwigTemplate(__DIR__ . '/template.twig');
$env = $template->getEnvironment();
// Environment-тэй шууд ажиллах боломжтой
```

---

#### `addGlobal(string $name, $value): void`

Темплейтэд гадаад глобал хувьсагч нэмэх.

**Parameters:**
- `string $name` - Хувьсагч нэр
- `mixed $value` - Хувьсагчийн утга

**Returns:** `void`

**Example:**
```php
$template = new TwigTemplate(__DIR__ . '/template.twig');
$template->addGlobal('app_name', 'MyApp');
// Template-д {{ app_name }} ашиглах боломжтой
```

---

#### `addFilter(TwigFilter $filter): void`

Twig-д custom filter нэмэх.

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
// Template-д {{ value|uppercase }} ашиглах боломжтой
```

---

#### `addFunction(TwigFunction $function): void`

Twig-д custom function нэмэх.

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
// Template-д {{ greet("World") }} ашиглах боломжтой
```

---

#### `protected compile(string $html): string`

TwigTemplate-ийн үндсэн compile функц.

FileTemplate -> файлын агуулгыг уншина, MemoryTemplate -> `compile()` override хийгдэж Twig рүү дамжина.

Энэ метод нь ArrayLoader ашиглан "result" нэртэй virtual template үүсгэж, Twig-ийн `render()` ашиглан боловсруулна.

**Parameters:**
- `string $html` - Файлаас уншсан template-ийн эх HTML

**Returns:** `string` - Twig engine-ээр боловсруулсан финал HTML

**Throws:**
- `\Twig\Error\LoaderError` - Template loader алдаа гарвал
- `\Twig\Error\RuntimeError` - Runtime алдаа гарвал
- `\Twig\Error\SyntaxError` - Template синтакс алдаа гарвал

**Note:** Protected method - дотоод ашиглалтад зориулагдсан

---

### Built-in Filters

TwigTemplate нь дараах filter-үүдийг автоматаар нэмнэ:

#### `int`
Тоон утга болгон хөрвүүлнэ.

**Example:**
```twig
{{ value|int }}
```

#### `json_decode`
JSON string-ийг массив эсвэл объект болгон хөрвүүлнэ.

**Example:**
```twig
{{ json|json_decode }}
```

---

### Inherited Methods

`TwigTemplate` нь `FileTemplate` болон `MemoryTemplate`-ийн бүх public method-уудыг өвлөж авна:

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

// Энгийн хувьсагч
$template = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
echo $template; // Output: "Hello, World!"

// Олон түвшний хувьсагч
$template = new MemoryTemplate('Email: {{ user.email }}', [
    'user' => ['email' => 'test@example.com']
]);
echo $template; // Output: "Email: test@example.com"

// Динамик хувьсагч нэмэх
$template = new MemoryTemplate('{{ name }} is {{ age }} years old');
$template->set('name', 'John');
$template->set('age', 30);
echo $template; // Output: "John is 30 years old"
```

### FileTemplate Example

```php
use codesaur\Template\FileTemplate;

// Файл template ашиглах
$template = new FileTemplate(__DIR__ . '/page.html', [
    'title' => 'Hello Codesaur',
    'message' => 'This is file-based templating.'
]);

echo $template->output();

// Файл замыг дараа нь тохируулах
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

// Энгийн Twig template
$template = new TwigTemplate(__DIR__ . '/template.twig', [
    'title' => 'Темплейтийн жишээ',
    'menu' => ['Нүүр', 'Бидний тухай', 'Бүтээгдэхүүн'],
    'items' => [
        ['title' => 'Хөнгөн', 'text' => 'Хурдтай темплейт систем.'],
        ['title' => 'Уян хатан', 'text' => 'Олон төрлийн темплейтийг дэмжинэ.']
    ]
]);

$template->render();

// Custom filter нэмэх
$template->addFilter(new TwigFilter('uppercase', function ($string) {
    return strtoupper($string);
}));

// Custom function нэмэх
$template->addFunction(new TwigFunction('greet', function ($name) {
    return "Hello, $name!";
}));

// Global хувьсагч нэмэх
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
- `FileTemplate::file()` - Файлын нэр хоосон байвал

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
- `FileTemplate::getFileSource()` - Файл заагаагүй, файл олдохгүй эсвэл уншихад алдаа гарвал
- `FileTemplate::output()` - Файл уншихад алдаа гарвал

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
- `TwigTemplate::compile()` - Template loader алдаа гарвал

### `\Twig\Error\RuntimeError`

**Thrown by:**
- `TwigTemplate::compile()` - Runtime алдаа гарвал

### `\Twig\Error\SyntaxError`

**Thrown by:**
- `TwigTemplate::compile()` - Template синтакс алдаа гарвал

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

- `{{ key }}` - Энгийн хувьсагч
- `{{key}}` - Whitespace-гүй хувьсагч
- `{{   key   }}` - Whitespace-тай хувьсагч
- `{{ user.profile.email }}` - Олон түвшний хувьсагч

### TwigTemplate

TwigTemplate нь Twig-ийн бүх синтакс дэмждэг:

- Variables: `{{ variable }}`
- Filters: `{{ variable|filter }}`
- Functions: `{{ function() }}`
- Control Structures: `{% if %}`, `{% for %}`, `{% block %}`, etc.
- Comments: `{# comment #}`

Дэлгэрэнгүй мэдээллийг [Twig Documentation](https://twig.symfony.com/doc/) хаягаас үзнэ үү.

---

## Best Practices

1. **MemoryTemplate** - Жижиг, энгийн template-д ашиглах
2. **FileTemplate** - Файл суурьтай template-д ашиглах
3. **TwigTemplate** - Нарийн төвөгтэй template, loops, conditions шаардлагатай үед ашиглах

4. **Exception handling** - Файл унших, template рэндэр хийх үед try-catch ашиглах
5. **Variable validation** - Хувьсагч байгаа эсэхийг `has()` методоор шалгах
6. **Template caching** - Production орчинд template cache ашиглах (TwigTemplate-д)
7. **Type hints** - Бүх method-ууд дээр type hints ашиглах (PHP 8.2+)
8. **PHPDoc** - Бүх public method-ууд дээр дэлгэрэнгүй PHPDoc comment бичих
