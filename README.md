Simple php mapper for RRD
=========================


## Prerequierments ##
 - Symfony 2.*
 - php5-rrd extension

# Usage guide

## Creating database file

```php
$pmxRrd = $this->get('pmx_rrd.db');


$pmxRrd->setDbName($this->dbFileName)
    ->addDataSource('input', DSType::COUNTER)
    ->addDataSource('output', DSType::COUNTER)

    ->addRoundRobinArchive(RRAConsolidationFunction::AVERAGE, 0.5, 1, 600)
    ->addRoundRobinArchive(RRAConsolidationFunction::AVERAGE, 0.5, 6, 700)
    ->addRoundRobinArchive(RRAConsolidationFunction::AVERAGE, 0.5, 24, 775)
    ->addRoundRobinArchive(RRAConsolidationFunction::AVERAGE, 0.5, 288, 797)

    ->addRoundRobinArchive(RRAConsolidationFunction::MAX, 0.5, 1, 600)
    ->addRoundRobinArchive(RRAConsolidationFunction::MAX, 0.5, 6, 700)
    ->addRoundRobinArchive(RRAConsolidationFunction::MAX, 0.5, 24, 775)
    ->addRoundRobinArchive(RRAConsolidationFunction::MAX, 0.5, 288, 797)
->create();
```



## Drawing Graphs

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



## Special symbols in lagends ##

### Aligment
* \l - выравнивание влево
* \r - выравнивание вправо
* \c - выравнивание в центр
* \j - justify
* \g - подавить вывод завершающих строку пробелов
* \s - переход на следующую строку (только для COMMENT)
* \t
### Format:

_любой символ, кроме '%', печатается как есть_
* %% - символ '%'
* %#.#le - экспоненциальный формат
* %#.#lf - вещественное число с точкой
* %s - сокращённое наименование использованных единиц СИ (k, M и т.д.)
* %a, %A - сокращённое или полное имя дня недели
* %b, %B - сокращённое или полное имя месяца
* %d, %m, %y, %H, %M, %S - день, месяц, год, часы, минуты, секунды в виде 2 цифр
* %Y - год в виде 4 цифр
* %j - номер дня недели (0-6)
* %w - день года (1-366)
* %c - дата и время
* %x - дата
* %X - время
* %U - номер недели (первая неделя года по первому воскресенью)
* %W - номер недели (первая неделя года по первому понедельнику)
* %Z - часовой пояс


