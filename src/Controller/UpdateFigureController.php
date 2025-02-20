<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateFigureType;
use App\Repository\FigureRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UpdateFigureController extends AbstractController
{
    #[Route('/update/figure/{id}', name: 'app_update_figure', requirements: ['id' => '\d+'])]
    public function __invoke(
        int $id,
        #[CurrentUser] ?User $user,
        UserRepository $userRepository,
        FigureRepository $figureRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $figure = $figureRepository->find($id);

        if (!$figure) {
            return new Response("figure non disponible", 404);
        }

        if (!$user) {
            throw new AccessDeniedHttpException('Vous devez être connecté pour pour modifier une figure');
        }

        $form = $this->createForm(CreateFigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $figure->updatedAt = new \DateTimeImmutable();
            $figure->author = $user;

            $entityManager->persist($figure);
            $entityManager->flush();

            $this->addFlash('success', 'Votre figure a été modifiée');

            return $this->redirectToRoute('app_single_figure', [
                'id' => $figure?->id,
            ]);
        }
        return $this->render('figure/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
