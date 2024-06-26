<?php

/*
 * This file is part of DiCoMa.
 *
 * (c) DiversWorld 2024 <eckhard@diversworld.eu>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/diversworld/contao-dicoma-bundle
 */

use Diversworld\ContaoDicomaBundle\Model\CheckInvoiceModel;
use Diversworld\ContaoDicomaBundle\Model\TanksModel;
use Diversworld\ContaoDicomaBundle\Model\CoursesModel;

/**
 * Backend modules
 */

// Add child table tl_calendar_events_member to tl_calendar_events
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_dw_tanks';

$GLOBALS['BE_MOD']['diversworld'] = [
    'check_collection' => ['tables' => ['tl_dw_tanks','tl_dw_check_invoice'],
    ]
];

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_dw_courses']          = CoursesModel::class;
$GLOBALS['TL_MODELS']['tl_dw_tanks']            = TanksModel::class;
$GLOBALS['TL_MODELS']['tl_dw_check_invoice']    = CheckInvoiceModel::class;
