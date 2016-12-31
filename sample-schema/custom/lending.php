CREATE TABLE %s (
    id bigint(20) NOT NULL AUTO_INCREMENT ,
	user_id bigint(20) NULL DEFAULT NULL,
    library_id bigint(20) NOT NULL,
	book_id int(10) NOT NULL,
	lending_start DATETIME,
	lending_end DATETIME,
	lending_status text,
	PRIMARY KEY (id)
)