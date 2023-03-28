<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// User Registration and Login 
// 
#[Route('/api', name: 'app_')]
class UserController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function index(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $entityManager = $doctrine->getManager();
        $errors = [];
        $user = new User();
        $userExists = $doctrine->getRepository(User::class)->findOneBy(['email' => $request->get('email')]);
        if ($userExists) {
            array_push($errors, 'User already exists');
            return $this->json($errors, 400);
        } else {

            $username = $request->request->get('username');
            $email = $request->request->get('email');
            $plaintextPassword = $request->request->get('password');

            if (empty($email)) {
                array_push($errors, 'Email is required');
            }
            if (empty($username)) {
                array_push($errors, 'Username is required');
            }
            if (empty($plaintextPassword)) {
                array_push($errors, 'Password is required');
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($hashedPassword);
        }

        $userToken = "Bearer " . bin2hex(random_bytes(32));

        // display all errors to string
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        // Return a json object with userID and token
        return $this->json([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'token' => $userToken
        ]);
    }

    #[Route('/login', name: 'app_user_login', methods: ['POST'])]
    public function login(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $errors = [];
        $email = $request->get('email');
        $plainPassword = $request->get('password');

        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $email]);

        if (empty($email)) {
            array_push($errors, 'Email is required');
        }

        if (empty($plainPassword)) {
            array_push($errors, 'Password is required');
        }
        if (!$user) {
            array_push($errors, 'User does not exist');
        }

        if (!$email) {
            array_push($errors, 'Email is required');
        }

        if (!$passwordEncoder->isPasswordValid($user, $plainPassword)) {
            array_push($errors, 'Invalid password');
        }

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        } else {
            $userToken = "Bearer " . bin2hex(random_bytes(32));

            return $this->json([
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'token' => $userToken
            ]);
        }
    }

    // List all users
    #[Route('/users', name: 'app_user_list', methods: ['GET'])]
    public function list(ManagerRegistry $doctrine): Response
    {
        $users = $doctrine
            ->getRepository(User::class)
            ->findAll();

        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
            ];
        }
        return $this->json($data);
    }
}
