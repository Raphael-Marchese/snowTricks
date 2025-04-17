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
use Symfony\Component\String\Slugger\SluggerInterface;

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
        SluggerInterface $slugger,
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
                $name = $form->get('name')->getData();
                $name = preg_replace('/[éèêë]/u', 'e', $name);
                $name = preg_replace('/[àáâä]/u', 'a', $name);
                $name = preg_replace('/[îï]/u', 'i', $name);
                $name = preg_replace('/[ôö]/u', 'o', $name);
                $name = preg_replace('/[ùúûü]/u', 'u', $name);
                $name = ucfirst($name);
                $figure->name = $name;

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
                $slug = $slugger->slug($figure->name);
                $entityManager->persist($figure);
                $entityManager->flush();

                $this->addFlash('success', 'Votre figure a été créée');

                return $this->redirectToRoute('app_single_figure', [
                    'slug' => $slug
                ]);
            } catch (\Exception $e) {
                throw new \RuntimeException($e->getMessage());
            }
        }

        return $this->render('figure/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
