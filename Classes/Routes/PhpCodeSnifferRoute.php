<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.costar".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\Costar\Routes
{

    use Flight;
    use JayBeeR\Costar\Rules\CodeSniffer\Export;
    use JayBeeR\Costar\Rules\CodeSniffer\Rules;
    use PHP_CodeSniffer\Config;
    use ReflectionClass;
    use ReflectionException;

    class PhpCodeSnifferRoute
    {
        protected static string $standardNamespace;

        /**
         * API: http://127.0.0.1:8080/phpcs
         *  {
         *      expandAttributes: false,
         *      expandExplanations: false,
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
         * @throws ReflectionException
         */
        public static function rules(): void
        {
            $rules = static::getRules();

            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');

            Flight::json($rules->toJson());
        }

        /**
         * @throws ReflectionException
         */
        protected static function getRules(): Rules
        {
            $standards = static::getStandardNamespaces();

            $rules = new Rules(static::$standardNamespace);
            $rules->analyseRules($standards);

            return $rules;
        }

        /**
         * @throws ReflectionException
         */
        public static function export(): void
        {
            $data = json_decode(Flight::request()->getBody(), true);

            $export = new Export(static::getRules());
            $export->process($data);

            header('Content-Type: application/xml; charset=utf-8');

            echo $export->asXml();
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