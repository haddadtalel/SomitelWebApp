<?php

namespace App\Controller;

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
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager , AboutUsRepository $aboutUs , ServiceRepository $serviceRepository ,AchievementsRepository $achivments ,QuoteDescRepository $quoteDesc): Response
    {
        $quote = new Quote();
        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quote);
            $entityManager->flush();
            
            $this->addFlash('success', 'Your quote request has been submitted successfully!');
        }

        $services = $serviceRepository->findAll();
        $aboutUs = $aboutUs->find(1);
        $achivments = $achivments->findAll();
        $quoteDesc = $quoteDesc->find(1);
        return $this->render('home/home.html.twig', [
            'quoteDesc'=>$quoteDesc,
            'aboutUs' => $aboutUs,
            'services' => $services,
            'achivments' => $achivments,
            'form' => $form->createView(),
        ]);
    }
}
