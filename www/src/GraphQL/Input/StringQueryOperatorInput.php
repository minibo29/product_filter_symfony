<?php

namespace App\GraphQL\Input;

use Exception;
use GraphQL\Error\Error;
use GraphQL\Language\AST\BooleanValueNode;
use GraphQL\Language\AST\FloatValueNode;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\NullValueNode;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;

class StringQueryOperatorInput extends ScalarType implements AliasedInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getAliases(): array
    {
        return ['DateTime', 'Date'];
    }
    // ...
    public function serialize($value)
    {
        // TODO: Implement serialize() method.
    }

    public function parseValue($value)
    {
        // TODO: Implement parseValue() method.
    }

    public function parseLiteral(Node $valueNode, ?array $variables = null)
    {
        // TODO: Implement parseLiteral() method.
    }
}