<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.costar".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\Costar\Rules\CodeSniffer
{

    use SimpleXMLElement;
    use stdClass;

    class Documentation
    {
        protected SimpleXMLElement $xml;

        protected string $standard;

        protected string $fileName;

        /**
         * @param string $fileName
         */
        public function __construct(string $fileName)
        {
            $this->fileName = $fileName;
            $this->xml = simplexml_load_file($this->fileName);
        }

        /**
         * @param stdClass $rule
         */
        public function setRuleDescription(stdClass $rule): void
        {
            $classDescription = static::extractContentFromComment(
                Sniff::getFirstCommentFromClass($this->fileName)
            );

            $descriptionDoc = trim((string)$this->xml->standard);

            $rule->description = (strlen($classDescription) > strlen($descriptionDoc))
                ? ($descriptionDoc ?: $classDescription)
                : ($classDescription ?: $descriptionDoc);
        }

        /**
         * @param stdClass $rule
         */
        public function setStandardDescription(stdClass $rule)
        {
            $comparisons = $this->xml->code_comparison;

            foreach ($comparisons as $comparison) {
                $rule->codeComparisons[] = [
                    'left' => $left = new stdClass,
                    'right' => $right = new stdClass,
                ];

                $left->code = (string)$comparison->code[0];
                $left->label = (string)$comparison->code[0]['title'];
                $right->code = (string)$comparison->code[1];
                $right->label = (string)$comparison->code[1]['title'];
            }

            $rule->expandCodeComparisons = count($rule->codeComparisons)
                ? false
                : null;
        }

        /**
         * @param string $comment
         *
         * @return string
         */
        public static function extractContentFromComment(string $comment)
        {
            $lines = explode("\n", $comment);
            $description = [];

            foreach ($lines as $line) {
                if (1 === preg_match('/^(\/\*+| *\**)(.*)(\**\/$|$)/', $line, $match)) {
                    if ((false !== strpos($match[2], '@var'))
                        || (false !== strpos($match[2], '@return'))
                        || (false !== strpos($match[2], '@var'))
                        || (false !== strpos($match[2], '@author'))
                        || (false !== strpos($match[2], '@copyright'))
                        || (false !== strpos($match[2], '@license'))
                        || (false !== strpos($match[2], '@param'))
                        || (false !== strpos($match[2], '@throws'))
                        || (false !== strpos($match[2], '<code>'))
                    ) {
                        break;
                    }

                    $description[] = trim($match[2]);
                }
            }

            return implode("\n", $description);
        }
    }
} 