<?php

namespace App\GraphQL\Resolver;

use App\Repository\PastaRepository;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;


class PastaResolver implements QueryInterface, AliasedInterface
{

    public function __construct(private PastaRepository $pastaRepository)
    {
    }

    public function resolveCollection(Argument $args)
    {
        return $this->pastaRepository->findBy(
            [],
            ['id' => 'desc'],
            $args['limit'],
            0
        );
    }

    public function resolve($id)
    {
        return $this->pastaRepository->find($id);
    }

    public static function getAliases(): array
    {
        return [
            'resolve' => 'Pasta'
        ];
    }
}