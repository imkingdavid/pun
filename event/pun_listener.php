<?php
/**
 *
 * @package pun
 * @copyright (c) 2013 David King (imkingdavid)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class phpbb_ext_imkingdavid_personalusernotes_event_pun_listener implements EventSubscriberInterface
{
	/**
	* Constructor
	*/
	public function __construct()
	{
		global $phpbb_container;

        // Let's get our table constants out of the way
        $table_prefix = $phpbb_container->getParameter('core.table_prefix');
		define('NOTES_TABLE', $table_prefix . 'user_notes');
	}

	/**
	* Subscribe methods of this class (i.e. listeners) to events
	*
	* @return array
	*/
	static public function getSubscribedEvents()
	{
		return [];
	}
}
