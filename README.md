# codesaur/template

[![CI](https://github.com/codesaur-php/Template/actions/workflows/ci.yml/badge.svg)](https://github.com/codesaur-php/Template/actions/workflows/ci.yml)
[![PHP Version](https://img.shields.io/badge/php-%5E8.2.1-777BB4.svg?logo=php)](https://www.php.net/)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## Агуулга / Table of Contents

1. [Монгол](#1-монгол-тайлбар) | 2. [English](#2-english-description) | 3. [Getting Started](#3-getting-started)

---

## 1. Монгол тайлбар

Энгийн текст-суурьтай темплейтээс эхлээд Twig-ээр бичсэн хүчирхэг темплейт хүртэл дэмждэг минимал, өргөтгөх боломжтой template engine.

`codesaur/template` нь codesaur Framework-ийн нэг хэсэг бөгөөд энгийн
текст-суурьтай темплейтээс эхлээд Twig-ээр бичсэн хүчирхэг темплейт хүртэл
дэмждэг минимал, өргөтгөх боломжтой PHP template engine юм.

Багц нь дараах 3 үндсэн class-аас бүрдэнэ:

- **MemoryTemplate** - энгийн {{key}} placeholder-той lightweight engine  
- **FileTemplate** - файл суурьтай template loader  
- **TwigTemplate** - Twig engine-тэй бүрэн интеграцлагдсан advanced renderer  

### Дэлгэрэнгүй мэдээлэл

- 📖 [Бүрэн танилцуулга](docs/mn/README.md) - Суурилуулалт, хэрэглээ, жишээнүүд
- 📚 [API тайлбар](docs/mn/api.md) - Бүх метод, exception-үүдийн тайлбар
- 🔍 [Шалгалтын тайлан](docs/mn/review.md) - Код шалгалтын тайлан

---

## 2. English description

A minimal, extensible template engine that supports everything from simple text-based templates to powerful templates written with Twig.

`codesaur/template` is part of the codesaur Framework and is a minimal, extensible PHP template engine that supports everything from simple text-based templates to powerful templates written with Twig.

The package consists of the following 3 core classes:

- **MemoryTemplate** - lightweight engine with simple {{key}} placeholders  
- **FileTemplate** - file-based template loader  
- **TwigTemplate** - advanced renderer fully integrated with Twig engine  

### Documentation

- 📖 [Full Documentation](docs/en/README.md) - Installation, usage, examples
- 📚 [API Reference](docs/en/api.md) - Complete API documentation
- 🔍 [Review](docs/en/review.md) - Code review report

---

## 3. Getting Started

### Requirements

- PHP **8.2.1+** (JSON extension is included by default)
- Composer
- Optional: `twig/twig` (^3.22.2) - Required only for TwigTemplate

### Installation

Composer ашиглан суулгана / Install via Composer:

```bash
composer require codesaur/template
```

### Quick Example

```php
use codesaur\Template\MemoryTemplate;

// Энгийн жишээ / Simple example
$template = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
echo $template; // Output: "Hello, World!"

// Олон түвшний хувьсагч / Nested variables
$template = new MemoryTemplate('Email: {{ user.email }}', [
    'user' => ['email' => 'test@example.com']
]);
echo $template; // Output: "Email: test@example.com"
```

### Running Tests

Тест ажиллуулах / Run tests:

```bash
# Бүх тестүүдийг ажиллуулах / Run all tests
composer test

# Coverage-тэй тест ажиллуулах / Run tests with coverage
composer test:coverage
```

---

## Changelog

- 📝 [CHANGELOG.md](CHANGELOG.md) - Full version history

## Contributing & Security

- 🤝 [Contributing Guide](.github/CONTRIBUTING.md)
- 🔐 [Security Policy](.github/SECURITY.md)

## License

This project is licensed under the MIT License.

## Author

**Narankhuu**  
📧 codesaur@gmail.com  
🌐 https://github.com/codesaur

🦖 **codesaur Ecosystem:** https://codesaur.net
