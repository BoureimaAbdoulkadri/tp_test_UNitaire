<?php

namespace App\Controller;
use App\Entity\word;
use App\Repository\WordRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WordController extends AbstractController
{   private $entityManager,$wordsRepository;
    public function __construct(ManagerRegistry $doctrine,WordRepository $wordsRepository)
    { $this ->wordsRepository =$wordsRepository;
        $this ->entityManager=$doctrine->getManager();
    }

    #[Route('/word', name: 'app_word')]
    public function index():Response
    {
        $countWords =$this->wordsRepository->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            -getSingleScalarResult();

        return new JsonResponse(array('count' => $countWords));
    }
}
