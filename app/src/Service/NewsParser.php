<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\News;
use App\Message\NewsNotification;
use DOMDocument;
use App\Util\CleanData;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class NewsParser
 * @author Teddy Wafula
 * @copyright (c) TeddyWafula 2022
 */
class NewsParser
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * @param MessageBusInterface $bus
     * @param CleanData $cleanData
     *
     * @return void
     */
    public function getNewsFeed(MessageBusInterface $bus, CleanData $cleanData): void
    {
        $info = new DOMDocument();
        $url = $this->params->get('news_url');
        $info->load($url);
        foreach ($info->getElementsByTagName('item') as $node) {
            $image="";
            if(count($node->getElementsByTagName('encoded')) ) {
                $content = $node->getElementsByTagName('encoded')->item(0)->nodeValue;
                $image = $cleanData->cleanData($this->getImage($content));
            }
            $excerpt ="";
            if(count($node->getElementsByTagName('description')) ) {
                $description = $node->getElementsByTagName('description')->item(0)->nodeValue;
                $excerpt = $cleanData->cleanData($this->extractExcerpt($description));
            }
            $createdAt = $this->formatDate(date('Y-m-d H:i:s'));
            if(count($node->getElementsByTagName('pubDate')) ) {
                $pubDate = $cleanData->cleanData($node->getElementsByTagName('pubDate')->item(0)->nodeValue);
                $createdAt = $this->formatDate($pubDate);
            }
            if(count($node->getElementsByTagName('title')) < 1) {
                break;
            }
            $title = $cleanData->cleanData($node->getElementsByTagName('title')->item(0)->nodeValue);

            $this->sendMessage($bus, $title, $excerpt, $image, $createdAt);

        }

    }
    private function sendMessage(MessageBusInterface $bus,$title,$excerpt,$image,$createdAt)
    {
        $news = new News();
        $news->setTitle($title);
        $news->setExcerpt($excerpt);
        $news->setPicture($image);
        $news->setPubDate($createdAt);
        $news->setStatus(1);
        $bus->dispatch(new NewsNotification($news));
    }

    /**
     * @param string $description
     *
     * @return mixed|string
     */
    private function extractExcerpt(string $description)
    {
        preg_match('#^<p>(.*?)</p>#i', $description, $matches);

        if (count($matches) < 1)
        {
            if (strlen($description) > 0){
                return $description;
            }
            return "";
        }
        return $matches[0];
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function getImage(string $content): string
    {
        $dom = new DOMDocument();
        $dom->loadHTML($content);
        $img = $dom->getElementsByTagName('img');
        if ($img->length < 1)
        {
            return "";
        }
        return $img->item(0)->getAttribute('src');
    }

    /**
     * @param string $pubDate
     *
     * @return \DateTime
     */
    private function formatDate(string $pubDate): \DateTime
    {
        $times = date('Y-m-d H:i:s',strtotime($pubDate));
        return \DateTime::createFromFormat('Y-m-d H:i:s',$times);
    }

}