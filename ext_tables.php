<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
call_user_func(function ($packageKey) {
    if (TYPO3_MODE === 'BE') {

        /**
         * Registers a Backend Module
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'Undkonsorten.' . $packageKey,
            'tools',     // Make module a submodule of 'tools'
            'taskqueue',    // Submodule key
            '',                        // Position
            array(
                'Task' => 'list, show, edit, update, delete, run, deleteFinished, deleteFailed',

            ),
            array(
                'access' => 'user,group',
                'icon' => 'EXT:' . $packageKey . '/ext_icon.svg',
                'labels' => 'LLL:EXT:' . $packageKey . '/Resources/Private/Language/locallang_taskqueue.xlf',
            )
        );

    }

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($packageKey, 'Configuration/TypoScript', 'task');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_taskqueue_domain_model_task',
        'EXT:taskqueue/Resources/Private/Language/locallang_csh_tx_taskqueue_domain_model_task.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_taskqueue_domain_model_task');
});