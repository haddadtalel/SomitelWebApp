<?php

namespace App\Controller\Administrator;

use App\Entity\Ticket;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administrator/tickets')]
#[IsGranted('ROLE_ADMIN')]
class MessageApiController extends AbstractController
{
    #[Route('/{id}/messages', name: 'app_administrator_ticket_messages', methods: ['GET'])]
    public function getMessages(Request $request, Ticket $ticket, MessageRepository $messageRepository): JsonResponse
    {
        $sinceId = $request->query->getInt('since', 0);
        $messages = [];

        if ($sinceId > 0) {
            // Get messages newer than the provided ID
            $qb = $messageRepository->createQueryBuilder('m')
                ->andWhere('m.ticket = :ticket')
                ->andWhere('m.id > :sinceId')
                ->setParameter('ticket', $ticket)
                ->setParameter('sinceId', $sinceId)
                ->orderBy('m.createdAt', 'ASC');

            $newMessages = $qb->getQuery()->getResult();

            foreach ($newMessages as $message) {
                $messages[] = [
                    'id' => $message->getId(),
                    'content' => $message->getContent(),
                    'createdAt' => $message->getCreatedAt()->format('M d, Y h:i a'),
                    'isFromAdmin' => $message->isFromAdmin(),
                    'senderName' => $message->getSender()->getFullName(),
                    'hasAttachment' => $message->hasAttachment(),
                    'attachment' => $message->getAttachment(),
                    'attachmentType' => $message->getAttachmentType(),
                ];
            }
        }

        return $this->json([
            'messages' => $messages
        ]);
    }
}
