select concat(first, ' ', last) as actorname
from Actor inner join MovieActor on Actor.id=MovieActor.aid inner join Movie on MovieActor.mid=Movie.id
where Movie.title='Die Another Day'
;


select count(*) from
(
  select aid from MovieActor
  group by aid having count(distinct mid)>1
) as t
;

-- query the title of the movie with the most actors
select title, count(aid) as numberofactor
from MovieActor join Movie on MovieActor.mid=Movie.id
group by mid
order by count(aid) desc
limit 1
;
