# codesaur/template

[![CI](https://github.com/codesaur-php/Template/actions/workflows/ci.yml/badge.svg)](https://github.com/codesaur-php/Template/actions/workflows/ci.yml)
[![PHP Version](https://img.shields.io/badge/php-%5E8.2.1-777BB4.svg?logo=php)](https://www.php.net/)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## Агуулга / Table of Contents

1. [Монгол](#1-монгол-тайлбар) | 2. [English](#2-english-description) | 3. [Getting Started](#3-getting-started)

---

## 1. Монгол тайлбар

Бие даасан PHP template engine. Хөгжлийн явцад Twig template engine-ийн синтакс, дизайн загвараас санаа авч чадамжуудаа өргөжүүлсэн.

`codesaur/template` нь **codesaur ecosystem**-ийн нэг хэсэг бөгөөд
энгийн текст-суурьтай темплейтээс эхлээд if/for/macro/filter бүхий
хүчирхэг темплейт хүртэл дэмждэг минимал PHP template engine юм.

Багц нь дараах 2 үндсэн class-аас бүрдэнэ:

- **MemoryTemplate** - бүрэн template engine (if, for, filter, function, macro, expression parser)
- **FileTemplate** - файлын системээс template уншиж рэндэрлэх (MemoryTemplate-ийг өргөтгөнө)

### Дэлгэрэнгүй мэдээлэл

- [Бүрэн танилцуулга](docs/mn/README.md) - Суурилуулалт, хэрэглээ, жишээнүүд
- [API тайлбар](docs/mn/api.md) - Бүх метод, exception-уудийн тайлбар
- [Шалгалтын тайлан](docs/mn/review.md) - Код шалгалтын тайлан

---

## 2. English description

A self-contained PHP template engine. During its evolution, adopted syntax and design patterns inspired by Twig.

`codesaur/template` is part of the **codesaur ecosystem** - a minimal PHP
template engine supporting everything from simple text placeholders to
powerful templates with if/for/macro/filter syntax.

The package consists of 2 core classes:

- **MemoryTemplate** - full template engine (if, for, filter, function, macro, expression parser)
- **FileTemplate** - file-based template loader (extends MemoryTemplate)

### Documentation

- [Full Documentation](docs/en/README.md) - Installation, usage, examples
- [API Reference](docs/en/api.md) - Complete API documentation
- [Review](docs/en/review.md) - Code review report

---

## 3. Getting Started

### Requirements

- PHP **8.2.1+** (json, mbstring extensions)
- Composer

### Installation

Composer ашиглан суулгана / Install via Composer:

```bash
composer require codesaur/template
```

### Quick Example

```php
use codesaur\Template\MemoryTemplate;

// Бүрэн engine - if, for, filter, function бүгд дэмжинэ
$page = new MemoryTemplate(
    '{% for item in items %}<li>{{ item|upper }}</li>{% endfor %}',
    ['items' => ['a', 'b', 'c']]
);
echo $page;
```

```php
use codesaur\Template\FileTemplate;

// Файл суурьтай template
$page = new FileTemplate('page.html', [
    'title' => 'Hello',
    'items' => ['a', 'b', 'c']
]);
$page->addFunction('link', fn($route) => "/app/$route");
echo $page;
```

```html
<!-- page.html -->
<h1>{{ title }}</h1>
<a href="{{ link('home') }}">Home</a>
<ul>
{% for item in items %}
    <li>{{ item }}</li>
{% endfor %}
</ul>
```

### Running Tests

Тест ажиллуулах / Run tests:

```bash
composer test
```

---

## Changelog

- [CHANGELOG.md](CHANGELOG.md) - Full version history

## Contributing & Security

- [Contributing Guide](.github/CONTRIBUTING.md)
- [Security Policy](.github/SECURITY.md)

## License

This project is licensed under the MIT License.

## Author

**Narankhuu**
codesaur@gmail.com  
https://github.com/codesaur  

**codesaur Ecosystem:** https://codesaur.net
