<?php

namespace codesaur\Template\Example;

/**
 * codesaur Template багцын жишээ скрипт.
 *
 * FileTemplate ашиглан энгийн HTML темплейт руу хувьсагчдын
 * массив дамжуулж хэрхэн рэндэр хийхийг харуулдаг.
 *
 * Онцлог:
 * - FileTemplate ашиглан файлын темплейт руу өгөгдөл дамжуулах
 * - {{ variable }} болон {% for %} синтакс ашиглах
 * - Bootstrap дээр суурилсан минимал layout рэндэрлэх
 *
 * @package codesaur\Template\Example
 * @author Narankhuu
 */

\ini_set('display_errors', 'On');
\error_reporting(\E_ALL);

require_once '../vendor/autoload.php';

use codesaur\Template\FileTemplate;

/**
 * Template-д дамжуулах хувьсагчдын тайлбар:
 * - title: Хуудасны гарчиг (string)
 * - menu:  Navigation menu-ийн жагсаалт (array<string>)
 * - items: Жишээ мэдээллийн картууд (array<array{title: string, text: string}>)
 */
$template = new FileTemplate(__DIR__ . '/example.html', [
    'title' => 'Темплейтийн жишээ',
    'menu'  => ['Нүүр', 'Танилцуулга', 'Бүтээгдэхүүн', 'Холбогдох'],
    'items' => [
        ['title' => 'Хөнгөн', 'text' => 'Хурдтай, ачаалал багатай темплейт систем.'],
        ['title' => 'Уян хатан', 'text' => 'Олон төрлийн темплейтийг зэрэг дэмжинэ.'],
        ['title' => 'Хүчирхэг', 'text' => 'Гүн түвшний хувьсагч, filter, function ашиглана.'],
    ]
]);

$template->render();
