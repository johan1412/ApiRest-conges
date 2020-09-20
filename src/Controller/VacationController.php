<?php

namespace App\Controller;

use App\Entity\Vacation;
use App\Repository\UserRepository;
use App\Repository\VacationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class VacationController extends AbstractController
{

    //Liste de tous les congés d'un employé
    /**
     * @Route("/api/users/{userId}/vacations", name="list_vacations", methods={"GET"})
     */
    public function getVacations(UserRepository $userRepo, $userId)
    {
        $user = $userRepo->find($userId);
        $data = $user->getVacations();

        $encoders = [new JsonEncoder()];
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, $defaultContext)];
        $serializer = new Serializer($normalizers, $encoders);

        $vacations = $serializer->serialize($data, 'json', []);

        return new Response($vacations, 200);
    }



    //Ajout d'un congés par un employé
    /**
     * @Route("/api/users/{userId}/vacations", name="add_vacations", methods={"POST"})
     */
    public function addVacation($userId, Request $request, UserRepository $userRepo, EntityManagerInterface $em)
    {
        $data = $request->getContent();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $vacation = $serializer->deserialize($data, Vacation::class, "json");
        $vacation->setStatus("en attente");
        $vacation->setEmployee($userRepo->find($userId));

        $em->persist($vacation);
        $em->flush();

        return $this->json($vacation, 201, [], ['groups' => 'add']);
    }



    //Annulation d'un congés par un employé
    /**
     * @Route("/api/users/{userId}/vacations/{vacationId}", name="cancel_vacations", methods={"DELETE"})
     */
    public function cancelVacation($userId, $vacationId, VacationRepository $vrepo, EntityManagerInterface $em)
    {
        $vacation = $vrepo->find($vacationId);
        if(!$vacation) {
            return new JsonResponse("Cet ID ne correspond à aucun congés", 404);
        }
        $em->remove($vacation);
        $em->flush();

        return new JsonResponse("supprime", 200);
    }



    //Validation ou refus d'un congé par le RH
    /**
     * @Route("/api/users/{userId}/vacations/{vacationId}", name="validated_vacation", methods={"PATCH"})
     */
    public function validatedVacation($userId, $vacationId, VacationRepository $vrepo, Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent());
        $code = 200;

        $vacation = $vrepo->find($vacationId);

        if(!$vacation) {
            return new JsonResponse("Cet ID ne correspond à aucun congés", 404);
        }

        $status = $data->status;

        if($status !== "valide" && $status !== "refus") {
            return new JsonResponse("statut invalide", 400);
        }

        $vacation->setStatus($status);

        $em->persist($vacation);
        $em->flush();

        return new JsonResponse("modifie", 200);
    }



    //Liste des congés pour chaque employés
    /**
     * @Route("/api/vacations", name="all_vacations", methods={"GET"})
     */
    public function AllVacations(UserRepository $urepo)
    {
        $vacations = $urepo->findAll();

        return $this->json($vacations, 200, [], ['groups' => 'user_list']);
    }

}
