<?php

class homePage extends page
{
    protected $title = 'Home';

    public function getContent()
    {
        $content = new template('pages/home.html');

        # Newest video's
        $content->setTagLoop(
            'newestvids',
            array(
                array(
                    'title' => 'Netsky - Memory Lane',
                    'url' => 'c/dnb/netsky-memory-lane',
                    'imageurl' => 'http://img.youtube.com/vi/cG7cRDcPY3k/0.jpg',
                ),
                array(
                    'title' => '"A Journey Through Sound" ~ Liquid Drum & Bass Mix.',
                    'url' => 'c/dnb/a-journey-trough-sound',
                    'imageurl' => 'http://img.youtube.com/vi/g9iqp0sIvSg/0.jpg',
                ),
            )
        );

        # Featured categories
        $content->setTagLoop(
            'categories',
            array(
                array(
                    'title' => 'Humor / entertainment',
                    'url' => 'category/humor',
                    'imageurl' => 'a',
                ),
                array(
                    'title' => 'Movie trailers',
                    'url' => 'category/trailers',
                    'imageurl' => 'b',
                ),
                array(
                    'title' => 'Popular vlogs',
                    'url' => 'category/vlog',
                    'imageurl' => 'b',
                ),
            )
        );

        # Featured music categories
        $content->setTagLoop(
            'musiccategories',
            array(
                array(
                    'title' => 'Drum and Base',
                    'url' => 'c/dnb',
                    'imageurl' => 'a',
                ),
                array(
                    'title' => 'Dubstep',
                    'url' => 'c/dubstep',
                    'imageurl' => 'b',
                ),
            )
        );

        return $content->getContent();
    }
}
