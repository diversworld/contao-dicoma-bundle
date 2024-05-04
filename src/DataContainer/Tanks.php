<?php

declare(strict_types=1);

/*
 * This file is part of DiCoMa.
 *
 * (c) DiversWorld 2024 <eckhard@diversworld.eu>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/diversworld/contao-dicoma-bundle
 */

namespace Diversworld\ContaoDicomaBundle\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\DataContainer;
use Contao\Input;
use Contao\System;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[AsCallback(table: 'tl_dw_tanks', target: 'edit.buttons', priority: 100)]
class Tanks
{
    private ContaoFramework $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    #[AsCallback(table: 'tl_dw_tanks', target: 'edit.buttons', priority: 100)]
    public function __invoke(array $arrButtons, DataContainer $dc): array
    {
        if ('edit' === Input::get('act')) {
            $arrButtons['createInvoice'] = '<button type="submit" name="createInvoice" id="createInvoice" class="tl_submit createInvoice" accesskey="x">'.$GLOBALS['TL_LANG']['tl_dw_tanks']['createInvoiceButton'].'</button>';
        }

        return $arrButtons;
    }


    #[AsCallback(table: 'tl_dw_tanks', target: 'config.onsubmit', priority: 100)]
    public function runCreateInvoice(): void
    {
        if ('' !== Input::get('id') && '' === Input::post('createInvoice') && 'tl_dw_tanks' === Input::post('FORM_SUBMIT') && 'auto' !== Input::post('SUBMIT_TYPE')) {
            $this->customButtonEvent();
        }
    }

    public function customButtonEvent()
    {
        $logger = System::getContainer()->get('monolog.logger.contao');

        $inputAdapter = $this->framework->getAdapter(Input::class);
        $tankId = $inputAdapter->get('id');

        $datum = date("Y-m-d");
        $title = " Rechnung_" . $datum;

        if ($tankId)
        {
            // Get database instance
            $db = Database::getInstance();

            // Get member, pid from tl_dw_tanks
            $result = $db->prepare("SELECT member, pid, size FROM tl_dw_tanks WHERE id = ?")
                ->execute($tankId);

            $row = $result->fetchAssoc();

            $eventId = $row['pid'];
            $member = $row['member'];
            $size = $row['size'];
            $alias = " rechnung_" . $datum;

            // Get checkArticles from tl_calendar_events
            $eventResult = $db->prepare("SELECT checkArticles FROM tl_calendar_events WHERE id = ?")
                ->execute($eventId);

            $eventRow = $eventResult->fetchAssoc();

            $checkArticles = unserialize($eventRow['checkArticles']);

            $filteredArticles = array_filter($checkArticles, function($article) use ($size) {
                return $article['articleSize'] == $size || $article['default'] == '1';
            });

            $filteredArticles = array_values($filteredArticles);

            $logger->info(
                'Filtered Articles: '. print_r($filteredArticles, true),
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
            );

            // Calculate total price
            $totalPrice = array_reduce($filteredArticles, function ($total, $article) {
                return $total + str_replace(',', '.', $article['articlePriceBrutto']);
            }, 0);

            //$totalPrice = number_format($totalPrice, 2, ',', '');

            $stmt = $db->prepare(
                "INSERT INTO tl_dw_check_invoice (title, alias, tstamp, pid, member, published, invoiceArticles, priceTotal) VALUES (?, ?, ?, ?, ?, 1, ?, ?)");
            $stmt->execute($title, $alias, time(), $tankId, $eventId, $member, $filteredArticles, $totalPrice);

            $logger->info(
                'Invoice created successfully. Tank ID: '. print_r($tankId, true) . 'Datum ' .print_r($datum, true),
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
            );

            $do = Input::get('do');
            $ref = Input::get('ref');

            $url = $this->framework->getAdapter('Contao\Backend')->addToUrl('do=' . $do . '&ref=' . $ref);

            return new RedirectResponse($url);
        }
    }
}
