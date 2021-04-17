<?php

/* DEV: v1.2021.03.04
 * 
 * This is an example script!
 */

require_once '../vendor/autoload.php';

use codesaur\Template\TwigTemplate;

$template = new TwigTemplate(
        dirname(__FILE__) . '/asperion.html',
        array('menu' => array('Home', 'About', 'Technologies', 'Projects', 'Contacts', 'Custom'))
);
$template->set('partners', array(
    'Google logo' => 'https://i.imgur.com/oSriTuP.png',
    'Apple logo' => 'https://i.imgur.com/kRgvevC.png',
    'Wii logo' => 'https://i.imgur.com/ZZjeIP3.png',
    'Valve logo' => 'https://i.imgur.com/536rtCW.png',
    'Adithya Institute of Technology logo' => 'https://i.imgur.com/VTq1c9p.png',
    'Intel logo' => 'https://i.imgur.com/GdYoyxo.png'
));
$template->render();
