<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}
(function () {
    if (TYPO3 === 'BE') {

        /**
         * Registers a Backend Module
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'Taskqueue',
            'tools',     // Make module a submodule of 'tools'
            'taskqueue',    // Submodule key
            '',                        // Position
            [
                \Undkonsorten\Taskqueue\Controller\TaskController::class => 'list, show, delete, run, deleteFinished, deleteFailed, reactivate, search, searchUid, searchResult',

            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:taskqueue/Resources/Public/Icons/Extension.svg',
                'labels' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_taskqueue.xlf',
            ]
        );
    }

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_taskqueue_domain_model_task',
        'EXT:taskqueue/Resources/Private/Language/locallang_csh_tx_taskqueue_domain_model_task.xlf'
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_taskqueue_domain_model_task');
})();
