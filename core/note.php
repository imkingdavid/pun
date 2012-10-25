<?php
/**
 *
 * @package pun
 * @copyright (c) 2012 David King (imkingdavid)
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

class phpbb_ext_imkingdavid_personalusernotes_core_note implements ArrayAccess
{
	use phpbb_ext_imkingdavid_personalusernotes_core_slug;
	/**
	* Whether or not the load() method has been run
	* @var bool
	*/
	protected $loaded = false;

	/**
	* Whether or not the note exists in the database
	* @var bool
	*/
	protected $exists = false;

	/**
	* Data array for internal use; can be accessed through ArrayAccess
	* Contents cannot be changed externally
	* @var array
	*/
	protected $data = [];

	/**
	* User object
	* @var phpbb_user
	*/
	protected $user;

	/**
	* DBAL object
	* @var dbal
	*/
	protected $db;

	/**
	* Note ID
	* @var int
	*/
	protected $note_id;

	/**
	* Constructor method
	*
	* @param phpbb_user $user User object
	* @param dbal $db DBAL object
	* @param int $note_id ID the note, if applicable
	*/
	public function __construct(phpbb_user $user, dbal $db, $note_id = 0)
	{
		$this->set_id($note_id);
		$this->user = $user;
		$this->db = $db;
	}

	/**
	* Whether or not the note has been loaded
	*
	* @return bool
	*/
	public function loaded()
	{
		return $this->loaded;
	}

	/**
	* Whether or not the note exists
	*
	* @return bool
	*/
	public function exists()
	{
		return $this->exists;
	}

	/**
	* Whether or not the note has enough data for database action
	* Notes must have at the very least a title and contents
	*
	* @return bool
	*/
	public function db_ready()
	{
		return !empty($this->data['note_title']) && !empty($this->data['note_contents']);
	}

	/**
	* Load then note
	*
	* @param bool $reload Whether or not to reload the data
	* @return phpbb_ext_imkingdavid_personalusernotes_core_note Current object for method chaining
	*/
	public function load($reload = false)
	{
		if ($this->loaded() && !$reload)
		{
			return $this;
		}

		$this->loaded = true;

		if (!$this->note_id)
		{
			$this->exists = false;
			return $this;
		}

		$sql = 'SELECT *
			FROM ' . NOTES_TABLE . '
			WHERE note_id = ' . (int) $this->note_id . '
				AND user_id = ' . (int) $this->user->data['user_id'];
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$this->exists = !empty($this->data);

		return $this;
	}

	/**
	* Set the note ID
	*
	* @param int $id The new ID
	* @return phpbb_ext_imkingdavid_personalusernotes_core_note Current object
	*/
	public function set_id($id)
	{
		$this->note_id = (int) $id;
		return $this;
	}

	/**
	* Get the note ID
	*
	* @return int $id Note ID
	*/
	public function get_id()
	{
		return (int) $this->note_id;
	}

	/**
	* Set the data array; this should come directly from the database
	*
	* @param array $new_data The data to set to the $data array
	* @param bool $exists Whether or not a note containing this data exists in the DB
	* @return phpbb_ext_imkingdavid_personalusernotes_core_note Current object for chaining
	*/
	public function set_data(array $new_data, $exists = false)
	{
		// Because we're forcefeeding data into the $data array, this has the
		// same effect as $this->load()
		$this->loaded = true;
		$this->exists = $exists;

		foreach ($new_data as $key => $value)
		{
			$this->offsetSet($key, $value);
		}

		return $this;
	}

	/**
	* ArrayAccess method to determine whether or not offset exists
	*
	* @param string $offset The offset name
	* @return bool Whether or not the offset
	*/
	public function offsetExists($offset)
	{
		return isset($this->data[$offset]) || $this->data[$offset] === null;
	}

	/**
	* ArrayAccess method to set the data for the offset
	*
	* @param string $offset The property name
	* @param mixed $value The new value of the property
	* @return null
	*/
	public function offsetSet($offset, $value)
	{
		$this->data[$offset] = $value;
	}

	/**
	* ArrayAccess method to return the value of the given property
	*
	* @param string $offset The property name
	* @return mixed null if the offset does not exist; otherwise,
	*			the value of the offset
	*/
	public function offsetGet($offset)
	{
		return $this->offsetExists($offset) ? $this->data[$offset] : null;
	}

	/**
	* ArrayAccess method to reset a property to null value
	*
	* @param string $offset The property name
	* @return null
	*/
	public function offsetUnset($offset)
	{
		if ($this->offsetExists($offset))
		{
			unset($this->data[$offset]);
		}
	}
}
