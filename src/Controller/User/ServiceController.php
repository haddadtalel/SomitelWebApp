<?php

namespace App\Controller\User;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use App\Repository\AboutUsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/service')]
final class ServiceController extends AbstractController
{
    #[Route(name: 'app_service_index', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository ,AboutUsRepository $aboutUsRepository): Response
    {
        return $this->render('service/base.html.twig', [
            'services' => $serviceRepository->findAll(),
            'aboutUs' => $aboutUsRepository->find(1),
        ]);
    }

    #[Route('/{id}', name: 'app_service_detail', methods: ['GET'])]
    public function getServiceDetail(Service $service)
    {
        return new JsonResponse([
            'id' => $service->getId(),
            'title' => $service->getTitle(),
            'description' => $service->getDescription(),
        ]);
    }
 
    #[Route('/{id}', name: 'app_service_show', methods: ['GET'])]
    public function show(Service $service): Response
    {
        return $this->render('service/show.html.twig', [
            'service' => $service,
        ]);
    }

}
