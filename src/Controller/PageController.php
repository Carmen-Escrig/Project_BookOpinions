<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use App\Entity\Review;
use App\Entity\User;

class PageController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home_page', []);
        }

        $reviewRepository = $doctrine->getRepository(Review::class);
        $reviews = $reviewRepository->findRandom();

        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
            'reviews' => $reviews
        ]);
    }

    #[Route('/home', name: 'home')]
    public function home(): Response
    {
        return $this->render('page/home.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
}
