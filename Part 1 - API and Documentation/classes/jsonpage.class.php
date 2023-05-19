<?php

/**
*   Creates a JSON webpage for each endpoint of the API
* 
*   @author Mario Nemecek
*/

class JSONpage {
    private $page;
    private $recordSet;
    
    public function __construct($pathArr, $recordSet) {
        $this->recordSet = $recordSet;
        
        $path = (empty($pathArr[1]) ? 'api' : $pathArr[1]);
        
        switch($path) {
            case 'api' :
                $this->page = $this->json_welcome();
                break;
            case 'authors' :
                $this->page = $this->jsonAuthors();
                break;
            case 'slots' :
                $this->page = $this->jsonSlots();
                break;
            case 'sessions' :
                $this->page = $this->jsonSessions();
                break;
            case 'rooms' :
                $this->page = $this->jsonRooms();
                break;
            case 'content' :
                $this->page = $this->jsonContent();   
                break;
            case 'update-title' :
                $this->page = $this->jsonUpdateTitle();
                break;
            case 'login' :
                $this->page = $this->json_login();
                break;
            default :
                $this->page = $this->json_error();
                break;
        }
    }
    
    /**
    * Sanitises any string inputs using the FILTER_SANITIZE_STRING function
    * 
    * @param string $x the string to be sanitised
    * @return the sanitised string
    */
    private function sanitiseString($x) {
        return trim(filter_var($x, FILTER_SANITIZE_STRING));
    }
    
    /**
    * Sanitises any number inputs using the FILTER_VALIDATE_INT function
    * 
    * @param integer $x the number to be sanitised
    * @return the sanitised number
    */
    private function sanitiseNum($x) {
        return filter_var($x, FILTER_VALIDATE_INT);
    }   
    
    /**
    * Creates a JSON object for the welcome page of the API from an associative array
    * 
    * @return JSON object that contains a welcome message, developer name and API endpoints
    */
    private function json_welcome() {
        $msg = array("message"=>"Welcome to my CHI2018 API. You can access data about the conference here.", "developer"=>"Mario Nemecek", "endpoints" => array("authors" => "/api/authors/", "slots" => "/api/slots/", "sessions" => "/api/sessions/", "rooms" => "/api/rooms/", "content" => "/api/content/", "login" => "/api/login", "update title" => "/api/update-title/"));
        return json_encode($msg);
    }
    
    /**
    * Creates a JSON object that displays an error message
    * 
    * @return JSON object that contains a status code and message
    */
    private function json_error() {
        $msg = array("status" => 400, "message"=>"Invalid endpoint. Please try again.");
        return json_encode($msg);
    }
    
    /**
    * Creates the authors endpoint by returning a list of all authors in the conference 
    * or if a valid url query is provided returns a filtered group of authors 
    * 
    * @return JSON object that contains the names of authors
    */
    private function jsonAuthors() {
        $query = "SELECT name FROM authors";
        $queryParams = [];
        
        // valid url parameters
        $acceptedUrlParams = array('search', 'id', 'limit', 'page');
        
        //if a URL query exists
        if($_SERVER['QUERY_STRING']) {
            // create a list of parameters in the URL
            $urlParams = explode("&", $_SERVER['QUERY_STRING']);
            
            // if the parameters in the URL are in the array of valid parameters
            if(in_array(explode("=", $urlParams[0])[0], $acceptedUrlParams)) {
                
                if(count($urlParams) === 2) {
                    // only the search and page parameters can be used in the same query
                    if(explode("=", $urlParams[0])[0] === "search" && explode("=", $urlParams[1])[0] === "page") {
                        $query .= " WHERE name LIKE :name ORDER BY name LIMIT 10 OFFSET ";
                        $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
                        $term = $this->sanitiseString("%".$_REQUEST['search']."%");
                        $queryParams = ["name" => $term];
                    } else {
                        // no other combination of URL parameters are accepted so return no data
                        $query = "";
                    }
                } elseif(count($urlParams) === 1) {
                    
                    // returns the slots that include the search value in the slot name
                    if(isset($_REQUEST['search'])) {
                        $query .= " WHERE name LIKE :name";
                        $term = $this->sanitiseString("%".$_REQUEST['search']."%");
                        $queryParams = ["name" => $term];
                    }
                    
                    // returns a specific slot using the slot id
                    if(isset($_REQUEST['id'])) {
                        $query .= " WHERE authorId = :id";
                        $num = $this->sanitiseNum($_REQUEST['id']);
                        $queryParams = ["id" => $num];
                    }
                    
                    // limits the number of results returned
                    if(isset($_REQUEST['limit'])) {
                        $query .= " LIMIT :num";
                        $num = $this->sanitiseNum($_REQUEST['limit']);
                        $queryParams["num"] = $num;
                    }
                    
                    // splits the data into pages of 10 results for each page
                    if(isset($_REQUEST['page'])) {
                        $query .= " ORDER BY name";
                        $query .= " LIMIT 10";
                        $query .= " OFFSET ";
                        $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
                    }   
                }
            } else {
                // if an invalid parameters is used then no results are returned
                $query = "";
            }
        }
        return ($this->recordSet->getJSONRecordSet($query, $queryParams));
    }
    
