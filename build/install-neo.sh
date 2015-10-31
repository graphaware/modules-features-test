#!/bin/bash

MODCONFIG=build/neo4j.properties
SERVERCONFIG=build/neo4j-server.properties

wget dist.neo4j.org/neo4j-enterprise-2.3.0-unix.tar.gz > null
mkdir neo
tar xzf neo4j-enterprise-2.3.0-unix.tar.gz -C neo --strip-components=1 > null
sed -i.bak 's/^\(dbms\.security\.auth_enabled=\).*/\1false/' ./neo/conf/neo4j-server.properties
wget http://products.graphaware.com/download/framework-server-enterprise/latest
wget http://products.graphaware.com/download/timetree/latest
wget http://products.graphaware.com/download/resttest/latest
wget http://products.graphaware.com/download/uuid/latest
mv *.jar neo/plugins
cat "$MODCONFIG" >> neo/conf/neo4j.properties
cat "$SERVERCONFIG" >> neo/conf/neo4j-server.properties

./neo/bin/neo4j start
