Simple php mapper for RRD
=========================


## Prerequierments ##
 - Symfony 2.*
 - rrdtool
 - php5-rrd extension

## Configuration ##

add to the config.yml
```yml
imports:
    - { resource: parameters.yml }
    - { resource: @PmxRrdBundle/Resources/config/config.yml}
```

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
* **\l** - выравнивание влево
* **\r** - выравнивание вправо
* **\c** - выравнивание в центр
* **\j** - justify
* **\g** - подавить вывод завершающих строку пробелов
* **\s** - переход на следующую строку (только для COMMENT)
* **\t**
### Format:

_любой символ, кроме '%', печатается как есть_
* **%%** - символ '%'
* **%#.#le** - экспоненциальный формат
* **%#.#lf** - вещественное число с точкой
* **%s** - сокращённое наименование использованных единиц СИ (k, M и т.д.)
* **%a**, **%A** - сокращённое или полное имя дня недели
* **%b**, **%B** - сокращённое или полное имя месяца
* **%d**, **%m**, **%y**, **%H**, **%M**, **%S** - день, месяц, год, часы, минуты, секунды в виде 2 цифр
* **%Y** - год в виде 4 цифр
* **%j** - номер дня недели (0-6)
* **%w** - день года (1-366)
* **%c** - дата и время
* **%x** - дата
* **%X** - время
* **%U** - номер недели (первая неделя года по первому воскресенью)
* **%W** - номер недели (первая неделя года по первому понедельнику)
* **%Z** - часовой пояс

### In CDEF abd DEF definitions you need to use expresion in reverse polish notation

- Элементы обратной польской записи (числа, имена ранее определенных переменных и коды операций) перечисляются через запятую.
- Истина - 1, ложь - 0.
- Сравнение с неопределённым или бесконечным значением всегда даёт 0.
- Логарифмы натуральные.
- Углы в радианах.

#### Допустимы операции:

* +, -, *, /, %, SIN, COS, LOG, EXP, SQRT, ATAN, ATAN2 (первым извлекается x, затем y),
* DEG2RAD, RAD2DEG, FLOOR, CEIL, LT, LE, GT, GE, EQ, IF (извлекает 3 элемента из стека; если последнее извлеченное значение не 0, т.е. истина, то в стек кладется второе извлеченное значение, иначе - первое),

* **MIN**, **MAX**, **LIMIT** (первые 2 значения из стека определяют границы, если третье значение лежит внутри этих границ, то оно кладется в стек, иначе в стек кладется неопределенное значение), DUP (дублирует верхний элемент стека),
* **EXC** (меняет 2 верхних элемента местами), POP (удаляет верхний элемент из стека),
* **UN** (если верхний элемент стека является неопределенным значением, то он заменяется на 1, иначе - на 0),
* **UNKN** (в стек кладется неопределенное значение),
* **INF** (бесконечность в стек),
* **NEGINF** (отрицательная бесконечность),
* **ISINF** (если верхний элемент стека является бесконечным значением, то он заменяется на 1, иначе - на 0),
* **SORT** (первый извлечённый элемент определяет число элементов для сортировки),
* **REV** (обратить последовательность, первый извлечённый элемент определяет число элементов),
* **TREND** (усреднение, первый извлечённый элемент определяет интервал времени),
* **PREV** (если это первый отсчет, то неопределенное значение в стек, иначе значение данного DS, вычисленное на предыдущем шаге),
* **PREV**(имя-переменной) (если это первый отсчет, то неопределенное значение в стек, иначе значение данной переменной VDEF, вычисленное на предыдущем шаге),
* **COUNT** (поместить в стек индекс текущего значения в DS),
* **NOW** (текущее время),
* **TIME** (время отсчета),
* **LTIME** (TIME с добавлением смещения временой зоны и учетом летнего времени).

### Агрегирующие операции для VDEF:
* **MAXIMUM**,
* **MINIMUM**,
* **AVERAGE**,
* **LAST**,
* **FIRST**,
* **TOTAL** (сумма значений, умноженных на интервал отсчёта; например, можно получить общий траффик по БД скоростей),
* **PERCENT** (первый параметр - число процентов; второй - имя DS или CDEF; значения последовательности сортируются; возвращается такое число, что заданный процент значений не превышает его), параметры корреляции (LSLSLOPE, LSLINT, LSLCORREL).

#### Примеры обратной польской записи:

    idat1,UN,0,idat1,IF (замена неопределенного значения на 0)

#TODO
* write down more unit tests
* write some docs (on usage/ and on rrd it self)
* Examples of using it for all stebs. based on some simple data(like kb/s network trafic)