    /**
    * Creates the slots endpoint by returning all slots in the conference 
    * or if a valid url query is provided returns the filtered slots
    * 
    * @return JSON object that contains the slot id, day in string format and the start and end times
    * of each slot
    */
    private function jsonSlots() {
        $query = 'SELECT slotId AS id, type, dayString AS day, ((startHour) || ":" || (CASE WHEN startMinute = 0 THEN startMinute || 0 ELSE startMinute END)) AS startTime, ((endHour) || ":" || (CASE WHEN endMinute = 0 THEN endMinute || 0 ELSE endMinute END)) AS endTime FROM slots';
        $queryParams = [];
        
        $acceptedUrlParams = array('day', 'type', 'limit', 'page');
        
        if($_SERVER['QUERY_STRING']) {
            $urlParams = explode("&", $_SERVER['QUERY_STRING']);
            
            if(in_array(explode("=", $urlParams[0])[0], $acceptedUrlParams)) {
                
                if(count($urlParams) === 1) {
                    // show all slots for a specific day
                    if(isset($_REQUEST['day'])) {
                        $query .= " WHERE day LIKE :day";
                        $term = $this->sanitiseString("%".$_REQUEST['day']."%");
                        $queryParams = ["day" => $term];
                    }

                    // show all slots of type "session" or "break"
                    if(isset($_REQUEST['type'])) {
                        $query .= " WHERE type = :type";
                        $query .= " ORDER BY slots.dayInt";
                        $term = $this->sanitiseString($_REQUEST['type']);
                        $term = strtoupper($term);
                        $queryParams = ["type" => $term];
                    }
                    
                    if(isset($_REQUEST['id'])) {
                        $query .= " WHERE slots.slotId = :id";
                        $query .= " ORDER BY slots.dayInt";
                        $num = $this->sanitiseNum($_REQUEST['id']);
                        $queryParams = ["id" => $num];
                    }

                    if(isset($_REQUEST['limit'])) {
                        $query .= " ORDER BY slots.dayInt";
                        $query .= " LIMIT :num";
                        $num = $this->sanitiseNum($_REQUEST['limit']);
                        $queryParams["num"] = $num;
                    }

                    if(isset($_REQUEST['page'])) {
                        $query .= " ORDER BY slots.dayInt";
                        $query .= " LIMIT 10";
                        $query .= " OFFSET ";
                        $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
                    }
                } elseif(count($urlParams) > 1) {
                    // more than one URL parameter is an invalid request so return no data
                    $query = "";
                }
            } else {
                // if the URL parameter keyword is not supported return no results
                $query = "";
            }
        } else {
            // if no URL parameters then order all results by day (sat->thurs)
            $query .= " ORDER BY slots.dayInt";
        }
        
        return ($this->recordSet->getJSONRecordSet($query, $queryParams));
    }
    
