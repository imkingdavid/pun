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

class phpbb_ext_imkingdavid_personalusernotes_controller
{
	use phpbb_ext_imkingdavid_personalusernotes_core_slug;

	public function __construct(phpbb_controller_helper $helper, phpbb_ext_imkingdavid_personalusernotes_core_manager $manager, phpbb_template $template, phpbb_user $user, dbal $db, phpbb_request $request)
	{
		$this->helper = $helper;
		$this->manager = $manager;
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->request = $request;
		
		$this->helper->set_base_url(['notes']);
	}

	/**
	* Handle requests to this controller
	*
	* @param string $action What to do
	* @param int $id Note ID (with optional slug)
	* @return Response
	*/
	public function handle($action = 'view', $id = 0)
	{
		$this->user->add_lang_ext('imkingdavid/personalusernotes', 'controller');

		if ($this->user->data['user_id'] === ANONYMOUS)
		{
			return $this->helper->error(401);
		}

		if (strpos('-', $id))
		{
			list($note_id, $slug) = $this->separate_slug($id);
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
					$total_notes = sizeof($notes);

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
				$page_title = $this->user->lang($note_id ? 'UPDATING_NOTE' : 'CREATING_NOTE');

				$error = [];

				$note = $this->manager->load_note($note_id);

				if ($this->request->is_set_post('submit'))
				{
					if (!check_form_key('pun_update_form'))
					{
						$error[] = 'NOTE_UPDATE_FORM_CSRF_ERROR';
					}

					$title = $this->request->variable('title', $note['note_title'] ?: '', true);
					$content = $this->request->variable('content', $note['note_content'] ?: '', true);

					if (empty($title))
					{
						$error[] = 'EMPTY_TITLE_ERROR';
					}

					if (empty($content))
					{
						$error[] = 'EMPTY_CONTENT_ERROR';
					}

					if (!sizeof($error))
					{
						$slug = $this->generate_slug($title);

						$uid = $bitfield = $options = '';
						generate_text_for_storage($content, $uid, $bitfield, $options, true, true, true);

						$note->set_data([
							'note_title' => $title,
							'note_content'	=> $content,
							'note_slug'	=> $slug,
						]);
						$this->manager->update($note);

						$message = $this->user->lang($action == 'edit' ? 'NOTE_UPDATED' : 'NOTE_CREATED') .
							'<br /><a href="' . $this->helper->url([$this->combine_slug($note['note_id'], $slug)]) . '">' .
							$this->user->lang('RETURN_TO_NOTE') .
							'</a><br /><a herf="' . $this->helper->url([]) . '">' .
							$this->user->lang('RETURN_TO_NOTE', 2) .
							'</a>';

						return $this->helper->error(200, $message);
					}
				}

				add_form_key('pun_update_form');
			break;

			default:
				return $this->helper->error(404);
			break;
		}

		return $this->helper->render($template_file, $page_title);
	}

	/**
	* Send a note to the template
	*
	* @param phpbb_ext_imkingdavid_personalusernotes_core_note $note
	* @param string $block Optional block; if set, call is made to
	*			   assign_block_vars($block, []), otherwise assign_vars([])
	* @return null
	*/
	protected function send_vars(phpbb_ext_imkingdavid_personalusernotes_core_note $note, $block = '')
	{
		$slug = $this->combine_slug($note['note_id'], $note['note_slug']);

		// Eventually these will be split out so users can change the options
		// on the posting page
		$options = OPTION_FLAG_BBCODE + OPTION_FLAG_SMILIES + OPTION_FLAG_LINKS;
		$contents = generate_text_for_display($note['note_contents'], $note['note_uid'], $note['note_bitfield'], $options);

		$template_vars = [
			'TITLE'			=> $note['note_title'],
			'CONTENT'		=> $note['note_contents'],

			'U_VIEW_ALL'	=> $this->helper->url([]),
			'U_VIEW'		=> $this->helper->url([$slug]),
			'U_EDIT'		=> $this->helper->url([$slug, 'edit']),
			'U_DELETE'		=> $this->helper->url([$slug, 'delete']),
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
}
