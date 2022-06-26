<?php

namespace App\Dto\Pasta\Transformer;

use App\Dto\Pasta\ImportPastaDto;
use App\Entity\Pasta;

class ImportPastaDtoTransformer
{

    /**
     * @param ImportPastaDto $object
     * @return Pasta
     */
    public function transformToEntity(ImportPastaDto $object): Pasta
    {

        return (new Pasta())
            ->setName($object->name)
            ->setRegion($object->region)
            ->setPrice($object->price)
            ->setBasePrice($object->basePrice)
            ->setManufacturer($object->manufacturer)
            ->setProperties($object->properties)
            ;
    }

    /**
     * @param Pasta $entity
     * @return ImportPastaDto
     */
    public function transformToDto(Pasta $entity): ImportPastaDto
    {
        $productDto = new ImportPastaDto();
        $productDto->id = $entity->getId();
        $productDto->name = $entity->getName();
        $productDto->region = $entity->getRegion();
        $productDto->price = $entity->getPrice();
        $productDto->basePrice = $entity->getBasePrice();
        $productDto->manufacturer = $entity->getManufacturer();
        $productDto->properties = $entity->getProperties();

        return $productDto;
    }

}