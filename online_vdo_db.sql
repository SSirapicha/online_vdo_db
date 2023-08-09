CREATE TABLE CUSTOMER (
  customerID int         not null,
  fname      varchar(20) not null,
  middle     varchar(20),
  lname      varchar(20) not null,
  phone      char(10),
  PRIMARY KEY (customerID)
);

CREATE TABLE VIDEO_PROVIDER (
  provider_url  varchar(200) not null,
  provider_name varchar(50)  not null,
  PRIMARY KEY (provider_url)
);

CREATE TABLE ONLINE_VIDEO (
  vdo_url        varchar(200) not null,
  vdo_name       varchar(50)  not null,
  year_released  char(4)      not null,
  isMovie        bool         not null,
  duration       int,
  isTVShow       bool         not null,
  num_of_seasons int,
  provider_url   varchar(200) not null, 
  PRIMARY KEY (vdo_url),
  FOREIGN KEY (provider_url) REFERENCES VIDEO_PROVIDER (provider_url)
);

CREATE TABLE BUYS (
  customerID int          not null,
  vdo_url    varchar(200) not null,
  price      decimal(5,2) not null,
  PRIMARY KEY (customerID, vdo_url),
  FOREIGN KEY (customerID) REFERENCES CUSTOMER (customerID),
  FOREIGN KEY (vdo_url) REFERENCES ONLINE_VIDEO (vdo_url)
);

-- DISJOINT trigger when insert on ONLINE_VIDEO table
DELIMITER $$
CREATE TRIGGER DISJOINT_INSERT_ONLINE_VIDEO
	BEFORE INSERT ON ONLINE_VIDEO
	FOR EACH ROW
    BEGIN
		IF NEW.isMovie = TRUE AND NEW.isTVShow = TRUE
        THEN SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Subclasses must be disjoint';
		END IF;
	END $$
DELIMITER ;

-- DISJOINT trigger when update on ONLINE_VIDEO table
DELIMITER $$
CREATE TRIGGER DISJOINT_UPDATE_ONLINE_VIDEO
	BEFORE UPDATE ON ONLINE_VIDEO
	FOR EACH ROW
    BEGIN
		IF NEW.isMovie = TRUE AND NEW.isTVShow = TRUE
        THEN SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Subclasses must be disjoint';
		END IF;
	END $$
DELIMITER ;

-- TOTAL PARTICIPATION trigger when insert on ONLINE_VIDEO table
DELIMITER $$
CREATE TRIGGER TOTAL_PARTICIPATION_INSERT_ONLINE_VIDEO
	BEFORE INSERT ON ONLINE_VIDEO
	FOR EACH ROW
    BEGIN
		IF NEW.isMovie = FALSE AND NEW.isTVShow = FALSE
        THEN SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Subclasses must be total participation';
		END IF;
	END $$
DELIMITER ;

-- TOTAL PARTICIPATION trigger when update on ONLINE_VIDEO table
DELIMITER $$
CREATE TRIGGER TOTAL_PARTICIPATION_UPDATE_ONLINE_VIDEO
	BEFORE UPDATE ON ONLINE_VIDEO
	FOR EACH ROW
    BEGIN
		IF NEW.isMovie = FALSE AND NEW.isTVShow = FALSE
        THEN SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Subclasses must be total participation';
		END IF;
	END $$
DELIMITER ;

-- Check NULL when insert on ONLINE_VIDEO table if video is a movie
DELIMITER $$
CREATE TRIGGER CHECK_NULL_ON_INSERT_MOVIE
	BEFORE INSERT ON ONLINE_VIDEO
	FOR EACH ROW
    BEGIN
		IF (NEW.isMovie = TRUE AND (NEW.duration IS NULL OR NEW.num_of_seasons IS NOT NULL))
        THEN SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Movie must specify only duration';
		END IF;
	END $$
DELIMITER ;

-- Check NULL when update on ONLINE_VIDEO table if video is a movie
DELIMITER $$
CREATE TRIGGER CHECK_NULL_ON_UPDATE_MOVIE
	BEFORE UPDATE ON ONLINE_VIDEO
	FOR EACH ROW
    BEGIN
		IF (NEW.isMovie = TRUE AND (NEW.duration IS NULL OR NEW.num_of_seasons IS NOT NULL))
        THEN SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Movie must specify only duration';
		END IF;
	END $$
DELIMITER ;

-- Check NULL when insert on ONLINE_VIDEO table if video is a tv show
DELIMITER $$
CREATE TRIGGER CHECK_NULL_ON_INSERT_TVSHOW
	BEFORE INSERT ON ONLINE_VIDEO
	FOR EACH ROW
    BEGIN
		IF (NEW.isTVShow = TRUE AND (NEW.num_of_seasons IS NULL OR NEW.duration IS NOT NULL))
        THEN SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'TV Show must specify only number of seasons';
		END IF;
	END $$
DELIMITER ;

-- Check NULL when update on ONLINE_VIDEO table if video is a tv show
DELIMITER $$
CREATE TRIGGER CHECK_NULL_ON_UPDATE_TVSHOW
	BEFORE UPDATE ON ONLINE_VIDEO
	FOR EACH ROW
    BEGIN
		IF (NEW.isTVShow = TRUE AND (NEW.num_of_seasons IS NULL OR NEW.duration IS NOT NULL))
        THEN SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'TV Show must specify only number of seasons';
		END IF;
	END $$
DELIMITER ;
    
-- FUNCTION to calculate the price with 10 percent discounted
DELIMITER $$
CREATE FUNCTION DISCOUNT_10PERCENT (original_price float(5,2))
RETURNS float(5,2)
	BEGIN 
    DECLARE discounted_price float(5,2) DEFAULT 000.00;
    SET discounted_price = original_price - (original_price * 0.1);
    
    RETURN discounted_price;
    END $$
DELIMITER ;