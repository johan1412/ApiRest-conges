
Readme
======

## **Informations**

Bundle d'authentification par tokens installé (JWT), qui normalement vérifie les identifiants envoyés par l'utilisateur par une requete POST sur /api/login_check.  
Si ils sont corrects alors un token d'identification est envoyé au client qui a fait la requete.

Ce token sera utilisé par le client pour effectué d'autres appels par requetes sur des fonctionnalités réservées aux personnes authentifiées.

**Cependant une erreur intervient car le $this->getUser() de la fonction login() du SecurityController renvoie toujours null.**  
J'ai donc mit tout le système d'authentification en commentaire pour pouvoir effectuer les autres requetes sans token.

## **Liste des API routes**

* Pour creer un utilisateur

    En méthode **POST**

    Dans le body: {email, firstname, lastname, password}

        /api/register

* Pour se connecter

    En méthode **POST**

    Dans le body: {email, password}

        /api/login_check

* Liste des utilisateurs

    En méthode **GET**

        /api/users

* Obtenir un utilisateur

    En méthode **GET**

        /api/users/{userId}

* Supprimer un utilisateur

    En méthode **DELETE**

        /api/users/{userId}

* Mise à jour des informations d'un utilisateur

    En méthode **PUT**

    Dans le body: {email, firstname, lastname, password}

        /api/users/{userId}

* Obtenir les congès d'un utilisateur

    En méthode **GET**

        /api/users/{userId}/vacations

* Ajouter un congés à un utilisateur

    En méthode **POST**

    Dans le body: {dateStart, dateEnd}

        /api/users/{userId}/vacations

* Annuler un congés d'un utilisateur

    En méthode **DELETE**

        /api/users/{userId}/vacations/{vacationId}

* Validation ou refus d'un congés d'un utilisateur

    En méthode **PATCH**

    Dans le body: {status}

    status parmi ["refus", "valide"]

        /api/users/{userId}/vacations/{vacationId}

* Liste des congés de tous les utilisateurs

    En méthode **GET**

        /api/vacations
