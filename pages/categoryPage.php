<?php

class categoryPage extends page
{

    public function getContent()
    {
        # Globals
        global $db;

        # Check if a video is set
        if (!empty($_GET['sub2'])) {
            $video = $db->query('
                SELECT * FROM
                `videos`
                WHERE `urltitle` = ?
            ', array(
                $_GET['sub2']
            ))->fetch();
        }

        if ($video) {
            $player = new videoPage();
            $player->setVideo($video);
            $this->setDescription($player->getDescription());
            $this->setTitle($player->getTitle());
            return $player->getContent();
        } else {
            # Check if the category is set
            return 'Select category page!';
        }
    }
}