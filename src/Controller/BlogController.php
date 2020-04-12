<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\ArticleFormType;

use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
        //$repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll();
        //findByTitle
        //findAll
        //findOneByTitle

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }
    /**
     * @Route("/", name="home")
     */
    public function home(){  
        return $this->render('blog/home.html.twig',[
            'title' => "Bienvenue",
            'age' =>31
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/edit/{id}",name="blog_edit")
     */

    public function create(Article $article = null,Request $req,EntityManagerInterface $manager){
        
        if(!$article){
            $article = new Article();
        }

        // 1er methode de La creation de form
        /* $form = $this->createFormBuilder($article)
                ->add('title')
                ->add('content')
                ->add('image')
                ->getForm();*/
        
        // 2eme methode de la creation de formulaire
        $form = $this->createForm(ArticleFormType::class , $article);
 
        // Analyser la requete
        $form->handleRequest($req);
        
        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreateAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show',[
                'id' => $article->getId()]
            );
        }
        
        return $this->render('blog/create.html.twig',[
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article,Request $req, EntityManagerInterface $manager){

            $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($req);
        
        if($form->isSubmitted() && $form->isValid()){
            

            if(!$comment->getId()){
                $comment->setCreateAt(new \DateTime())
                        ->setArticle($article);
            }
            
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show',['id'=>
            $article->getId() ]);
        }

        return $this->render('blog/show.html.twig',[
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }

    

}
