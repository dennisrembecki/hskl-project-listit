<?php

namespace App\Controller;

use App\Entity\Liste;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="front")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $lists = $doctrine->getRepository(Liste::class)->findBy(array(), array('id' => 'DESC'));

        /*
        foreach($lists as $list) {
            if ($list->getName() == "asffasfas") {
                $doctrine->getManager()->remove($list);
                $doctrine->getManager()->flush();
            }
        }
        */

        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
            'lists' => $lists
        ]);
    }

    /**
     * @Route("/search/{keyword}", name="search")
     */
    public function search($keyword, ManagerRegistry $doctrine): Response
    {
        $lists = $doctrine->getRepository(Liste::class)->search($keyword);

        return $this->render('front/search.html.twig', [
            'controller_name' => 'ListeController',
            'lists' => $lists,
            'keyword' => $keyword
        ]);
    }
}
