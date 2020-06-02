<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.costar".
 * See LICENSE.txt that was shipped with this package.
 */

require_once __DIR__ . '/vendor/autoload.php';

{
    Flight::route('/', function () {
        echo file_get_contents(__DIR__ . '/Resources/Templates/Rules.html');
    });

    Flight::route('/Layout.vue', function () {
        echo file_get_contents(__DIR__ . '/Resources/Templates/Layout.vue');
    });

    Flight::route('POST /phpcs-export', [\JayBeeR\Costar\Routes\PhpCodeSnifferRoute::class, 'export']);
    Flight::route('GET /phpcs-rules', [\JayBeeR\Costar\Routes\PhpCodeSnifferRoute::class, 'rules']);
    Flight::route('GET /phpmd-rules', [\JayBeeR\Costar\Routes\PhpMassDetectorRoute::class, 'rules']);

    Flight::start();
}