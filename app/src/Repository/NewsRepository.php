<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 *
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    /**
     * @param News $entity
     * @param bool $flush
     *
     * @return void
     */
    public function add(News $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param News $entity
     * @param bool $flush
     *
     * @return void
     */
    public function remove(News $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Get news that are paginated
     *
     * @param int $start
     * @param int $perPage
     *
     * @return \Doctrine\ORM\Query
     */
    public function getLimitedNews(int $start, int $perPage)
    {
        return $this->createQueryBuilder('h')
            ->setFirstResult($start)
            ->setMaxResults($perPage)
            ->orderBy('h.pub_date', 'DESC')
            ->getQuery();
    }

    /**
     * Check items by title if they exist
     *
     * @param string $title
     *
     * @return float|int|mixed|string|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByTitle(string $title)
    {
        return $this->createQueryBuilder('h')
            ->where('h.title = :title')
            ->setParameter('title', $title)
            ->setMaxResults(1)
            ->select(['h.id','h.pub_date'])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Update a given news item
     *
     * @param int $id
     * @param News $news
     *
     * @return float|int|mixed|string
     */
    public function updateNews(int $id, News $news)
    {
        return $this->createQueryBuilder('h')
            ->update()
            ->set('h.title', ':title')
            ->set('h.excerpt', ':excerpt')
            ->set('h.picture', ':picture')
            ->set('h.pub_date', ':pub_date')
            ->where('h.id = :id')
            ->setParameter('title', $news->getTitle())
            ->setParameter('excerpt', $news->getExcerpt())
            ->setParameter('picture', $news->getPicture())
            ->setParameter('pub_date', $news->getPubDate())
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

}
