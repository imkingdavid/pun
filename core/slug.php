<?php

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
		// If the input is already a valid slug, just return it
		if ($this->valid_slug($input) === 1)
		{
			return $input;
		}

		// generate the slug
		$input = strtolower($input);
		$input = str_replace([' ', '_', '.', '/'], '-', $input);

		// Trim extra dashes
		$previous = $slug = '';
		foreach (str_split($input) as $character)
		{
			if ($character == '-' && (empty($previous) || $previous == '-'))
			{
				continue;
			}

			// Append the character to the slug and update the
			// previous character
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
}
