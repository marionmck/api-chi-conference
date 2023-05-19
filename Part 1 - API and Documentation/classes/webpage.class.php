<?php

/**
*   Creates an HTML webpage using the given parameters
* 
*   @author Mario Nemecek
*/

abstract class WebPage {
    
    private $pageStart;
    private $pageTitle;
    private $css;
    private $nav;
    private $navItems;
    private $main; 
    private $footer;
    private $pageEnd;

    public function __construct($pageTitle, $heading, $text, $footerText) {
        // @todo - initialise properties and call methods as required
        $this->set_css();
        $this->set_pageStart($pageTitle, $this->css);
        $this->set_header();
        $this->set_main($heading, $text);
        $this->set_footer($footerText);
        $this->set_pageEnd();
    }

    /**
     * Sets the starting HTML tags and metadata for the start of the page
     *
     * @param string $pageTitle the title for the page
     * @param string $css link to the css file
     */
    private function set_pageStart($pageTitle, $css) {
        $this->pageStart = <<<PAGESTART
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="A website about the CHI2018 academic conference.">
    <meta name="keywords" content="CHI2018, academic, conference, research, CHI">
    <meta name="author" content="Mario Nemecek">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$pageTitle</title>
    <link href="$css" rel="stylesheet"/>
</head>
<body>
PAGESTART;
    }
    
    /**
     * Sets the header section of the page
     */
    protected function set_header() {
        $this->set_nav();
        $nav = $this->nav;
        $this->pageHeader = <<<HEADER
<div class="header">
    <div class="headerTitle">CHI2018</div>
    $nav
</div>
HEADER;
    }
    
    /**
     * Creates a nav menu for the header
     *
     * @param string $listItems the links for the nav
     *
     * @return the nav menu section of the page
     */
    protected function navHTML($listItems) {
        return <<<MYNAV
<div class="header-right">
    $listItems
</div>
MYNAV;
    }
    
    /**
     * Sets the links for the nav section using the array created by set_navItems()
     *
     * @return the heredoc for neav
     */
    private function set_nav() {
        $listItems = "";
        $this->set_navItems();
        foreach($this->navItems as $key => $value) {
            $listItems .= "<a href='" . BASEPATH . "$value'>$key</a>\n";
        }
        $this->nav = $this->navHTML($listItems);
    }
    
    
    /**
     * Creates an associative array of menu items generated from routes.ini
     */
    private function set_navItems() {
        $ini['routes'] = parse_ini_file("config/routes.ini", true);
        foreach($ini['routes'] as $key => $value) {
            if($key != "error"){
                $this->navItems[$key] = $key."/";
            }
        }
    }
    
    /**
     * Sets the path for the css file
     */
    private function set_css() {
        $this->css = BASEPATH.CSSPATH;
    }

    /**
     * Sets $main to the main section of the page
     */
    private function set_main($heading, $text) {
        $this->main = $this->mainHTML($heading, $text);
    }
    
    /**
     * Creates the main section of the page using heredoc
     *
     * @param string $heading the page heading
     * @param string $text a paragraph about the page
     * 
     * @return the HTML for the main section
     */
    protected function mainHTML($heading, $text) {
        return <<<MAIN
<main>
    <h1>$heading</h1>
    $text
</main>
MAIN;
    }

    /**
     * Creates the footer section of the page using heredoc
     *
     * @param string $footerText text to be included in the footer section
     * 
     * @return the HTML for the footer section
     */
    private function set_footer($footerText) {
        $this->footer = <<<FOOTER
<footer>
    <p>$footerText</p>
</footer>
FOOTER;
    }
    
    /**
     * Sets the closing HTML tags for the end of the page
     */
    private function set_pageEnd() {
        $this->pageEnd = <<<PAGEEND
</body>
</html>
PAGEEND;
    }
    
    /**
     * Returns the whole webpage
     *
     * @return all the sections needed to make this webpage
     */
    public function get_page() {
        return 
            $this->pageStart .
            $this->pageHeader .
            $this->main .
            $this->footer .
            $this->pageEnd;
    }
}

?>