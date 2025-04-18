<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\FigureRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/video')]
final class VideoController extends AbstractController
{
    #[Route(name: 'app_video_index', methods: ['GET'])]
    public function index(VideoRepository $videoRepository): Response
    {
        return $this->render('video/index.html.twig', [
            'videos' => $videoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_video_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($video);
            $entityManager->flush();

            return $this->redirectToRoute('app_video_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('video/new.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_video_show', methods: ['GET'])]
    public function show(Video $video): Response
    {
        return $this->render('video/show.html.twig', [
            'video' => $video,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_video_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Video $video, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_video_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('video/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('delete/{id}', name: 'app_video_delete', requirements: ['id' => '\d+'])]
    public function delete(Request $request, Video $video, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $figure = $video->getFigure();

        if(!$figure)
        {
            return $this->redirectToRoute('app_homepage', ['id' => $figure->id]);

        }

        if ($this->isCsrfTokenValid('delete'.$video->id, $request->getPayload()->getString('_token'))) {
            $figure?->getVideos()->removeElement($video);
            $entityManager->remove($video);
            $entityManager->flush();
        }

        $slug = $slugger->slug($figure->name);

        return $this->redirectToRoute('app_update_figure', ['slug' => $slug]);
    }
}
