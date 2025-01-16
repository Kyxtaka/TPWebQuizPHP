CREATE TABLE IF NOT EXISTS USERS (
  userId NUMBER(11) NOT NULL,
  username VARCHAR(255) NOT NULL UNIQUE,
  motDePasse VARCHAR(255) NOT NULL,
  PRIMARY KEY (userId)
);

CREATE TABLE IF NOT EXISTS ROLES (
  roleId NUMBER(11) NOT NULL,
  nomRole VARCHAR(255) NOT NULL UNIQUE CHECK(nomRole IN("ADMIN", "USER")),
  PRIMARY KEY (roleId)
);

CREATE TABLE IF NOT EXISTS USER_ROLES (
  userId NUMBER(11) NOT NULL,   
  roleId NUMBER(11) NOT NULL,
  PRIMARY KEY (userId, roleId),
  FOREIGN KEY (userId) REFERENCES USERS(userId),
  FOREIGN KEY (roleId) REFERENCES ROLES(roleId)
);

-- CREATE TABLE IF NOT EXISTS QCM (
--   id NUMBER(11) NOT NULL,
--   uuid VARCHAR(255) NOT NULL,
--   nomQCM VARCHAR(255) NOT NULL,
--   nombreQuestion NUMBER(11) NULL,
--   PRIMARY KEY (id)
-- );

CREATE TABLE IF NOT EXISTS QCMTENTATIVE (
  qcmUID VARCHAR(255) NOT NULL,
  userId NUMBER(11) NOT NULL,
  score NUMBER(11) NOT NULL,
  num NUMBER(11) NOT NULL,
  PRIMARY KEY (qcmUID, userId, num)
);

-- CREATE TABLE IF NOT EXISTS QUESTIONS (
--   id VARCHAR(255) NOT NULL,
--   qcmId NUMBER(11) NOT NULL,
--   question VARCHAR(255) NOT NULL,
--   typeQuestion VARCHAR(10) NOT NULL CHECK(typeQuestion IN("RADIO", "CHECKBOX", "TEXT")),
--   PRIMARY KEY (id),
--   FOREIGN KEY (qcmId) REFERENCES QCM(id)
-- );

-- CREATE TABLE IF NOT EXISTS ANSWERS (
--   id NUMBER(11) NOT NULL,
--   questionId NUMBER(11) NOT NULL,
--   answer VARCHAR(255) NOT NULL,
--   correct BOOLEAN NOT NULL,
--   PRIMARY KEY (id),
--   FOREIGN KEY (questionId) REFERENCES QUESTIONS(id)
-- );
