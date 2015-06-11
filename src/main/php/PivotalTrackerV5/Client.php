<?php
/**
 * This file is part of the PivotalTracker API component.
 *
 * @version 1.0
 * @copyright Copyright (c) 2012 Manuel Pichler
 * @license LGPL v3 license <http://www.gnu.org/licenses/lgpl>
 */

namespace PivotalTrackerV5;

/**
 * Simple Pivotal Tracker api client.
 *
 * This class is loosely based on the code from Joel Dare's PHP Pivotal Tracker
 * Class: https://github.com/codazoda/PHP-Pivotal-Tracker-Class
 */
class Client
{
    /**
     * Base url for the PivotalTracker service api.
     */
    const API_URL = 'https://www.pivotaltracker.com/services/v5';

    /**
     * Name of the context project.
     *
     * @var string
     */
    private $project;

    /**
     * Used client to perform rest operations.
     *
     * @var \PivotalTracker\Rest\Client
     */
    private $client;

    private $apiKey;


    /**
     *
     * @param string $apiKey  API Token provided by PivotalTracking
     * @param string $project Project ID
     */
    public function __construct( $apiKey, $project = null)
    {
        $this->client = new Rest\Client( self::API_URL );
        $this->client->addHeader( 'Content-type', 'application/json' );
        $this->client->addHeader( 'X-TrackerToken',  $apiKey );

        if ($project) {
            $this->project = $project;
        }
    }

    public function setProject($project)
    {
        $this->project = $project;
    }


    /**
     * Adds a new story to PivotalTracker and returns the newly created story
     * object.
     *
     * @param array $story
     * @param string $name
     * @param string $description
     * @return object
     */
    public function addStory( array $story  )
    {

        return $this->processResponse(
            $this->client->post(
                "/projects/{$this->project}/stories",
                json_encode( $story )
            )
        );
    }

    /**
     * Updates the story on PivotalTracker and returns its
     * object.
     *
     * @param integer $id
     * @param array $story
     * @param string $name
     * @param string $description
     * @return object
     */
    public function updateStory( $id, array $story  )
    {

        return $this->processResponse(
            $this->client->put(
                "/projects/{$this->project}/stories/{$id}",
                json_encode( $story )
            )
        );
    }

    /**
     * Adds a new task with <b>$description</b> to the story identified by the
     * given <b>$storyId</b>.
     *
     * @param integer $storyId
     * @param string $description
     * @return \SimpleXMLElement
     */
    public function addTask( $storyId, $description )
    {
        return simplexml_load_string(
            $this->client->post(
                "/projects/{$this->project}/stories/$storyId/tasks",
                json_encode( array( 'description' => $description ) )

            )
        );
    }

    /**
     * Adds the given <b>$labels</b> to the story identified by <b>$story</b>
     * and returns the updated story instance.
     *
     * @param integer $storyId
     * @param array $labels
     * @return object
     */
    public function addLabels( $storyId, array $labels )
    {
        return $this->processResponse(
            $this->client->put(
                "/projects/{$this->project}/stories/$storyId",
                json_encode(  $labels )
            )
        );
    }

    /**
     * Returns all stories for the context project.
     *
     * @param array $filter
     * @return object
     */
    public function getStories( $filter = null )
    {
        return $this->processResponse(
            $this->client->get(
                "/projects/{$this->project}/stories",
                $filter ? array( 'filter' => $filter ) : null
            )
        );
    }


    /**
     * Returns story by its id.
     *
     * @param integer $id
     * @return object
     */
    public function getStory( $id )
    {
        return $this->processResponse(
            $this->client->get(
                "/projects/{$this->project}/stories/{$id}"
            )
        );
    }


    public function getProjectIdByName( $name )
    {
        $projects = $this->getProjects();

        $project = null;
        foreach ($projects as $item) {
            if ($item['name'] == $name) return $item['id'];
        }

        return false;
    }


    /**
     * Returns a list of projects for the currently authenticated user.
     *
     * @return object
     */
    public function getProjects()
    {
        return $this->processResponse(
            $this->client->get(
                "/projects"
            )
        );

    }

    /**
     * Returs json decoded respose in an array instead of std objects
     *
     * $param response std class object
     * @return array
     *
     */

    protected function processResponse($response){
        return json_decode($response,true);
    }


}
