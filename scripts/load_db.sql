CREATE TABLE awards (
    'action'     varchar(40) CONSTRAINT firstkey PRIMARY KEY,
    'from'       varchar(40) NOT NULL,
    'to'         varchar(40) NOT NULL,
    'award'      varchar(40),
    'value'      integer NOT NULL
);