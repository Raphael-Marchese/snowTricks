<?php

declare(strict_types=1);

namespace App\Controller\Figure;

use App\Entity\User;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class DeleteImageController extends AbstractController
{
    #[Route('/figure/{id}/delete-image', name: 'app_delete_image', requirements: ['id' => '\d+'])]
    public function __invoke(
        int $id,
        #[CurrentUser] ?User $user,
        Request $request,
        FigureRepository $figureRepository,
        EntityManagerInterface $entityManager
    ) {
        $imagePath = $request->request->get('image_path'); // Récupération du chemin de l'image

        $figure = $figureRepository->find($id);

        if (!$figure) {
            return new Response("figure non disponible", 404);
        }

        if (!$user) {
            throw new AccessDeniedHttpException('Vous devez être connecté pour pour modifier une figure');
        }

        // Suppression du chemin de l'image dans le tableau
        $illustrations = $figure->illustrations;
        if (($key = array_search($imagePath, $illustrations)) !== false) {
            unset($illustrations[$key]);
            $figure->illustrations = $illustrations; // Réindexer le tableau
        }

        $entityManager->persist($figure);
        $entityManager->flush();

        $this->addFlash('success', 'L\'image a été supprimée avec succès.');

        return $this->redirectToRoute('figure_show', ['id' => $figure->id]);
    }
}
