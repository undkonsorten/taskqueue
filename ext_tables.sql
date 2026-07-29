#
# Table structure for table 'tx_taskqueue_domain_model_task'
#
CREATE TABLE tx_taskqueue_domain_model_task (
	data longtext NOT NULL,
	status int(11) DEFAULT '0' NOT NULL,
	message text NULL
);
