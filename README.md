pmxRRDBundle
============

RRD bundle for symfony2





Draw a graph
------------
```php
$pmxRrd = $this->get('pmx_rrd.graph');

$pmxRrd->setTitle('Chart Title')
    ->setVerticalLabel('Gold count')
    ->setFileName('nameOfBase.rrd')
    ->setStart('now-1w')
    ->setEnd('now')
    ->addDef('goldcount', 'gold', 'AVERAGE', null, null)
    ->addCDef('goldAvg', 'goldcount,1,*')
    ->line('goldAvg', 'Gold amount on this week\\r')
    ->doDraw();
```