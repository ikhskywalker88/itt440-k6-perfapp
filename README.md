**Web Application Performance Testing with k6** (PerfApp – nginx + PHP)

**Course**: ITT440 – Individual Assignment
**Studen**t: Muhammad Ikhwan bin Mohammad Faisal
**Matric No.**: 2023516519
**Tool**: k6
**Target Application**: PerfApp (simple flight booking demo on nginx + PHP)
**Video Walkthrough**: \[Your YouTube link]



### 1\. Introduction



This project demonstrates a basic web application performance testing workflow using k6 on a small PHP application called PerfApp. The goals are to:



&nbsp;	•	Design and run different performance test types (Load, Stress, Spike).

&nbsp;	•	Measure key metrics (response time, throughput, error rate).

&nbsp;	•	Observe how a simple nginx + PHP setup behaves under increasing load.

&nbsp;	•	Suggest improvements based on the observed bottlenecks.



PerfApp is hosted locally on my machine using nginx and PHP. k6 is used to simulate virtual users calling three main endpoints that represent a simple user journey.



### 2\. Objectives and Hypothesis



#### 2.1 Objectives



&nbsp;	1.	Design a simple performance test plan for a web application using k6.

&nbsp;	2.	Execute at least three performance test types: Load, Stress, and Spike tests.

&nbsp;	3.	Collect and interpret performance metrics:



&nbsp;		◦	Response time (average and 95th percentile),

&nbsp;		◦	Throughput (requests per second),

&nbsp;		◦	Error rate (% failed HTTP requests).



&nbsp;	4.	Observe CPU and memory usage using Task Manager.

&nbsp;	5.	Identify performance bottlenecks and propose realistic improvements.



#### 2.2 Initial Hypothesis



&nbsp;	Before testing, my expectations were:



&nbsp;	•	Under light load (a few users), PerfApp should respond in less than 500–800 ms with 0% errors.

&nbsp;	•	As load increases, I expected response times to increase gradually, and errors to appear only at higher concurrency (tens of users).



&nbsp;	The actual results show that, in this single-process PHP environment, the system starts struggling much earlier than expected.



### 3\. Tool Selection – k6



#### 3.1 Overview of k6



&nbsp;	k6 is an open-source command-line tool for load and performance testing. Test scripts are written in JavaScript and define:



&nbsp;		•	Virtual users (VUs),



&nbsp;		•	Duration and stages (ramp up, hold, ramp down),


&nbsp;		•	HTTP requests and checks (for example, status === 200).



&nbsp;	k6 provides:



&nbsp;		•	Built-in metrics such as:



&nbsp;			◦	http\_req\_duration – response time per request,

&nbsp;			◦	http\_reqs – total number of HTTP requests,

&nbsp;			◦	http\_req\_failed – percentage of failed requests.



&nbsp;		•	Human-readable CLI output.



&nbsp;		•	Optional JSON output for further analysis (used in the load test).



#### 3.2 Why k6



&nbsp;	I chose k6 because:



&nbsp;		•	It is easy to script using JavaScript.



&nbsp;		•	It runs from the CLI, which is convenient for assignments and GitHub.



&nbsp;		•	The output is clear and simple to interpret.



&nbsp;		•	It is lighter than GUI-based tools like JMeter, which is ideal for a small local demo app.



&nbsp;	For this assignment, k6 provides enough power without overcomplicating the setup.



### 4\. Target Web Application – PerfApp



#### 4.1 Functional Description



PerfApp is a very simple PHP demo application with three main endpoints:



&nbsp;	•	index.php


Displays a static list of sample flights in a table.



&nbsp;	•	search.php


Simulates searching for flights based on from and to query parameters, returning a list of matches.

&nbsp;	•	booking.php


Simulates a heavier booking operation, including a random usleep() delay to mimic the cost of database work.



These three endpoints form a simple but realistic user flow:



Home → Search → Booking confirmation

#### 

#### 4.2 Architecture



•	Web server: nginx on Windows

•	Backend: PHP 8.x (CGI/FastCGI, using php-cgi.exe)

