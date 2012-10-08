<?php

class phpbb_ext_imkingdavid_personalusernotes_controller implements phpbb_controller_interface
{
	public function __construct(phpbb_controller_helper $helper, phpbb_ext_imkingdavid_personalusernotes_core_manager $manager, phpbb_template $template, phpbb_user $user, dbal $db, $phpbb_root_path = './', $php_ext = '.php')
	{
		$this->helper = $helper;
		$this->manager = $manager;
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	public function handle($action = 'view', $id = 0)
	{
		$this->user->add_lang_ext('imkingdavid/personalusernotes', 'controller');

		if ($this->user->data['user_id'] === ANONYMOUS)
		{
			return $this->helper->error(401);
		}

		if (strpos('-', $id))
		{
			// Turn "###-ab-cd-ef"
			// Into:
			// id: ###
			// slug: ab-cd-ef
			$id = explode('-', $id);
			$note_id = array_shift($id);
			$slug = implode('-', $id);
		}
		else
		{
			$note_id = (int) $id;
		}

		switch ($action)
		{
			case 'view':
				if (!$note_id)
				{
					$template_file = 'note_list_body.html';
					/* @todo make this language string */
					$page_title = $this->user->lang('VIEWING_MY_NOTES');
					$notes = $this->manager->load_notes();

					foreach ($notes as $note)
					{
						$this->template->assign_block_vars('notes', array(
							'TITLE'			=> $note['note_title'],
							'CONTENT'		=> $note['note_contents'], // @todo: parse this

							'U_VIEW'		=> $this->helper->url(array('notes', 'view', "{$note['note_id']}-{$note['note_slug']}")),
							'U_EDIT'		=> $this->helper->url(array('notes', 'edit', "{$note['note_id']}-{$note['note_slug']}")),
							'U_DELETE'		=> $this->helper->url(array('notes', 'delete', "{$note['note_id']}-{$note['note_slug']}")),
							'U_VIEW_ALL'	=> $this->helper->url(array('notes', 'view')),
						));
					}
				}
				else
				{
					$note = $this->manager->load_note($note_id);
					$template_file = 'note_view_body.html';
					/* @todo make this language string */
					$page_title = $note['note_title'] . ' &bull; ' . $this->user->lang('VIEWING_NOTE');

					if (!$note->exists())
					{
						$response = $this->helper->error(404);
						break;
					}

					$this->template->assign_vars(array(
						'TITLE'			=> $note['note_title'],
						'CONTENT'		=> $note['note_contents'], // @todo: parse this

						'U_VIEW'		=> $this->helper->url(array('notes', 'view', "{$note['note_id']}-{$note['note_slug']}")),
						'U_EDIT'		=> $this->helper->url(array('notes', 'edit', "{$note['note_id']}-{$note['note_slug']}")),
						'U_DELETE'		=> $this->helper->url(array('notes', 'delete', "{$note['note_id']}-{$note['note_slug']}")),
						'U_VIEW_ALL'	=> $this->helper->url(array('notes', 'view')),
					));
				}

				$response = $this->helper->render($template_file, $page_title);
			break;

			case 'add':
			case 'edit':

			break;

			default:
				$response = $this->helper->error(404);
			break;
		}

		return $response;
	}

	public function handle_view()
	{
	}

	public function handle_add_edit($mode = 'add')
	{
	}

	public function handle_delete()
	{
	}

	/**
	* Generate a URL-friendly slug from a string of text
	* This takes something like: "I am a PHP String"
	* and turns it into "i-am-a-php-string"
	*
	* @param string $title The original string
	* @return string The URL slug
	*/
	private function generate_slug($title)
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
