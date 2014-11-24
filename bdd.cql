create keyspace bdd with replication = ('class':'SimpleStrategy','replication_factor':3};
use bdd;
create table tweet(id varchar, hashtag varchar, createdAt varchar, text varchar, long float, lat float, place varchar, retweetCount int, PRIMARY KEY(hashtag, id));
