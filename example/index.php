<?php

namespace codesaur\Router\Example;

/* DEV: v1.2021.03.04
 * 
 * This is an example script!
 */

ini_set('display_errors', 'On');
error_reporting(\E_ALL & ~\E_STRICT & ~\E_NOTICE);

require_once '../vendor/autoload.php';

use codesaur\Template\TwigTemplate;

$template = new TwigTemplate(
    dirname(__FILE__) . '/asperion.html',
    ['menu' => ['Home', 'About', 'Technologies', 'Projects', 'Contacts', 'Custom']]
);
$template->set('partners', [
    'Google logo' => 'https://i.imgur.com/oSriTuP.png',
    'Apple logo' => 'https://i.imgur.com/kRgvevC.png',
    'Wii logo' => 'https://i.imgur.com/ZZjeIP3.png',
    'Valve logo' => 'https://i.imgur.com/536rtCW.png',
    'Adithya Institute of Technology logo' => 'https://i.imgur.com/VTq1c9p.png',
    'Intel logo' => 'https://i.imgur.com/GdYoyxo.png'
]);
$template->render();
