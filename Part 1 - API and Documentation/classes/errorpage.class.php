<?php


/**
 *  Updates the mainHTML function to display an error message
 *
 *  @param string $heading 404 error heading 
 *  @param string $text an html paragraph with an error message
 *  @return the heredoc for the main section of the page
 * 
 *  @author Mario Nemecek
 */

class ErrorPage extends WebPage {
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