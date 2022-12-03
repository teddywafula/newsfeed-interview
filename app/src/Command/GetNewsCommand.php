<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\NewsParser;
use App\Util\CleanData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class GetNewsCommand
 */
class GetNewsCommand extends Command
{

    protected static $defaultName = 'app:get-news';
    private MessageBusInterface $bus;
    private CleanData $cleanData;
    private ParameterBagInterface $params;

    public function __construct(
        MessageBusInterface $bus,
        CleanData $cleanData,
        ParameterBagInterface $params
    )
    {
        $this->bus = $bus;
        $this->cleanData = $cleanData;
        $this->params = $params;
        parent::__construct();
    }

    protected function configure():void
    {
        $this->setDescription('Get news from a feed by running the command');
        $this->setHelp('This command is for getting news');
    }

    protected function execute(InputInterface $input, OutputInterface $output):int
    {
        $parser = new NewsParser($this->params);
        $output->writeln([
            'Getting News',
            '============',
            '',
        ]);
        $parser->getNewsFeed($this->bus, $this->cleanData);
        return Command::SUCCESS;

    }
}