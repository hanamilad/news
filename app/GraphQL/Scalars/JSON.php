<?php

namespace App\GraphQL\Scalars;

use GraphQL\Language\AST\ListValueNode;
use GraphQL\Language\AST\ObjectValueNode;
use GraphQL\Language\AST\ObjectFieldNode;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\FloatValueNode;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Language\AST\BooleanValueNode;
use GraphQL\Language\AST\NullValueNode;
use GraphQL\Language\AST\EnumValueNode;
use GraphQL\Language\AST\VariableNode;
use GraphQL\Type\Definition\ScalarType;

class JSON extends ScalarType
{
public string $name = 'JSON';
public ?string $description = 'The `JSON` scalar type represents JSON values (objects, arrays, strings, numbers, booleans and null).';


    public function serialize($value)
    {
        // عند الإخراج نحتفظ بالقيمة كما هي (Laravel models عادة ترجع arrays أو stdClass)
        return $value;
    }

    public function parseValue($value)
    {
        // عند استقبال قيمة من variables — نفترض إنها صالحة JSON (array/object)
        return $value;
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        // يحول AST nodes إلى قيم PHP مناسبة (recursively)
        switch (true) {
            case $valueNode instanceof IntValueNode:
                return (int) $valueNode->value;
            case $valueNode instanceof FloatValueNode:
                return (float) $valueNode->value;
            case $valueNode instanceof StringValueNode:
                return (string) $valueNode->value;
            case $valueNode instanceof BooleanValueNode:
                return (bool) $valueNode->value;
            case $valueNode instanceof NullValueNode:
                return null;
            case $valueNode instanceof EnumValueNode:
                return $valueNode->value;
            case $valueNode instanceof ListValueNode:
                $values = [];
                foreach ($valueNode->values as $v) {
                    $values[] = $this->parseLiteral($v, $variables);
                }
                return $values;
            case $valueNode instanceof ObjectValueNode:
                $obj = [];
                /** @var ObjectFieldNode $field */
                foreach ($valueNode->fields as $field) {
                    $obj[$field->name->value] = $this->parseLiteral($field->value, $variables);
                }
                return $obj;
            case $valueNode instanceof VariableNode:
                $varName = $valueNode->name->value;
                return $variables[$varName] ?? null;
            default:
                return null;
        }
    }
}
