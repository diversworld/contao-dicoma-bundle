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
use Contao\Backend;
use Contao\BackendUser;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\System;
use Diversworld\ContaoDicomaBundle\DataContainer\Courses;
use Diversworld\ContaoDicomaBundle\Model\CoursesModel;

/**
 * Table tl_dw_courses
 */
$GLOBALS['TL_DCA']['tl_dw_courses'] = array(
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
            'fields'        => array('title','alias','category','published'),
            'flag'          => DataContainer::SORT_ASC,
            'panelLayout'   => 'filter;sort,search,limit'
        ),
        'label'             => array(
            'fields' => array('title','alias','category','published'),
            'format' => '%s',
        ),
        'global_operations' => array(
            'all' => array(
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations'        => array(
            'edit',
            'children',
            'copy',
            'delete',
            'show',
            'toggle'
        )
    ),
    'palettes'    => array(
        '__selector__' => array('addImage', 'overwriteMeta'),
        'default'      => '{first_legend},title,alias,category;
                           {details_section},description,requirements;
                           {image_legend},addImage;
                           {publish_legend},published,start,stop'
    ),
    'subpalettes' => array(
        'addImage'      => 'singleSRC,fullsize,size,floating,overwriteMeta',
        'overwriteMeta' => 'alt,imageTitle,imageUrl,caption'
    ),
    'fields'      => array(
        'id'        => array(
            'sql'       => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp'        => array(
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'title'     => array(
            'inputType' => 'text',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'flag'      => DataContainer::SORT_ASC,
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'alias'     => array
        (
            'search'    => true,
            'inputType' => 'text',
            'eval'      => array('rgxp'=>'alias', 'doNotCopy'=>true, 'unique'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
            'save_callback' => array
            (
                array('tl_dw_courses', 'generateAlias')
            ),
            'sql'       => "varchar(255) BINARY NOT NULL default ''"
        ),
        'pid'       => [
            'inputType' => 'text',
            'foreignKey'=> 'tl_dw_tanks.title',
            'eval'      => ['submitOnChange' => true,'mandatory'=>true, 'tl_class' => 'w33 clr'],
            'sql'       => "int(10) unsigned NOT NULL default 0",
        ],
        'description' => array(
            'inputType' => 'textarea',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'eval'      => array('rte' => 'tinyMCE', 'tl_class' => 'clr'),
            'sql'       => 'text NULL'
        ),
        'requirements'  => array(
            'inputType' => 'textarea',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'eval'      => array('rte' => 'tinyMCE', 'tl_class' => 'clr'),
            'sql'       => 'text NULL'
        ),
        'addArticleList'=> array(
            'inputType' => 'checkbox',
            'eval'      => array('submitOnChange'=>true),
            'sql'       => array('type' => 'boolean', 'default' => false)
        ),
        'category'    => array(
            'inputType' => 'select',
            'exclude'   => true,
            'search'    => true,
            'filter'    => true,
            'sorting'   => true,
            'reference' => &$GLOBALS['TL_LANG']['tl_dw_courses'],
            'options'   => array('basic', 'specialty', 'professional'),
            'eval'      => array('includeBlankOption' => true, 'tl_class' => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''",
        ),
        'addImage' => array
        (
            'inputType' => 'checkbox',
            'eval'      => array('submitOnChange'=>true),
            'sql'       => array('type' => 'boolean', 'default' => false)
        ),
        'overwriteMeta' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['overwriteMeta'],
            'inputType' => 'checkbox',
            'eval'      => array('submitOnChange'=>true, 'tl_class'=>'w50 clr'),
            'sql'       => array('type' => 'boolean', 'default' => false)
        ),
        'singleSRC' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['singleSRC'],
            'inputType' => 'fileTree',
            'eval'      => array('filesOnly'=>true, 'fieldType'=>'radio', 'extensions'=>'%contao.image.valid_extensions%', 'mandatory'=>true),
            'sql'       => "binary(16) NULL"
        ),
        'alt' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['alt'],
            'search'    => true,
            'inputType' => 'text',
            'eval'      => array('maxlength'=>255, 'tl_class'=>'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'imageTitle' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['imageTitle'],
            'search'    => true,
            'inputType' => 'text',
            'eval'      => array('maxlength'=>255, 'tl_class'=>'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'size' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['MSC']['imgSize'],
            'inputType' => 'imageSize',
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval'      => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50 clr'),
            'options_callback' => static function () {
                return System::getContainer()->get('contao.image.sizes')->getOptionsForUser(BackendUser::getInstance());
            },
            'sql'           => "varchar(64) NOT NULL default ''"
        ),
        'imageUrl' => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_content']['imageUrl'],
            'search'        => true,
            'inputType'     => 'text',
            'eval'          => array('rgxp'=>'url', 'decodeEntities'=>true, 'maxlength'=>2048, 'dcaPicker'=>true, 'tl_class'=>'w50 wizard'),
            'sql'           => "varchar(2048) NOT NULL default ''"
        ),
        'fullsize' => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_content']['fullsize'],
            'inputType'     => 'checkbox',
            'eval'          => array('tl_class'=>'w50'),
            'sql'           => array('type' => 'boolean', 'default' => false)
        ),
        'caption' => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_content']['caption'],
            'search'        => true,
            'inputType'     => 'text',
            'eval'          => array('maxlength'=>255, 'allowHtml'=>true, 'tl_class'=>'w50'),
            'sql'           => "varchar(255) NOT NULL default ''"
        ),
        'floating' => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_content']['floating'],
            'inputType'     => 'radioTable',
            'options'       => array('above', 'left', 'right', 'below'),
            'eval'          => array('cols'=>4, 'tl_class'=>'w50'),
            'reference'     => &$GLOBALS['TL_LANG']['MSC'],
            'sql'           => "varchar(32) NOT NULL default 'above'"
        ),
        'checkboxField'  => array(
            'inputType'     => 'select',
            'exclude'       => true,
            'search'        => true,
            'filter'        => true,
            'sorting'       => true,
            'reference'     => &$GLOBALS['TL_LANG']['tl_dw_courses'],
            'options'       => array('firstoption', 'secondoption'),
            'eval'          => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'),
            'sql'           => "varchar(255) NOT NULL default ''",
        ),
        'remarks'  => array(
            'inputType'     => 'textarea',
            'exclude'       => true,
            'search'        => true,
            'filter'        => true,
            'sorting'       => true,
            'eval'          => array('rte' => 'tinyMCE', 'tl_class' => 'clr'),
            'sql'           => 'text NULL'
        ),
        'published' => array
        (
            'toggle'        => true,
            'filter'        => true,
            'flag'          => DataContainer::SORT_INITIAL_LETTER_DESC,
            'inputType'     => 'checkbox',
            'eval'          => array('doNotCopy'=>true, 'tl_class' => 'w50 clr'),
            'sql'           => array('type' => 'boolean', 'default' => false)
        ),
        'start' => array
        (
            'inputType'     => 'text',
            'eval'          => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'clr w50 wizard'),
            'sql'           => "varchar(10) NOT NULL default ''"
        ),
        'stop' => array
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
 * @property Courses $Courses
 *
 * @internal
 */
class tl_dw_courses extends Backend
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
    public function generateAlias(mixed $varValue, DataContainer $dc): mixed
    {
        $aliasExists = static function (string $alias) use ($dc): bool {
            $result = Database::getInstance()
                ->prepare("SELECT id FROM tl_dw_courses WHERE alias=? AND id!=?")
                ->execute($alias, $dc->id);

            return $result->numRows > 0;
        };

        // Generate the alias if there is none
        if (!$varValue)
        {
            $varValue = System::getContainer()->get('contao.slug')->generate($dc->activeRecord->title, CoursesModel::findById($dc->activeRecord->pid)->jumpTo, $aliasExists);
        }
        elseif (preg_match('/^[1-9]\d*$/', $varValue))
        {
            throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasNumeric'], $varValue));
        }
        elseif ($aliasExists($varValue))
        {
            throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
        }

        return $varValue;
    }
}
