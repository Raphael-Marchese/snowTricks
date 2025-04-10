<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\CreateMessageType;
use App\Repository\FigureRepository;
use App\Repository\ImageRepository;
use App\Repository\MessageRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class GetFigureController extends AbstractController
{
    #[Route('/figure/{slug}', name: 'app_single_figure', requirements: ['slug' => '[a-zA-Z0-9-]+'])]
    public function __invoke(
        string $slug,
        #[CurrentUser] ?User $user,
        FigureRepository $figureRepository,
        MessageRepository $messageRepository,
        ImageRepository $imageRepository,
        VideoRepository $videoRepository,
        Request $request
    ): Response {

        $singleFigure = $figureRepository->findOneBy(['name' => str_replace('-', ' ', $slug)]);

        $comments = $messageRepository->findBy(['figure' => $singleFigure], ['createdAt' => 'DESC']);

        $message = new Message();
        $message->figure = $singleFigure;
        $form = $this->createForm(CreateMessageType::class, $message);

        if (!$singleFigure) {
            throw $this->createNotFoundException('La figure demandée n\'existe pas.');
        }

        $images = $imageRepository->findBy(['figure' => $singleFigure->id]);
        $videos = $videoRepository->findBy(['figure' => $singleFigure->id]);

        return $this->render('figure/index.html.twig', [
            'figure' => $singleFigure,
            'comments' => $comments,
            'images' => $images,
            'videos' => $videos,
            'form' => $form->createView(),
        ]);
    }
}
