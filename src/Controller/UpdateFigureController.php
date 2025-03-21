<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\User;
use App\Entity\Video;
use App\Form\CreateFigureType;
use App\Repository\CategoryRepository;
use App\Repository\FigureRepository;
use App\Repository\UserRepository;
use App\Service\FilesUploader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
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
        #[Autowire('%kernel.project_dir%/public/uploads/illustrations')] string $illustrationsDirectory,
        #[Autowire('%kernel.project_dir%/public/uploads/videos')] string $videosDirectory,
        FilesUploader $filesUploader,
        UserRepository $userRepository,
        FigureRepository $figureRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
    ): Response {
        $figure = $figureRepository->find($id);

        if (!$figure) {
            return new Response("figure non disponible", 404);
        }

        if (!$user) {
            throw new AccessDeniedHttpException('Vous devez être connecté pour pour modifier une figure');
        }

        $form = $this->createForm(CreateFigureType::class, $figure, [
            'figureGroup' => $figure->figureGroup
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $figure->updatedAt = new \DateTimeImmutable();
            $figure->author = $user;
            $categoryName = $form->get('figureGroup')->getData();

            $category = $categoryRepository->findOneBy(['name' => $categoryName]);

            if ($category) {
                $figure->figureGroup = $category;
            }

            $illustrations = $form->get('illustrations')->getData();

            if ($illustrations) {
                foreach ($illustrations as $illustration) {
                    $path = $filesUploader->upload($illustration, $illustrationsDirectory);
                    $image = new Image();
                    $image->setFigure($figure);
                    $image->path = $path;

                    $figure->addIllustration($image);
                }
            }

            $videos = $form->get('videos')->getData();

            if ($videos) {
                foreach ($videos as $video) {
                    $filename = $filesUploader->upload($video, $videosDirectory);
                    $video = new Video();
                    $video->setFigure($figure);
                    $video->path = $filename;
                    $figure->addVideo($video);
                }
            }

            $entityManager->persist($figure);

            $entityManager->flush();

            $this->addFlash('success', 'Votre figure a été modifiée');

            return $this->redirectToRoute('app_single_figure', [
                'id' => $figure?->id,
            ]);
        }
        return $this->render('figure/update.html.twig', [
            'form' => $form->createView(),
            'figure' => $figure
        ]);
    }
}
