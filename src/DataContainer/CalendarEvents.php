<?php

declare(strict_types=1);

/*
 * This file is part of DiCoMa.
 *
 * (c) Diversworld 2024 <eckhard@diversworld.eu>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/diversworld/contao-dicoma-bundle
 */

namespace Diversworld\ContaoDicomaBundle\DataContainer;

use Contao\Calendar;
use Contao\CalendarEventsModel;
use Contao\Config;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\DataContainer;
use Contao\Date;
use Contao\System;
use Diversworld\ContaoDicomaBundle\Model\TanksModel;
use Diversworld\ContaoDicomaBundle\DataContainer\CalendarEvents as CalendarEventsDiversworld;
use Markocupic\CalendarEventBookingBundle\DataContainer\CalendarEvents as CalendarEventsMarkoCupic;

class CalendarEvents
{
       private CalendarEventsMarkoCupic $inner;

       public function __construct(CalendarEventsMarkoCupic $inner)
       {
           $this->inner = $inner;
       }

       #[AsCallback(table: 'tl_calendar_events', target: 'list.sorting.child_record')]
       public function listEvents(array $arrRow): string
       {
           $logger = System::getContainer()->get('monolog.logger.contao');

           $logger->error(
               'ListTanks: ' . ' addBookingInfo '. $arrRow['addBookingInfo'] . ' addCheckInfo '. $arrRow['addCheckInfo'],
               ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
           );

           if ($arrRow['addCheckInfo'] === '1') {
               $logger->error(
                   'ListTanks Diverworld: ',
                   ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
               );
               // Your listTanks logic goes here
               return $this->listTanks($arrRow);
           }
           if ($arrRow['addCourseInfo'] === '1'){
               $logger->error(
                   'ListTanks Markocupic: ',
                   ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
               );
               // Run the original service's logic
               return $this->inner->listEvents($arrRow);
           }

           // Default return
           $span = Calendar::calculateSpan($arrRow['startTime'], $arrRow['endTime']);

           if ($span > 0)
           {
               $date = Date::parse(Config::get($arrRow['addTime'] ? 'datimFormat' : 'dateFormat'), $arrRow['startTime']) . $GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'] . Date::parse(Config::get($arrRow['addTime'] ? 'datimFormat' : 'dateFormat'), $arrRow['endTime']);
           }
           elseif ($arrRow['startTime'] == $arrRow['endTime'])
           {
               $date = Date::parse(Config::get('dateFormat'), $arrRow['startTime']) . ($arrRow['addTime'] ? ' ' . Date::parse(Config::get('timeFormat'), $arrRow['startTime']) : '');
           }
           else
           {
               $date = Date::parse(Config::get('dateFormat'), $arrRow['startTime']) . ($arrRow['addTime'] ? ' ' . Date::parse(Config::get('timeFormat'), $arrRow['startTime']) . $GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'] . Date::parse(Config::get('timeFormat'), $arrRow['endTime']) : '');
           }

           return '<div class="tl_content_left">' . $arrRow['title'] . ' <span class="label-info">[' . $date . ']</span></div>';
       }

    public function listTanks(array $arrRow): string
    {
        if ($arrRow['addCheckInfo'] === '1') {
            $countTanks = TanksModel::countBy('pid', $arrRow['id']);

            $span = Calendar::calculateSpan($arrRow['startTime'], $arrRow['endTime']);

            if ($span > 0) {
                $date = Date::parse(Config::get(($arrRow['addTime'] ? 'datimFormat' : 'dateFormat')), $arrRow['startTime']).$GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'].Date::parse(Config::get(($arrRow['addTime'] ? 'datimFormat' : 'dateFormat')), $arrRow['endTime']);
            } elseif ($arrRow['startTime'] === $arrRow['endTime']) {
                $date = Date::parse(Config::get('dateFormat'), $arrRow['startTime']).($arrRow['addTime'] ? ' '.Date::parse(Config::get('timeFormat'), $arrRow['startTime']) : '');
            } else {
                $date = Date::parse(Config::get('dateFormat'), $arrRow['startTime']).($arrRow['addTime'] ? ' '.Date::parse(Config::get('timeFormat'), $arrRow['startTime']).$GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'].Date::parse(Config::get('timeFormat'), $arrRow['endTime']) : '');
            }

            return '<div class="tl_content_left">'.$arrRow['title'].' <span style="color:#999;padding-left:3px">['.$date.']</span><span style="color:#999;padding-left:3px">['.$GLOBALS['TL_LANG']['MSC']['tanks'].': '.$countTanks.'x]</span></div>';
        }

        return (new tl_calendar_events())->listEvents($arrRow);
    }

    public function calculateAllGrossPrices(DataContainer $dc): void
    {
        $logger = System::getContainer()->get('monolog.logger.contao');

        $id = $dc->id;
        $model = CalendarEventsModel::findById($id);

        if($dc->addCheckInfo == 1)
        {
            $checkArticles = unserialize($model->checkArticles);

            if (!$checkArticles) {
                $logger->error(
                    'Ungültige Daten für checkArticles, kann nicht deserialisiert werden: ' . $model->checkArticles,
                    ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
                );
                // handle error, or exit
                return;
            }

            // Iterieren Sie über jede Zeile in checkArticles
            foreach ($checkArticles as &$row) {
                // Überprüfen Sie, ob das Feld 'articlePriceNetto' gesetzt ist
                if (isset($row['articlePriceNetto'])) {
                    $nettoPrice = number_format((float)$row['articlePriceNetto'], 2);//str_replace(',', '.', $row['articlePriceNetto']);
                    $grossPrice = $nettoPrice * 1.19;
                    $grossRoundedPrice = ceil($grossPrice / 0.05) * 0.05;

                    // Setzen Sie das Feld 'articlePriceBrutto'
                    $row['articlePriceBrutto'] = number_format($grossRoundedPrice, 2);
                }  else {
                    $logger->info(
                        'articlePriceNetto ist nicht gesetzt.',
                        ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
                    );
                }
            }

            unset($row);

            // Speichern Sie die Änderungen in der Datenbank
            Database::getInstance()->prepare("UPDATE tl_calendar_events SET checkArticles = ? WHERE id = ?")
                ->execute(serialize($checkArticles), $id);
        }
    }
}
