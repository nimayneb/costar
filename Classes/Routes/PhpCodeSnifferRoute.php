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
    use stdClass;

    class PhpCodeSnifferRoute
    {
        protected static string $standardNamespace;

        /**
         * API: http://127.0.0.1:8080/phpcs
         *  {
         *      name: String
         *      description: String,
         *
         *      rules: {
         *          category: String
         *          rule: String
         *          enable: Boolean
         *          description: String
         *          severity: Integer
         *
         *          expandAttributes: Boolean
         *          attributes: [
         *              {
         *                  name: String
         *                  value: Mixed
         *                  description: String
         *              }
         *          ]
         *
         *          expandExplanations: Boolean
         *          explanations: [
         *              {
         *                  left: {
         *                      code: String
         *                      label: String
         *                  }
         *                  right: {
         *                      code: String
         *                      label: String
         *                  }
         *              }
         *          ]
         *
         *          expandFixableErrors: Boolean
         *          fixableErrors: [
         *              {
         *                  name: String
         *                  enable: Boolean
         *              }
         *          ]
         *      }
         *  }
         *
         * @throws ReflectionException
         */
        public static function rules(): void
        {
            $rules = static::getRules();

            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');

            $pmd = new stdClass;
            $pmd->name = 'Coding standards for PHPCS';
            $pmd->description = 'Generated coding standard rules with Costar.';
            $pmd->rules = $rules->getApplicationArray();

            Flight::json($pmd);
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