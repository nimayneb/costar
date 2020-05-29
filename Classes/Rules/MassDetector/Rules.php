<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.costar".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\Costar\Rules\MassDetector
{

    use Exception;
    use HaydenPierce\ClassFinder\ClassFinder;
    use PHP_CodeSniffer\Util\Tokens;
    use ReflectionException;

    class Rules
    {
        protected array $rules = [];

        protected string $path;

        /**
         * @param string $path
         */
        public function __construct(string $path)
        {
            // load defined constant from PHP_CodeSniffer
            new Tokens;

            $this->path = $path;
        }

        /**
         * @param array $standards
         *
         * @throws Exception
         */
        public function analyseRules(array $standards): void
        {
            foreach ($standards as $standard) {
                $standard = basename($standard);
                $categoryNamespace = "PHPMD\\Rule\\{$standard}\\";
                $sniffs = ClassFinder::getClassesInNamespace($categoryNamespace, ClassFinder::STANDARD_MODE);

                $this->analyseSniffs($standard, $sniffs);
            }
        }

        /**
         * @param string $standard
         * @param array $sniffs
         */
        protected function analyseSniffs(string $standard, array $sniffs)
        {
            foreach ($sniffs as $sniff) {
                // src/main/resources/rulesets/*
            }
        }

        /**
         * @return array
         */
        public function toArray(): array
        {
            return $this->rules;
        }
    }
} 