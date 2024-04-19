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

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\DataContainer;
use Contao\Input;
use Contao\System;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsCallback(table: 'tl_dw_tanks', target: 'edit.buttons', priority: 100)]
class Tanks
{
    private ContaoFramework $framework;
    private RequestStack $requestStack;

    public function __construct(ContaoFramework $framework, RequestStack $requestStack)
    {
        $this->framework = $framework;
        $this->requestStack = $requestStack;
    }

    public function __invoke(array $arrButtons, DataContainer $dc): array
    {
        $logger = System::getContainer()->get('monolog.logger.contao');

        $inputAdapter = $this->framework->getAdapter(Input::class);
        $systemAdapter = $this->framework->getAdapter(System::class);

        $systemAdapter->loadLanguageFile('tl_dw_tanks');

        if ('edit' === $inputAdapter->get('act')) {
            $arrButtons['customButton'] = '<button type="submit" name="customButton" id="customButton" class="tl_submit customButton" accesskey="x">'.$GLOBALS['TL_LANG']['tl_dw_tanks']['customButton'].'</button><input type="hidden" name="SUBMIT_TYPE" id="SUBMIT_TYPE" value="createInvoice" />';
        }

        $logger->info(
            'Rechnungserstellung wird gestartet:',
            ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
        );

        $request = $this->requestStack->getCurrentRequest();

        $logger->info(
            'Request: '. print_r($arrButtons, true) . " - " . print_r($request->query->all(), true) . " - " . print_r($request->request->all(), true),
            ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
        );

        if ($request->request->has('customButton')) {
            $logger->info(
                'Rechnungserstellung wird gestartet: Aufruf der Funktion',
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
            );
            $this->customButtonEvent();
        }

        return $arrButtons;
    }

    public function customButtonEvent()
    {
        $logger = System::getContainer()->get('monolog.logger.contao');

        $inputAdapter = $this->framework->getAdapter(Input::class);
        $tankId = $inputAdapter->get('id');

        $datum = date("Y-m-d");
        $title = " Rechnung_". $datum;

        $logger->info(
            'Ermittelte Tank ID: ' . $tankId,
            ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
        );

        $logger->info(
            'Invoice wird erstellt',
            ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
        );

        if ($tankId)
        {
            $logger->info(
                'Tank ID: ' . $tankId,
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
            );

            // Get database instance
            $db = Database::getInstance();

            // Get member, pid from tl_dw_tanks
            $result = $db->prepare("SELECT member, pid FROM tl_dw_tanks WHERE id = ?")
                ->execute($tankId);

            $row = $result->fetchAssoc();

            $logger->info(
                'Show row' . print_r($row, true),
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
            );

            $eventId = $row['pid'];

            // Get checkArticles from tl_calendar_events
            $eventResult = $db->prepare("SELECT checkArticles FROM tl_calendar_events WHERE id = ?")
                ->execute($eventId);

            $eventRow = $eventResult->fetchAssoc();
            $checkArticles = unserialize($eventRow['checkArticles']);

            $logger->info(
                'Daten erfolgreich ermittelt',
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
            );

            // Filter checkArticles for those marked with default = 1
            $defaultArticles = array_filter($checkArticles, function($article) {
                return $article['default'] == 1 || $article['default'] === '1';
            });

            $logger->info(
                'Datenarray invoiceArticles'. print_r($defaultArticles, true),
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
            );

            $stmt = $db->prepare(
                "INSERT INTO tl_dw_check_invoice (title, pid, published, invoiceArticles) VALUES (?, ?, 1, ?)");
            $stmt->execute($title, $tankId, $eventRow['checkArticles']);

            $logger->info(
                'Invoice created successfully'. print_r($defaultArticles, true),
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
            );

            $url = $this->framework->getAdapter('Contao\Backend')->addToUrl('');
            return new RedirectResponse($url);
        }
    }
}
