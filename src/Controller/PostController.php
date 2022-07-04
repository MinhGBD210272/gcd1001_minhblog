<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
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
     * @Route("/post/new", name="post_new")
     */
    public function create(Request $request){
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $post->setCreatedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash(
                'Success',
                'Your post was added'
            );

            return $this->redirectToRoute('app_post');
        }
        return $this->render('post/new.html.twig',[
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route ("/post/{id}", name="post_show")
     */
    public function show(Request $request, PostRepository $postRepository)
    {
        $postId = $request->attributes->get('id');
        $post = $postRepository->find($postId);
        return $this->render('post/show.html.twig', [
            'post'=>$post
        ]);
    }
    /**
     * @Route ("/post/{id}/edit", name="post_edit")
     */
    public function edit(Post $post, Request $request){
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash(
                'Success',
                'Your post was edited'
            );
            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }
        return $this->render('/post/edit.html.twig', [
            'post' => $post,
            'editForm' => $form->createView()
        ]);
    }
}
