CREATE TABLE topic (
  pk_topic_id SERIAL,
  author varchar(25) NOT Null,
  title VARCHAR(45) NOT NULL,
  message TEXT NOT NULL,
  timestamp TIMESTAMP,
  PRIMARY KEY(pk_topic_id)
);

CREATE TABLE reply (
  pk_reply_id SERIAL,
  fk_topic_id integer not null,
  fk_reply_id integer,
  timestamp TIMESTAMP,
  author varchar(25) NOT NULL,
  message TEXT NOT NULL default '',
  position integer default 1,
  parent integer,
  PRIMARY KEY(pk_reply_id),
  FOREIGN KEY (fk_topic_id) REFERENCES topic (pk_topic_id),
  FOREIGN KEY (fk_reply_id) REFERENCES reply (pk_reply_id) ON DELETE CASCADE
);

INSERT INTO topic VALUES (1, 'Name1', 'Title1', 'Message1', '2010-12-02 23:27:05.252682');
INSERT INTO topic VALUES (2, 'Name2', 'Title2', 'Message2', '2010-12-02 23:31:11.807236');

INSERT INTO reply VALUES (1, 2, NULL, '2010-12-02 23:33:14.963945', 'reply_name1', 'Reply1 to Message2', 1, NULL);
INSERT INTO reply VALUES (2, 2, 1, '2010-12-02 23:33:31.313822', 'reply_name2', 'Reply2 to Reply1', 2, 1);









