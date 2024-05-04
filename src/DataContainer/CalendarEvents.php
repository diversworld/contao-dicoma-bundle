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
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\DataContainer;
use Contao\Date;
use Contao\System;
use Diversworld\ContaoDicomaBundle\Model\TanksModel;

class CalendarEvents
{
    /**
     * @param array $arrRow
     */
    public function listTanks($arrRow): string
    {
        if ('1' === $arrRow['addCheckInfo']) {
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

        return (new \tl_calendar_events())->listEvents($arrRow);
    }

    public function calculateAllGrossPrices(DataContainer $dc)
    {
        $logger = System::getContainer()->get('monolog.logger.contao');

        $id = $dc->id;
        $model = CalendarEventsModel::findById($id);

        if($model !== null) {
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
                    $nettoPrice = str_replace(',', '.', $row['articlePriceNetto']);
                    $grossPrice = $nettoPrice * 1.19;
                    $grossRoundedPrice = ceil($grossPrice / 0.05) * 0.05;

                    // Setzen Sie das Feld 'articlePriceBrutto'
                    $row['articlePriceBrutto'] = number_format((float)$grossRoundedPrice, 2, '.', ',');

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
