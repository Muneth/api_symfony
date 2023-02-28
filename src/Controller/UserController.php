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

        $user = new User();
        $userExists = $doctrine->getRepository(User::class)->findOneBy(['email' => $request->get('email')]);
        if ($userExists) {
            return $this->json('User already exists', 404);
        } else {
            $email = $request->get('email');
            $username = $request->get('username');
            $plaintextPassword = $request->get('password');
            $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($hashedPassword);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json('Registered successfully with id ' . $user->getId());
    }

    #[Route('/login', name: 'app_user_login', methods: ['POST'])]
    public function login(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $email = $request->get('email');
        $plainPassword = $request->get('password');

        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->json('No user found for email ' . $email, 404);
        }

        if (!$passwordEncoder->isPasswordValid($user, $plainPassword)) {
            return $this->json('Invalid password', 404);
        }

        return $this->json('Logged in successfully');
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