    /**
    * Creates the sessions endpoint by returning all sessions in the conference 
    * or if a valid url query is provided returns the filtered sessions
    * 
    * @return JSON object that contains the sesssion id, title, type, session chair, room and slot id
    */
    private function jsonSessions() {
        $query = 'select sessionId, sessions.name AS title, session_types.name AS sessionType, ifnull(authors.name, "N/A") AS sessionChair, rooms.name as room, sessions.slotId AS slotId FROM sessions JOIN session_types ON (sessions.typeId = session_types.typeId) LEFT JOIN authors ON (sessions.chairId = authors.authorId) JOIN rooms ON (sessions.roomId = rooms.roomId)';
        $queryParams = [];
        
        $acceptedUrlParams = array('search', 'slot', 'id', 'limit', 'page', 'type');
        
        if($_SERVER['QUERY_STRING']) {
            $urlParams = explode("&", $_SERVER['QUERY_STRING']);
            
            if(in_array(explode("=", $urlParams[0])[0], $acceptedUrlParams)) {

                if(count($urlParams) === 2) {
                    if(explode("=", $urlParams[0])[0] === "search" && explode("=", $urlParams[1])[0] === "page") {
                        $query .= " WHERE title LIKE :title ORDER BY title LIMIT 10 OFFSET ";
                        $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
                        $term = $this->sanitiseString("%".$_REQUEST['search']."%");
                        $queryParams = ["title" => $term];
                    } else {
                        $query = "";
                    }
                } elseif(count($urlParams) === 1) {
                    
                    // returns all sessions in a specific slot
                    if(isset($_REQUEST['slot'])) {
                        $query .= " WHERE slotId = :id";
                        $num = $this->sanitiseNum($_REQUEST['slot']);
                        $queryParams = ["id" => $num];
                    }

                    if(isset($_REQUEST['search'])) {
                        $query .= " WHERE title LIKE :name";
                        $term = $this->sanitiseString("%".$_REQUEST['search']."%");
                        $queryParams = ["name" => $term];
                    }
                    
                    // if the value of type is all return all session types
                    if(isset($_REQUEST['type'])) {
                        if($_REQUEST['type'] === "all") {
                            $query = "select name from session_types";
                        // or if other string value, use to search the session types
                        } else {
                            $query .= " WHERE sessionType LIKE :name";
                            $term = $this->sanitiseString("%".$_REQUEST['type']."%");
                            $queryParams = ["name" => $term];
                        }
                    }

                    if(isset($_REQUEST['id'])) {
                        $query .= " WHERE sessionId = :id";
                        $num = $this->sanitiseNum($_REQUEST['id']);
                        $queryParams = ["id" => $num];
                    }

                    if(isset($_REQUEST['limit'])) {
                        $query .= " LIMIT :num";
                        $num = $this->sanitiseNum($_REQUEST['limit']);
                        $queryParams["num"] = $num;
                    }

                    if(isset($_REQUEST['page'])) {
                        $query .= " ORDER BY name";
                        $query .= " LIMIT 10";
                        $query .= " OFFSET ";
                        $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
                    }   
                } else {
                    $query = "";
                }
            } else {
                $query = "";
            }
        }
            
        return ($this->recordSet->getJSONRecordSet($query, $queryParams));
    }
    
    /**
    * Creates the rooms endpoint by returning all rooms data
    * 
    * @return room id and room name
    */
    private function jsonRooms() {
        $query = "SELECT roomId AS id, rooms.name FROM rooms";
        $queryParams = [];        
        return ($this->recordSet->getJSONRecordSet($query, $queryParams));
    }
    
    /**
    * Creates the content endpoint by returning all presentations in the conference 
    * or if a valid url query is provided returns the filtered presentations
    * 
    * @return JSON object that contains the presentation id, title, abstract, awards and authors
    */
    private function jsonContent() {
        $query = 'SELECT content.contentId AS id, title, ifnull(nullif(abstract, ""), "N/A") AS abstract, ifnull(nullif(award, ""), "N/A") AS award, group_concat(ifnull(authors.name, "N/A")) AS "authors" FROM content LEFT JOIN content_authors ON (content.contentId = content_authors.contentId) LEFT JOIN authors ON (content_authors.authorId = authors.authorId)';
        $queryParams = [];
        
        $acceptedUrlParams = array('search', 'author-name', 'author-id', 'id', 'session', 'limit', 'page');
        
        if($_SERVER['QUERY_STRING']) {
            $urlParams = explode("&", $_SERVER['QUERY_STRING']);
            
            if(in_array(explode("=", $urlParams[0])[0], $acceptedUrlParams)) {
        
                if(count($urlParams) === 2) {

                    if(explode("=", $urlParams[0])[0] === "search" && explode("=", $urlParams[1])[0] === "page") {
                        $query .= " WHERE title LIKE :title GROUP BY content.contentId LIMIT 10 OFFSET ";
                        $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
                        $term = $this->sanitiseString("%".$_REQUEST['search']."%");
                        $queryParams = ["title" => $term];
                    } else {
                        $query = ""; 
                    }

                } elseif(count($urlParams) === 1) {
                    
                    // search for authors and return the presentations they are involved in
                    if(isset($_REQUEST['author-name'])) {
                        $query .= " GROUP BY content.contentId HAVING authors LIKE :name";
                        $term = $this->sanitiseString("%".$_REQUEST['author-name']."%");
                        $queryParams = ["name" => $term];
                    }
                    
                    // show all presentations by a specific author using their id
                    if(isset($_REQUEST['author-id'])) {
                        $query .= " WHERE authors.authorId = :id GROUP BY content.contentId";
                        $num = $this->sanitiseNum($_REQUEST['author-id']);
                        $queryParams = ["id" => $num];
                    }
                    
                    // show all presentations in a specific session using the session id
                    if(isset($_REQUEST['session'])) {
                        $query .= " JOIN sessions_content ON (content.contentId = sessions_content.contentId) JOIN sessions ON (sessions_content.sessionId = sessions.sessionId) WHERE sessions.sessionId = :id GROUP BY content.contentId";
                        $num = $this->sanitiseNum($_REQUEST['session']);
                        $queryParams = ["id" => $num];
                    }

                    if(isset($_REQUEST['search'])) {
                        $query .= " WHERE title LIKE :name GROUP BY content.contentId";
                        $term = $this->sanitiseString("%".$_REQUEST['search']."%");
                        $queryParams = ["name" => $term];
                    }

                    if(isset($_REQUEST['id'])) {
                        $query .= " WHERE content.contentId = :id GROUP BY content.contentId";
                        $num = $this->sanitiseNum($_REQUEST['id']);
                        $queryParams = ["id" => $num];
                    }

                    if(isset($_REQUEST['limit'])) {
                        $query .= " GROUP BY content.contentId LIMIT :num";
                        $num = $this->sanitiseNum($_REQUEST['limit']);
                        $queryParams["num"] = $num;
                    }

                    if(isset($_REQUEST['page'])) {
                        $query .= " GROUP BY content.contentId LIMIT 10 OFFSET ";
                        $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
                    }   
                }
            } else {
                $query = "";
            }
        } else {
            $query .= " GROUP BY content.contentId";   
        }
        return ($this->recordSet->getJSONRecordSet($query, $queryParams));
    }
    
