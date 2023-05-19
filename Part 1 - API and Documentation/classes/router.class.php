<?php

/**
* This Router will return a documentation or about page
* 
* @author Mario Nemecek
*
*/

class Router {
    private $page;
    private $type = "HTML";
    
    public function __construct($recordSet) { 
        $url = $_SERVER["REQUEST_URI"]; //get the URL
        $path = parse_url($url)["path"]; //get only the path from the URL

        $path = str_replace(BASEPATH,"",$path); //remove the base path to get file name
        $pathArr = explode("/", $path);
        $path = (empty($pathArr[0])) ? "documentation" : $pathArr[0];
                
        ($path == "api")
            ? $this->api_route($pathArr, $recordSet)
            : $this->html_route($path);
    }
    
    /**
    * Function to create a new json page
    *
    * @param array $pathArr the url path sliced into array
    * @param object $recordSet the data retrieved from the db
    */
    public function api_route($pathArr, $recordSet) {
        $this->type = "JSON";
        $this->page = new JSONpage($pathArr, $recordSet);
    }
    
    /**
    * Function to create a new html page
    *
    * @param string $path the current path/page name in url
    */
    public function html_route($path) {
        $ini['routes'] = parse_ini_file("config/routes.ini", true);
        $pageInfo = isset($path, $ini['routes'][$path])
            ? $ini['routes'][$path]
            : $ini['routes']['error'];
        
        if($path === "documentation") {
            $this->page = new DocumentationPage($pageInfo['title'], $pageInfo['heading'], $pageInfo['text'], $pageInfo['footer']);   
        } elseif($path === "about") {
            $this->page = new AboutPage($pageInfo['title'], $pageInfo['heading'], $pageInfo['text'], $pageInfo['footer']);
        } else {
            $this->page = new ErrorPage($pageInfo['title'], $pageInfo['heading'], $pageInfo['text'], $pageInfo['footer']);
        }
    }
    
    /**
    * Function that returns the type of page - html or json
    *
    * @return the page type as string
    */
    public function getType() {
        return $this->type;
    }
    
    /**
    * Function to return the json or html page
    *
    * @return page
    */
    public function get_page() {
        return $this->page->get_page(); 
    }
}
?>