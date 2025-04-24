<?php

namespace App\Controller\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Quote;
use App\Form\QuoteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AboutUsRepository;
use App\Repository\ServiceRepository;
use App\Repository\AchievementsRepository;
use App\Repository\QuoteDescRepository;
use App\Repository\ProductRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request, 
        EntityManagerInterface $entityManager,
        AboutUsRepository $aboutUs,
        ServiceRepository $serviceRepository,
        AchievementsRepository $achivments,
        QuoteDescRepository $quoteDesc,
        ProductRepository $productRepository
    ): Response {
        $quote = new Quote();
        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quote);
            $entityManager->flush();
            
            $this->addFlash('success', 'Your quote request has been submitted successfully!');
        }

        return $this->render('home/home.html.twig', [
            'quoteDesc' => $quoteDesc->find(1),
            'aboutUs' => $aboutUs->find(1),
            'services' => $serviceRepository->findAll(),
            'achivments' => $achivments->findAll(),
            'products' => $productRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }
}