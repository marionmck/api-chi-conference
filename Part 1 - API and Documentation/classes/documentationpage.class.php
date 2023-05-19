<?php

/**
 *  Updates the mainHTML function to display the main section of the documentation page 
 *
 *  @param string $heading the heading for the about page
 *  @param string $text an html paragraph with a description about the page
 *  @return the heredoc for the main section of the documentation page
 * 
 *  @author Mario Nemecek
 */

class DocumentationPage extends WebPage {
    
    protected function mainHTML($heading, $text) {
        return <<<MAIN
<div id="main">
    <div id=mainContainer>
        <h1>$heading</h1>
        $text
        
        <!-- API Introduction -->
        <div class="endpoint">
            <h2 class="endpointHeading">API Introduction</h2>
            <p>The link below will direct you to the welcome page of the API. You will be able to see a list of all the available endpoints.</p>
            <span class="codeBlock">http://unn-w17015200.newnumyspace.co.uk/kf6012/assessment/part1/api/</span>
            
            <h3 class="heading3">Query Parameters</h3>
            <p>Query parameters can be used with the endpoints to filter the returned results.</p>
            <p>This API either supports one or two parameters, these will be specified in each endpoint section below. An example of a URL query is shown below:</p>
            
            <span class="codeBlock">/api/endpoint?parameter1=value</span><br>
            <span class="codeBlock">?parameter1=value1&ampparameter2=value2</span>
            
            <p>The parameters listed below can be used with any of the endpoints that return data.</p>
            <span class="codeBlock">/api/endpoint?id=1234</span>
            <span class="codeBlock">/api/endpoint?limit=25</span>
            <span class="codeBlock">/api/endpoint?page=2</span>
            <p>To search for some specific data like an author or a specific session, you can use the <strong>id</strong> parameter. To limit the number of returned results, you can use the <strong>limit</strong> parameter. And to divide the returned data into pages of 10 results, you can use the <strong>page</strong> parameter.</p>
        </div>
        
        <!-- Authors Endpoint -->
        <div class="endpoint">
            <h2 class="endpointHeading">Authors</h2>
            <p>The authors endpoint returns the full names of all the authors.</p>
            <span class="codeBlock">/api/authors/</span><br>
<pre class="codeBlock">
{
    "name" : "John Smith"
}</pre>

            <h3 class="heading3">Parameters</h3>
            <p>You can search for any author by name. In the example below, the search term <strong>daniel</strong> is passed into the <strong>search</strong> parameter. If the number of returned search results is very high, then another parameter <strong>page</strong> can also be used to make it easier to manage the results by only returning 10 results per page.</p>
            <span class="codeBlock">/api/authors?search=daniel</span>
            <span class="codeBlock">/api/authors?search=daniel&page=2</span>
        </div>
        
        <!-- Slots Endpoint -->
        <div class="endpoint">
            <h2 class="endpointHeading">Slots</h2>
            <p>The slots endpoint returns data about each available slot including the slot type (session or break), day, start time and end time.</p>
            <span class="codeBlock">/api/slots/</span><br>
<pre class="codeBlock">
{
    "id" : "1234",
    "type" : "session",
    "day" : "Monday",
    "startTime" : "9:00",
    "endTime" : "10:30"
}</pre>       
            <h3 class="heading3">Parameters</h3>
            <p>To search for all the slots in a specific day use the day paramter:</p>
            <span class="codeBlock">/api/slots?day=monday</span><br>
            <p>To seperate the slots into different types (session or break) use the type parameter:</p>
            <span class="codeBlock">/api/slots?type=session</span>
            <span class="codeBlock">/api/slots?type=break</span>
        </div>
        
        <!-- Sessions Endpoint -->
        <div class="endpoint">
            <h2 class="endpointHeading">Sessions</h2>
            <p>The sessions endpoint returns data about each session including the title, type of session, session chair and room.</p>
            <span class="codeBlock">/api/sessions/</span><br>
<pre class="codeBlock">
{
    "sessionId" : "1234"
    "title" : "Gender",
    "sessionType" : "Paper",
    "sessionChair" : "John Smith",
    "room" : "516AB", 
    "slotId" : "1234"
}</pre> 
            
            <h3 class="heading3">Parameters</h3>
            
            <p>You can search for any session by title. In the example below, the search term <strong>design</strong> is passed into the <strong>search</strong> parameter. If the number of returned search results is very high, then the <strong>page</strong> parameter can also be used to make it easier to manage the results by only returning 10 results per page.</p>
            <span class="codeBlock">/api/sessions?search=design</span>
            <span class="codeBlock">/api/sessions?search=design&page=2</span>
            
            <p>You can also filter the sessions by which slot they are in. The slot parameter can be used and the value would need to be the ID of the specific slot.</p>
            <span class="codeBlock">/api/sessions?slot=1234</span>
            
            <p>You can return all the sessions of a specific type. The type parameter can be used and the value would need to be the name of the type of session. As a helpful guide, setting the value of type to "all" will return the names of all session types.</p>
            <span class="codeBlock">/api/sessions?type=keynote</span>
            <span class="codeBlock">/api/sessions?type=all</span>
        </div>
        
        <!-- Content Endpoint -->
        <div class="endpoint">
            <h2 class="endpointHeading">Content</h2>
            <p>The content endpoint returns data about the content being presented at the conference. This includes the title, abstract, awards and authors.</p>
            <span class="codeBlock">/api/content/</span><br>
<pre class="codeBlock">
{
    "id" : "1234",
    "title" : "content title",
    "abstract" : "content abstract",
    "award" : "award name",
    "authors" : "John Smith"
}</pre> 
            
            <h3 class="heading3">Parameters</h3>
            
            <p>You can search all content by title. In the example below, the search term <strong>health</strong> is passed into the <strong>search</strong> parameter. If the number of returned search results is very high, then the <strong>page</strong> parameter can also be used to make it easier to manage the results by only returning 10 results per page.</p>
            <span class="codeBlock">/api/content?search=health</span>
            <span class="codeBlock">/api/content?search=health&page=2</span>
            
            <p>You can filter the content by the authors. The author-name or author-id parameter can be used and the value would need to be the name of the author (note: could also search the authors names using any string like "son" or "jack", it does not need to be the exact name of the author) and the ID of a specific author respectively.</p>
            <span class="codeBlock">/api/content?author-name=son</span>
            <span class="codeBlock">/api/content?author-id=1234</span>
            
            <p>You can find all the content for a specific session. The session parameter can be used for this and the value would need to be the id of the session.</p>
            <span class="codeBlock">/api/content?session=1234</span>
        </div>
        
        <!-- Rooms Endpoint -->
        <div class="endpoint">
            <h2 class="endpointHeading">Rooms</h2>
            
            <p>The rooms endpoint returns the id and name of each room that will be used for the conference.</p>
            <span class="codeBlock">/api/rooms/</span><br>
<pre class="codeBlock">
{
    "id" : "1234",
    "name" : "250C"
}</pre> 
             <p><strong>Note:</strong> this endpoint does not support any query parameters.</p>
        </div>
        
        <!-- Login Endpoint -->
        <div class="endpoint">
            <h2 class="endpointHeading">Login</h2>
            <p>The login endpoint authorises a user with the correct login details to access the admin page.</p>
            <span class="codeBlock">/api/login /</span>
            <p><strong>Note:</strong> this endpoint does not support any query parameters.</p>
        </div>
        
        <!-- Update Title Endpoint -->
        <div class="endpoint">
            <h2 class="endpointHeading">Update Title</h2>
            <p>The update-title endpoint can be used to update the title of content. Authentication is requried as only admin users can update the title.</p>
            <span class="codeBlock">/api/update-title/</span>
            <p><strong>Note:</strong> this endpoint does not support any query parameters.</p>
        </div>
    </div>
</div>
MAIN;
    }
    
}

?>