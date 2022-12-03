<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\News;
use App\Message\NewsNotification;
use App\Repository\NewsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Class NewsNotificationHandler
 * @author Teddy Wafula
 */
class NewsNotificationHandler implements MessageHandlerInterface
{
    private LoggerInterface $logger;
    private ManagerRegistry $managerRegistry;
    private NewsRepository $newsRepository;

    public function __construct(
        LoggerInterface $logger,
        ManagerRegistry $managerRegistry,
        NewsRepository $newsRepository
    )
    {
        $this->logger = $logger;
        $this->managerRegistry = $managerRegistry;
        $this->newsRepository = $newsRepository;
    }

    /**
     * @param NewsNotification $message
     *
     * @return void
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function __invoke(NewsNotification $message)
    {
        $message = $message->getContent();
        $title = $message->getTitle();
        $result = $this->newsRepository->findByTitle($title);
        $entityManager = $this->managerRegistry->getManager();

        if ($result){
            if($result['pub_date'] < $message->getPubDate()){
                $this->newsRepository->updateNews($result['id'], $message);
                $this->logger->info("Title found and item updated" . $result['id']);
            }else{
                $this->logger->info("Title found but item not updated " . $result['id']);
            }
        }else{
            $this->logger->info("title : not found " . json_encode($result));
            $entityManager->persist($message);
            $entityManager->flush();
        }

    }
}