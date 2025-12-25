# 🧩 codesaur/template  

[![CI](https://github.com/codesaur-php/Template/actions/workflows/ci.yml/badge.svg)](https://github.com/codesaur-php/Template/actions/workflows/ci.yml)
[![PHP Version](https://img.shields.io/badge/php-%5E8.2.1-777BB4.svg?logo=php)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

**Хэл:** **🇲🇳 Монгол** | [🇬🇧 English](README.EN.md)

Энгийн текст-суурьтай темплейтээс эхлээд Twig-ээр бичсэн хүчирхэг темплейт хүртэл дэмждэг минимал, өргөтгөх боломжтой template engine.

`codesaur/template` нь codesaur Framework-ийн нэг хэсэг бөгөөд энгийн
текст-суурьтай темплейтээс эхлээд Twig-ээр бичсэн хүчирхэг темплейт хүртэл
дэмждэг минимал, өргөтгөх боломжтой PHP template engine юм.

Багц нь дараах 3 үндсэн class-аас бүрдэнэ:

- **MemoryTemplate** - энгийн {{key}} placeholder-той lightweight engine  
- **FileTemplate** - файл суурьтай template loader  
- **TwigTemplate** - Twig engine-тэй бүрэн интеграцлагдсан advanced renderer  

---

## CI/CD

Энэ төсөл нь GitHub Actions ашиглан автоматаар тест хийгддэг. CI/CD pipeline нь дараах зүйлсийг шалгана:

- ✅ PHP 8.2, 8.3, 8.4 дээр синтакс шалгалт
- ✅ Composer dependencies суурилуулалт
- ✅ PHP файлуудын синтакс алдаа шалгалт
- ✅ PHPUnit unit тестүүд ажиллуулах
- ✅ PHPUnit integration тестүүд ажиллуулах

CI/CD статусыг [GitHub Actions](https://github.com/codesaur-php/Template/actions) хуудаснаас харж болно.

---

## Онцлог

- 🟢 `{{ key }}`, `{{key}}`, `{{ user.profile.email }}` зэрэг бүх whitespace-тай/гүй форматыг дэмжинэ  
- 🟢 Nested variable support (олон түвшний массив)  
- 🟢 Төгс override бүтэц - Memory → File → Twig  
- 🟢 Twig filters, functions, globals бүрэн дэмжлэгтэй  
- 🟢 Zero external dependencies (TwigTemplate ашигласан үед л Twig шаардлагатай)  
- 🟢 Framework-agnostic тул codesaur, Laravel, Symfony, Slim болон бусад бүх PHP framework-тэй бүрэн нийцтэй
- 🟢 Бүрэн PHPDoc баримт бичиг (бүх method, parameter, return type тодорхой)
- 🟢 Unit болон Integration тестүүд (45 unit + 10 integration тест)

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

## Unit Test ажиллуулах (Running Unit Tests)

Энэ багц нь PHPUnit ашиглан бүрэн unit test-тэй. Test-үүдийг ажиллуулахын тулд:

### 1. Dependencies суулгах

```bash
composer install
```

Эсвэл зөвхөн dev dependencies:

```bash
composer install --dev
```

### 2. Test ажиллуулах

#### Windows (PowerShell эсвэл Command Prompt):

```powershell
# PowerShell ашиглаж байгаа бол
.\vendor\bin\phpunit

# Эсвэл Command Prompt
vendor\bin\phpunit.bat
```

#### Linux / macOS:

```bash
./vendor/bin/phpunit
```

#### Аль ч OS дээр (Composer ашиглан):

```bash
composer test
```

Coverage report үүсгэх:

```bash
composer test-coverage
```

### 3. Test coverage харах

Coverage report үүсгэхээсээ өмнө Xdebug суусан байх шаардлагатай. Xdebug coverage mode-ийг тохируулах:

#### Windows (Command Prompt):

```cmd
REM Xdebug mode тохируулах
set XDEBUG_MODE=coverage

REM Coverage report үүсгэх
vendor\bin\phpunit.bat --coverage-html coverage
```

#### Linux / macOS:

```bash
# Xdebug mode тохируулах
export XDEBUG_MODE=coverage

# Coverage report үүсгэх
./vendor/bin/phpunit --coverage-html coverage

# Эсвэл Composer script ашиглах
composer test-coverage
```

**Анхаар:** Хэрэв Xdebug суусан бол `php.ini` файлд дараах тохиргоо нэмнэ үү:

```ini
[xdebug]
zend_extension=xdebug
xdebug.mode=coverage,debug
```

Coverage report `coverage/` фолдерт үүснэ. HTML файлыг браузер дээр нээж харж болно.

### 4. Тодорхой test файл ажиллуулах

#### Windows:

```cmd
vendor\bin\phpunit.bat tests/MemoryTemplateTest.php
```

#### Linux / macOS:

```bash
./vendor/bin/phpunit tests/MemoryTemplateTest.php
```

### 5. Тодорхой test method ажиллуулах

#### Windows:

```cmd
vendor\bin\phpunit.bat --filter testSimpleVariableReplacement tests/MemoryTemplateTest.php
```

#### Linux / macOS:

```bash
./vendor/bin/phpunit --filter testSimpleVariableReplacement tests/MemoryTemplateTest.php
```

### Test файлууд

#### Unit Tests
- `tests/MemoryTemplateTest.php` - MemoryTemplate классын unit test
- `tests/FileTemplateTest.php` - FileTemplate классын unit test
- `tests/TwigTemplateTest.php` - TwigTemplate классын unit test

#### Integration Tests
- `tests/Integration/TemplateIntegrationTest.php` - Template classes-ийн integration test
  - Бодит файл системтэй ажиллах тест
  - Олон template файлууд хамтдаа ажиллах тест
  - Real-world scenarios тест
  - Template inheritance chain тест

### 6. Integration test ажиллуулах

Integration test нь бодит файл системтэй ажиллах, олон template-үүд хамтдаа ажиллах зэрэг бодит use case-уудыг шалгана.

#### Windows:

```cmd
vendor\bin\phpunit.bat tests/Integration/
```

#### Linux / macOS:

```bash
./vendor/bin/phpunit tests/Integration/
```

#### Тодорхой integration test method ажиллуулах:

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

**Дэлгэрэнгүй API баримт бичиг:** [API.md](API.md) файлыг үзнэ үү.

---

## Баримт бичиг (Documentation)

Энэ багц нь дараах баримт бичгүүдтэй:

- **[API.md](API.md)** / [API.EN.md](API.EN.md) - Бүрэн API баримт бичиг (PHPDoc-уудаас Cursor AI ашиглан автоматаар үүсгэсэн)
  - Бүх класс, метод, параметр, return type-уудын дэлгэрэнгүй тайлбар
  - Exception reference
  - Ашиглалтын жишээнүүд
  - Best practices
  
- **[REVIEW.md](REVIEW.md)** / [REVIEW.EN.md](REVIEW.EN.md) - Шалгалтын тайлан (Cursor AI ашиглан үүсгэсэн)
  - Код сайжруулалтын тайлбар
  - Test coverage report
  - Code quality assessment
  - Metrics болон дүгнэлт

---

## Жишээ фолдер бүтэц

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
API.md
REVIEW.md
composer.json
phpunit.xml.dist
.github/workflows/ci.yml
LICENSE
```

---

## Лиценз

Энэ төсөл MIT лицензтэй.

---

## Зохиогч

**Narankhuu**  
📧 codesaur@gmail.com  
📲 [+976 99000287](https://wa.me/97699000287)  
🌐 https://github.com/codesaur  
