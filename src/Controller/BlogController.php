<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
        // $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles'=> $articles,
            'title'=>"BIENVENUE"

        ]);
    
    }



    /**
     * @Route("/", name="home")
     */

    public function home(){
        return $this->render('blog/home.html.twig');

    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */

    public function form(Article $article = null, Request $request, EntityManagerInterface $manager){

        if(!$article){

            $article = new Article();
        }
        $form = $this->createForm(ArticleType::class, $article);
                
        $form->handleRequest($request);
    
        if($form->isSubmitted() && $form->isValid()){
            if($article->getId()){
                $article->setCreateAt(new \DateTime());
           }
            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('blog_show', ['id'=>$article->getId()]);

        }
            return $this->render('blog/create.html.twig', [
            'form'=>$form->createView(),
            'editMode'=>$article->getId()!== null
        ]);

            
       
    }
   

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article, Request $request, EntityManagerInterface $manager){

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
       
        if($form->isSubmitted()&& $form->isValid()){
            $comment->setCreateAt(new \DateTime())
                    ->setArticle($article);
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id'=>$article->getId()]);
        }
        return $this->render('blog/show.html.twig', [
            'article'=>$article,
            'commentForm'=>$form->createView()
        ]);

    }

    
    
    // /**
    //  * @Route("/blog/delete", name="blog_delete")
    //  */
    // public function delete(Article $article, EntityManagerInterface $manager){
    //     $manager->remove($article);
    //     $manager->flush();
    //     $this->addFlash(
    //         'success',
    //         "L'article <strong>{$article->getTitle()}</strong> a bien été supprimée !"
    //     );

    //     return $this->redirectToRoute('blog');
    // }
}