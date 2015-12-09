<?php
namespace PODataHeaven\ConsoleCommand;

use Cron\CronExpression;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PODataHeaven\Service\DenormalizerParserService;
use PODataHeaven\Service\DenormalizerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Connection;

class DenormalizeConsoleCommand extends Command
{
    /**
     * @var DenormalizerParserService
     */
    private $denormalizerParserService;

    /**
     * @var DenormalizerService
     */
    private $denormalizerService;

    /**
     * DenormalizerConsoleCommand constructor.
     * @param DenormalizerParserService $denormalizerParserService
     * @param Connection $sourceConnection
     * @param Connection $targetConnection
     */
    public function __construct(
        DenormalizerParserService $denormalizerParserService,
        DenormalizerService $denormalizerService
    ) {
        parent::__construct();

        $this->denormalizerParserService = $denormalizerParserService;
        $this->denormalizerService = $denormalizerService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('pdh:denormalizer')
            ->setDescription('Data denormalizer')
            ->addArgument(
                'denormalizer',
                InputArgument::OPTIONAL,
                'denormalizer filename without .yml extension'
            )
            ->addOption(
                'scheduled',
                's',
                InputOption::VALUE_NONE,
                'run only scheduled'
            )
            ->addOption(
                'all',
                'a',
                InputOption::VALUE_NONE,
                'run all'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new Logger('po-data-heaven');
        $logger->pushHandler(new StreamHandler('php://output', Logger::DEBUG));

        if ($input->getOption('all')) {
            foreach ($this->denormalizerParserService->getAll() as $config) {
                $logger->info('running ' . $config['resultTable']);
                $this->denormalizerService->denormalize($config, $logger);
            }

        } elseif ($input->getOption('scheduled')) {
            $run = [];
            foreach ($this->denormalizerParserService->getAll() as $config) {
                $cron = CronExpression::factory($config['cron']);
                if (!$cron->isDue()) {
                    $logger->info('skipping ' . $config['resultTable']);
                    continue;
                }
                $run[] = $config;
            }

            foreach ($run as $config) {
                $logger->info('running ' . $config['resultTable']);
                $this->denormalizerService->denormalize($config, $logger);
            }

        } else {
            try {
                $config = $this->denormalizerParserService->get($input->getArgument('denormalizer'));
            } catch (\Exception $e) {
                $logger->error('denormalizer not found');
                return;
            }

            $logger->info('running ' . $config['resultTable']);
            $this->denormalizerService->denormalize($config, $logger);
        }
    }
}
