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
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VacationController extends AbstractController
{

    //Liste de tous les congés d'un employé
    /**
     * @Route("/api/users/{userId}/vacations", name="list_vacations", methods={"GET"})
     */
    public function read(UserRepository $userRepo, $userId)
    {
        $user = $userRepo->find($userId);
        $data = $user->getVacations();

        return $this->json($data, 200, [], ['groups' => 'add_vac']);
    }



    //Ajout d'un congés par un employé
    /**
     * @Route("/api/users/{userId}/vacations", name="add_vacations", methods={"POST"})
     */
    public function create($userId, Request $request, UserRepository $userRepo, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        //recupère les données de la requète
        $data = $request->getContent();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        try {
            //transformer les donnees en objet Vacation
            $vacation = $serializer->deserialize($data, Vacation::class, "json");
            $vacation->setStatus("en attente");
            $vacation->setEmployee($userRepo->find($userId));

            //verifier si les donnees de l'objet valident les contraintes de l'entité User
            $errors = $validator->validate($vacation);
            if(count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $em->persist($vacation);
            $em->flush();

            return $this->json($vacation, 201, [], ['groups' => 'add_vac']);
        } catch(NotEncodableValueException $e) {
            return $this->json(["message" => $e->getMessage()], 400);
        }
    }



    //Annulation d'un congés par un employé
    /**
     * @Route("/api/users/{userId}/vacations/{vacationId}", name="cancel_vacations", methods={"DELETE"})
     */
    public function delete($userId, $vacationId, VacationRepository $vrepo, EntityManagerInterface $em)
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
    public function edit($userId, $vacationId, VacationRepository $vrepo, Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent());

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
    public function list(UserRepository $urepo)
    {
        $vacations = $urepo->findAll();

        return $this->json($vacations, 200, [], ['groups' => 'vac_list']);
    }

}
