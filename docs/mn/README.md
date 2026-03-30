# codesaur/template

Бие даасан PHP template engine. Хөгжлийн явцад Twig template engine-ийн синтакс, дизайн загвараас санаа авч чадамжуудаа өргөжүүлсэн.

`codesaur/template` нь **codesaur ecosystem**-ийн нэг хэсэг бөгөөд энгийн
текст-суурьтай темплейтээс эхлээд if, for, macro, filter зэрэг бүрэн боломжтой
хүчирхэг темплейт хүртэл дэмждэг минимал, өргөтгөх боломжтой PHP template engine юм.

Багц нь дараах 2 үндсэн class-аас бүрдэнэ:

- **MemoryTemplate** - бүрэн template engine (if, for, filter, function, macro, expression parser, 33 built-in filter)
- **FileTemplate** - файлын системээс template уншиж рэндэрлэх wrapper (MemoryTemplate-ийг өргөтгөнө)

---

## CI/CD

Энэ төсөл нь GitHub Actions ашиглан автоматаар тест хийгддэг. CI/CD pipeline нь дараах зүйлсийг шалгана:

- PHP 8.2, 8.3, 8.4 дээр синтакс шалгалт
- Composer dependencies суурилуулалт
- PHP файлуудын синтакс алдаа шалгалт
- PHPUnit unit тестүүд ажиллуулах
- PHPUnit integration тестүүд ажиллуулах

CI/CD статусыг [GitHub Actions](https://github.com/codesaur-php/Template/actions) хуудаснаас харж болно.

---

## Онцлог

- `{{ key }}`, `{{key}}`, `{{ user.profile.email }}` зэрэг бүх whitespace-тай/гүй форматыг дэмжинэ
- Nested variable support (олон түвшний массив)
- Бүрэн template синтакс: if/elseif/else, for loops, macros, filters, functions, expressions
- 33 built-in filter (e, escape, date, length, keys, slice, split, merge, json_encode, number_format, capitalize, upper, lower, default, round, nl2br, url_encode, format, first, last гм.)
- Custom filter/function бүртгэх: `addFilter()`, `addFunction()`
- MemoryTemplate дээрээ бүрэн engine ажиллана. FileTemplate зөвхөн файл уншдаг wrapper
- Framework-agnostic тул codesaur, Laravel, Symfony, Slim болон бусад бүх PHP framework-тэй нийцтэй
- Бүрэн PHPDoc баримт бичиг
- Unit, Integration, Performance болон Memory тестүүд (95 тест, 1267 assertions)

---

## Суурилуулалт (Installation)

Composer ашиглан суулгах:

```
composer require codesaur/template
```

---

## Ашиглах жишээ 1 - MemoryTemplate

MemoryTemplate одоо бүрэн engine-тэй - if, for, filter, function бүгдийг дэмжинэ:

```php
use codesaur\Template\MemoryTemplate;

$template = new MemoryTemplate(
    '{% for user in users %}{{ user.name|upper }}, {% endfor %}',
    ['users' => [
        ['name' => 'Narankhuu'],
        ['name' => 'Erdenebat],
    ]]
);

echo $template; // NARANKHUU, ERDENEBAT,
```

Энгийн placeholder жишээ:

```php
$template = new MemoryTemplate(
    'Hello, {{ user.name }}!',
    ['user' => ['name' => 'Narankhuu']]
);

echo $template; // Hello, Narankhuu!
```

Custom function бүртгэх:

```php
$template = new MemoryTemplate('{{ link("home") }}');
$template->addFunction('link', fn($route) => "/app/$route");
echo $template; // /app/home
```

---

## Ашиглах жишээ 2 - FileTemplate

FileTemplate нь файлаас template уншиж MemoryTemplate-ийн engine-ээр боловсруулна:

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

## Ашиглах жишээ 3 - FileTemplate (Bootstrap ашигласан example)

`example/index.php`:

```php
use codesaur\Template\FileTemplate;

$template = new FileTemplate(__DIR__ . '/example.html', [
    'title' => 'Темплейтийн жишээ',
    'menu'  => ['Нүүр', 'Бидний тухай', 'Бүтээгдэхүүн', 'Холбоо барих'],
    'items' => [
        ['title' => 'Хөнгөн жинтэй', 'text' => 'Хурдтай, энгийн ажиллагаатай темплейт систем.'],
        ['title' => 'Уян хатан', 'text' => 'Plain болон File суурьтай Template-үүдийг дэмжинэ.'],
        ['title' => 'Хүчирхэг', 'text' => 'Nested variable, filters, functions ашиглах боломжтой.'],
    ]
]);

$template->render();
```

`example/example.html`:

```html
<!doctype html>
<html lang="mn">
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

## Тест ажиллуулах (Running Tests)

Энэ багц нь PHPUnit ашиглан бүрэн тест-тэй. Бүх тестүүдийг Composer ашиглан ажиллуулж болно:

### 1. Dependencies суулгах

```bash
composer install
```

### 2. Бүх тестүүдийг ажиллуулах

```bash
composer test
```

### 3. Тодорхой test файл ажиллуулах

```bash
vendor/bin/phpunit tests/MemoryTemplateTest.php
```

**Windows хэрэглэгчид:** `vendor/bin/phpunit`-ийг `vendor\bin\phpunit.bat` гэж солино

### Test файлууд

- `tests/MemoryTemplateTest.php` - MemoryTemplate классын unit test
- `tests/EngineTest.php` - Template engine-ийн unit test (if, for, filter, macro)
- `tests/Integration/TemplateIntegrationTest.php` - Integration test
- `tests/PerformanceTest.php` - Performance тестүүд
- `tests/MemoryTest.php` - Memory usage тестүүд

### Test статистик

- **Нийт тест:** 100+ тест
- **Assertions:** 1300+ assertions

---

## API Overview

### MemoryTemplate

Бүрэн template engine - if, for, filter, function, macro, expression parser бүгдийг агуулна:

- `__construct(string $template = '', array $vars = [])`
- `set(string $key, $value)` / `setVars(array $values)` / `get(string $key)` / `getVars(): array`
- `source(string $html)` / `getSource(): string`
- `output(): string` / `render()` / `__toString()`
- `addFilter(string, callable)` / `addFunction(string, callable)`

### FileTemplate

MemoryTemplate-ийн бүх API-г өвлөж авна + файл удирдлага:

- `file(string $filepath)` / `getFileName(): string`
- `getFileSource(): string`
- `output(): string` (файлаас уншиж compile хийнэ)

---

## Баримт бичиг (Documentation)

- **[API](api.md)** - Бүрэн API баримт бичиг
- **[REVIEW](review.md)** - Шалгалтын тайлан

---

## Changelog

Багцын бүх өөрчлөлтийн түүхийг [CHANGELOG](../../CHANGELOG.md) файлаас үзнэ үү.

---

## Лиценз

Энэ төсөл MIT лицензтэй.

---

## Зохиогч

**Narankhuu**
https://github.com/codesaur
