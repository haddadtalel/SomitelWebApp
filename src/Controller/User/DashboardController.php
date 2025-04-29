<?php

namespace App\Controller\User;

use App\Entity\Message;
use App\Entity\Ticket;
use App\Form\MessageType;
use App\Form\TicketType;
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

#[Route('/dashboard')]
#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'app_user_dashboard')]
    public function index(TicketRepository $ticketRepository): Response
    {
        $user = $this->getUser();
        $tickets = $ticketRepository->findByUser($user, [], ['createdAt' => 'DESC']);

        return $this->render('user/dashboard/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/tickets', name: 'app_user_tickets')]
    public function tickets(TicketRepository $ticketRepository): Response
    {
        $user = $this->getUser();
        $tickets = $ticketRepository->findByUser($user, [], ['createdAt' => 'DESC']);

        return $this->render('user/dashboard/tickets.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/tickets/new', name: 'app_user_ticket_new', methods: ['GET', 'POST'])]
    public function newTicket(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setUser($this->getUser());
            $ticket->setStatus(Ticket::STATUS_OPEN);
            
            $entityManager->persist($ticket);
            $entityManager->flush();

            $this->addFlash('success', 'Your ticket has been created successfully.');
            return $this->redirectToRoute('app_user_ticket_show', ['id' => $ticket->getId()]);
        }

        return $this->render('user/dashboard/ticket_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tickets/{id}', name: 'app_user_ticket_show', methods: ['GET', 'POST'])]
    public function showTicket(
        Request $request, 
        Ticket $ticket, 
        EntityManagerInterface $entityManager,
        MessageRepository $messageRepository,
        SluggerInterface $slugger
    ): Response {
        // Security check - only allow users to view their own tickets
        if ($ticket->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot access this ticket.');
        }

        // Mark all messages as read
        $messageRepository->markAllAsReadInTicket($ticket, $this->getUser());

        // Create message form
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setSender($this->getUser());
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
            
            // Update ticket status to in progress if it was open
            if ($ticket->getStatus() === Ticket::STATUS_OPEN) {
                $ticket->setStatus(Ticket::STATUS_IN_PROGRESS);
                $entityManager->persist($ticket);
            }
            
            $entityManager->flush();

            $this->addFlash('success', 'Your message has been sent.');
            return $this->redirectToRoute('app_user_ticket_show', ['id' => $ticket->getId()]);
        }

        return $this->render('user/dashboard/ticket_show.html.twig', [
            'ticket' => $ticket,
            'messages' => $messageRepository->findByTicket($ticket),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tickets/{id}/close', name: 'app_user_ticket_close', methods: ['POST'])]
    public function closeTicket(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        // Security check - only allow users to close their own tickets
        if ($ticket->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot access this ticket.');
        }

        if ($this->isCsrfTokenValid('close'.$ticket->getId(), $request->request->get('_token'))) {
            $ticket->setStatus(Ticket::STATUS_CLOSED);
            $ticket->setClosedAt(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'Ticket has been closed.');
        }

        return $this->redirectToRoute('app_user_tickets');
    }

    #[Route('/profile', name: 'app_user_profile')]
    public function profile(): Response
    {
        return $this->render('user/dashboard/profile.html.twig');
    }

    #[Route('/messages/unread-count', name: 'app_user_messages_unread_count', methods: ['GET'])]
    public function unreadMessagesCount(MessageRepository $messageRepository): JsonResponse
    {
        $count = $messageRepository->countUnreadByUser($this->getUser());
        
        return $this->json([
            'count' => $count
        ]);
    }
}
