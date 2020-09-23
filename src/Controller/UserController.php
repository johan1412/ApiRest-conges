<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
    public function read(UserRepository $urepo)
    {
        $users = $urepo->findAll();

        return $this->json($users, 200, [], ['groups' => 'user_list']);
    }



    //Obtenir les informations d'un utilisateur
    /**
     * @Route("/api/users/{userId}", name="get_user", methods={"GET"})
     */
    public function read_user($userId, UserRepository $urepo)
    {
        $user = $urepo->find($userId);

        if(!$user) {
            return $this->json("L'id ne correspond à aucun utilisateur", 400);
        }

        return $this->json($user, 200, [], ['groups' => 'user_list']);
    }



    //Ajout d'un utilisateur
    /**
     * @Route("/api/register", name="add_user", methods="POST")
     */
    public function create(Request $request, ValidatorInterface $validator, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {   
        $data = $request->getContent();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        try {
            $user = $serializer->deserialize($data, User::class, "json");
            $user->setRoles(["employee"]);

            //verifier si les donnees de l'objet valident les contraintes de l'entité User
            $errors = $validator->validate($user);
            if(count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));

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
    public function delete($userId, UserRepository $urepo, EntityManagerInterface $em)
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
    public function edit($userId, UserRepository $urepo, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $data = json_decode($request->getContent());
        $code = 200;

        $user = $urepo->find($userId);

        //si $user n'existe pas alors on le créer (car méthode PUT)
        if(!$user) {
            $user = new User();
            //on change le code en 201 pour envoyer dans la réponse qu'il y a eu une création
            $code = 201;
        }

        //on récupère toutes les données de la requete et on les mets dans le user récupéré ou créé
        $user->email = $data->email;
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->roles = "employee";
        $user->password = $passwordEncoder->encodePassword($user, $data->password);
        

        $em->persist($user);
        $em->flush();

        return new JsonResponse("modifie", $code);
    }

}
