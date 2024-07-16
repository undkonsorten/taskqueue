<?php
declare(strict_types=1);
if (!defined('TYPO3')) {
    die('Access denied.');
}

return [
    'ctrl' => [
        'title'	=> 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'delete' => 'deleted',
        'type' => 'type',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        'searchFields' => 'name,data,status,start_date,message,priority,',
        'iconfile' => 'EXT:taskqueue/Resources/Public/Icons/tx_taskqueue_domain_model_task.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,--palette--;;1,name,data,status,start_date,message,priority,retries,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,starttime,endtime'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ]
        ],

        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y'))
                ],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y'))
                ],
            ],
        ],
        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'data' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.data',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 6
            ],
        ],
        'status' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.status',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'start_date' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.start_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 6,
                'eval' => 'timesec',
                'checkbox' => 1,
                'default' => time()
            ]
        ],
        'last_run' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.last_run',
            'config' => [
                'type' => 'input',
                'dbType' => 'datetime',
                'renderType' => 'inputDateTime',
                'size' => 7,
                'eval' => 'datetime',
                'default' => '0000-00-00 00:00:00',
            ],
        ],
        'message' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.message',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'priority' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.priority',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'retries' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.retries',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'type' => [
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => '',
                'items' => [
                ],
            ],
        ],

    ],
];
