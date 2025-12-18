<?php

namespace codesaur\Template\Example;

/**
 * codesaur Template багцын жишээ скрипт.
 *
 * Энэ файл нь TwigTemplate ашиглан энгийн HTML темплейт рүү
 * хувьсагчдын массив дамжуулж хэрхэн рэндэр хийхийг харуулдаг.
 *
 * Онцлог:
 * - FileTemplate + TwigTemplate ашиглан файлын темплейт рүү өгөгдөл дамжуулах
 * - Twig-ийн {{ variable }} болон {% for %} синтакс ашиглах
 * - Bootstrap дээр суурилсан минимал layout рэндэрлэх
 *
 * Ашиглах нөхцөл:
 * - Composer autoload шаардлагатай (vendor/autoload.php)
 * - example.html файл энэхүү скрипттэй нэг хавтсанд байрлана
 * - PHP 8.2.1 эсвэл түүнээс дээш хувилбар шаардлагатай
 *
 * @package codesaur\Template\Example
 * @author Narankhuu
 * @since 1.0.0
 */

// Debug тохиргоо (жишээ код тул алдаа дэлгэцэнд шууд гаргана)
\ini_set('display_errors', 'On');
\error_reporting(\E_ALL);

// Autoload (codesaur Template болон Twig-ийг ачааллана)
require_once '../vendor/autoload.php';

use codesaur\Template\TwigTemplate;

/**
 * TwigTemplate объект үүсгээд example.html темплейтэд дамжуулах өгөгдлүүдийг заая.
 *
 * Template-д дамжуулах хувьсагчдын тайлбар:
 * - title  → Хуудасны гарчиг (string)
 * - menu   → Navigation menu-ийн жагсаалт (array<string>)
 * - items  → Жишээ мэдээллийн картууд (array<array{title: string, text: string}>)
 */
$template = new TwigTemplate(__DIR__ . '/example.html', [
    'title' => 'Темплейтийн жишээ',
    'menu'  => ['Нүүр', 'Танилцуулга', 'Бүтээгдэхүүн', 'Холбогдох'],
    'items' => [
        ['title' => 'Хөнгөн', 'text' => 'Хурдтай, ачаалал багатай темплейт систем.'],
        ['title' => 'Уян хатан', 'text' => 'Олон төрлийн темплейтийг зэрэг дэмжинэ.'],
        ['title' => 'Хүчирхэг', 'text' => 'Гүн түвшний хувьсагч, Twig фильтер, функцуудыг ашиглана.'],
    ]
]);

/**
 * Темплейттэй холбоотой бүх өгөгдлийг ашиглан финал HTML рэндэрлэж
 * browser руу харуулна.
 *
 * render() метод нь output() методыг дуудаж echo хийнэ.
 */
$template->render();
