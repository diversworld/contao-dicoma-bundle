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
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\System;
use Diversworld\ContaoDicomaBundle\DataContainer\CalendarEvents;
use Diversworld\ContaoDicomaBundle\DataContainer\CheckInvoice;
use Diversworld\ContaoDicomaBundle\DataContainer\Tanks;

/**
 * Table tl_dw_check_invoice
 */
$GLOBALS['TL_DCA']['tl_dw_check_invoice'] = array(
    'config'      => array(
        'dataContainer'     => DC_Table::class,
        'ptable'            => 'tl_dw_tanks',
        'ctable'            => array('tl_content'),
        'enableVersioning'  => true,
        'sql'               => array(
            'keys' => array(
                'id'        => 'primary',
                'tstamp'    => 'index',
                'alias'     => 'index',
                'published,start,stop' => 'index'
            )
        ),
    ),
    'list'        => array(
        'sorting'           => array(
            'mode'          => DataContainer::MODE_SORTABLE,
            'fields'        => array('title','alias','published'),
            'flag'          => DataContainer::SORT_ASC,
            'panelLayout'   => 'filter;sort,search,limit'
        ),
        'label'             => array(
            'fields' => array('title','priceTotal'),
            'format' => '%s %s €',
        ),
        'global_operations' => array(
            'all' => array(
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations'        => array(
            'edit'   => array(
                'href'          => 'act=edit',
                'icon'          => 'edit.svg'
            ),
            'children',
            'copy'   => array(
                'href'          => 'act=copy',
                'icon'          => 'copy.svg'
            ),
            'delete' => array(
                'href'          => 'act=delete',
                'icon'          => 'delete.svg',
                'attributes'    => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show'   => array(
                'href'          => 'act=show',
                'icon'          => 'show.svg',
                'attributes'    => 'style="margin-right:3px"'
            ),
            'toggle' => array
            (
                'href'          => 'act=toggle&amp;field=published',
                'icon'          => 'visible.svg',
                'showInHeader'  => true
            )
        )
    ),
    'palettes'          => array(
        '__selector__'      => array('addArticleInfo'),
        'default'           => '{title_legend},title,alias,pid;
                                {details_legend},member,checkId;
                                {article_legend},invoiceArticles,priceTotal;
                                {notes_legend},notes;
                                {publish_legend},published,start,stop;'
    ),
    'subpalettes'       => array(
    ),
    'fields'            => array(
        'id'                => array(
            'sql'           => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid'           => [
            'inputType'     => 'text',
            'foreignKey'    => 'tl_dw_tanks.title',
            'eval'          => ['submitOnChange' => true,'mandatory'=>true, 'tl_class' => 'w33 clr'],
            'sql'           => "int(10) unsigned NOT NULL default 0",
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
        'member'            => array(
            'inputType'         => 'select',
            'exclude'           => true,
            'search'            => true,
            'filter'            => true,
            'sorting'           => true,
            'eval'              => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w33'),
            'options_callback'  => array('tl_dw_check_invoice', 'getMemberOptions'),
            'sql'               => "varchar(255) NOT NULL default ''",
        ),
        'checkId'           => array(
            'inputType'     => 'text',
            'foreignKey'    => 'tl_dw_tanks.pid',
            'eval'          => ['submitOnChange' => true,'mandatory'=>true, 'tl_class' => 'w33 '],
            'sql'           => "int(10) unsigned NOT NULL default 0",
        ),
        'invoiceArticles'  => array(
            'inputType' => 'multiColumnEditor',
            'eval'      => [
                'tl_class' => 'clr compact',
                'multiColumnEditor' => [
                    'skipCopyValuesOnAdd' => false,
                    'editorTemplate' => 'multi_column_editor_backend_default',
                    'fields' => [
                        'articleName' => [
                            'label' => &$GLOBALS['TL_LANG']['tl_dw_check_invoice']['articleName'],
                            'inputType' => 'text',
                            'eval' => ['groupStyle' => 'width:300px']
                        ],
                        'articleSize' => [
                            'label'     => &$GLOBALS['TL_LANG']['tl_dw_check_invoice']['articleSize'],
                            'inputType' => 'select',
                            'options'   => ['2','3','5','7','8','10','12','15','18','20','40','80'],
                            'eval'      => ['includeBlankOption' => true, 'groupStyle' => 'width:60px']
                        ],
                        'articleNotes'  => [
                            'label'     => &$GLOBALS['TL_LANG']['tl_dw_check_invoice']['articleNotes'],
                            'inputType' => 'textarea',
                            'eval'      => ['groupStyle' => 'width:400px']
                        ],
                        'articlePriceNetto' => [
                            'label'     => &$GLOBALS['TL_LANG']['tl_dw_check_invoice']['articlePriceNetto'],
                            'inputType' => 'text',
                            'eval'      => ['groupStyle' => 'width:100px', 'submitOnChange' => true],
                            'save_callback' => [CheckInvoice::class, 'calculateAllGrossPrices'],
                        ],
                        'articlePriceBrutto' => [
                            'label'     => &$GLOBALS['TL_LANG']['tl_dw_check_invoice']['articlePriceBrutto'],
                            'inputType' => 'text',
                            'eval'      => ['groupStyle' => 'width:100px']
                        ],
                        'default' => [
                            'label'     => &$GLOBALS['TL_LANG']['tl_dw_check_invoice']['default'],
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
            'save_callback' => ['tl_dw_check_invoice', 'calculateTotalPrice'],
            'sql'           => "DECIMAL(10,2) NOT NULL default '0.00'"
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

    public function calculateTotalPrice($varValue, DataContainer $dc)
    {
        // Get invoiceArticles from the current record
        $invoiceArticles = unserialize($dc->activeRecord->invoiceArticles);

        // Calculate total price
        $totalPrice = array_reduce($invoiceArticles, function ($total, $article) {
            return $total + str_replace(',', '.', $article['articlePriceBrutto']);
        }, 0);

        // Return the total price
        return $totalPrice;
    }
    /*
    public function getCheckArticlesOptions(DataContainer $dc)
    {
        // Zugriff auf PID des aktuellen Datensatzes
        $tankId = $dc->activeRecord->pid;

        $tankPid = Database::getInstance()->prepare("SELECT pid FROM tl_dw_tanks WHERE id=?")->execute($tankId)->fetchAssoc();

        // Zugriff auf das 'checkArticles' Feld des Events
        $eventRecord = Database::getInstance()->prepare("SELECT checkArticles FROM tl_calendar_events WHERE id=?")->execute($tankPid['pid'])->fetchAssoc();
        $checkArticles = unserialize($eventRecord['checkArticles']);

        // Baue die Optionen für das 'invoiceArticles' Feld auf
        $options = array();
        foreach($checkArticles as $key => $value)
        {
            $options[$key] = $value;
        }

        return $options;
    }*/
}
