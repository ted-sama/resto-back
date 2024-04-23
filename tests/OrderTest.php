<?php

namespace App\Tests;

use App\Entity\Order;
use App\Entity\OrderDetail;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testOrderEntity()
    {
        $order = new Order();

        // Test setTotalItems and getTotalItems
        $order->setTotalItems(3);
        $this->assertEquals(3, $order->getTotalItems());

        // Test setTotalPrice and getTotalPrice
        $order->setTotalPrice(59.97);
        $this->assertEquals(59.97, $order->getTotalPrice());

        // Test addOrderDetail and getOrderDetails
        $orderDetail = new OrderDetail();
        $order->addOrderDetail($orderDetail);
        $this->assertInstanceOf(ArrayCollection::class, $order->getOrderDetails());
        $this->assertTrue($order->getOrderDetails()->contains($orderDetail));
    }
}
