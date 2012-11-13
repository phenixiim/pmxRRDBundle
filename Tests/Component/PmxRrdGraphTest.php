<?php
/**
 * Created by JetBrains PhpStorm.
 * @author: pomaxa none <pomaxa@gmail.com>
 * @date: 10/23/12
 */

namespace Pmx\Bundle\RrdBundle\Test\Component;

include __DIR__.'/BaseTest.php';

use Pmx\Bundle\RrdBundle\Component\DSType;
use Pmx\Bundle\RrdBundle\Component\RRAConsolidationFunction;

class PmxRrdGraphTest extends BaseTest
{

    public $dbFileName = "/tmp/myrouter33.rrd";

    public function testDatabase()
    {
        /** @var $pmxRrd \Pmx\Bundle\RrdBundle\Component\PmxRrdDatabase */
        $pmxRrd = $this->get('pmx_rrd.db');

        //create DB;
        $pmxRrd->setDbName('router.rrd')
            ->setStart(mktime(0,0,0,1,11,2012))
            ->addDataSource('input', DSType::COUNTER, 600)
            ->addDataSource('output', DSType::COUNTER, 600)

            ->addRoundRobinArchive(RRAConsolidationFunction::AVERAGE, 0.5, 1, 600)
            ->addRoundRobinArchive(RRAConsolidationFunction::AVERAGE, 0.5, 6, 700)
            ->addRoundRobinArchive(RRAConsolidationFunction::AVERAGE, 0.5, 24, 775)
            ->addRoundRobinArchive(RRAConsolidationFunction::AVERAGE, 0.5, 288, 797)

            ->addRoundRobinArchive(RRAConsolidationFunction::MAX, 0.5, 1, 600)
            ->addRoundRobinArchive(RRAConsolidationFunction::MAX, 0.5, 6, 700)
            ->addRoundRobinArchive(RRAConsolidationFunction::MAX, 0.5, 24, 775)
            ->addRoundRobinArchive(RRAConsolidationFunction::MAX, 0.5, 288, 797)
        ->create();

        //fill with data
        $minute = 0;
        while(mktime(0,0,0,14,11,2012) > mktime(0,$minute,0,1,11,2012))
        {
            $pmxRrd->update('input', rand(1,255), mktime(0,$minute,0,1,11,2012));
            $minute +=5;
        }
        //tune

        //create image
        /** @var $pmxRrdGraph \Pmx\Bundle\RrdBundle\Component\PmxRrdGraph */
        $pmxRrdGraph = $this->get('pmx_rrd.graph');
        $pmxRrdGraph->setStart(mktime(0,0,0,14,11,2012))
            ->setFileName($pmxRrd->getDatabaseName())
            ;
        //check data from graph
    }


//
//    public function testRrdDatabaseOnRouterData()
//    {
//        /** @var $pmxRrd \Pmx\Bundle\RrdBundle\Component\PmxRrdDatabase */
//
//        $pmxRrd = $this->get('pmx_rrd.db');
//
//
//        $pmxRrd->setDbName($this->dbFileName)
//            ->addDataSource('input', DSType::COUNTER)
//            ->addDataSource('output', DSType::COUNTER)
//
//            ->addRoundRobinArchive(RRAConsolidationFunction::AVERAGE, 0.5, 1, 600)
//            ->addRoundRobinArchive(RRAConsolidationFunction::AVERAGE, 0.5, 6, 700)
//            ->addRoundRobinArchive(RRAConsolidationFunction::AVERAGE, 0.5, 24, 775)
//            ->addRoundRobinArchive(RRAConsolidationFunction::AVERAGE, 0.5, 288, 797)
//
//            ->addRoundRobinArchive(RRAConsolidationFunction::MAX, 0.5, 1, 600)
//            ->addRoundRobinArchive(RRAConsolidationFunction::MAX, 0.5, 6, 700)
//            ->addRoundRobinArchive(RRAConsolidationFunction::MAX, 0.5, 24, 775)
//            ->addRoundRobinArchive(RRAConsolidationFunction::MAX, 0.5, 288, 797)
//        ->create();
//
//        $this->assertTrue(file_exists($this->dbFileName));
//
//        /** @var $pmxRrdGraph \Pmx\Bundle\RrdBundle\Component\PmxRrdGraph */
//        $pmxRrdGraph = $this->get('pmx_rrd.graph');
//
//
//        $info = $pmxRrdGraph->rrdinfo($this->dbFileName);
//
//        $this->assertTrue($info['filename'] == $this->dbFileName);
//        $this->assertTrue($info['step'] == $pmxRrd->step);
//
//        $this->assertTrue($info['step'] == $pmxRrd->step);
//
//        $this->assertTrue(count($info['ds']) == 2);
//
//        $this->assertTrue($info['ds']['input']['type'] == DSType::COUNTER);
//        $this->assertTrue($info['ds']['output']['type'] == DSType::COUNTER);
//
//        $this->assertTrue(count($info['rra']) == 8);
//    }
//
//    public function testBlah()
//    {
//        /** @var $pmxRrd \Pmx\Bundle\RrdBundle\Component\PmxRrdGraph */
//
//        $pmxRrd = $this->get('pmx_rrd.graph');
//
//        $pmxRrd->setTitle('setTitle')->setVerticalLabel('setVerticalLabel')->setFileName('filename')->setLowerLimit(1)->setStart('-2d');
//
//
//        $this->assertTrue($pmxRrd->title == 'setTitle');
//        $this->assertTrue($pmxRrd->verticalLabel == 'setVerticalLabel');
//        $this->assertTrue($pmxRrd->filename == 'filename');
//        $this->assertTrue($pmxRrd->lowerLimit == 1);
//        $this->assertTrue($pmxRrd->start == '-2d');
//
//        $this->assertTrue(is_array($pmxRrd->defs) && count($pmxRrd->defs) == 0);
//
//
////        $pmxRrd->addDef('goldCount', 'gold');
//
//
//
//    }
}
