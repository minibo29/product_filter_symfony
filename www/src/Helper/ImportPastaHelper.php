<?php

namespace App\Helper;

use App\Dto\Pasta\ImportPastaDto;
use App\Dto\Pasta\ImportPastaHeaderDto;
use App\Dto\Pasta\Transformer\ImportPastaDtoTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportPastaHelper
{

    private int $importedProductsCount = 0;
    private int $skippedProductsCount = 0;
    private array $importCurrency;
    private bool $transactionOpened;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ImportPastaDtoTransformer $importPastaDtoTransformer
     * @param LoggerInterface $logger
     * @param ValidatorInterface $validator
     */
    public function __construct(private EntityManagerInterface $entityManager,
                                private ImportPastaDtoTransformer $importPastaDtoTransformer,
                                private LoggerInterface $logger,
                                private ValidatorInterface $validator,
    )
    {
    }

    /**
     * @param iterable $csvData import data.
     * @param ImportPastaHeaderDto $headers list of header
     *
     * @throws \Exception
     */
    public function importPasta(iterable $csvData, ImportPastaHeaderDto $headers): void
    {
        foreach ($csvData as $product) {
            $productDto = new ImportPastaDto();
            $productDto->name = $product[$headers->name];
            $productDto->region = $product[$headers->region];
            $productDto->price = $product[$headers->price];
            $productDto->basePrice = $product[$headers->basePrice];
            $productDto->manufacturer = $product[$headers->manufacturer];
            $productDto->properties = $product[$headers->properties];

            $errors = $this->validator->validate($productDto);
            if (count($errors) > 0) {
                $this->logErrors($product, $errors);
                $this->skippedProductsCount++;
            } else {
                $this->entityManager->persist($this->importPastaDtoTransformer->transformToEntity($productDto));
                $this->importedProductsCount++;
            }
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->rollBackTransaction();
            $this->logger->info($e->getMessage());
            $errorMessage = sprintf('Products cannot be imported. Please check a log file for more information(%s).',
                $this->logger->popHandler()->getUrl());

            throw new \Exception($errorMessage);
        }
    }

    public function startTransaction()
    {
        $this->entityManager->beginTransaction();
        $this->transactionOpened = true;
    }

    public function commitTransaction()
    {
        if ($this->transactionOpened) {
            $this->entityManager->commit();
            $this->transactionOpened = false;
        }
    }

    public function rollBackTransaction()
    {
        if ($this->transactionOpened) {
            $this->entityManager->rollBack();
            $this->transactionOpened = false;
        }
    }


    /**
     * @return int
     */
    public function getImportedProductsCount(): int
    {
        return $this->importedProductsCount;
    }

    /**
     * @return int
     */
    public function getSkippedProductsCount(): int
    {
        return $this->skippedProductsCount;
    }

    /**
     * @param $product
     * @param ConstraintViolationList $errors
     * @return void
     */
    private function logErrors($product, ConstraintViolationList $errors): void
    {
        foreach ($errors as $error) {
            $this->logger->info($error->getPropertyPath() . ': ' .  $error->getMessage(), $product);
        }
    }
}