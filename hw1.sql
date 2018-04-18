select hour(DateTime) as hour, sum(Throughput) as trips
from rides2017 group by hour(DateTime);


select Origin, Destination from rides2017
where weekday(DateTime)=0 or weekday(DateTime)=1 or weekday(DateTime)=2 or weekday(DateTime)=3 or weekday(DateTime)=4
group by Origin, Destination
order by sum(Throughput) desc
limit 1;



select Destination, avg(Throughput) from rides2017
where weekday(DateTime)=0 and hour(DateTime)>=7 and hour(DateTime)<10
group by Destination
order by avg(Throughput) desc
limit 5;



select Origin from rides2017
group by Origin
having max(Throughput)>100*avg(Throughput);
