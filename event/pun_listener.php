<?php

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class phpbb_ext_imkingdavid_personalusernotes_event_pun_listener implements EventSubscriberInterface
{
	public function __construct()
	{
		global $phpbb_container;

        // Let's get our table constants out of the way
        $table_prefix = $phpbb_container->getParameter('core.table_prefix');
		define('NOTES_TABLE', $table_prefix . 'user_notes');
	}

	static public function getSubscribedEvents()
	{
		return array();
	}
}
