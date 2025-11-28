# 🧩 codesaur/template  
A lightweight and flexible PHP templating component for PHP 8.2.1 or newer.

`codesaur/template` нь codesaur Framework-ийн нэг хэсэг бөгөөд энгийн
текст-суурьтай темплейтээс эхлээд Twig-ээр бичсэн хүчирхэг темплейт хүртэл
дэмждэг минимал, өргөтгөх боломжтой PHP template engine юм.

Багц нь дараах 3 үндсэн class-аас бүрдэнэ:

- **MemoryTemplate** — энгийн {{key}} placeholder-той lightweight engine  
- **FileTemplate** — файл суурьтай template loader  
- **TwigTemplate** — Twig engine-тэй бүрэн интеграцлагдсан advanced renderer  

---

## Онцлог

- 🟢 `{{ key }}`, `{{key}}`, `{{ user.profile.email }}` зэрэг бүх whitespace-тай/гүй форматыг дэмжинэ  
- 🟢 Nested variable support (олон түвшний массив)  
- 🟢 Төгс override бүтэц — Memory → File → Twig  
- 🟢 Twig filters, functions, globals бүрэн дэмжлэгтэй  
- 🟢 Zero external dependencies (TwigTemplate ашигласан үед л Twig шаардлагатай)  
- 🟢 Framework-agnostic тул codesaur, Laravel, Symfony, Slim болон бусад бүх PHP framework-тэй бүрэн нийцтэй

---

## Суурилуулалт (Installation)

Composer ашиглан суулгах:

```
composer require codesaur/template
```

---

## Ашиглах жишээ 1 — MemoryTemplate (simple)

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

## Ашиглах жишээ 2 — FileTemplate

```
use codesaur\Template\FileTemplate;

$template = new FileTemplate(__DIR__ . '/page.html', [
    'title' => 'Hello Codesaur',
    'message' => 'This is file-based templating.'
]);

echo $template->output();
```

---

## Ашиглах жишээ 3 — TwigTemplate (Bootstrap ашигласан example)

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

## Жишээ фолдер бүтэц

```
/example
    index.php
    example.html
/src
    MemoryTemplate.php
    FileTemplate.php
    TwigTemplate.php
README.md
composer.json
LICENSE
```

---

## Лиценз

Энэ төсөл MIT лицензтэй.

---

## Зохиогч

**Narankhuu**  
codesaur@gmail.com
