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

trait phpbb_ext_imkingdavid_personalusernotes_core_slug
{
	/**
	* Generate a URL-friendly slug from a string of text
	* This takes something like: "I am a PHP String"
	* and turns it into "i-am-a-php-string"
	*
	* @param string $input The original string
	* @return string The URL slug
	*/
	public function generate_slug($input)
	{
		if ($this->valid_slug($input) === 1)
		{
			return $input;
		}

		$input = strtolower($input);
		$input = str_replace([' ', '_', '.', '/'], '-', $input);

		$previous = $slug = '';
		foreach (str_split($input) as $character)
		{
			if ($character == '-' && (empty($previous) || $previous == '-'))
			{
				continue;
			}

			$slug .= $previous = $character;
		}
		return trim($slug, "-");
	}

	/**
	* Determine if a given slug is valid (matches against regex)
	*
	* @param string $slug The string to check
	* @return int|bool Values: 1 (matches), 0 (does not match), and false (error).
	*/
	public function valid_slug($slug)
	{
		return preg_match('/([0-9]+)(-[a-zA-Z0-9-]+)?/', $slug);
	}

	/**
	* Separate the ID from the slug in a URL
	*
	* @param string $url The ID+slug combined string in the URL
	* @return array [(int) id, (string) slug]
	*/
	public function separate_slug($url)
	{
		// Turn "###-ab-cd-ef" into:
		// id: ###
		// slug: ab-cd-ef
		$url = explode('-', $url);
		return [
			(int) array_shift($url),
			implode('-', $url),
		];
	}

	/**
	* Simply combines the ID and the slug together, separated by a dash
	*
	* @param int $id
	* @param string $slug
	* @return string
	*/
	public function combine_slug($id, $slug)
	{
		if (!$this->valid_slug($slug))
		{
			$slug = $this->generate_slug($slug);
		}

		return "{$id}-{$slug}";
	}
}
