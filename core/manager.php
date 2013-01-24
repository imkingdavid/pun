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

class phpbb_ext_imkingdavid_personalusernotes_core_manager
{
	use phpbb_ext_imkingdavid_personalusernotes_core_slug;

	/**
	* Constructor
	*
	* @param phpbb_template $template Template object
	* @param phpbb_user $user User object
	* @param dbal $db DBAL object
	* @param string $phpbb_root_path Root path
	* @param string $php_ext PHP extension
	*/
	public function __construct(phpbb_template $template, phpbb_user $user, dbal $db, $phpbb_root_path = './', $php_ext = '.php')
	{
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Load a single note
	* This will return a note object whether or not the note exists
	* The existence of the note not only depends on physical existence in the
	* database but also on whether or not the note belongs to the current user
	*
	* @param int $note_id ID number of the note to load
	* @return phpbb_ext_imkingdavid_personalusernotes_core_note
	*/
	public function load_note($note_id = 0)
	{
		// New syntax allowed by PHP 5.4
		$note = (new phpbb_ext_imkingdavid_personalusernotes_core_note($this->user, $this->db, $note_id))
				->load();

		return $note;
	}

	/**
	* Load a user's notes
	*
	* @return array
	*/
	public function load_notes()
	{
		$sql = 'SELECT *
			FROM ' . NOTES_TABLE . '
			WHERE user_id = ' . (int) $this->user->data['user_id'];
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$notes = [];
		foreach ($rows as $row)
		{
			$notes[] = (new phpbb_ext_imkingdavid_personalusernotes_core_note($this->user, $this->db, $row['note_id']))
						->set_data($row, true);
		}

		return $notes;
	}

	/**
	* Add or update a note in the database
	*
	* @param phpbb_ext_imkingdavid_personalusernotes_core_note $note Note object
	* @return bool
	*/
	public function update(phpbb_ext_imkingdavid_personalusernotes_core_note $note)
	{
		$note->load();
		if (!$note->db_ready())
		{
			return false;
		}

		if ($note->exists())
		{
			$sql = 'UPDATE ' . NOTES_TABLE . '
				SET ' . $this->db->sql_build_array('UPDATE', $note) . '
				WHERE note_id = ' . (int) $note['note_id'];
		}
		else
		{
			$sql = 'INSERT INTO ' . NOTES_TABLE . ' ' . $this->db->sql_build_array('INSERT', $note);
		}
		$this->db->sql_query($sql);

		if (!$note->exists())
		{
			$note->set_id($this->db->sql_nextid());
		}

		// Reload the note
		$note->load(true);

		return true;
	}

	/**
	* Delete a note from the database
	*
	* @param phpbb_ext_imkingdavid_personalusernotes_core_note $note Note object
	* @return bool
	*/
	public function delete(phpbb_ext_imkingdavid_personalusernotes_core_note $note)
	{
		if (!$note->loaded())
		{
			$note->load();
		}

		if (!$note->exists())
		{
			return false;
		}

		$sql = 'DELETE FROM ' . NOTES_TABLE . '
			WHERE note_id = ' . (int) $note->get_id() . '
				AND user_id = ' . (int) $this->user->data['user_id'];
		$this->db->sql_query($sql);

		return $this->db->sql_affectedrows() ? true : false;
	}

	/**
	* Post a note to a forum
	*
	* @param phpbb_ext_imkingdavid_personalusernotes_core_note $note Note object
	* @param int $forum_id ID of the forum to which to post the note
	* @param bool $retain_note Whether or not to delete the note after posting it
	*/
	public function post(phpbb_ext_imkingdavid_personalusernotes_core_note $note, $forum_id, $retain_note = false)
	{
		if (!$auth->acl_get('f_post', $forum_id))
		{
			return false;
		}

		// Do the posting action
		$note->load();

		if (!$retain_note)
		{
			$this->delete($note);
		}

		return true;
	}
}
