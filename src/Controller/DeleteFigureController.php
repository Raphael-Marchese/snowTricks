<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
class DeleteFigureController extends AbstractController
{
    #[Route('/delete/figure/{id}', name: 'app_delete_figure', requirements: ['id' => '\d+'])]
    public function __invoke(
        int $id,
        #[CurrentUser] ?User $user,
        FigureRepository $figureRepository,
        EntityManagerInterface $entityManager
    ) {
        $figure = $figureRepository->find($id);
        if (!$figure) {
            return new Response("figure non disponible", 404);
        }
        $entityManager->remove($figure);
        $entityManager->flush();

        $this->addFlash('success', 'Votre figure a été supprimée');

        return $this->redirectToRoute('app_homepage');
    }
}
