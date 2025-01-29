<?php

namespace App\Controller;

use App\Repository\AboutUsRepository;
use App\Entity\Quote;
use App\Form\QuoteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuoteController extends AbstractController
{
    #[Route('/quote', name: 'app_quote')]
    public function index(Request $request, EntityManagerInterface $entityManager , AboutUsRepository $aboutUs ): Response
    {
        $quote = new Quote();
        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);
        $aboutUs = $aboutUs->find(1);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quote);
            $entityManager->flush();
            
            $this->addFlash('success', 'Your quote request has been submitted successfully!');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('quote/quote.html.twig', [
            'form' => $form->createView(),
            'aboutUs' => $aboutUs,
        ]);
    }
}