•	Host: Local machine (localhost)



High-level flow:



k6 (client) → nginx → php-cgi.exe → PerfApp PHP scripts



Everything runs on the same machine, so CPU usage includes nginx, PHP, and k6 itself.



#### 4.3 User Journey Modeled



For all main tests, each virtual user performs this sequence:



&nbsp;	1	GET http://localhost/perfapp/index.php

&nbsp;	2	GET http://localhost/perfapp/search.php?from=Boston\&to=Rome

&nbsp;	3	GET http://localhost/perfapp/booking.php

&nbsp;	4	Sleep 1 second “think time”



This journey touches both a lighter page (index.php) and a heavier page (booking.php), so it is useful for observing how the backend behaves under different levels of load.



### 5\. Test Environment



#### 5.1 Hardware \& OS



&nbsp;	•	Machine: Local desktop / laptop

&nbsp;	•	CPU: 4-core Intel (e.g. i5)

&nbsp;	•	RAM: 16 GB

&nbsp;	•	OS: Windows 11



#### 5.2 Software

&nbsp;	•	nginx: 1.28.0 (Windows build)

&nbsp;	•	PHP: 8.x (CGI/FastCGI, via php-cgi.exe)

&nbsp;	•	k6: v1.4.2 (CLI)

&nbsp;	•	Browser: Edge/Chrome (for manual checking)



#### 5.3 Monitoring



For basic resource monitoring I used Task Manager → Performance to observe:

&nbsp;	•	Overall CPU percentage,

&nbsp;	•	Overall memory usage.



This is simple but enough to see when the system is clearly under load.



### 6\. Test Design and Methodology



#### 6.1 Overall Strategy



I designed three scenarios:



&nbsp;	1	Load test (T1) – 5 VUs to simulate small, normal traffic.

&nbsp;	2	Stress test (T2) – ramp up to very high concurrency (up to 100 VUs) to see where the application breaks.

&nbsp;	3	Spike test (T3) – a sudden jump to high load and then back down, to see how the system reacts to a burst.





index.php → search.php → booking.php → sleep(1)



#### 6.2 Scenario Summary



&nbsp;	Test ID

&nbsp;	Type

&nbsp;	Scenario Name

&nbsp;	Max VUs

&nbsp;	Duration (approx.)

&nbsp;	Purpose

&nbsp;	T1

&nbsp;	Load

&nbsp;	Normal Load

&nbsp;	5

&nbsp;	~5 minutes

&nbsp;	Small load baseline

&nbsp;	T2

&nbsp;	Stress

&nbsp;	High Load

&nbsp;	100

&nbsp;	~7 minutes

&nbsp;	Push system beyond capacity

&nbsp;	T3

&nbsp;	Spike

&nbsp;	Traffic Spike

&nbsp;	100

&nbsp;	~3 minutes



Sudden short burst and observe behaviour



#### 6.3 Example k6 Script Structure



All scripts share the same default function; only the options (stages, VUs, duration) change:



import http from 'k6/http';

import { check, sleep } from 'k6';



export default function () {

&nbsp; const res1 = http.get('http://localhost/perfapp/index.php');

&nbsp; check(res1, { 'home status 200': (r) => r.status === 200 });



&nbsp; const res2 = http.get('http://localhost/perfapp/search.php?from=Boston\&to=Rome');

&nbsp; check(res2, { 'search status 200': (r) => r.status === 200 });



&nbsp; const res3 = http.get('http://localhost/perfapp/booking.php');

&nbsp; check(res3, { 'booking status 200': (r) => r.status === 200 });



&nbsp; sleep(1);

}



The options block in each script file defines whether the test is a load, stress, or spike test.



### 7\. Execution and Raw Results



#### 7.1 Sanity Check – 1 VU



Before running the main scenarios, I ran a simple 1 VU script that repeatedly hit index.php.

Key metrics:



&nbsp;	•	http\_req\_duration average ≈ 144.72 ms, p95 ≈ 204.97 ms

&nbsp;	•	http\_req\_failed = 0.00% (0 / 70)

