<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Image;
use App\Entity\User;
use App\Entity\Video;
use App\Form\CreateFigureType;
use App\Repository\CategoryRepository;
use App\Repository\FigureRepository;
use App\Repository\UserRepository;
use App\Service\FilesUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class CreateFigureController extends AbstractController
{
    #[Route('/figure/create', name: 'app_create_figure')]
    public function __invoke(
        UserRepository $userRepository,
        Request $request,
        #[CurrentUser] ?User $user,
        #[Autowire('%kernel.project_dir%/public/uploads/illustrations')] string $illustrationsDirectory,
        #[Autowire('%kernel.project_dir%/public/uploads/videos')] string $videosDirectory,
        FilesUploader $filesUploader,
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
    ) {
        $figure = new Figure();
        $form = $this->createForm(CreateFigureType::class, $figure);
        $form->handleRequest($request);

        if (!$user) {
            throw new AccessDeniedHttpException('Vous devez être connecté pour pour créer une figure');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $figure = $form->getData();
                $figure->author = $user;
                $figure->createdAt = new \DateTimeImmutable();

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

                $this->addFlash('success', 'Votre figure a été créée');

                return $this->redirectToRoute('app_single_figure', [
                    'id' => $figure->id
                ]);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

        return $this->render('figure/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
