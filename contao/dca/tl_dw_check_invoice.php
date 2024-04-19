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
use Diversworld\ContaoDicomaBundle\DataContainer\CalendarEvents;
use Diversworld\ContaoDicomaBundle\DataContainer\Tanks;

/**
 * Table tl_dw_check_invoice
 */
$GLOBALS['TL_DCA']['tl_dw_check_invoice'] = array(
    'config'            => array(
        'dataContainer'     => DC_Table::class,
        'ptable'            => 'tl_dw_tanks',
        'enableVersioning'  => true,
        'onsubmit_callback' => [],
        'onload_callback'   => [],
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
            'fields'            => array('title', 'alias', 'pid','member','priceTotal','published'),
            'flag'              => DataContainer::SORT_DESC,
            'panelLayout'       => 'filter;sort,search,limit'
        ),
        'label'             => array(
            'fields'            => array('title', 'priceTotal'),
            'showColumns'       => true,
            'showFirstOrderBy'  => false,
            'format'            => '%s Gesamtpreis %s €',
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
        '__selector__'      => array(),
        'default'           => '{title_legend},title,alias;
                                {details_legend},member,invoiceArticles, priceTotal, notes;
                                {publish_legend},published,start,stop;'
    ),
    'subpalettes'       => array(),
    'fields'            => array(
        'id'                => array(
            'sql'           => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid'           => [
            'inputType'     => 'text',
            'foreignKey'    => 'tl_dw_tanks.title',
            'eval'          => ['submitOnChange' => true,'mandatory'=>true, 'tl_class' => 'w33 clr'],
            'sql'           => "int(10) unsigned NOT NULL default 0",
            'relation'      => array('type'=>'hasOne', 'load'=>'lazy'),
        ],
        'tstamp'        => array(
            'sql'           => "int(10) unsigned NOT NULL default '0'"
        ),
        'title'         => array(
            'inputType'     => 'text',
            'exclude'       => true,
            'search'        => true,
            'filter'        => true,
            'sorting'       => true,
            'flag'          => DataContainer::SORT_INITIAL_LETTER_ASC,
            'eval'          => array('mandatory' => true, 'maxlength' => 25, 'tl_class' => 'w33'),
            'sql'           => "varchar(255) NOT NULL default ''"
        ),
        'alias'         => array
        (
            'search'        => true,
            'inputType'     => 'text',
            'eval'          => array('rgxp'=>'alias', 'doNotCopy'=>true, 'unique'=>true, 'maxlength'=>255, 'tl_class'=>'w33'),
            'save_callback' => array
            (
                array('tl_dw_check_invoice', 'generateAlias')
            ),
            'sql'           => "varchar(255) BINARY NOT NULL default ''"
        ),
        'member'        => array(
            'inputType'     => 'select',
            'exclude'       => true,
            'search'        => true,
            'filter'        => true,
            'sorting'       => true,
            'reference'     => &$GLOBALS['TL_LANG']['tl_dw_tanks'],
            'foreignKey'    => 'tl_member.id',
            'eval'          => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w33 clr'),
            'options_callback' => array('tl_dw_check_invoice', 'getMemberOptions'),
            'sql'           => "varchar(255) NOT NULL default ''",
        ),
        'invoiceArticles' => array(
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
                            'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['articleName'],
                            'inputType' => 'select',
                            'options'   => ['2','3','5','7','8','10','12','15','18','20','40','80'],
                            'eval'      => ['includeBlankOption' => true, 'groupStyle' => 'width:300px']
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
                ]
            ],
            'sql'       => "blob NULL"
        ),
        'priceTotal'         => array
        (
            'inputType'     => 'text',
            'eval'          => array('tl_class'=>'w25 clr'),
            'sql'           => "varchar(10) NOT NULL default ''"
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
class tl_dw_check_invoice extends Backend
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
                ->prepare("SELECT id FROM tl_dw_check_invoice WHERE alias=? AND id!=?")
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

    public function getMemberOptions()
    {
        $members = Database::getInstance()->execute("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM tl_member")->fetchAllAssoc();
        $options = array();

        foreach($members as $member)
        {
            $options[$member['id']] = $member['name'];
        }

        return $options;
    }
}
