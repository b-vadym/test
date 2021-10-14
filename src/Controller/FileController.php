<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends AbstractController
{
    #[Route(path: '/file-optimize', name: 'app_optimize_image_test')]
    public function testOptimize(FileRepository $fileRepository): Response
    {
        return $this->render('file/test_optimize.html.twig', [
            'file' => $fileRepository->getByUuid('bd4cfb5b-978c-46d9-9af1-0387ba99cc54'),
        ]);
    }
}
