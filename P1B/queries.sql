/*
select concat(first, ' ', last)
from Actor inner join MovieActor on Actor.id=MovieActor.aid inner join Movie on MovieActor.mid=Movie.id
where Movie.title='Die Another Day'
;
*/

/*
select count(*) from
(
  select aid from MovieActor
  group by aid having count(distinct mid)>1
) as t
;
*/
