<?php

namespace App\Controller\Administrator;

use App\Entity\Message;
use App\Entity\Ticket;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/administrator/tickets')]
#[IsGranted('ROLE_ADMIN')]
class TicketController extends AbstractController
{
    #[Route('', name: 'app_administrator_tickets')]
    public function index(TicketRepository $ticketRepository): Response
    {
        return $this->render('administrator/tickets/index.html.twig', [
            'tickets' => $ticketRepository->findBy([], ['createdAt' => 'DESC']),
            'openTicketsCount' => $ticketRepository->countByStatus(Ticket::STATUS_OPEN),
            'inProgressTicketsCount' => $ticketRepository->countByStatus(Ticket::STATUS_IN_PROGRESS),
            'resolvedTicketsCount' => $ticketRepository->countByStatus(Ticket::STATUS_RESOLVED),
            'closedTicketsCount' => $ticketRepository->countByStatus(Ticket::STATUS_CLOSED),
        ]);
    }

    #[Route('/{id}', name: 'app_administrator_ticket_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request, 
        Ticket $ticket, 
        EntityManagerInterface $entityManager,
        MessageRepository $messageRepository,
        SluggerInterface $slugger
    ): Response {
        // Mark all messages as read
        $messageRepository->markAllAsReadInTicketForAdmin($ticket);

        // Create message form
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setSender($this->getUser());
            $message->setRecipient($ticket->getUser());
            $message->setTicket($ticket);
            
            // Handle file upload
            $attachmentFile = $form->get('attachment')->getData();
            if ($attachmentFile) {
                $originalFilename = pathinfo($attachmentFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$attachmentFile->guessExtension();

                try {
                    $attachmentFile->move(
                        $this->getParameter('attachments_directory'),
                        $newFilename
                    );
                    $message->setAttachment($newFilename);
                    $message->setAttachmentType($attachmentFile->getMimeType());
                } catch (FileException $e) {
                    $this->addFlash('error', 'There was an error uploading your file.');
                }
            }

            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Your response has been sent.');
            return $this->redirectToRoute('app_administrator_ticket_show', ['id' => $ticket->getId()]);
        }

        return $this->render('administrator/tickets/show.html.twig', [
            'ticket' => $ticket,
            'messages' => $messageRepository->findByTicket($ticket),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/status', name: 'app_administrator_ticket_status', methods: ['POST'])]
    public function updateStatus(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        $status = $request->request->get('status');
        
        if (in_array($status, [Ticket::STATUS_OPEN, Ticket::STATUS_IN_PROGRESS, Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED])) {
            $ticket->setStatus($status);
            
            if ($status === Ticket::STATUS_RESOLVED) {
                $ticket->setResolvedAt(new \DateTime());
            } elseif ($status === Ticket::STATUS_CLOSED) {
                $ticket->setClosedAt(new \DateTime());
            }
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Ticket status has been updated.');
        } else {
            $this->addFlash('error', 'Invalid status.');
        }
        
        return $this->redirectToRoute('app_administrator_ticket_show', ['id' => $ticket->getId()]);
    }

    #[Route('/messages/unread-count', name: 'app_administrator_messages_unread_count', methods: ['GET'])]
    public function unreadMessagesCount(MessageRepository $messageRepository): JsonResponse
    {
        $count = $messageRepository->countUnreadForAdmin();
        
        return $this->json([
            'count' => $count
        ]);
    }
}
