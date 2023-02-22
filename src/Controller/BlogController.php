<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\BlogPost;

#[Route('/api', name: 'app_')]

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog_index', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): Response
    {
        $blogPosts = $doctrine
            ->getRepository(BlogPost::class)
            ->findAll();

        $data = [];

        foreach ($blogPosts as $blogPost) {
            $data[] = [
                'id' => $blogPost->getId(),
                'title' => $blogPost->getTitle(),
                'published' => $blogPost->getPublished(),
                'content' => $blogPost->getContent(),
                'author' => $blogPost->getAuthor(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/blog/{id}', name: 'blog_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $blogPost = $doctrine->getRepository(BlogPost::class)->find($id);

        if (!$blogPost) {
            return $this->json('No blog post found for id' . $id, 404);
        }

        $data = [
            'id' => $blogPost->getId(),
            'title' => $blogPost->getTitle(),
            'published' => $blogPost->getPublished(),
            'content' => $blogPost->getContent(),
            'author' => $blogPost->getAuthor(),
        ];

        return $this->json($data);
    }

    #[Route('/blog', name: 'blog_create', methods: ['POST'])]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $blogPost = new BlogPost();
        $blogPost->setTitle($request->request->get('title'));
        $blogPost->setPublished(new \DateTime($request->request->get('published')));
        $blogPost->setContent($request->request->get('content'));
        $blogPost->setAuthor($request->request->get('author'));

        $entityManager->persist($blogPost);
        $entityManager->flush();

        return $this->json('Created new blog post successfully with id ' . $blogPost->getId());
    }

    #[Route('/blog/{id}', name: 'blog_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $blogPost = $entityManager->getRepository(BlogPost::class)->find($id);

        if (!$blogPost) {
            return $this->json('No blog post found for id' . $id, 404);
        }

        $blogPost->setTitle($request->request->get('title'));
        $blogPost->setPublished($request->request->get('published'));
        $blogPost->setContent($request->request->get('content'));
        $blogPost->setAuthor($request->request->get('author'));

        $entityManager->flush();

        return $this->json('Updated blog post successfully with id ' . $blogPost->getId());
    }

    #[Route('/blog/{id}', name: 'blog_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $blogPost = $entityManager->getRepository(BlogPost::class)->find($id);

        if (!$blogPost) {
            return $this->json('No blog post found for id  ' . $id, 404);
        }

        $entityManager->remove($blogPost);
        $entityManager->flush();

        return $this->json('Deleted blog post successfully');
    }
}