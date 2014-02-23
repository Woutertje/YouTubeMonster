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
                    'title' => 'Test video 1',
                    'url' => 'c/dnb/test1',
                    'imageurl' => 'a',
                ),
                array(
                    'title' => 'Test video 2',
                    'url' => 'c/dnb/test2',
                    'imageurl' => 'b',
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
                    'title' => 'Drumm and Base',
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
