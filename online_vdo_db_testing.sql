create schema video;
use video;

drop schema video;
drop table buys;
drop table customer;
drop table online_video;
drop table video_provider;
drop trigger disjoint_insert_online_video;
drop trigger disjoint_update_online_video;
drop trigger total_participation_insert_online_video;
drop trigger total_participation_update_online_video;
drop trigger check_null_on_insert_movie;
drop trigger check_null_on_update_movie;
drop trigger check_null_on_insert_tvshow;
drop trigger check_null_on_update_tvshow;
drop function discount_10percent;

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

-- *********************** TESTING **************************
insert into video_provider values ('youtube.com', 'YouTube');
insert into video_provider values ('amazon.com', 'Amazon');
insert into video_provider values ('vudu.com', 'Vudu');
insert into video_provider values ('netflix.com', 'Netflix');

select discount_10percent(5.75);
select discount_10percent(8.99);

-- > isMovie cannot be NULL
insert into online_video
values ('amazon.CallMeByYourName','Call Me By Your Name', '2017', null, 130, null, null, 'amazon.com');

-- > isTVShow cannot be NULL
insert into online_video
values ('amazon.CallMeByYourName','Call Me By Your Name', '2017', true, 130, null, null, 'amazon.com');

-- > 1 row(s) affected
insert into online_video
values ('amazon.CallMeByYourName','Call Me By Your Name', '2017', true, 130, false, null, 'amazon.com');

-- > Subclasses must be disjoint
insert into online_video
values ('youtube.CallMeByYourName','Call Me By Your Name', '2017', true, 130, true, null, 'youtube.com');

-- > Subclasses must be total participation
insert into online_video
values ('youtube.CallMeByYourName','Call Me By Your Name', '2017', false, 130, false, 1, 'youtube.com');

-- > Movie must specify only duration
insert into online_video
values ('youtube.CallMeByYourName','Call Me By Your Name', '2017', true, null, false, null, 'youtube.com');

-- > Movie must specify only duration
insert into online_video
values ('youtube.CallMeByYourName','Call Me By Your Name', '2017', true, null, false, 1, 'youtube.com');

-- > Movie must specify only duration
insert into online_video
values ('youtube.CallMeByYourName','Call Me By Your Name', '2017', true, 130, false, 1, 'youtube.com');

-- > 1 row(s) affected
insert into online_video
values ('youtube.CallMeByYourName','Call Me By Your Name', '2017', true, 130, false, null, 'youtube.com');

-- > TV Show must specify only number of seasons
insert into online_video
values ('vudu.Silent','Silent', '2022', false, null, true, null, 'vudu.com');

-- > TV Show must specify only number of seasons
insert into online_video
values ('vudu.Silent','Silent', '2022', false, 40, true, null, 'vudu.com');

-- > TV Show must specify only number of seasons
insert into online_video
values ('vudu.Silent','Silent', '2022', false, 40, true, 1, 'vudu.com');

-- > 1 row(s) affected
insert into online_video
values ('vudu.Silent','Silent', '2022', false, null, true, 1, 'vudu.com');

-- > Subclasses must be disjoint
update online_video
set isTVShow = true
where vdo_url = 'youtube.CallMeByYourName';

-- > Subclasses must be total participation
update online_video
set isMovie = false
where vdo_url = 'youtube.CallMeByYourName';

-- > Subclasses must be disjoint
update online_video
set isMovie = true
where vdo_url = 'vudu.Silent';

-- > Subclasses must be total participation
update online_video
set isTVShow = false
where vdo_url = 'vudu.Silent';

-- > Subclasses must be disjoint
update online_video
set isMovie = true, isTVShow = true
where vdo_url = 'vudu.Silent';

-- > Subclasses must be total participation
update online_video
set isMovie = false, isTVShow = false
where vdo_url = 'vudu.Silent';

-- > TV Show must specify only number of seasons
update online_video
set isMovie = false, isTVShow = true
where vdo_url = 'youtube.CallMeByYourName';

-- > TV Show must specify only number of seasons
update online_video
set isMovie = false, isTVShow = true, duration = null
where vdo_url = 'youtube.CallMeByYourName';

-- > 1 row(s) affected
update online_video
set isMovie = false, isTVShow = true, duration = null, num_of_seasons = 1
where vdo_url = 'youtube.CallMeByYourName';

-- > Movie must specify only duration
update online_video
set isMovie = true, isTVShow = false
where vdo_url = 'youtube.CallMeByYourName';

-- > Movie must specify only duration
update online_video
set isMovie = true, isTVShow = false, num_of_seasons = null
where vdo_url = 'youtube.CallMeByYourName';

-- > 1 row(s) affected
update online_video
set isMovie = true, isTVShow = false, num_of_seasons = null, duration = 130
where vdo_url = 'youtube.CallMeByYourName';


insert into online_video
values ('netflix.20thCenturyGirl','20th Century Girl', '2022', true, 130, false, null, 'netflix.com');
insert into customer values (45235, 'John', null, 'Doe', null);
insert into customer values (45236, 'Sara', null, 'Sue', null);
insert into customer values (45237, 'Adam', null, 'William', 9164545544);
insert into customer values (45238, 'Alice', null, 'Bob', 9161111111);
insert into buys values (45235, 'vudu.Silent', 5.99);
insert into buys values (45235, 'youtube.CallMeByYourName', 11.99);
insert into buys values (45236, 'amazon.CallMeByYourName', 12.99);
insert into buys values (45237, 'amazon.CallMeByYourName', 8.99);
insert into buys values (45238, 'vudu.Silent', 5.99);
insert into buys values (45238, 'netflix.20thCenturyGirl', 6.99);
insert into buys values (45236, 'netflix.20thCenturyGirl', 13.99);

select * from buys;
select * from customer;
select * from online_video;
select * from video_provider;

-- a customer who buys online video from netflix.com -> Alice Bob
select fname, lname
from customer, buys, online_video
where provider_url = 'netflix.com' and
      customer.customerID = buys.customerID and
      buys.vdo_url = online_video.vdo_url;
      
-- a customer who buys online video from Amazon -> Sara Sue, Adam William
select fname, lname
from customer, buys, online_video, video_provider
where provider_name = 'Amazon' and
      customer.customerID = buys.customerID and
      buys.vdo_url = online_video.vdo_url and 
      online_video.provider_url = video_provider.provider_url;
      
-- display name of video and count how many of each videos are bought -> Call Me By Your Name | 3, Silent | 2, 20th Century Girl | 2
select online_video.vdo_name, count(*)
from buys, online_video
where buys.vdo_url = online_video.vdo_url
group by online_video.vdo_name;
      
-- display name of video, name of provider, price bought by customer
select online_video.vdo_name, video_provider.provider_name, price
from buys, online_video, video_provider
where buys.vdo_url = online_video.vdo_url and 
      online_video.provider_url = video_provider.provider_url
group by video_provider.provider_name
having price > 10.00
      