    /**
    * Creates the login endpoint. Fetches a login request and authorises the user by checking
    * the password. Also creates an authorisation token that gets returned upon valid login
    * 
    * @return JSON object with a status code and message and if login successful also return
    * if admin as boolean and an authorisation token
    */
    private function json_login() {
        $msg = "Invalid request. Username and password required.";
        $status = 400;
        $token = null;
        $admin = false;
        $input = json_decode(file_get_contents("php://input"));
        $jwtkey = JWTKEY;

        if($input) {
            if (isset($input->email) && isset($input->password)) {  
                $query  = "SELECT username, password, admin FROM users WHERE email LIKE :email";
                $params = ["email" => $input->email];
                $res = json_decode($this->recordSet->getJSONRecordSet($query, $params), true);
                $password = ($res['count']) ? $res['data'][0]['password'] : null;
                
                if(password_verify($input->password, $password)) {
                    if($res['data'][0]['admin'] == 1) {
                        $admin = true;
                    }
                    $msg = "User authorised. Welcome ". $res['data'][0]['username'] . ".";
                    $status = 200;
                    $token = array();
                    $token['email'] = $input->email;
                    $token['username'] = $res['data'][0]['username'];
                    $token['iat'] = time();
                    $token['exp'] = time() + (60*60);
                    $token = \Firebase\JWT\JWT::encode($token, $jwtkey);
                } else { 
                    $msg = "Username or password are invalid.";
                    $status = 401;
                }
            }
        } 
        return json_encode(array("status" => $status, "message" => $msg, "admin" => $admin, "token" => $token));
    }
    
    /**
    * Creates the update title endpoint. Gets data from a post request, authenticates the user by checking
    * the token and if valid token update the title
    * 
    * @return JSON object with a status code and message
    */
    private function jsonUpdateTitle() {
        
        $input = json_decode(file_get_contents("php://input"));
        $jwtkey = JWTKEY;
        
        if(!$input) {
            return json_encode(array("status" => 400, "message" => "Invalid request."));
        }
        
        if(is_null($input->title) || is_null($input->sessionId) || is_null($input->token)) {
            return json_encode(array("Status" => 401, "message" => "Missing values."));
        }
        
        try {
            $tokenDecoded = \Firebase\JWT\JWT::decode($input->token, $jwtkey, array('HS256'));
        }
        catch(UnexpectedValueException $e) {
            return json_encode(array("status" => 401, "message" => $e->getMessage()));
        }
        
        $query = "UPDATE sessions SET name = :title WHERE sessionId = :id";
        $params = ["title" => $input->title, "id" => $input->sessionId];
        $res = $this->recordSet->getJSONRecordSet($query, $params);
        return json_encode(array("status" => 200, "message" => "Request successful."));
    }
    
    /**
    * @return the page for a specific API endpoint
    */
    public function get_page() {
        return $this->page;
    }
}

?>