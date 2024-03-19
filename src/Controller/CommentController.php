<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommentController extends AbstractController
{
    #[Route('/comment/{id_article}', name: 'app_comment', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, int $id_article): Response
    {
        $article = $em->getRepository(Article::class)->find($id_article);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setUser($this->getUser());
            $comment->setDate(new \DateTime);
            $comment->setArticle($article);
            $comment->setIsVerified(false);
            
            $em->persist($comment);
            $em->flush();

            $this->addFlash(
                'success',
                'Your comment has been send'
            );
        }
    
        return $this->render('comment/index.html.twig', [
            'commentForm' => $form,
            'article' => $article,
            
         
        ]);
    }
}
