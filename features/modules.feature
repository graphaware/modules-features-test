Feature: Timetree
  In order to manage time in my graphs
  As a Neo4j user
  I need a module that handle it for me

  Background:
    Given an unsecure Neo4j connection on host "localhost" and port "7474"
    And the Neo4j database is empty

  Scenario: Module should create timetree and attach event
    Given the Neo4j database is empty
    And I create a node with label "Event" and with time property value "1446326262000"
    When I ask the timetree API for events in range from "1446326261000" to "1446326263000"
    Then it should return me "1" event

  Scenario: Module should add a uuid property to created nodes
    Given the Neo4j database is empty
    And I create a node with label "Event" and with time property value "1446326262000"
    When I issue the "MATCH (n:Event) RETURN n" statement
    Then it should return me a node with label "Event" and a property named "uuid"