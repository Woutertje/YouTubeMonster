<?php

class page
{
	protected $title = 'Info';
	protected $description = 'Stream the latest viral video\'s, movie trailers or your favorite music. YouTubeMonster is an open source project!';
	
	public function getTitle()
	{
		return $this->title . ' | YouTubeMonster';
	}
	
	public function getFixedTitle()
	{
		return (strlen($this->title) > 25) ? substr($this->title, 0, 22) . '...' : $this->title;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
	
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}
	
	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}
	
	public function getContent(){
		return '';
	}
}
