<?php

class phpbb_ext_imkingdavid_personalusernotes_controller
{
	public function __construct(phpbb_controller_helper $helper, phpbb_ext_imkingdavid_personalusernotes_core_manager $manager, phpbb_template $template, phpbb_user $user, dbal $db, phpbb_request $request)
	{
		$this->helper = $helper;
		$this->manager = $manager;
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->request = $request;
		
		$this->helper->set_base_url(array('notes'));
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
					$page_title = $this->user->lang('VIEWING_MY_NOTES');
					$notes = $this->manager->load_notes();

					foreach ($notes as $note)
					{
						$this->send_vars($note, 'notes');
					}
				}
				else
				{
					$note = $this->manager->load_note($note_id);
					$template_file = 'note_view_body.html';
					$page_title = $note['note_title'];

					if (!$note->exists())
					{
						return $this->helper->error(404, $this->user->lang('NOTE_NOT_FOUND_ERROR'));
					}

					$this->send_vars($note);
				}
			break;

			case 'add':
			case 'edit':
				$template_file = 'note_update_body.html';
				$page_title = $this->user->lang($note_id ? 'UPDATING_NOTE' : 'ADDING_NOTE');


			break;

			default:
				return $this->helper->error(404);
			break;
		}

		return $this->helper->render($template_file, $page_title);
	}

	private function send_vars(array $vars, $block = '')
	{

		$template_vars = [
			'TITLE'			=> $note['note_title'],
			'CONTENT'		=> $note['note_contents'], // @todo: parse this

			'U_VIEW'		=> $this->helper->url(["{$note['note_id']}-{$note['note_slug']}"]),
			'U_EDIT'		=> $this->helper->url(["{$note['note_id']}-{$note['note_slug']}", 'edit']),
			'U_DELETE'		=> $this->helper->url(["{$note['note_id']}-{$note['note_slug']}", 'delete']),
			'U_VIEW_ALL'	=> $this->helper->url([]),
		];

		if ($block)
		{
			$this->template->assign_block_vars($block, $template_vars);
		}
		else
		{
			$this->template->assign_vars($template_vars);
		}
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
