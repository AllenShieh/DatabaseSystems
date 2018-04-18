create table Movie(
  id      int,
  title   varchar(100),
  year    int,
  rating  varchar(10),
  company varchar(50),
  PRIMARY KEY (id), -- primary key constraints
  CHECK (year>=1800) -- CHECK constraints
) ENGINE = INNODB;

create table Actor(
  id    int,
  last  varchar(20),
  first varchar(20),
  sex   varchar(6),
  dob   date,
  dod   date,
  PRIMARY KEY (id), -- primary key constraints
  CHECK (sex='Male' or sex='Female') -- CHECK constraints
) ENGINE = INNODB;

create table Director(
  id    int,
  last  varchar(20),
  first varchar(20),
  dob   date,
  dod   date,
  PRIMARY KEY (id) -- primary key constraints
) ENGINE = INNODB;

create table MovieGenre(
  mid   int,
  genre varchar(20),
  FOREIGN KEY (mid) REFERENCES Movie(id) -- referential integrity constraints
) ENGINE = INNODB;

create table MovieDirector(
  mid int,
  did int,
  PRIMARY KEY (mid, did), -- primary key constraints
  FOREIGN KEY (mid) REFERENCES Movie(id), -- referential integrity constraints
  FOREIGN KEY (did) REFERENCES Director(id) -- referential integrity constraints
) ENGINE = INNODB;

create table MovieActor(
  mid   int,
  aid   int,
  role  varchar(50),
  PRIMARY KEY (mid, aid, role), -- primary key constraints
  FOREIGN KEY (mid) REFERENCES Movie(id), -- referential integrity constraints
  FOREIGN KEY (aid) REFERENCES Actor(id) -- referential integrity constraints
) ENGINE = INNODB;

create table Review(
  name    varchar(20),
  time    timestamp,
  mid     int,
  rating  int,
  comment varchar(500),
  FOREIGN KEY (mid) REFERENCES Movie(id), -- referential integrity constraints
  CHECK (rating>=0 and rating<=5) -- CHECK constraints
) ENGINE = INNODB;

create table MaxPersonID(
  id int
) ENGINE = INNODB;

create table MaxMovieID(
  id int
) ENGINE = INNODB;
