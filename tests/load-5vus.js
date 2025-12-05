import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
  stages: [
    { duration: '1m', target: 5 },  // ramp up to 5 users
    { duration: '3m', target: 5 },  // hold 5 users
    { duration: '1m', target: 0 },  // ramp down
  ],
};

export default function () {
  let res1 = http.get('http://localhost/perfapp/index.php');
  check(res1, { 'home status is 200': (r) => r.status === 200 });

  let res2 = http.get('http://localhost/perfapp/search.php?from=Boston&to=Rome');
  check(res2, { 'search status is 200': (r) => r.status === 200 });

  let res3 = http.get('http://localhost/perfapp/booking.php');
  check(res3, { 'booking status is 200': (r) => r.status === 200 });

  sleep(1); // think time
}