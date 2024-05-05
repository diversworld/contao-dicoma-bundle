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

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Diversworld\ContaoDicomaBundle\DataContainer\CalendarEvents;

// Overwrite child record callback
$GLOBALS['TL_DCA']['tl_calendar_events']['list']['sorting']['child_record_callback'] = [
    CalendarEvents::class,
    'listTanks',
];

$GLOBALS['TL_DCA']['tl_calendar_events']['config']['onsubmit_callback'] = [
    [CalendarEvents::class, 'calculateAllGrossPrices']
];

// Palettes
PaletteManipulator::create()
    ->addLegend('tuv_legend', 'date_legend')
    ->addLegend('vendor_legend', 'tuv_legend')
    ->addLegend('article_legend', 'vendor_legend')
    ->addField(['addCheckInfo'], 'tuv_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_calendar_events');

// Selector
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'addCheckInfo';
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'addVendorInfo';
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'addArticleInfo';

// Subpalettes
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['addCheckInfo'] = 'addVendorInfo, addArticleInfo';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['addVendorInfo'] = 'vendorName, street, postal, city, vendorEmail, vendorPhone, vendorMobile';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['addArticleInfo'] = 'checkArticles';

// Operations
$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['tanks'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['tanks'],
    'href' => 'do=calendar&table=tl_dw_tanks',
    'icon' => 'bundles/diversworldcontaodicoma/icons/tanks.png',
];

//Fields
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['checkArticles'] = [
    'inputType' => 'multiColumnEditor',
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
                    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['articleSize'],
                    'inputType' => 'select',
                    'options'   => ['2','3','5','7','8','10','12','15','18','20','40','80'],
                    'eval'      => ['includeBlankOption' => true, 'groupStyle' => 'width:60px']
                ],
                'articleNotes'  => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['articleNotes'],
                    'inputType' => 'textarea',
                    'eval'      => ['groupStyle' => 'width:400px']
                ],
                'articlePriceNetto' => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['articlePriceNetto'],
                    'inputType' => 'text',
                    'eval'      => ['groupStyle' => 'width:100px', 'submitOnChange' => true],
                    'save_callback' => [CalendarEvents::class, 'calculateAllGrossPrices'],
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
        ]
    ],
    'sql'       => "blob NULL"
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['is_tuv_appointment'] = [
    'eval'      => ['tl_class' => 'clr m12'],
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['addCheckInfo'] = [
    'eval'      => [
        'submitOnChange' => true,
        'tl_class'       => 'clr m12',
    ],
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'sql'       => "char(1) NOT NULL default ''",
];$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['addVendorInfo'] = [
    'eval'      => [
        'submitOnChange' => true,
        'tl_class'       => 'clr m12',
    ],
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'sql'       => "char(1) NOT NULL default ''",
];$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['addArticleInfo'] = [
    'eval'      => [
        'submitOnChange' => true,
        'tl_class'       => 'clr m12',
    ],
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'sql'       => "char(1) NOT NULL default ''",
];
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['vendorName'] = [
    'eval' => [
        'mandatory' => false,
        'maxlength' => 255,
        'tl_class' => 'w33',
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
        'tl_class' => 'w33 clr',
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
        'maxlength' => 12,
        'tl_class' => 'w25',
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
        'tl_class' => 'w33',
    ],
    'exclude' => true,
    'flag' => 1,
    'inputType' => 'text',
    'search' => true,
    'sorting' => true,
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['vendorEmail'] = [
    'default' => null,
    'eval' => [
        'mandatory' => false,
        'doNotCopy' => true,
        'tl_class' => 'clr w33 wizard',
    ],
    'exclude' => true,
    'inputType' => 'text',
    'sorting' => true,
    'sql' => 'int(10) unsigned NULL',
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['vendorPhone'] = [
    'default' => null,
    'eval' => [
        'mandatory' => false,
        'doNotCopy' => true,
        'tl_class' => 'w33 wizard',
    ],
    'exclude' => true,
    'inputType' => 'text',
    'sorting' => true,
    'sql' => 'int(10) unsigned NULL',
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['vendorMobile'] = [
    'default' => null,
    'eval' => [
        'mandatory' => false,
        'doNotCopy' => true,
        'tl_class' => 'w33 wizard',
    ],
    'exclude' => true,
    'inputType' => 'text',
    'sorting' => true,
    'sql' => 'int(10) unsigned NULL',
];

