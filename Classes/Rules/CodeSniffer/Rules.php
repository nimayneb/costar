<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.costar".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\Costar\Rules\CodeSniffer
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
         * @throws ReflectionException
         * @throws Exception
         */
        public function analyseRules(array $standards): void
        {
            foreach ($standards as $standard) {
                $standard = basename($standard);
                $categoryNamespace = "PHP_CodeSniffer\\Standards\\{$standard}\\Sniffs\\";
                $sniffs = ClassFinder::getClassesInNamespace($categoryNamespace, ClassFinder::RECURSIVE_MODE);

                $this->analyseSniffs($standard, $sniffs);
            }
        }

        /**
         * @param string $standard
         * @param array $sniffs
         *
         * @throws ReflectionException
         */
        protected function analyseSniffs(string $standard, array $sniffs)
        {
            foreach ($sniffs as $sniff) {
                if (('Test' === substr($sniff, -4)) || (!class_exists($sniff))) {
                    continue;
                }

                $sniff = new Sniff($standard, $sniff);
                $fileName = $sniff->getFileName($this->path);

                if (is_file($fileName)) {
                    $documentation = new Documentation($fileName);
                    $documentation->setStandardDescription($sniff->getRule());
                    $documentation->setRuleDescription($sniff->getRule());
                }

                $this->rules[$sniff->getFullName()] = $sniff->getRule();
            }
        }

        /**
         * @return array
         */
        public function toArray(): array
        {
            return $this->rules;
        }

        /**
         * @return array
         */
        public function getApplicationArray(): array
        {
            $rules = array_values($this->rules);

            foreach ($rules as $rule) {
                $attributes = [];

                foreach ($rule->attributes as $attribute) {
                    $attributes[] = $attribute;
                }

                $rule->attributes = $attributes;
            }

            return $rules;
        }
    }
} 