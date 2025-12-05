import http from 'k6/http';
import { check, sleep } from 'k6';

export let options = {
  stages: [
    { duration: '30s', target: 10 },   // small steady load
    { duration: '10s', target: 100 },  // sudden spike
    { duration: '2m', target: 10 },    // recover
    { duration: '30s', target: 0 },    // ramp down
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