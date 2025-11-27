<?php

namespace App\GraphQL\Scalars;

use Carbon\Carbon;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Language\AST\VariableNode;
use GraphQL\Type\Definition\ScalarType;

class ISODateTime extends ScalarType
{
    public string $name = 'ISODateTime';

    public ?string $description = 'Accepts ISO 8601 date-time strings and serializes to Y-m-d H:i:s.';

    public function serialize($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->toDateTimeString();
        }

        if (is_string($value)) {
            try {
                return Carbon::parse($value)->toDateTimeString();
            } catch (\Throwable $e) {
                return $value;
            }
        }

        return $value;
    }

    public function parseValue($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (is_string($value)) {
            return Carbon::parse($value);
        }

        return $value;
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        if ($valueNode instanceof StringValueNode) {
            return Carbon::parse($valueNode->value);
        }

        if ($valueNode instanceof VariableNode) {
            $varName = $valueNode->name->value;
            $raw = $variables[$varName] ?? null;
            return is_string($raw) ? Carbon::parse($raw) : $raw;
        }

        return null;
    }
}