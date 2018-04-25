DROP TABLE IF EXISTS MovieDirector;
DROP TABLE IF EXISTS MovieActor;
DROP TABLE IF EXISTS Review;
DROP TABLE IF EXISTS MovieGenre;
DROP TABLE IF EXISTS Movie;
DROP TABLE IF EXISTS Actor;
DROP TABLE IF EXISTS Director;
DROP TABLE IF EXISTS MaxPersonID;
DROP TABLE IF EXISTS MaxMovieID;


create table Movie(
	id					INT,
	title				varchar(100) NOT NULL,
	year				INT,
	rating				varchar(10),
	company				varchar(50),

	primary key(id), -- primary key constraints
	check (year >= 1800) -- CHECK constrains

)ENGINE = INNODB;

create table Actor(
	id					INT,
	last_name			varchar(20),
	first_name			varchar(20),
	sex					varchar(6),
	dob					date,
	dod					date,

	primary key(id), -- primary key constraints
	check (sex='Male' or sex='Female') -- CHECK constraints

)ENGINE = INNODB;

create table Director(
	id					INT,
	last_name			varchar(20),
	first_name			varchar(20),
	dob					date,
	dod					date,

	primary key(id) -- primary key constraints

)ENGINE = INNODB;

create table MovieGenre(
	mid					INT,
	genre 				varchar(20),

	Foreign key(mid) references Movie(id) -- referential integrity constraints

	-- primary key(mid)
)ENGINE = INNODB;

create table MovieDirector(
	mid					INT,
	did 				INT,

	primary key(mid, did), -- primary key constraints
	Foreign key(mid) references Movie(id), -- referential integrity constraints
	Foreign key(did) references Director(id) -- referential integrity constraints
)ENGINE = INNODB;

create table MovieActor(
	mid					INT,
	aid 				INT,
	role				varchar(50),

	primary key(mid, aid, role), -- primary key constraints
	Foreign key(mid) references Movie(id), -- referential integrity constraints
	Foreign key(aid) references Actor(id) -- referential integrity constraints
)ENGINE = INNODB;

create table Review(
	name 				varchar(20),
	time				timestamp,
	mid 				INT,
	rating				INT,
	comment				varchar(500),

	Foreign key(mid) references Movie(id), -- referential integrity constraints
	check (rating>=0 and rating<=5) -- CHECK constraints
)ENGINE = INNODB;

create table MaxPersonID(
	id 					INT
)ENGINE = INNODB;

create table MaxMovieID(
	id 					INT
)ENGINE = INNODB;



