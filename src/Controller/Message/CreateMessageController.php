<?php

declare(strict_types=1);

namespace App\Controller\Message;

use App\Entity\Message;
use App\Entity\User;
use App\Form\CreateMessageType;
use App\Repository\FigureRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

#[AsController]
class CreateMessageController extends AbstractController
{
    #[Route('/figure/{id}/comments', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsCsrfTokenValid('create_message', tokenKey: '_token')]
    public function __invoke(
        int $id,
        #[CurrentUser] ?User $user,
        MessageRepository $messageRepository,
        FigureRepository $figureRepository,
        UserRepository $userRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $figure = $figureRepository->find($id);


        if (!$user instanceof User) {
            // throw $this->createAccessDeniedException();
        }
        $message = new Message();
        $form = $this->createForm(CreateMessageType::class, $message);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
            $message->author = $user ?: $userRepository->find(1);
            $message->figure = $figure;

            $entityManager->persist($message);
            $entityManager->flush();
            $this->addFlash('success', 'Votre figure a été créée');

            return $this->redirectToRoute('app_single_figure', [
                'id' => $id,
            ]);
        }

        return $this->redirectToRoute('app_single_figure', [
            'id' => $id,
        ]);
    }
}