&nbsp;	•	http\_reqs = 70 in about 10 seconds



This confirmed that with only one user, the app is fast and stable.



#### 7.2 Load Test Results (T1 – 5 VUs)



For the load test, I used up to 5 VUs over about 5 minutes, with the full user journey per iteration.



k6 summary:



&nbsp;	•	http\_reqs = 672 total (~2.24 req/s)



http\_req\_duration (all requests):



&nbsp;	•	average ≈ 1.52 s

&nbsp;	•	median ≈ 1.70 s

&nbsp;	•	p90 ≈ 2.05 s

&nbsp;	•	p95 ≈ 2.07 s



http\_req\_duration (successful responses only):



&nbsp;	•	average ≈ 1.23 s

&nbsp;	•	median ≈ 1.30 s

&nbsp;	•	p90 ≈ 1.92 s

&nbsp;	•	p95 ≈ 2.01 s



Failures and checks:



&nbsp;	•	http\_req\_failed = 36.60% (246 / 672)

&nbsp;	•	checks\_total = 672

&nbsp;	•	checks\_succeeded = 63.39%

&nbsp;	•	checks\_failed = 36.60%



Interpretation:



&nbsp;	•	Even at just 5 concurrent users, the app is already under stress.

&nbsp;	•	Successful requests are still around 1–2 seconds, which is borderline acceptable.

&nbsp;	•	However, more than one-third of all requests fail (non-200 status).

&nbsp;	•	This strongly suggests that the backend (single PHP FastCGI process) has very limited concurrency capacity.



For this test, I stored the JSON output in:



&nbsp;	•	test-results/load-5vus-results.json



#### 7.3 Stress Test Results (T2 – up to 100 VUs)



For the stress test, I used stress-test.js, which ramps up through multiple stages until it reaches 100 VUs.



k6 summary:



&nbsp;	•	http\_reqs = 7,446 total (~17.47 req/s)



http\_req\_duration (all requests):



&nbsp;	•	average ≈ 2.52 s

&nbsp;	•	median ≈ 2.05 s

&nbsp;	•	p90 ≈ 2.06 s

&nbsp;	•	p95 ≈ 5.16 s



http\_req\_duration (successful responses only):



&nbsp;	•	average ≈ 8.54 s

&nbsp;	•	median ≈ 8.89 s

&nbsp;	•	p90 ≈ 15.31 s

&nbsp;	•	p95 ≈ 15.79 s



Failures and checks:



&nbsp;	•	http\_req\_failed = 93.35% (6,951 / 7,446)

&nbsp;	•	checks\_total = 7,446

&nbsp;	•	checks\_succeeded = 6.64%

&nbsp;	•	checks\_failed = 93.35%



Interpretation:



&nbsp;	•	Under this aggressive load, the application is overwhelmed.

&nbsp;	•	Almost all requests fail, and the few successful ones are very slow (around 9–16 seconds).

&nbsp;	•	This clearly shows the current environment cannot support high concurrency.



(For this test I used the CLI summary only, without saving a JSON file.)



#### 7.4 Spike Test Results (T3 – sudden surge)



For the spike test, I used spike-test.js to ramp quickly from a small baseline to high load and then back down:



5 VUs baseline → spike up to 100 VUs → back down.



k6 summary:



&nbsp;	•	http\_reqs = 528 total (~2.73 req/s)



http\_req\_duration (all requests):



&nbsp;	•	average ≈ 16.34 s

&nbsp;	•	median ≈ 12.98 s

&nbsp;	•	p90 ≈ 39.25 s

&nbsp;	•	p95 ≈ 42.08 s



http\_req\_duration (successful responses only):



&nbsp;	•	average ≈ 18.54 s

&nbsp;	•	median ≈ 15.44 s

&nbsp;	•	p90 ≈ 39.65 s

&nbsp;	•	p95 ≈ 42.63 s



Failures and checks:



&nbsp;	•	http\_req\_failed = 15.15% (80 / 528)

&nbsp;	•	checks\_total = 528

