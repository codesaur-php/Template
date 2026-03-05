# codesaur/template  

Энгийн текст-суурьтай темплейтээс эхлээд Twig-ээр бичсэн хүчирхэг темплейт хүртэл дэмждэг минимал, өргөтгөх боломжтой template engine.

`codesaur/template` нь **codesaur ecosystem**-ийн нэг хэсэг бөгөөд энгийн
текст-суурьтай темплейтээс эхлээд Twig-ээр бичсэн хүчирхэг темплейт хүртэл
дэмждэг минимал, өргөтгөх боломжтой PHP template engine юм.

Багц нь дараах 3 үндсэн class-аас бүрдэнэ:

- **MemoryTemplate** - энгийн {{key}} placeholder-той lightweight engine  
- **FileTemplate** - файл суурьтай template loader  
- **TwigTemplate** - Twig engine-тэй бүрэн интеграцлагдсан advanced renderer  

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
- Төгс override бүтэц - Memory -> File -> Twig  
- Twig filters, functions, globals бүрэн дэмжлэгтэй  
- Zero external dependencies (TwigTemplate ашигласан үед л Twig шаардлагатай)  
- Framework-agnostic тул codesaur, Laravel, Symfony, Slim болон бусад бүх PHP framework-тэй бүрэн нийцтэй
- Бүрэн PHPDoc баримт бичиг (бүх method, parameter, return type тодорхой)
- Unit, Integration, Performance болон Memory тестүүд (70+ тест, 1200+ assertions)

---

## Суурилуулалт (Installation)

Composer ашиглан суулгах:

```
composer require codesaur/template
```

---

## Ашиглах жишээ 1 - MemoryTemplate (simple)

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

## Ашиглах жишээ 2 - FileTemplate

```
use codesaur\Template\FileTemplate;

$template = new FileTemplate(__DIR__ . '/page.html', [
    'title' => 'Hello Codesaur',
    'message' => 'This is file-based templating.'
]);

echo $template->output();
```

---

## Ашиглах жишээ 3 - TwigTemplate (Bootstrap ашигласан example)

`example/index.php`:

```
use codesaur\Template\TwigTemplate;

$template = new TwigTemplate(__DIR__ . '/example.html', [
    'title' => 'Темплейтийн жишээ',
    'menu'  => ['Нүүр', 'Бидний тухай', 'Бүтээгдэхүүн', 'Холбоо барих'],
    'items' => [
        ['title' => 'Хөнгөн жинтэй', 'text' => 'Хурдтай, энгийн ажиллагаатай темплейт систем.'],
        ['title' => 'Уян хатан', 'text' => 'Plain, File суурьтай болон Twig Template-үүдийг дэмжинэ.'],
        ['title' => 'Хүчирхэг', 'text' => 'Nested variable, Twig filters, functions ашиглах боломжтой.'],
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
        <p class="text-muted">TwigTemplate болон Bootstrap ашигласан энгийн, цэвэр жишээ.</p>
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

## Тест ажиллуулах (Running Tests)

Энэ багц нь PHPUnit ашиглан бүрэн тест-тэй. Бүх тестүүдийг Composer ашиглан ажиллуулж болно (аль ч OS дээр адилхан):

### 1. Dependencies суулгах

```bash
composer install
```

### 2. Бүх тестүүдийг ажиллуулах

```bash
composer test
```

### 3. Test coverage үүсгэх

Coverage report үүсгэх (Xdebug шаардлагатай):

```bash
composer test-coverage
```

**Анхаар:** Coverage report үүсгэхийн тулд Xdebug суусан байх шаардлагатай. `php.ini` файлд дараах тохиргоо нэмнэ үү:

```ini
[xdebug]
zend_extension=xdebug
xdebug.mode=coverage,debug
```

Coverage report `coverage/` фолдерт үүснэ. HTML файлыг браузер дээр нээж харж болно.

### 4. Тодорхой test файл эсвэл method ажиллуулах

Тодорхой test файл ажиллуулах:

```bash
vendor/bin/phpunit tests/MemoryTemplateTest.php
```

Тодорхой test method ажиллуулах:

```bash
vendor/bin/phpunit --filter testSimpleVariableReplacement tests/MemoryTemplateTest.php
```

**Windows хэрэглэгчид:** `vendor/bin/phpunit`-ийг `vendor\bin\phpunit.bat` гэж солино

### Test файлууд

#### Unit Tests
- `tests/MemoryTemplateTest.php` - MemoryTemplate классын unit test
- `tests/FileTemplateTest.php` - FileTemplate классын unit test (100% method coverage)
- `tests/TwigTemplateTest.php` - TwigTemplate классын unit test

#### Integration Tests
- `tests/Integration/TemplateIntegrationTest.php` - Template classes-ийн integration test
  - Бодит файл системтэй ажиллах тест
  - Олон template файлууд хамтдаа ажиллах тест
  - Real-world scenarios тест
  - Template inheritance chain тест

#### Performance Tests
- `tests/PerformanceTest.php` - Performance тестүүд
  - Том template-үүдтэй ажиллах performance тест
  - Олон хувьсагчтай template-үүдийн performance тест
  - Гүн nested хувьсагчтай template-үүдийн performance тест
  - Олон дараалсан render-ийн performance тест

#### Memory Tests
- `tests/MemoryTest.php` - Memory usage тестүүд
  - Том template-үүдийн memory usage тест
  - Олон template instance-ийн memory usage тест
  - Гүн nested хувьсагчтай template-үүдийн memory usage тест
  - Олон дараалсан render-ийн memory usage тест

### Test статистик

- **Нийт тест:** 70+ тест
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
- MemoryTemplate API-г бүхэлд нь өвлөж авна
- `file(string $filepath)`
- `getFileSource(): string`
- `output(): string`

### TwigTemplate
- FileTemplate-г өргөтгөнө
- Нэмэлт API:
  - `getEnvironment(): Environment`
  - `addGlobal(string $name, $value)`
  - `addFilter(TwigFilter $filter)`
  - `addFunction(TwigFunction $function)`

---

## Баримт бичиг (Documentation)

Энэ багц нь дараах баримт бичгүүдтэй:

- **[API](api.md)** - Бүрэн API баримт бичиг (PHPDoc-уудаас Cursor AI ашиглан автоматаар үүсгэсэн)
  - Бүх класс, метод, параметр, return type-уудын дэлгэрэнгүй тайлбар
  - Exception reference
  - Ашиглалтын жишээнүүд
  - Best practices
  
- **[REVIEW](review.md)** - Шалгалтын тайлан (Cursor AI ашиглан үүсгэсэн)
  - Код сайжруулалтын тайлбар
  - Test coverage report
  - Code quality assessment
  - Metrics болон дүгнэлт

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
