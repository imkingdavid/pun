<?php

class phpbb_ext_imkingdavid_personalusernotes_core_manager
{
	public function __construct(phpbb_template $template, phpbb_user $user, dbal $db, $phpbb_root_path = './', $php_ext = '.php')
	{
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	public function load_note($note_id = 0)
	{
		if (!$note_id)
		{
			return false;
		}

		// New syntax allowed by PHP 5.4
		$note = (new phpbb_ext_imkingdavid_personalusernotes_core_note($this->user, $this->db, $note_id))->load();

		return $note;
	}

	public function load_notes()
	{
		$sql = 'SELECT *
			FROM ' . NOTES_TABLE . '
			WHERE user_id = ' . (int) $this->user->data['user_id'];
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$notes = array();
		foreach ($rows as $row)
		{
			$notes[] = (new phpbb_ext_imkingdavid_personalusernotes_core_note($this->user, $this->db, $row['note_id']))->set_data($row, true);
		}

		return $notes;
	}

	/**
	* Add or update a note in the database
	*
	* @param phpbb_ext_imkingdavid_personalusernotes_core_note $note Note object
	*/
	protected function update(phpbb_ext_imkingdavid_personalusernotes_core_note $note)
	{
		if (!$note->loaded())
		{
			$note->load();
		}

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
	* Generate a URL-friendly slug from a string of text
	* This takes something like: "I am a PHP String"
	* and turns it into "i-am-a-php-string"
	*
	* @param string $title The original string
	* @return string The URL slug
	*/
	public function generate_slug($title)
	{
		// generate the slug
		$title = strtolower($title);
		$title = str_replace(array(' ', '_', '.', '/'), '-', $title);

		// Trim extra dashes
		$previous = $slug = '';
		foreach (str_split($title) as $character)
		{
			if ($character == '-' && (empty($previous) || $previous == '-'))
			{
				continue;
			}

			// Append the character to the title and update the
			// previous character
			$slug .= $previous = $character;
		}
		return trim($slug, "-");
	}
}
