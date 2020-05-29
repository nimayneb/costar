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

    Flight::route('/phpcs', [\JayBeeR\Costar\Routes\PhpCodeSnifferRoute::class, 'rules']);
    Flight::route('/phpmd', [\JayBeeR\Costar\Routes\PhpMassDetectorRoute::class, 'rules']);
    Flight::start();
}