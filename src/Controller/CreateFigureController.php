<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\User;
use App\Form\CreateFigureType;
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
        EntityManagerInterface $entityManager
    ) {
        $figure = new Figure();
        $form = $this->createForm(CreateFigureType::class, $figure);
        $form->handleRequest($request);

        /*        if(!$user) {
                    throw new AccessDeniedHttpException('Vous devez être connecté pour pour créer une figure');
                }*/

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $figure = $form->getData();
                $figure->author = $user ?: $userRepository->find(1);
                $figure->createdAt = new \DateTimeImmutable();

                $illustrations = $form->get('illustrations')->getData();

                if ($illustrations) {
                    foreach ($illustrations as $illustration) {
                        $filename = $filesUploader->upload($illustration, $illustrationsDirectory);
                        $figure->illustrations[] = $filename;
                    }
                }

                $videos = $form->get('videos')->getData();

                if ($videos) {
                    foreach ($videos as $video) {
                        $filename = $filesUploader->upload($video, $videosDirectory);
                        $figure->videos[] = $filename;
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
