<?php
/**
 * Created by JetBrains PhpStorm.
 * @author: pomaxa none <pomaxa@gmail.com>
 * @date: 10/23/12
 */
namespace Pmx\Bundle\RrdBundle\Test\Component;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

require_once __DIR__ . '/../../../../../../app/AppKernel.php';

abstract class BaseTest extends WebTestCase
{

    static $kernel;
    static $container;

    protected function setUp()
    {
        parent::setUp();

        self::$kernel = new \AppKernel('test', true);
        self::$kernel->boot();
        self::$container = self::$kernel->getContainer();

    }

    protected function get($id)
    {
        return self::$container->get($id);
    }

    public function tearDown(){
//        self::$container->get('doctrine')->getConnection()->close();
        parent::tearDown();
    }
}
