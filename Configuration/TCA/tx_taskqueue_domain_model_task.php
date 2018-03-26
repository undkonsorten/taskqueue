<?php

return [
    'ctrl' => array(
        'title'	=> 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => TRUE,

        'versioningWS' => 2,
        'versioning_followPages' => TRUE,

        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'type' => 'type',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        'searchFields' => 'name,data,status,start_date,message,priority,',
        'iconfile' => 'EXT:taskqueue/Resources/Public/Icons/tx_taskqueue_domain_model_task.gif'
    ),
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, data, status, start_date, message, priority',
    ),
    'types' => array(
        '1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, name, data, status, start_date, message, priority, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
    ),
    'columns' => array(

        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages', -1),
                    array('LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.default_value', 0)
                ),
            ),
        ),
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_taskqueue_domain_model_task',
                'foreign_table_where' => 'AND tx_taskqueue_domain_model_task.pid=###CURRENT_PID### AND tx_taskqueue_domain_model_task.sys_language_uid IN (-1,0)',
            ),
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),

        't3ver_label' => array(
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            )
        ),

        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'starttime' => array(
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ),
            ),
        ),
        'endtime' => array(
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ),
            ),
        ),

        'name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.name',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'data' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.data',
            'config' => array(
                'type' => 'text',
                'cols' => 40,
                'rows' => 6
            ),
        ),
        'status' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.status',
            'config' => array(
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            )
        ),
        'start_date' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.start_date',
            'config' => array(
                'type' => 'input',
                'size' => 6,
                'eval' => 'timesec',
                'checkbox' => 1,
                'default' => time()
            )
        ),
        'message' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.message',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'priority' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.priority',
            'config' => array(
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            )
        ),

    ),
];