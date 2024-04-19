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
use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\System;
use Diversworld\ContaoDicomaBundle\Model\TanksModel;
use Diversworld\ContaoDicomaBundle\DataContainer\CalendarEventsMember;

/**
 * Table tl_dw_tanks
 */
$GLOBALS['TL_DCA']['tl_dw_tanks'] = array(
    'config'            => array(
        'dataContainer'     => DC_Table::class,
        'ptable'            => 'tl_calendar_events',
        'enableVersioning'  => true,
        'onsubmit_callback' => [],
        'onload_callback'   => [
            [
                CalendarEventsMember::class,
                'downloadEventRegistrations',
            ],
        ],
        'ondelete_callback' => [],
        'sql'               => array(
            'keys'          => array(
                'id'        => 'primary'
            )
        ),
    ),
    'list'              => array(
        'sorting'           => array(
            'mode'              => DataContainer::MODE_SORTABLE,
            'fields'            => array('title', 'serialnumber', 'size', 'lastCheckDate', 'nextCheckDate'),
            'flag'              => DataContainer::SORT_INITIAL_LETTERS_DESC,
            'panelLayout'       => 'filter;sort,search,limit'
        ),
        'label'             => array(
            'fields'            => array('title', 'serialnumber', 'size', 'lastCheckDate', 'nextCheckDate'),
            'format'            => '%s - %s %s L - Letzter TÜV %s nächster TÜV %s ',
            'label_callback'    => array('tl_dw_tanks', 'formatCheckDates'),
        ),
        'global_operations' => array(
            'all'               => array(
                'href'          => 'act=select',
                'class'         => 'header_edit_all',
                'attributes'    => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations'        => array(
            'edit'              => array(
                'href'          => 'act=edit',
                'icon'          => 'edit.svg'
            ),
            'copy'          => array(
                'href'          => 'act=copy',
                'icon'          => 'copy.svg'
            ),
            'delete'        => array(
                'href'          => 'act=delete',
                'icon'          => 'delete.svg',
                'attributes'    => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show'          => array(
                'href'          => 'act=show',
                'icon'          => 'show.svg',
                'attributes'    => 'style="margin-right:3px"'
            ),
            'toggle'        => array
            (
                'href'          => 'act=toggle&amp;field=published',
                'icon'          => 'visible.svg',
                'showInHeader'  => true
            )
        )
    ),
    'palettes'          => array(
        '__selector__'      => array('addSubpalette'),
        'default'           => '{first_legend},title,alias;{details_section},serialnumber,size,member,lastCheckDate,nextCheckDate;
                                {notes_legend},addSubpalette;
                                {publish_legend},published,start,stop'
    ),
    'subpalettes'       => array(
        'addSubpalette'     => 'notes',
    ),
    'fields'            => array(
        'id'                => array(
            'sql'           => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid'           => array
        (
            'foreignKey'    => 'tl_checks.title',
            'sql'           => "int(10) unsigned NOT NULL default 0",
            'relation'      => array('type'=>'belongsTo', 'load'=>'lazy')
        ),
        'tstamp'        => array(
            'sql'           => "int(10) unsigned NOT NULL default '0'"
        ),
        'title'         => array(
            'inputType'     => 'text',
            'exclude'       => true,
            'search'        => true,
            'filter'        => true,
            'sorting'       => true,
            'reference'     => &$GLOBALS['TL_LANG']['tl_dw_tanks'],
            'flag'          => DataContainer::SORT_INITIAL_LETTER_ASC,
            'eval'          => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'           => "varchar(255) NOT NULL default ''"
        ),
        'alias'         => array
        (
            'search'        => true,
            'inputType'     => 'text',
            'eval'          => array('rgxp'=>'alias', 'doNotCopy'=>true, 'unique'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
            'save_callback' => array
            (
                array('tl_dw_tanks', 'generateAlias')
            ),
            'sql'           => "varchar(255) BINARY NOT NULL default ''"
        ),
        'serialnumber'  => array(
            'inputType'     => 'text',
            'exclude'       => true,
            'search'        => true,
            'filter'        => true,
            'sorting'       => true,
            'flag'          => DataContainer::SORT_ASC,
            'eval'          => array('mandatory' => true, 'maxlength' => 25, 'tl_class' => 'w25'),
            'sql'           => "varchar(255) NOT NULL default ''"
        ),
        'size'          => array(
            'inputType'     => 'select',
            'exclude'       => true,
            'search'        => true,
            'filter'        => true,
            'sorting'       => true,
            'reference'     => &$GLOBALS['TL_LANG']['tl_dw_tanks'],
            'options'       => array('3','5','7','8','10','12','15','18','20'),
            'eval'          => array('includeBlankOption' => true, 'tl_class' => 'w25'),
            'sql'           => "varchar(255) NOT NULL default ''",
        ),
        'lastCheckDate' => array
        (
            'inputType'     => 'text',
            'eval'          => array('submitOnChange' => true, 'rgxp'=>'date', 'mandatory'=>true, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
            'save_callback' => array
            (
                array('tl_dw_tanks', 'calculateNextCheckDate')
            ),
            'sql' => "bigint(20) NULL"
        ),
        'nextCheckDate' => array
        (
            'inputType'     => 'text',
            'eval'          => array('rgxp'=>'date', 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
            'sql'           => "bigint(20) NULL"
        ),
        'member'        => array(
            'inputType'     => 'select',
            'exclude'       => true,
            'search'        => true,
            'filter'        => true,
            'sorting'       => true,
            'reference'     => &$GLOBALS['TL_LANG']['tl_dw_tanks'],
            'foreignKey'    => 'tl_user.name',
            'eval'          => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w33'),
            'sql'           => "varchar(255) NOT NULL default ''",
        ),
        'addSubpalette' => array(
            'exclude'       => true,
            'inputType'     => 'checkbox',
            'eval'          => array('submitOnChange' => true, 'tl_class' => 'w50'),
            'sql'           => "char(1) NOT NULL default ''"
        ),
        'notes'         => array(
            'inputType'     => 'textarea',
            'exclude'       => true,
            'search'        => false,
            'filter'        => true,
            'sorting'       => false,
            'eval'          => array('rte' => 'tinyMCE', 'tl_class' => 'clr'),
            'sql'           => 'text NULL'
        ),
        'published'     => array
        (
            'toggle'        => true,
            'filter'        => true,
            'flag'          => DataContainer::SORT_INITIAL_LETTER_DESC,
            'inputType'     => 'checkbox',
            'eval'          => array('doNotCopy'=>true, 'tl_class' => 'w50'),
            'sql'           => array('type' => 'boolean', 'default' => false)
        ),
        'start'         => array
        (
            'inputType'     => 'text',
            'eval'          => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 clr wizard'),
            'sql'           => "varchar(10) NOT NULL default ''"
        ),
        'stop'          => array
        (
            'inputType'     => 'text',
            'eval'          => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
            'sql'           => "varchar(10) NOT NULL default ''"
        )
    )
);

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @property Tanks $Tanks
 *
 * @internal
 */
class tl_dw_tanks extends Backend
{
    /**
     * Auto-generate the event alias if it has not been set yet
     *
     * @param mixed $varValue
     * @param DataContainer $dc
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function generateAlias($varValue, DataContainer $dc)
    {
        $aliasExists = static function (string $alias) use ($dc): bool {
            $result = Database::getInstance()
                ->prepare("SELECT id FROM tl_dw_tanks WHERE alias=? AND id!=?")
                ->execute($alias, $dc->id);

            return $result->numRows > 0;
        };

        // Generate the alias if there is none
        if (!$varValue) {
            $varValue = System::getContainer()->get('contao.slug')->generate(
                $dc->activeRecord->title,
                [],
                $aliasExists
            );
        } elseif (preg_match('/^[1-9]\d*$/', $varValue)) {
            throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasNumeric'], $varValue));
        } elseif ($aliasExists($varValue)) {
            throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
        }

        return $varValue;
    }

    public function calculateNextCheckDate($varValue, DataContainer $dc)
    {
        if (!$varValue) {
            return $varValue;
        }

        $lastCheckDate = new \DateTime('@'.$varValue);
        $lastCheckDate->modify('+2 years');

        $nextCheckDate = $lastCheckDate->getTimestamp();

        $updateStmt = Database::getInstance()
            ->prepare("UPDATE tl_dw_tanks SET nextCheckDate=? WHERE id=?");

        $updateStmt->execute($nextCheckDate, $dc->id);

        // The new value of lastCheckDate is returned to be saved in the database.
        return $varValue;
    }

    function formatCheckDates($row, $label) {
        $title = isset($row['title']) ? $row['title'] : '';
        $serialnumber = isset($row['serialnumber']) ? $row['serialnumber'] : '';
        $size = isset($row['size']) ? $row['size'] : '';

        $lastCheckDate = isset($row['lastCheckDate']) && is_numeric($row['lastCheckDate'])
            ? date('d.m.Y', $row['lastCheckDate'])
            : 'N/A';

        $nextCheckDate = isset($row['nextCheckDate']) && is_numeric($row['nextCheckDate'])
            ? date('d.m.Y', $row['nextCheckDate'])
            : 'N/A';

        return sprintf('Inventarnr. %s - Seriennr. %s Größe %s L - Letzter TÜV %s - naechster TÜV %s',
            $title,
            $serialnumber,
            $size,
            $lastCheckDate,
            $nextCheckDate
        );
    }
}
