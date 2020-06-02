<?php declare(strict_types=1);

/*
 * This file belongs to the package "nimayneb.costar".
 * See LICENSE.txt that was shipped with this package.
 */

namespace JayBeeR\Costar\Rules\CodeSniffer
{

    use SimpleXMLElement;

    class Export
    {
        protected Rules $rules;

        protected SimpleXMLElement $ruleset;

        /**
         * @param Rules $rules
         */
        public function __construct(Rules $rules)
        {
            $this->rules = $rules;
        }

        /**
         * @param array $data
         */
        public function process(array $data)
        {
            $defaultRules = $this->rules->toArray();

            $this->ruleset = new SimpleXMLElement('<ruleset/>');

            $this->ruleset->addAttribute('xmlns', 'http://pmd.sf.net/ruleset/1.0.0');
            $this->ruleset->addAttribute('name', $data['data']['name']);
            $this->ruleset->addAttribute('language', 'php');

            $this->ruleset->addAttribute(
                'xsi:schemaLocation',
                'http://pmd.sf.net/ruleset/1.0.0 http://pmd.sf.net/ruleset_xml_schema.xsd',
                'http://www.w3.org/2001/XMLSchema-instance'
            );

            $this->ruleset->addAttribute(
                'xsi:noNamespaceSchemaLocation',
                'http://pmd.sf.net/ruleset_xml_schema.xsd',
                'http://www.w3.org/2001/XMLSchema-instance'
            );

            $this->ruleset->addChild('description', $data['data']['description']);

            foreach ($data['data']['rules'] as $item) {
                if ($item['enable']) {
                    $rule = $this->ruleset->addChild('rule');
                    $fullRuleName = "{$item['category']}.{$item['rule']}";
                    $rule->addAttribute('ref', str_replace('\\', '.', $fullRuleName));

                    if (count($item['attributes'])) {
                        $properties = null;

                        foreach ($item['attributes'] as $attribute) {
                            $attributeName = $attribute['name'];

                            if ($defaultRules[$fullRuleName]->attributes[$attributeName]->value !== $attribute['value']) {
                                $properties ??= $rule->addChild('properties');
                                $property = $properties->addChild('property');
                                $property->addAttribute('name', $attributeName);
                                $property->addAttribute('value', (string) $attribute['value']);
                            }
                        }
                    }

                    if (count($item['fixableErrors'])) {
                        foreach ($item['fixableErrors'] as $fixableError) {
                            if (!$fixableError['enable']) {
                                $exclude = $rule->addChild('exclude');
                                $exclude->addAttribute(
                                    'name',
                                    sprintf(
                                        '%s.%s',
                                        str_replace('\\', '.', $fullRuleName),
                                        $fixableError['name']
                                    )
                                );
                            }
                        }
                    }
                }
            }
        }

        /**
         * @return string
         */
        public function asXml(): string
        {
            return $this->ruleset->asXML();
        }
    }
} 