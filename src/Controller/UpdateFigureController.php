<?php

namespace App\Controller;

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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\String\Slugger\SluggerInterface;

class UpdateFigureController extends AbstractController
{
    #[Route('/update/figure/{slug}', name: 'app_update_figure', requirements: ['slug' => '[a-zA-Z0-9-]+'])]
    public function __invoke(
        string $slug,
        #[CurrentUser] ?User $user,
        #[Autowire('%kernel.project_dir%/public/uploads/illustrations')] string $illustrationsDirectory,
        #[Autowire('%kernel.project_dir%/public/uploads/videos')] string $videosDirectory,
        FilesUploader $filesUploader,
        UserRepository $userRepository,
        FigureRepository $figureRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
        SluggerInterface $slugger,
    ): Response {

        $figure = $figureRepository->findOneBy(['name' => str_replace('-', ' ', $slug)]);

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
            $name = $form->get('name')->getData();
                $name = preg_replace('/[éèêë]/u', 'e', $name);
                $name = preg_replace('/[àáâä]/u', 'a', $name);
                $name = preg_replace('/[îï]/u', 'i', $name);
                $name = preg_replace('/[ôö]/u', 'o', $name);
                $name = preg_replace('/[ùúûü]/u', 'u', $name);
                $name = ucfirst($name);
                $figure->name = $name;
            $slug = $slugger->slug($name);

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
                'slug' => $slug,
            ]);
        }
        return $this->render('figure/update.html.twig', [
            'form' => $form->createView(),
            'figure' => $figure
        ]);
    }
}
