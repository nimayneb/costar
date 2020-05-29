<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.costar".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\Costar\Routes
{

    use Exception;
    use Flight;
    use JayBeeR\Costar\Rules\CodeSniffer\Rules;
    use PHP_CodeSniffer\Config;
    use ReflectionClass;

    class PhpCodeSnifferRoute
    {
        protected static string $standardNamespace;

        /**
         * API: http://127.0.0.1:8080/phpcs
         *  {
         *      expandAttributes: false,
         *      expandCodeComparisons: false,
         *      category: 'Generic',
         *      rule: 'Classes.DuplicateClassName',
         *      enable: true,
         *      severity: 'error',
         *      attributes: [
         *          {
         *              name: "test",
         *              value: 123,
         *              description: "description bla"
         *          }
         *      ],
         *      description: 'Class and Interface names should be unique in a project.  They should never be duplicated.',
         *  }
         *
         * @throws Exception
         */
        public static function rules(): void
        {
            $standards = static::getStandardNamespaces();

            $rules = new Rules(static::$standardNamespace);
            $rules->analyseRules($standards);

            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');

            Flight::json($rules->toArray());
        }

        /**
         * @return array
         */
        protected static function getStandardNamespaces(): array
        {
            $reflectedClass = new ReflectionClass(Config::class);
            static::$standardNamespace = $path = dirname($reflectedClass->getFileName());

            return glob("{$path}/Standards/*", GLOB_ONLYDIR);
        }
    }
} 