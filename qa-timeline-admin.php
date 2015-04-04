<?php
class qa_timeline_admin {
	
	var $directory;
	var $urltoroot;
	
	function load_module($directory, $urltoroot)
	{
		$this->directory=$directory;
		$this->urltoroot=$urltoroot;
	}

	
	function option_default($option)
	{
		switch ($option) {
			case 'tl_link_nav':
				return true;
				break;
		}
	}


	function admin_form()
	{
		$saved=false;
		
		if (qa_clicked('tl_save_button')) {
			qa_opt('tl_link_nav', (int)qa_post_text('tl_link_nav'));
			
			$saved=true;
		}
		
		$form=array(
			'ok' => $saved ? 'settings saved' : null,
			
			'fields' => array(
				array(
					'label' => 'Show timeline Link in header navigation',
					'value' => qa_html(qa_opt('tl_link_nav')),
					'tags' => 'name="tl_link_nav"',
					'type' => 'checkbox',
				),
				array(
					'type' => 'static',
					'value' =>'<hr>',
				),
				array(
					'type' => 'static',
					'value' =>'Visit <a href="'. qa_opt('site_url') .'index.php?qa=timeline">timeline Tool</a>',
				),
			),
			
			'buttons' => array(
				array(
					'label' => 'Save Changes',
					'tags' => 'name="tl_save_button"',
				),
			),			);

		return $form;
	}	
}


/*
	Omit PHP closing tag to help avoid accidental output
*/