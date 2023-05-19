<?php


/**
 *  Updates the mainHTML function to display a paragraph and developer name 
 *
 *  @param string $heading the heading for the about page
 *  @param string $text an html paragraph with details about the page and the developer name
 *  @return the heredoc for the main section of the page
 * 
 *  @author Mario Nemecek
 */

class AboutPage extends WebPage {
    protected function mainHTML($heading, $text) {
        return <<<MAIN
<div id="main">
    <div id=mainContainer>
        <h1>$heading</h1>
        $text
    </div>
</div>
MAIN;
    }
}

?>