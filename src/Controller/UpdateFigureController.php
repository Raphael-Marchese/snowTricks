<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UpdateFigureController extends AbstractController
{
    #[Route('/update/figure/{id}', name: 'app_update_figure', requirements: ['id' => '\d+'])]
    public function __invoke(int $id, #[CurrentUser] ?User $user): Response
    {

        return $this->render('update_figure/index.html.twig', [
            'controller_name' => 'UpdateFigureController',
        ]);
    }
}
