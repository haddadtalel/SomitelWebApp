<?php

namespace App\Controller\Administrator;

use App\Entity\AboutUs;
use App\Form\AboutUsType;
use App\Repository\AboutUsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/administrator/about/us')]
final class AboutUsController extends AbstractController
{
    #[Route(name: 'app_administrator_about_us_index', methods: ['GET'])]
    public function index(AboutUsRepository $aboutUsRepository): Response
    {
        return $this->render('administrator/about_us/index.html.twig', [
            'aboutuses' => $aboutUsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_administrator_about_us_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $aboutUs = new AboutUs();
        $form = $this->createForm(AboutUsType::class, $aboutUs);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($aboutUs);
            $entityManager->flush();

            return $this->redirectToRoute('app_administrator_about_us_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('administrator/about_us/new.html.twig', [
            'about_us' => $aboutUs,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_administrator_about_us_show', methods: ['GET'])]
    public function show(AboutUs $aboutUs): Response
    {
        return $this->render('administrator/about_us/show.html.twig', [
            'about_us' => $aboutUs,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_administrator_about_us_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AboutUs $aboutUs, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AboutUsType::class, $aboutUs);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_administrator_about_us_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('administrator/about_us/edit.html.twig', [
            'about_us' => $aboutUs,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_administrator_about_us_delete', methods: ['POST'])]
    public function delete(Request $request, AboutUs $aboutUs, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$aboutUs->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($aboutUs);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_administrator_about_us_index', [], Response::HTTP_SEE_OTHER);
    }
}
