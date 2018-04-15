/*
rides2017
  Origin
  Destination
  Throughput
  DateTime
station
*/

-- select Origin, Destination, sum(Throughput) from rides2017 where Origin="12TH" and Destination="12TH";

/*
select Origin, Destination, max(s) from
(
  select Origin, Destination, sum(Throughput) as s from rides2017
  (
    select Origin, Destination, Throughput from rides2017
    where weekday(DateTime)=0 or weekday(DateTime)=1 or weekday(DateTime)=2 or weekday(DateTime)=3 or weekday(DateTime)=4
  ) as t1 -- where Origin="12TH" and Destination="12TH"
  group by Origin, Destination
) as t2
;
*/

/*
select Origin, Destination from rides2017
where weekday(DateTime)=0 or weekday(DateTime)=1 or weekday(DateTime)=2 or weekday(DateTime)=3 or weekday(DateTime)=4
group by Origin, Destination
order by sum(Throughput) desc
limit 1;
*/

/*
select Destination, avg(Throughput) from rides2017
where weekday(DateTime)=0 and hour(DateTime)>=7 and hour(DateTime)<10
group by Destination
order by avg(Throughput) desc
limit 5;
*/

select Origin from rides2017
group by Origin
having max(Throughput)>100*avg(Throughput);
