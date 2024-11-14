<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function __invoke(#[CurrentUser] ?User $user, FigureRepository $figureRepository): Response
    {
        $figures = $figureRepository->findAll();

        return $this->render('homepage/index.html.twig', [
            'figures' => $figures
        ]);
    }
}
