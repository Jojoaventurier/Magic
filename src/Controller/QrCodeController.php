<?php

namespace App\Controller;

use Endroid\QrCode\Builder\BuilderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QrCodeController extends AbstractController
{
    private BuilderInterface $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    #[Route('/qr-code/{slug}', name: 'app_qr_code')]
    public function generateQrCode(string $slug): Response
    {
        $url = $this->generateUrl('app_specific_page', ['slug' => $slug], true);

        $result = $this->builder
            ->data($url)
            ->size(300)
            ->margin(10)
            ->labelText('Scannez pour voir le deck !')
            ->build();

        // Use QrCodeResponse to automatically handle headers and return the QR code
        return new QrCodeResponse($result);
    }


    #[Route('/page/{slug}', name: 'app_specific_page')]
    public function specificPage(string $slug): Response
    {
        // Retrieve data related to the slug and return a page
        return $this->render('page/show.html.twig', [
            'slug' => $slug,
        ]);
    }
}