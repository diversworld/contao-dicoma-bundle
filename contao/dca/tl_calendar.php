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

// Palettes
PaletteManipulator::create()
    ->addLegend('dive_check_legend', 'title_legend')
    ->addField(['diveCourse, tankChecks'], 'dive_check_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_calendar');

// Fields
$GLOBALS['TL_DCA']['tl_calendar']['fields']['tankChecks'] = [
    'eval'      => ['tl_class' => 'clr w50'],
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_calendar']['fields']['diveCourse'] = [
    'eval'      => ['tl_class' => 'clr w50'],
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'sql'       => "char(1) NOT NULL default ''",
];
