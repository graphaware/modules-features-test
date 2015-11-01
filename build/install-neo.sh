#!/bin/bash

MODCONFIG=build/neo4j.properties
SERVERCONFIG=build/neo4j-server.properties

wget dist.neo4j.org/neo4j-enterprise-2.3.0-unix.tar.gz > null
mkdir neo
tar xzf neo4j-enterprise-2.3.0-unix.tar.gz -C neo --strip-components=1 > null
sed -i.bak 's/^\(dbms\.security\.auth_enabled=\).*/\1false/' ./neo/conf/neo4j-server.properties
wget http://products.graphaware.com/download/framework-server-enterprise/graphaware-server-enterprise-all-2.3.0.35.jar
wget http://products.graphaware.com/download/resttest/graphaware-resttest-2.3.0.35.13.jar
wget http://products.graphaware.com/download/timetree/graphaware-timetree-2.3.0.35.24.jar
wget http://products.graphaware.com/download/uuid/graphaware-uuid-2.3.0.35.7.jar
mv *.jar neo/plugins
cat "$MODCONFIG" >> neo/conf/neo4j.properties
cat "$SERVERCONFIG" >> neo/conf/neo4j-server.properties

./neo/bin/neo4j start
