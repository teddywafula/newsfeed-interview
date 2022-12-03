<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\NewsRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request, NewsRepository $newsRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $page = (int) $request->get('page', 1);
        $results = $this->paginate($page, $newsRepository);

        return $this->render('home/index.html.twig', $results);

    }

    /**
     * @param $currentPage
     * @param NewsRepository $newsRepository
     *
     * @return array
     */
    private function paginate($currentPage, NewsRepository $newsRepository): array
    {
        $perPage = $this->getParameter('max_results', 10);
        $first = ($currentPage - 1) * $perPage;
        $news = $newsRepository->getLimitedNews($first, $perPage);
        $paginator = new Paginator($news, $fetchJoinCollection = true);
        $totalCount = count($paginator);
        $left = $currentPage - 3;
        $right = $currentPage + 3;
        $totalPages = ceil($totalCount / $perPage);

        if($left < 1 || $currentPage == 1) {
            $left = 1;
        }
        if ($right > $totalPages || $currentPage == $totalPages)
        {
            $right = $totalPages;
        }
        return [
            'news'=> $paginator,
            'totalPages' => $totalPages,
            'left' => $left,
            'right' => $right,
            'currentPage' => $currentPage,
            'maxResults' => $perPage,
        ];
    }

}
