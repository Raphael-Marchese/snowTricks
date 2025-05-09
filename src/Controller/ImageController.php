<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use App\Service\FilesUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/image')]
final class ImageController extends AbstractController
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    #[Route(name: 'app_image_index', methods: ['GET'])]
    public function index(ImageRepository $imageRepository): Response
    {
        return $this->render('image/index.html.twig', [
            'images' => $imageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_image_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('app_image_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('image/new.html.twig', [
            'image' => $image,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_image_show', methods: ['GET'])]
    public function show(Image $image): Response
    {
        return $this->render('image/show.html.twig', [
            'image' => $image,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_image_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Image $image,
        EntityManagerInterface $entityManager,
        FilesUploader $filesUploader,
    ): Response {
        $form = $this->createForm(ImageType::class, null, ['image' => $image]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('image')['illustrations'];

            $newImage = $filesUploader->upload($file, 'uploads/illustrations');

            $image->path = $newImage;
            $figure = $image->getFigure();

            if (!$figure) {
                return $this->redirectToRoute('app_homepage', ['id' => $figure->id]);
            }

            $entityManager->persist($image);
            $entityManager->flush();
            $slug = $this->slugger->slug($figure->name);

            return $this->redirectToRoute('app_update_figure', ['slug' => $slug]);
        }

        return $this->render('image/edit.html.twig', [
            'image' => $image,
            'form' => $form,
        ]);
    }

    #[Route('delete/{id}', name: 'app_image_delete', methods: ['POST'])]
    public function delete(Request $request, Image $image, EntityManagerInterface $entityManager): Response
    {
        $figure = $image->getFigure();

        if (!$figure) {
            return $this->redirectToRoute('app_homepage', ['id' => $figure->id]);
        }

        if ($this->isCsrfTokenValid('delete' . $image->id, $request->getPayload()->getString('_token'))) {
            $entityManager->remove($image);
            $entityManager->flush();
        }

        $slug = $this->slugger->slug($figure->name);

        return $this->redirectToRoute('app_update_figure', ['slug' => $slug]);
    }
}
