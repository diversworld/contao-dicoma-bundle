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

// Palettes
PaletteManipulator::create()
    ->addLegend('tank_check_legend', 'title_legend', PaletteManipulator::POSITION_AFTER)
    ->addField(['tankChecks'], 'tank_check_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_calendar');

// Fields
$GLOBALS['TL_DCA']['tl_calendar']['fields']['tankChecks'] = [
    'exclude'    => true,
    'inputType'  => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval'       => [
        'mandatory' => true,
        'fieldType' => 'radio',
        'tl_class'  => 'clr',
    ],
    'sql'        => "int(10) unsigned NOT NULL default '0'",
    'relation'   => [
        'type' => 'hasOne',
        'load' => 'lazy',
    ],
];
