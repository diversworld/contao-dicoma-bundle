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
use Contao\Input;
use Contao\System;
use Diversworld\ContaoDicomaBundle\DataContainer\Tanks;

/**
 * Table tl_dw_tanks
 */
$GLOBALS['TL_DCA']['tl_dw_tanks'] = array(
    'config'            => array(
        'dataContainer'     => DC_Table::class,
        'ptable'            => 'tl_calendar_events',
        'enableVersioning'  => true,
        'onsubmit_callback' => [],
        'onload_callback'   => array('tl_dw_tanks', 'filterTanksByEventId'),
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
            'fields'            => array('member, lastCheckDate, nextCheckDate, o2clean'),
            'flag'              => DataContainer::SORT_ASC,
            'panelLayout'       => 'filter;sort,search,limit',
        ),
        'label'             => array(
            'fields'            => array('title', 'serialNumber', 'size', 'o2clean', 'lastCheckDate', 'nextCheckDate', 'member'),
            'showColumns'       => false,
            'format'            => '%s',
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
            'toggle'        => array(
                'href'          => 'act=toggle&amp;field=published',
                'icon'          => 'visible.svg',
                'showInHeader'  => true
            )
        ),
    ),
    'palettes'          => array(
        '__selector__'      => array('addSubpalette'),
        'default'           => '{title_legend},title,alias;
                                {details_legend},serialNumber,size,o2clean,member,pid,lastCheckDate,nextCheckDate;
                                {notes_legend},addSubpalette;
                                {publish_legend},published,start,stop;'
    ),
    'subpalettes'       => array(
        'addSubpalette'     => 'notes',
    ),
    'fields'            => array(
        'id'                => array(
            'sql'               => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid'               => array(
            'inputType'         => 'select',
            'foreignKey'        => 'tl_calendar_events.title',
            'eval'              => ['submitOnChange' => true,'mandatory'=>false, 'includeBlankOption'=>true, 'tl_class' => 'w33 clr'],
            'sql'               => "int(10) unsigned NOT NULL default 0",
            'relation'          => array('type'=>'hasOne', 'load'=>'lazy'),
            'save_callback'     => array(
                array('tl_dw_tanks', 'setLastCheckDate')
            ),
            'options_callback'  => function() {
                $db = Contao\Database::getInstance();
                $result = $db->execute("SELECT id, title FROM tl_calendar_events WHERE addCheckInfo = '1'");

                $options = [];
                while($result->next()) {
                    $options[$result->id] = $result->title;
                }
                return $options;
            }
        ),
        'tstamp'            => array(
            'sql'               => "int(10) unsigned NOT NULL default '0'"
        ),
        'title'                 => array(
            'inputType'         => 'text',
            'exclude'           => true,
            'search'            => true,
            'filter'            => true,
            'sorting'           => true,
            'flag'              => DataContainer::SORT_INITIAL_LETTER_ASC,
            'eval'              => array('mandatory' => true, 'maxlength' => 25, 'tl_class' => 'w50'),
            'sql'               => "varchar(255) NOT NULL default ''"
        ),
        'alias'             => array(
            'search'            => true,
            'inputType'         => 'text',
            'eval'              => array('rgxp'=>'alias', 'doNotCopy'=>true, 'unique'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
            'save_callback'     => array(
                array('tl_dw_tanks', 'generateAlias')
            ),
            'sql'           => "varchar(255) BINARY NOT NULL default ''"
        ),
        'serialNumber'      => array(
            'inputType'         => 'text',
            'exclude'           => true,
            'search'            => true,
            'filter'            => true,
            'sorting'           => true,
            'flag'              => DataContainer::SORT_INITIAL_LETTER_ASC,
            'eval'              => array('mandatory' => true, 'maxlength' => 25, 'tl_class' => 'w25'),
            'sql'               => "varchar(50) NOT NULL default ''"
        ),
        'size'              => array(
            'inputType'         => 'select',
            'exclude'           => true,
            'search'            => true,
            'filter'            => true,
            'sorting'           => true,
            'reference'         => &$GLOBALS['TL_LANG']['tl_dw_tanks']['sizes'],
            'options'           => array('2','3','5','7','8','10','12','15','18','20','40','80'),
            'eval'              => array('includeBlankOption' => true, 'tl_class' => 'w25'),
            'sql'               => "varchar(20) NOT NULL default ''",
        ),
        'o2clean'           => array(
            'exclude'           => true,
            'filter'            => true,
            'inputType'         => 'checkbox',
            'eval'              => array('submitOnChange' => true, 'tl_class' => 'w50'),
            'sql'               => "char(1) NOT NULL default ''"
        ),
        'lastCheckDate'     => array(
            'inputType'         => 'text',
            'sorting'           => true,
            'filter'            => true,
            'flag'              => DataContainer::SORT_YEAR_DESC,
            'eval'              => array('submitOnChange' => true, 'rgxp'=>'date', 'mandatory'=>false, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w33 wizard'),
            'sql'               => "bigint(20) NULL"
        ),
        'nextCheckDate'     => array(
            'inputType'         => 'text',
            'sorting'           => true,
            'filter'            => true,
            'flag'              => DataContainer::SORT_YEAR_DESC,
            'eval'              => array('submitOnChange' => true,'rgxp'=>'date', 'doNotCopy'=>false, 'datepicker'=>true, 'tl_class'=>'w33 wizard'),
            'sql'               => "bigint(20) NULL"
        ),
        'member'            => array(
            'inputType'         => 'select',
            'exclude'           => true,
            'search'            => true,
            'filter'            => true,
            'sorting'           => true,
            'eval'              => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w33'),
            'options_callback'  => array('tl_dw_tanks', 'getMemberOptions'),
            'sql'               => "varchar(255) NOT NULL default ''",
        ),
        'addSubpalette'     => array(
            'exclude'           => true,
            'inputType'         => 'checkbox',
            'eval'              => array('submitOnChange' => true, 'tl_class' => 'w50'),
            'sql'               => "char(1) NOT NULL default ''"
        ),
        'notes'             => array(
            'inputType'         => 'textarea',
            'exclude'           => true,
            'search'            => false,
            'filter'            => false,
            'sorting'           => false,
            'eval'              => array('rte' => 'tinyMCE', 'tl_class' => 'clr'),
            'sql'               => 'text NULL'
        ),
        'published'         => array(
            'toggle'            => true,
            'filter'            => true,
            'flag'              => DataContainer::SORT_INITIAL_LETTER_DESC,
            'inputType'         => 'checkbox',
            'eval'              => array('doNotCopy'=>true, 'tl_class' => 'w50'),
            'sql'               => array('type' => 'boolean', 'default' => false)
        ),
        'start'             => array(
            'inputType'         => 'text',
            'eval'              => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 clr wizard'),
            'sql'               => "varchar(10) NOT NULL default ''"
        ),
        'stop'              => array(
            'inputType'         => 'text',
            'eval'              => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
            'sql'               => "varchar(10) NOT NULL default ''"
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

    function formatCheckDates($row, $label)
    {
        $logger = System::getContainer()->get('monolog.logger.contao');

        $members = $this->getMemberOptions(); // Add this line to get member options stucture
        $memberName = isset($members[$row['member']]) ? $members[$row['member']] : 'N/A';

        $title = $row['title'] ?? '';
        $serialnumber = $row['serialNumber'] ?? '';
        $size = $row['size'] ?? '';
        $invoices = $this->listChildren($row);
        $lastTotal = $this->getLastInvoiceTotal($row);

        if($row['o2clean'] == 1){
            $o2CleanValue = 'ja';
        } else {
            $o2CleanValue = 'nein';
        }

        $lastCheckDate = isset($row['lastCheckDate']) && is_numeric($row['lastCheckDate'])
            ? date('d.m.Y', $row['lastCheckDate'])
            : 'N/A';

        $nextCheckDate = isset($row['nextCheckDate']) && is_numeric($row['nextCheckDate'])
            ? date('d.m.Y', $row['nextCheckDate'])
            : 'N/A';

        $logger->info(
            'Label-String: '. print_r($title, true) . ' - ' .print_r($serialnumber, true) . ' - ' .print_r($size .'L', true) . ' - ' .print_r($o2CleanValue, true) . ' - ' .print_r($lastCheckDate, true) . ' - ' .print_r($nextCheckDate, true) . ' - ' .print_r($memberName, true),
            ['contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL)]
        );

        if($invoices == 1) {
            return sprintf(' %s - %s - %s L - O2: %s - %s - letzter TÜV %s - nächster TÜV %s <span style="color:#b3b3b3; padding-left:4px;">[ %s Rechnung ] [ letzte Rechnung: %s € ]</span>',
                $title,
                $serialnumber,
                $size,
                $o2CleanValue,
                $memberName,
                $lastCheckDate,
                $nextCheckDate,
                $invoices,
                $lastTotal
            );
        }elseif ($invoices >= 2) {
            return sprintf('%s - %s - %s L - O2: %s - %s - letzter TÜV %s - nächster TÜV %s <span style="color:#b3b3b3; padding-left:4px;">[%s Rechnungen] [letzte Rechnung: %s€]</span>',
                $title,
                $serialnumber,
                $size,
                $o2CleanValue,
                $memberName,
                $lastCheckDate,
                $nextCheckDate,
                $invoices,
                $lastTotal
            );
        } else {
            return sprintf('%s - %s - %s L - O2: %s - %s - letzter TÜV %s - nächster TÜV %s',
                $title,
                $serialnumber,
                $size,
                $o2CleanValue,
                $memberName,
                $lastCheckDate,
                $nextCheckDate
            );
        }
    }

    public function setLastCheckDate($varValue, DataContainer $dc)
    {
        if ($varValue)
        {
            // Holen Sie das startDate des ausgewählten TÜV-Termins
            $db = Contao\Database::getInstance();
            $result = $db->prepare("SELECT startDate FROM tl_calendar_events WHERE id = ?")
                ->execute($varValue);

            $row = $result->fetchAssoc();

            $lastCheckDate = new \DateTime('@'.$row['startDate']);
            $lastCheckDate->modify('+2 years');

            $nextCheckDate = $lastCheckDate->getTimestamp();

            // Setzen Sie lastCheckDate auf das startDate des ausgewählten TÜV-Termins
            $updateStmt = Database::getInstance()
                ->prepare("UPDATE tl_dw_tanks SET lastCheckDate = ?, nextCheckDate = ? WHERE id = ?");
            $updateStmt->execute($row['startDate'], $nextCheckDate, $dc->id);
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

    public function getLastInvoiceTotal($arrRow)
    {
        $tankId = $arrRow['id'];

        $lastInvoice = Database::getInstance()
            ->prepare("SELECT priceTotal AS total FROM tl_dw_check_invoice WHERE pid = ?")
            ->execute($tankId)
            ->fetchAssoc()['total'];

        return $lastInvoice;
    }

    public function listChildren($arrRow)
    {
        // Get the ID of the current tank
        $tankId = $arrRow['id'];

        // Query the database to find the number of invoices related to this tank
        $numberOfInvoices = Database::getInstance()
            ->prepare("SELECT COUNT(*) AS count FROM tl_dw_check_invoice WHERE pid = ?")
            ->execute($tankId)
            ->fetchAssoc()['count'];

        // Return the count of invoices
        return $numberOfInvoices;
    }

    public function filterTanksByEventId(DataContainer $dc)
    {
        if (Input::get('do') == 'calendar' && ($eventId = Input::get('event_id')) !== null) {
            $GLOBALS['TL_DCA']['tl_dw_tanks']['list']['sorting']['filter'] = [['pid=?', $eventId]];
        }
    }
}
