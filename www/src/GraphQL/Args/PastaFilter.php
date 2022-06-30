<?php

namespace App\GraphQL\Args;

use Overblog\GraphQLBundle\Definition\Builder\MappingInterface;

class PastaFilter implements MappingInterface
{

    public function toMappingDefinition(array $config): array
    {
        return [
//            'pagination' => [
//                'argsBuilder' => 'Pager'
////                'type' => '[String!]!',
////                'defaultValue' => [],
//            ],
            'region' => [
                'type' => '[String!]!',
                'defaultValue' => [],
            ],
            'price' => [
                'type' => '[Int!]',
                'defaultValue' => [],
            ],
            'manufacturer' => [
                'type' => '[String!]!',
                'defaultValue' => [],
            ],
            'properties' => [
                'type' => '[String!]!',
                'defaultValue' => [],
            ],
        ];
    }
}