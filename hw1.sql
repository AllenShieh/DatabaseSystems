/*
rides2017
  Origin
  Destination
  Throughput
  DateTime
station
*/

-- select Origin, Destination, sum(Throughput) from rides2017 where Origin="12TH" and Destination="12TH";


select Origin, Destination, max(s) from (
  select Origin, Destination, sum(Throughput) as s from (
    select Origin, Destination, Throughput from rides2017
    where weekday(DateTime)=0 or weekday(DateTime)=1 or weekday(DateTime)=2 or weekday(DateTime)=3 or weekday(DateTime)=4
  ) as t1 -- where Origin="12TH" and Destination="12TH"
  group by Origin, Destination
) as t2
;
