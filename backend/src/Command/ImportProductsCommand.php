<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use League\Csv\Exception as CsvException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProductsCommand extends Command
{
    protected static $defaultName = 'app:import-products';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import products from CSV')
            ->addArgument('file', InputArgument::REQUIRED, 'CSV file path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');

        if (!file_exists($file)) {
            $output->writeln("<error>File not found: $file</error>");
            return Command::FAILURE;
        }

        try {
            $csv = Reader::createFromPath($file, 'r');
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords();
        } catch (CsvException $e) {
            $output->writeln("<error>CSV error: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }

        $categoryRepo = $this->em->getRepository(Category::class);
        $productRepo = $this->em->getRepository(Product::class);
        $categoryCache = [];
        $count = 0;

        foreach ($records as $record) {
            // --- Üres sorok kihagyása ---
            if (empty($record['identifier']) || empty($record['name'])) {
                continue;
            }

            // --- Kategória ---
            $categoryName = trim($record['category'] ?? 'N/A');

            if (isset($categoryCache[$categoryName])) {
                $category = $categoryCache[$categoryName];
            } else {
                $category = $categoryRepo->findOneBy(['name' => $categoryName]);
                if (!$category) {
                    $category = new Category();
                    $category->setName($categoryName);
                    $category->setSlug(strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $categoryName), '-')));
                    $this->em->persist($category);
                }
                $categoryCache[$categoryName] = $category;
            }

            // --- Termék ---
            $externalId = trim($record['identifier']);
            $product = $productRepo->findOneBy(['externalId' => $externalId]);

            if (!$product) {
                $product = new Product();
                $product->setExternalId($externalId);
                $this->em->persist($product);
                $count++;
            }

            // --- Gumi típus automatikus beállítása ---
            $nameUpper = strtoupper($record['name']);
            if (str_contains($nameUpper, 'M+S')) {
                $type = str_contains($nameUpper, '3PMSF') ? 'winter' : 'all-season';
            } else {
                $type = 'summer';
            }

            // --- Gumi átmérő automatikus beállítása ---
            $diameter = null;
            if (preg_match('/R(\d{2})/i', $record['name'], $matches)) {
                $diameter = (int)$matches[1];
            }

            $product->setName(trim($record['name']));
            $product->setPrice((float)($record['price'] ?? 0));
            $product->setNetPrice((float)($record['net_price'] ?? 0));
            $product->setImage(trim($record['image_url'] ?? ''));
            $product->setDescription('Lorem ipsum dolor sit amet...');
            $product->setCategory($category);
            $product->setType($type);
            $product->setDiameter($diameter);
        }


        $this->em->flush();
        $output->writeln("<info>Imported/Updated $count products successfully.</info>");

        return Command::SUCCESS;
    }
}