&nbsp;	•	checks\_succeeded = 84.84%

&nbsp;	•	checks\_failed = 15.15%



Interpretation:



&nbsp;	•	During and around the spike, the application becomes very slow (many requests take tens of seconds).

&nbsp;	•	About 15% of requests fail.

&nbsp;	•	The system does not completely collapse, but user experience would be very poor during a traffic spike.



### 8\. Analysis and Discussion



#### 8.1 Hypothesis vs Reality



I expected the app to handle small loads comfortably and only start failing at higher concurrency. The results show:



&nbsp;	-	At 1 VU, the app is fast and stable (≈145 ms, 0% errors).

&nbsp;	-	At 5 VUs, it already shows:

&nbsp;	-	average ≈ 1.5 s, about 36.6% failed requests.

&nbsp;	-	At high load (up to 100 VUs), error rates jump to 93%+, with successful requests taking around 9–16 seconds.

&nbsp;	-	Under a spike to 100 VUs, average latency is 16–18 seconds, with p95 around 42 seconds and about 15% failures.



So the hypothesis that “normal small load will be fine” is false for this single-process setup. The system is much more fragile than expected once there is meaningful concurrency.



#### 8.2 Bottlenecks



The behaviour suggests:



&nbsp;	1. Backend concurrency bottleneck



&nbsp;	   - A single php-cgi.exe process handles all PHP requests. Under load, requests queue or fail, leading to high error rates and long tail latencies.



&nbsp;	2. No caching or optimisation


	   -Every request runs full PHP logic, including usleep() delays. There is no opcode cache or data cache to reduce repeated work.



&nbsp;	3. Resource saturation


	   -Under stress and spike tests, CPU usage is likely high and the FastCGI backend cannot keep up with the incoming traffic.



#### 8.3 User Impact



If this were production:



&nbsp;	•	At 1 VU, users are happy.



&nbsp;	•	At 5 VUs, users start to see slow pages and some failed actions.



&nbsp;	•	Under heavy load or spikes, many users would abandon the site because of:



&nbsp;		-	10–40 second response times,



&nbsp;		-	frequent errors.



The tests show how quickly user experience degrades when backend capacity is too low.



### 9\. Recommendations



To improve performance and stability:



1. &nbsp;	Increase PHP backend capacity



&nbsp;	- 	Use multiple FastCGI workers instead of a single php-cgi.exe.

&nbsp;	- 	On Linux, use PHP-FPM with an appropriate process pool.



2\.	Optimise application logic



&nbsp;	- 	Remove or reduce usleep() in booking.php.

&nbsp;	- 	In a real system, profile heavy operations and database queries.



3\.	Introduce caching



&nbsp;	- 	Enable opcode caching (for example, OPcache).

&nbsp;	- 	Cache static or semi-static data like the flight list.



4\.	Tune nginx / FastCGI configuration



&nbsp;	- 	Review timeouts, buffer sizes and process limits.

&nbsp;	- 	Ensure nginx is not dropping connections too aggressively.



5\.	Scale out for production



&nbsp;	-	Run multiple application instances behind a load balancer.

&nbsp;	-	Serve static assets separately from dynamic PHP content.





### 10\. Conclusion



This project covered:



&nbsp;	•	Building a small PHP demo app (PerfApp) behind nginx,



&nbsp;	•	Writing k6 scripts to model a realistic user journey,



&nbsp;	•	Running three test types: Load, Stress and Spike,



&nbsp;	•	Interpreting k6 metrics and relating them to user experience.



Key takeaways:

&nbsp;	•	Even simple setups can fail under surprisingly low concurrency.



&nbsp;	•	Response time, error rate and throughput together give a clear picture of system health.


&nbsp;	•	A structured test plan and a tool like k6 make it easier to justify performance improvements and scaling decisions.





### 11\. Project Structure



perfapp/       → PHP application (index.php, search.php, booking.php)



tests/         → k6 scripts (load-5vus.js, stress-test.js, spike-test.js)



test-results/  → k6 JSON output for load test (load-5vus-results.json)



README.md      → This report

