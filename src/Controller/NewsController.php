<?php

namespace App\Controller;

use App\Analytics\AnalyticsClient;
use App\Analytics\Plausible\EventRequest;
use App\Entity\News\Entry;
use App\Repository\JobRepository;
use App\Repository\News\EntryRepository;
use Knp\Component\Pager\PaginatorInterface;
use League\Uri\Uri;
use League\Uri\UriModifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    public function __construct(
        private readonly EntryRepository $articleRepository,
        private readonly JobRepository $jobRepository,
        private readonly AnalyticsClient $client,
    ) {
    }

    #[Route('/news', name: 'news_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->articleRepository->createQueryBuilderLastNews();

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('news/index.html.twig', [
            'pagination' => $pagination,
            'lastJobs' => $this->jobRepository->findLastJobs(5),
        ]);
    }

    #[Route('/news/{id}', name: 'news_entry', methods: ['GET'])]
    public function entry(Request $request, Entry $article): RedirectResponse
    {
        $this->client->event(EventRequest::create([
            'User-Agent' => $request->headers->get('User-Agent'),
            'X-Forwarded-For' => implode(',', $request->getClientIps()),
            'domain' => 'jobbsy.dev',
            'name' => 'pageview',
            'url' => $request->getUri(),
        ]));

        $uri = Uri::createFromString($article->getLink());
        $uri = UriModifier::appendQuery($uri, 'ref=jobbsy');

        return $this->redirect($uri);
    }
}
