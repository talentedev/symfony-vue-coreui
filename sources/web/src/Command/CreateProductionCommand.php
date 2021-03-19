<?php
namespace App\Command;

use App\Entity\Production;
use App\Service\ProductService;
use App\Service\ProductionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \DateTime;
use \DateInterval;

class CreateProductionCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-production';

    /** @var ProductService $productService */
    private $productService;

    /** @var ProductionService $productionService */
    private $productionService;

    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * CreateProductionCommand constructor
     * @param ProductService $productService
     * @param ProductionService $productionService
     * @param EntityManagerInterface $em
     */
    public function __construct(ProductService $productService, ProductionService $productionService, EntityManagerInterface $em)
    {
        parent::__construct();

        $this->productService = $productService;
        $this->productionService = $productionService;
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Creates new productions')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create productions for every month')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $products = $this->productService->getAll();
        foreach ($products as $product) {
            $now = new DateTime();
            $now->modify('first day of this month');
            $now->modify('noon');
            $now->add(new DateInterval('P14D'));
            $now->sub(new DateInterval('P1M'));
            $productId = $product->getId();
            // Get production 1 month ago
            $production = $this->productionService->getProductionByDate($now, $productId);
            if ($production === null) {
                // Get 2 months ago.
                $now->sub(new DateInterval('P1M'));
                $lastProduction = $this->productionService->getProductionByDate($now, $productId);

                if ($lastProduction) {
                    $capacity = $lastProduction->getCapacity() | 0;
                    // Set middle day of last month
                    $now->add(new DateInterval('P1M'));
                    $now->modify('first day of this month');
                    $now->add(new DateInterval('P14D'));

                    $production = new Production();
                    $production->setCapacity($capacity);
                    $production->setDate($now);
                    $production->setProduct($product);

                    $this->em->persist($production);
                }
            }
        }

        $this->em->flush();
    }
}
