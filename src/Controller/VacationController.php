<?php

namespace App\Controller;

use App\Entity\Vacation;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class VacationController extends AbstractController
{

    /**
     * @Route("/users/{userId}/vacations", name="list_vacations", methods={"GET"})
     */
    public function getVacations(UserRepository $userRepo, $userId)
    {
        $user = $userRepo->find($userId);
        return $this->json($user->getVacations(), 200, [], []);
    }


    /**
     * @Route("/users/{userId}/vacations", name="add_vacations", methods={"POST"})
     */
    public function addVacation($userId, Request $request, UserRepository $userRepo, EntityManagerInterface $em)
    {
        $data = $request->getContent();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $vacation = $serializer->deserialize($data, Vacation::class, "json");
        return new Response($vacation->getDateStart());
        $vacation->setStatus("en attente");
        $vacation->setEmployee($userRepo->find($userId));

        $em->persist($vacation);
        $em->flush();

        return $this->json($vacation, 201, [], ['groups' => 'add']);
    }
}
