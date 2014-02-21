<?php

class notFoundPage extends page
{
	protected $title = '404 not found';
	protected $description = 'The requested page or file could not be found on our server.';
	
	public function getContent()
	{
		$content = new template('pages/notFound.html');
		return $content->getContent();
	}
}
