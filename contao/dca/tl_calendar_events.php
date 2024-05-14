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
    ->addLegend('dive_legend', 'details_legend')
    ->addLegend('vendor_legend', 'dive_legend')
    ->addLegend('article_legend', 'vendor_legend')
    ->addField(['addCheckInfo', 'addCourseInfo'], 'dive_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_calendar_events');

// Selector
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'addCheckInfo';
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'addVendorInfo';
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'addArticleInfo';
$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'addCourseInfo';

// Subpalettes
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['addCheckInfo']     = 'addVendorInfo, addArticleInfo';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['addVendorInfo']    = 'vendorName, street, postal, city, vendorEmail, vendorPhone, vendorMobile';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['addArticleInfo']   = 'checkArticles';
$GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes']['addCourseInfo']    = 'category, courseFee';

// Operations
$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['tanks'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['tanks'],
    'href'  => 'do=calendar&table=tl_dw_tanks',
    'icon'  => 'bundles/diversworldcontaodicoma/icons/tanks.png',
];

//Fields
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['checkArticles'] = [
    'inputType' => 'multiColumnEditor',
    'tl_class'  => 'compact',
    'eval'      => [
        'multiColumnEditor' => [
            'skipCopyValuesOnAdd' => true,
            'editorTemplate' => 'multi_column_editor_backend_default',
            'fields' => [
                'articleName' => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['articleName'],
                    'inputType' => 'text',
                    'eval'      => ['groupStyle' => 'width:300px']
                ],
                'articleSize' => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['articleSize'],
                    'inputType' => 'select',
                    'options'   => ['2','3','5','7','8','10','12','15','18','20','40','80'],
                    'eval'      => ['includeBlankOption' => true, 'groupStyle' => 'width:60px']
                ],
                'articleNotes' => [
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
    'sql' => "blob NULL"
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['is_tuv_appointment'] = [
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'clr w25'],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['addCheckInfo'] = [
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true, 'tl_class' => 'clr w25',],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['addVendorInfo'] = [
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true, 'tl_class' => 'clr w25',],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['addArticleInfo'] = [
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true, 'tl_class' => 'clr w25'],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['addCourseInfo'] = [
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true, 'tl_class' => 'clr w25',],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['courseFee'] = [
    'inputType' => 'text',
    'exclude'   => true,
    'search'    => true,
    'filter'    => true,
    'sorting'   => true,
    'eval'      => array('tl_class'=>'w25', 'alwaysSave' => true, 'rgxp' => 'digit',),
    'sql'       => "DECIMAL(10,2) NOT NULL default '0.00'"
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['category'] = [
    'inputType' => 'select',
    'exclude'   => true,
    'search'    => true,
    'filter'    => true,
    'sorting'   => true,
    'reference' => &$GLOBALS['TL_LANG']['tl_calendar_events'],
    'options'   => array('basicOption', 'advancedOption', 'professionalOption','technicalOption'),
    'eval'      => array('includeBlankOption' => true, 'tl_class' => 'w25'),
    'sql'       => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['vendorName'] = [
    'exclude'   => true,
    'flag'      => SORT_STRING,
    'inputType' => 'text',
    'search'    => true,
    'sorting'   => true,
    'eval'      => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w33',],
    'sql'       => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['street'] = [
    'exclude'   => true,
    'flag'      => SORT_STRING,
    'inputType' => 'text',
    'search'    => true,
    'sorting'   => true,
    'eval'      => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w33 clr',],
    'sql'       => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['postal'] = [
    'exclude'   => true,
    'inputType' => 'text',
    'search'    => true,
    'sorting'   => true,
    'eval'      => ['maxlength' => 12, 'tl_class' => 'w25',],
    'sql'       => "varchar(32) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['city'] = [
    'exclude'   => true,
    'flag'      => SORT_STRING,
    'inputType' => 'text',
    'search'    => true,
    'sorting'   => true,
    'eval'      => ['mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w33',],
    'sql'       => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['vendorEmail'] = [
    'default'   => null,
    'exclude'   => true,
    'inputType' => 'text',
    'sorting'   => true,
    'eval'      => ['mandatory' => false, 'doNotCopy' => true, 'tl_class' => 'clr w33 wizard',],
    'sql'       => 'int(10) unsigned NULL',
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['vendorPhone'] = [
    'default'   => null,
    'exclude'   => true,
    'inputType' => 'text',
    'sorting'   => true,
    'eval'      => ['mandatory' => false, 'doNotCopy' => true, 'tl_class' => 'w33 wizard',],
    'sql'       => 'int(10) unsigned NULL',
];

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['vendorMobile'] = [
    'default'   => null,
    'exclude'   => true,
    'inputType' => 'text',
    'sorting'   => true,
    'eval'      => ['mandatory' => false, 'doNotCopy' => true, 'tl_class' => 'w33 wizard',],
    'sql'       => 'int(10) unsigned NULL',
];
