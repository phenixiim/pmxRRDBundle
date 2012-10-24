<?php
/**
 * Created by JetBrains PhpStorm.
 * @author: pomaxa none <pomaxa@gmail.com>
 * @date: 10/19/12
 */

namespace Pmx\Bundle\RrdBundle\Component;

/**
 * Этот параметр показывает сколько отсчетов комбинировать в одну ячейку.
 */
class RRAConsolidationFunction
{
    //высчитывается среднее арифметическое всех отсчетов
    const AVERAGE = 'AVERAGE';
    //максимальное значение отсчетов соответственно
    const MAX = 'MAX';
    //минимальное значение отсчетов соответственно
    const MIN = 'MIN';
    //последний полученный отсчет
    const LAST = 'LAST';

    const TOTAL = 'TOTAL';



    // функции консолидации для версии 1.2 и больше
    //дополнение к прежним функциям консолидации добавились функции сглаживания (предсказание на основе предыдущих данных)
    //и фильтрации предположительно ошибочных данных
    //слишком большое отклонение от предсказанного

    //(предсказание методом Holt-Winters
    const HWPREDICT = 'HWPREDICT';

    //отклонение от предсказания, взвешенное для одного цикла
    const DEVPREDICT = 'DEVPREDICT';
    //слишком большое отклонение от предсказанного
    const FAILURES = 'FAILURES';
    //предсказание по алгоритму Holt-Winters со скользящим окном в 288 отсчётов
    const SEASONAL = 'SEASONAL';
    const DEVSEASONAL = 'DEVSEASONAL';
}