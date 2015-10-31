<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Neoxygen\NeoClient\ClientBuilder;
use GuzzleHttp\Client;
use PHPUnit_Framework_Assert as Assert;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{

    protected $client;

    protected $host;

    protected $port;

    protected $guzzle;

    protected $response;

    protected $result;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given an unsecure Neo4j connection on host :arg1 and port :arg2
     */
    public function anUnsecureNeojConnectionOnHostAndPort($arg1, $arg2)
    {
        $this->client = ClientBuilder::create()
            ->addConnection('default', 'http', $arg1, (int) $arg2)
            ->enableNewFormattingService()
            ->setAutoFormatResponse(true)
            ->build();

        $this->host = $arg1;
        $this->port = (int) $arg2;
    }

    /**
     * @Given the Neo4j database is empty
     */
    public function theNeojDatabaseIsEmpty()
    {
        $this->checkGuzzle();
        $this->guzzle->post('http://' . $this->host . ':' . $this->port . '/graphaware/resttest/clear');
    }

    /**
     * @Given I create a node with label :arg1 and with time property value :arg2
     */
    public function iCreateANodeWithLabelAndWithTimePropertyValue($arg1, $arg2)
    {
        $this->client->sendCypherQuery('CREATE (n:' . $arg1 . ' {time: {time}})', ['time' => (int) $arg2]);
    }

    /**
     * @When I ask the timetree API for events in range from :arg1 to :arg2
     */
    public function iAskTheTimetreeApiForEventsInRangeFromTo($arg1, $arg2)
    {
        $response = $this->guzzle->get('http://' . $this->host . ':' . $this->port  .'/graphaware/timetree/range/' . (int) $arg1 . '/' . (int) $arg2);

        $this->response = (string) $response->getBody();
    }

    /**
     * @Then it should return me :arg1 event
     */
    public function itShouldReturnMeEvent($arg1)
    {
        $c = (int) $arg1;
        $a = json_decode($this->response);
        Assert::assertCount(1, $a);
    }

    private function checkGuzzle()
    {
        if (null === $this->guzzle) {
            $this->guzzle = new Client();
        }
    }

    /**
     * @When I issue the :arg1 statement
     */
    public function iIssueTheStatement($arg1)
    {
        $result = $this->client->sendCypherQuery($arg1)->getResult();

        $this->result = $result;

    }

    /**
     * @Then it should return me a node with label :arg1 and a property named :arg2
     */
    public function itShouldReturnMeANodeWithLabelAndAPropertyNamed($arg1, $arg2)
    {
        $nodes = array_values($this->result->getNodes());
        $node = $nodes[0];
        Assert::assertTrue(in_array($arg1, $node->getLabels()));
        Assert::assertTrue(array_key_exists($arg2, $node->getProperties()));
    }
}
