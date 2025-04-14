<?php

namespace App\Controller\Administrator;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Enity\AboutUs;
use App\Enity\ContactUs;
use App\Enity\Home;
use App\Enity\OurServices;
class DashboardController extends AbstractController
{
    #[Route('/administrator/dashboard', name: 'app_administrator_dashboard')]
    public function index(): Response
    {
        return $this->render('administrator/dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
