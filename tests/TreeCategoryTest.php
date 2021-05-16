<?php
/**
 * @author yuriy.yakubskiy@rocketroute.com
 *
 */

namespace App\Tests;

use App\Entity\Category;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TreeCategoryTest extends KernelTestCase
{
    public function purgeData()
    {
        $purger = new ORMPurger($this->em);
        $purger->purge();
    }

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function setUp(): void
    {
        static::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }    
    
    /**
     * @test
     */
    public function retrieve_category_tree()
    {
        /* @var $root Category */
        $root = $this->em->getRepository(Category::class)->findOneBy([
            'parent' => null,
        ]);
        
        $this->printTreeItems($root);
    }

    /**
     * @test
     */
    public function attach_child()
    {
        $newCategory = new Category();
        $newCategory->setTitle("category 5");
        $newCategory->setDescription("This is the fifth category");

        $newCategory1 = new Category();
        $newCategory1->setTitle("category 6");
        $newCategory1->setDescription("This is the sixth category");      
        
        /* @var $category Category */
        $category = $this->em->getRepository(Category::class)->findOneBy([
             'title' => "category 3",   
        ]);
        
        $category->addChild($newCategory);
        $category->addChild($newCategory1);
        
        $this->em->persist($newCategory);
        $this->em->persist($newCategory1);
        $this->em->persist($category);
        
        $this->em->flush();
    }
    
    private function printTreeItems(Category $root)
    {
        if (!$root->getChildren()) {
            return;
        }
        
        foreach ($root->getChildren() as $child) {
            \dump($child->getTitle());
            $this->printTreeItems($child);
        }
        
    }
}