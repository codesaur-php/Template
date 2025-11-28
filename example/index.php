<?php

namespace codesaur\Router\Example;

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
 * - Composer autoload шаардлагатай
 * - example.html файл энэхүү скрипттэй нэг хавтсанд байрлана
 */

// Debug тохиргоо (жишээ код тул алдаа дэлгэцэнд шууд гаргана)
\ini_set('display_errors', 'On');
\error_reporting(\E_ALL);

// Autoload (codesaur Template болон Twig-ийг ачааллана)
require_once '../vendor/autoload.php';

use codesaur\Template\TwigTemplate;

/**
 * TwigTemplate объект үүсгээд example.html темплейтэд дамжуулах өгөгдлүүдийг заана.
 * 
 * - title  → Хуудасны гарчиг
 * - menu   → Navigation menu-ийн жагсаалт
 * - items  → Жишээ мэдээллийн картууд
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

// Темплейттэй холбоотой бүх өгөгдлийг ашиглан финал HTML рэндэрлэж browser руу харуулна.
$template->render();
