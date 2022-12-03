<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\NewsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    /**
     * @Route("/news/delete/{id}", name="app_news_delete_item", methods={"GET","DELETE"})
     */
    public function delete(NewsRepository $repository, $id, ManagerRegistry $managerRegistry): RedirectResponse
    {
        $news = $repository->find($id);
        if (!$news)
        {
            return $this->createNotFoundException('News Item not found');
        }
        $entityManager = $managerRegistry->getManager();
        $entityManager->remove($news);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}
