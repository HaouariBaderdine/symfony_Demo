<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        //Créer 3 catégorie fakées
        for($i=1; $i<=3 ;$i++){
            $category = new Category();
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());

            $manager->persist($category);          
        }

        //creer entre 4 et 6 articles
        for($j = 1;$j<= mt_rand(3,5) ; $j++){

            $article = new Article();
            $content = '<p>'.join($faker->paragraphs(2),'</p><p>'.'</p>');

            $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreateAt($faker->dateTimeBetween('-6 month'))
                    ->setCategory($category);
                    
            $manager->persist($article);    
            
            // on donne des commentaires à l'article
            for($k = 1;$k<= mt_rand(2,3) ; $k++){
                $comment = new Comment();
                $content .= '<p>'.join($faker->paragraphs(2),'</p><p>'.'</p>');
                
                //creation de date inferirur de date de creation d'un article
                $now = new \DateTime();
                $interval = $now->diff($article->getCreateAt());
                $days = $interval->days;
                $minimum ='-'.$days.'days'; //-100 days

                $comment->setAuthor($faker->name)
                        ->setContent($content)
                        ->setCreateAt($faker->dateTimeBetween($minimum))
                        ->setArticle($article);
                
                $manager->persist($comment);
            }    
        }

        $manager->flush();
    }
}
