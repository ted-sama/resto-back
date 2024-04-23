<?php

namespace App\Tests;

use App\Entity\Food;
use PHPUnit\Framework\TestCase;

class FoodTest extends TestCase
{
    public function testFoodEntity()
    {
        $food = new Food();

        // Test setName and getName
        $food->setName('Pizza');
        $this->assertEquals('Pizza', $food->getName());

        // Test setDescription and getDescription
        $food->setDescription('Délicieuse Pizza Italienne');
        $this->assertEquals('Délicieuse Pizza Italienne', $food->getDescription());

        // Test setPrice and getPrice
        $food->setPrice(10.99);
        $this->assertEquals(10.99, $food->getPrice());

        // Test setImage and getImage
        $food->setImage('pizza.jpg');
        $this->assertEquals('pizza.jpg', $food->getImage());
    }
}
