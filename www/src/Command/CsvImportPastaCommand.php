<?php

namespace App\Command;

use App\Dto\Pasta\ImportPastaHeaderDto;
use App\Helper\ImportPastaHelper;
use Doctrine\DBAL\Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Traversable;


class CsvImportPastaCommand extends Command
{

    const FILE_NOT_EXIST = 1;
    const FILE_ENCODING_INCORRECT = 1;


    private int $batchingItemsCount = 1000;
    private ContainerBagInterface $params;
    private SymfonyStyle $io;

    /**
     * @param ImportPastaHelper $importPastaHelper
     * @param ContainerBagInterface $params
     */
    public function __construct(private ImportPastaHelper $importPastaHelper,
                                ContainerBagInterface     $params)
    {
        parent::__construct();
        $this->params = $params;
    }

    protected function configure(): void
    {
        $this->setName('csv:import-products')
            ->setDescription('Import Products from file.')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to import file.');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     *
     * @throws \League\Csv\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');

        try {
            $this->isFailValid($path);

            $reader = Reader::createFromPath($path)
                ->setDelimiter(';')
                ->skipEmptyRecords();

            $csvData = $reader->setHeaderOffset(1);
            $headers = new ImportPastaHeaderDto();
            $headers->name = 'name';
            $headers->region = 'region';
            $headers->price = 'price';
            $headers->basePrice = 'basePrice';
            $headers->manufacturer = 'manufacturer';
            $headers->properties = 'properties';

            $this->importFile($csvData, $headers);
        } catch (\Exception $e) {
            $this->io->newLine(2);
            $this->io->error($e->getMessage());
            return Command::INVALID;
        }

        return Command::SUCCESS;
    }

    /**
     * @param Traversable $csvData
     * @param ImportPastaHeaderDto $headers
     *
     * @return void
     *
     * @throws \League\Csv\Exception
     * @throws \Exception
     */
    public function importFile(Traversable $csvData, ImportPastaHeaderDto $headers): void
    {
        $productsCount = iterator_count($csvData);

        $this->io->title('Start to Import products!!!');
        $this->io->progressStart($productsCount);

        $offset = 0;
        $limit = $this->batchingItemsCount;
        $this->importPastaHelper->startTransaction();
        while ($productsCount > $offset) {
            $stmt = Statement::create()
                ->offset($offset)
                ->limit($limit);
            $products = $stmt->process($csvData);
            $this->importPastaHelper->importPasta($products, $headers);

            $this->io->progressAdvance(iterator_count($products));
            $offset += $limit;
        }
        $this->importPastaHelper->commitTransaction();

        $this->io->progressFinish();
        $this->io->listing([
            printf('Skipped %s Stocks.', $this->importPastaHelper->getSkippedProductsCount()),
            printf('Imported %s Stocks.', $this->importPastaHelper->getImportedProductsCount()),
        ]);
    }

    /**
     * @param $pathToFile
     *
     * @throws Exception
     */
    public function isFailValid($pathToFile): bool
    {
        if (!file_exists($pathToFile)) {
            throw new Exception('File not exist: ' . $pathToFile, 1);
        }

        if (!mb_detect_encoding(file_get_contents($pathToFile), ['UTF-8'], true)) {
            throw new Exception('File encoding is not correct.', 2);
        }

        return true;
    }
}
