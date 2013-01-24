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

/**
 * Initial schema changes needed for Extension installation
 */
class phpbb_ext_imkingdavid_pun_migrations_1_initial_schema extends phpbb_db_migration
{
	/**
	 * @inheritdoc
	 */
	public static function update_schema()
	{
		return [
			'add_tables'	=> [
				$this->table_prefix . 'topic_prefixes'	=> [
					'COLUMNS'	=> [
						'note_id'			=> ['UINT', NULL, 'auto_increment'],
						'user_id'			=> ['UINT', 0],
						'note_title'		=> ['VCHAR_UNI', ''],
						'note_slug'			=> ['VCHAR_UNI', ''],
						'note_contents'		=> ['TEXT', ''],
						'allow_bbcode'		=> ['BOOL', 0],
						'allow_smilies'		=> ['BOOL', 0],
						'bbcode_uid'		=> ['VCHAR_UNI', ''],
						'bbcode_bitfield'	=> ['VCHAR_UNI', ''],
						'note_created_time' => ['VCHAR_UNI', ''],
						'note_edited_time'	=> ['VCHAR_UNI', ''],
					],
					'PRIMARY_KEY'	=> 'note_id',
					'KEYS'		=> [
						'slug'		=> ['UNIQUE', 'note_slug'],
						'title'		=> ['INDEX', 'note_title'],
					],
				],
			],
		];
	}
}
