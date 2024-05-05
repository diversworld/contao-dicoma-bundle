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

namespace Diversworld\ContaoDicomaBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Input;
use Contao\System;

#[AsHook('createInvoice')]
class InvoiceListener
{
    public function onCreateInvoice($row, $href, $label, $title, $attributes): string
    {
        $logger = System::getContainer()->get('monolog.logger.contao');

        $logger->info(
            'PreCheck If Anweisung: ' . print_r($_SERVER['REQUEST_METHOD'], true) . /*' 2-' . print_r($_POST['invoiceButton'], true) . ' 3-' . */print_r(Input::get('SUBMIT_TYPE'), true),
            ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
        );

        // Führen Sie hier Ihre Routine aus ...
        // Sie können die $row, $href, $label, $title, $icon, $attributes Variablen verwenden, um die ausgeführte Aktion zu personalisieren.

        return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$label.'</a> ';

    }

}
