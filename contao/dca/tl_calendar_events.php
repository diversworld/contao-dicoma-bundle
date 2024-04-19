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

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Diversworld\ContaoDicomaBundle\DataContainer\CalendarEvents;

// Table config
$GLOBALS['TL_DCA']['tl_calendar']['config']['ctable'][] = 'tl_dw_tanks';

// Overwrite child record callback
$GLOBALS['TL_DCA']['tl_calendar_events']['list']['sorting']['child_record_callback'] = [
    CalendarEvents::class,
    'listEvents',
];

// Palettes
PaletteManipulator::create()
    ->addLegend('vendor_legend', 'details_legend', PaletteManipulator::POSITION_AFTER)
    ->addField([
        'vendorname',
        'street',
        'postal',
        'city',
    ], 'location', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_calendar_events');

// Selector
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'addBCheckInfo';

// Subpalettes
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['addBCheckInfo'] = '';

// Operations
$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['tanks'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['tanks'],
    'href' => 'do=calendar&table=tl_calendar_events_member',
    'icon' => 'bundles/diversworlddicomabundle/icons/tanks.png',
];

// Fields
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['checkArticles'] = [
    'inputType' => 'multiColumnEditor',
    'onsubmit_callback' => [CalendarEvents::class, 'calculateAllGrossPrices'],
    'eval'      => [
        'multiColumnEditor' => [
            'skipCopyValuesOnAdd' => false,
            'editorTemplate' => 'multi_column_editor_backend_default',
            'fields' => [
                'articleName' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['articleName'],
                    'inputType' => 'text',
                    'eval' => ['groupStyle' => 'width:300px']
                ],
                'articleSize' => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['articleName'],
                    'inputType' => 'select',
                    'options'   => ['3','5','7','8','10','12','15','18','20','40','80'],
                    'eval'      => ['groupStyle' => 'width:300px']
                ],
                'articleNotes'  => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['articleNotes'],
                    'inputType' => 'textarea',
                    'eval'      => ['groupStyle' => 'width:400px']
                ],
                'articlePriceNetto' => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['articlePriceNetto'],
                    'inputType' => 'text',
                    'eval'      => ['groupStyle' => 'width:100px', 'submitOnChange' => true]
                ],
                'articlePriceBrutto' => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['articlePriceBrutto'],
                    'inputType' => 'text',
                    'eval'      => ['groupStyle' => 'width:100px']
                ],
                'default' => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['default'],
                    'inputType' => 'checkbox',
                    'eval'      => ['groupStyle' => 'width:40px']
                ],
            ]
        ],
        'sql'       => "blob NULL"
    ]
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['vendorname'] = [
    'eval' => [
        'mandatory' => false,
        'maxlength' => 255,
        'tl_class' => 'w50',
    ],
    'exclude' => true,
    'flag' => 1,
    'inputType' => 'text',
    'search' => true,
    'sorting' => true,
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['street'] = [
    'eval' => [
        'mandatory' => false,
        'maxlength' => 255,
        'tl_class' => 'w50',
    ],
    'exclude' => true,
    'flag' => 1,
    'inputType' => 'text',
    'search' => true,
    'sorting' => true,
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['postal'] = [
    'eval' => [
        'maxlength' => 32,
        'tl_class' => 'w50',
    ],
    'exclude' => true,
    'inputType' => 'text',
    'search' => true,
    'sorting' => true,
    'sql' => "varchar(32) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['city'] = [
    'eval' => [
        'mandatory' => false,
        'maxlength' => 255,
        'tl_class' => 'w50',
    ],
    'exclude' => true,
    'flag' => 1,
    'inputType' => 'text',
    'search' => true,
    'sorting' => true,
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['bookingStartDate'] = [
    'default' => null,
    'eval' => [
        'rgxp' => 'datim',
        'mandatory' => true,
        'doNotCopy' => true,
        'datepicker' => true,
        'tl_class' => 'clr w50 wizard',
    ],
    'exclude' => true,
    'inputType' => 'text',
    'sorting' => true,
    'sql' => 'int(10) unsigned NULL',
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['bookingEndDate'] = [
    'default' => null,
    'eval' => [
        'rgxp' => 'datim',
        'mandatory' => true,
        'doNotCopy' => true,
        'datepicker' => true,
        'tl_class' => 'w50 wizard',
    ],
    'exclude' => true,
    'inputType' => 'text',
    'sorting' => true,
    'sql' => 'int(10) unsigned NULL',
];

