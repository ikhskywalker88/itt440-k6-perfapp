import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
  stages: [
    { duration: '1m', target: 20 },
    { duration: '1m', target: 40 },
    { duration: '1m', target: 60 },
    { duration: '1m', target: 80 },
    { duration: '1m', target: 100 },
    { duration: '2m', target: 0 },
  ],
};

export default function () {
  let res1 = http.get('http://localhost/perfapp/index.php');
  check(res1, { 'home status 200': (r) => r.status === 200 });

  let res2 = http.get('http://localhost/perfapp/search.php?from=Boston&to=Rome');
  check(res2, { 'search status 200': (r) => r.status === 200 });

  let res3 = http.get('http://localhost/perfapp/booking.php');
  check(res3, { 'booking status 200': (r) => r.status === 200 });

  sleep(1);
}