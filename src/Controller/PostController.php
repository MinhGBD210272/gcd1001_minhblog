<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController
{
    /**
     * @Route("/", name="app_post")
     */
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $posts = $postRepository->findAll();

        $posts = $paginator->paginate(
            $postRepository->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }
    /**
     * @Route ("/post/{id}", name="post_show")
     * @return Response
     */
    public function show(Request $request, PostRepository $postRepository)
    {
        $postId = $request->attributes->get('id');
        $post = $postRepository->find($postId);
        return $this->render('post/show.html.twig', [
            'post'=>$post
        ]);
    }
}
