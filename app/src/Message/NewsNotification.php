<?php
declare(strict_types=1);

namespace App\Message;

use App\Entity\News;

/**
 * Class NewsNotification
 *
 * @author Teddy Wafula
 */
class NewsNotification
{
    private News $news;

    public function __construct(News $news)
    {
        $this->news = $news;
    }

    /**
     * Get message from messenger
     *
     * @return News
     */
    public function getContent(): News
    {
        return $this->news;
    }

}