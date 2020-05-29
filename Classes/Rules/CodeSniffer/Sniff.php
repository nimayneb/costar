<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.costar".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\Costar\Rules\CodeSniffer
{

    use ReflectionClass;
    use ReflectionException;
    use ReflectionProperty;
    use stdClass;

    class Sniff
    {
        protected ReflectionClass $reflectedSniff;

        protected stdClass $rule;

        protected string $standard;

        /**
         * @param string $standard
         * @param string $sniffClassName
         *
         * @throws ReflectionException
         */
        public function __construct(string $standard, string $sniffClassName)
        {
            $this->reflectedSniff = new ReflectionClass($sniffClassName);
            $this->standard = $standard;
            $categoryNamespace = "PHP_CodeSniffer\\Standards\\{$standard}\\Sniffs\\";

            $this->createRule($categoryNamespace);
        }

        /**
         * @param string $categoryNamespace
         */
        protected function createRule(string $categoryNamespace): void
        {
            $ruleName = preg_replace('/^(.*)Sniff$/', '$1', $this->reflectedSniff->getName());
            $ruleName = str_replace($categoryNamespace, '', $ruleName);

            $this->rule = new stdClass;
            $this->rule->category = $this->standard;
            $this->rule->rule = $ruleName;
            $this->rule->enable = false;
            $this->rule->severity = 'error';
            $this->rule->description = '';
            $this->rule->codeComparisons = [];
            $this->rule->expandCodeComparisons = null;

            $this->setRuleAttributes();
        }

        /**
         * @return stdClass
         */
        public function getRule(): stdClass
        {
            return $this->rule;
        }

        /**
         *
         */
        protected function setRuleAttributes(): void
        {
            $this->rule->attributes = [];

            $properties = $this->reflectedSniff->getProperties(ReflectionProperty::IS_PUBLIC);

            foreach ($properties as $property) {
                $propertyName = $property->getName();

                // TODO: belongs to properties?
                if ('supportedTokenizers' === $propertyName) {
                    continue;
                }

                $this->rule->attributes[] = $attribute = new stdClass;
                $attribute->name = $propertyName;
                $attribute->value = $property->isDefault()
                    ? $property->getValue($this->reflectedSniff->newInstanceWithoutConstructor())
                    : null;

                $attribute->description = Documentation::extractContentFromComment($property->getDocComment());
            }

            $this->rule->expandAttributes = count($this->rule->attributes) ? false : null;
        }

        /**
         * @param string $path
         *
         * @return string|string[]|null
         */
        public function getFileName(string $path): string
        {
            $file = $this->reflectedSniff->getFileName();

            $componentSniffs = [$path, 'Standards', $this->standard, 'Sniffs'];
            $partlySniffs = implode(DIRECTORY_SEPARATOR, $componentSniffs) . DIRECTORY_SEPARATOR;

            $componentDocs = [$path, 'Standards', $this->standard, 'Docs'];
            $partlyDocs = implode(DIRECTORY_SEPARATOR, $componentDocs) . DIRECTORY_SEPARATOR;

            $file = str_replace($partlySniffs, $partlyDocs, $file);

            return preg_replace('/^(.*)Sniff.php$/', '$1Standard.xml', $file);
        }

        /**
         * @param string $fileName
         *
         * @return string
         */
        public static function getFirstCommentFromClass(string $fileName): string
        {
            $tokens = token_get_all(file_get_contents($fileName));

            foreach ($tokens as $token) {
                if ($token[0] == T_DOC_COMMENT) {
                    return $token[1];
                }
            }

            return '';
        }
    }
} 