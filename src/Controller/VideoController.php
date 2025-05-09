<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use App\Service\FilesUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/video')]
final class VideoController extends AbstractController
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

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
    public function edit(Request $request, Video $video, EntityManagerInterface $entityManager, FilesUploader $filesUploader): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $request->files->get('video')['videos'];

            $newvideo = $filesUploader->upload($file, 'uploads/videos');

            $video->path = $newvideo;
            $figure = $video->getFigure();

            if (!$figure) {
                return $this->redirectToRoute('app_homepage', ['id' => $figure->id]);
            }

            $entityManager->persist($video);
            $entityManager->flush();
            $slug = $this->slugger->slug($figure->name);

            return $this->redirectToRoute('app_update_figure', ['slug' => $slug]);
        }

        return $this->render('video/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('delete/{id}', name: 'app_video_delete', requirements: ['id' => '\d+'])]
    public function delete(Request $request, Video $video, EntityManagerInterface $entityManager): Response
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

        $slug = $this->slugger->slug($figure->name);

        return $this->redirectToRoute('app_update_figure', ['slug' => $slug]);
    }
}
