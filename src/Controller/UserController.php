<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    //Liste des utilisateurs
    /**
     * @Route("/api/users", name="list_users", methods={"GET"})
     */
    public function liste()
    {
        
    }



    //Ajout d'un utilisateur
    /**
     * @Route("/api/users", name="add_user", methods="POST")
     */
    public function addUser(Request $request, ValidatorInterface $validator, EntityManagerInterface $em)
    {   
        $data = $request->getContent();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        try {
            $user = $serializer->deserialize($data, User::class, "json");
            $user->setRoles(["employee"]);

            $errors = $validator->validate($user);
            if(count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $em->persist($user);
            $em->flush();

            return $this->json($user, 201, [], ['groups' => 'user_add']);
        } catch(NotEncodableValueException $e) {
            return $this->json(["message" => $e->getMessage()], 400);
        }
    }



    //Supprimer un utilisateur
    /**
     * @Route("/api/users/{userId}", name="delete_user", methods="DELETE")
     */
    public function deleteUser($userId, UserRepository $urepo, EntityManagerInterface $em)
    {   
        $user = $urepo->find($userId);
        if(!$user) {
            return new JsonResponse("Cet ID ne correspond à aucun utilisateur", 404);
        }
        $em->remove($user);
        $em->flush();

        return new JsonResponse("supprime", 200);
    }



    //Mise a jour des infos d'un utilisateur
    /**
     * @Route("/api/users/{userId}", name="validated_vacation", methods={"PUT"})
     */
    public function update($userId, UserRepository $urepo, Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent());
        $code = 200;

        $user = $urepo->find($userId);

        if(!$user) {
            $user = new User();
            $code = 201;
        }

        $user->email = $data->email;
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->roles = "employee";
        $user->password = $data->password;
        

        $em->persist($user);
        $em->flush();

        return new JsonResponse("modifie", $code);
    }

}
