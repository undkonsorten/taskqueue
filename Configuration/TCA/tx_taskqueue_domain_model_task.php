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
        'iconfile' => 'EXT:taskqueue/Resources/Public/Icons/Extension.svg'
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,--palette--;;1,name,data,status,start_date,message,priority,ttl,last_run,crdate,retries,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,starttime,endtime'],
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
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.status.0',
                        'value' => 0,
                    ],
                    [
                        'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.status.1',
                        'value' => 1,
                    ],
                    [
                        'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.status.2',
                        'value' => 2,
                    ],
                    [
                        'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.status.3',
                        'value' => 3,
                    ],
                    [
                        'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.status.4',
                        'value' => 4,
                    ],
                    [
                        'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.status.5',
                        'value' => 5,
                    ],
                    [
                        'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.status.6',
                        'value' => 6,
                    ],
                ],
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
                'type' => 'datetime',
                'dbType' => 'datetime',
                'format' => 'datetime',
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
        'ttl' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.ttl',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
                'default' => 900
            ]
        ],
        'crdate' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.created_at',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'readOnly' => 1
            ],
        ],
    ],
];
