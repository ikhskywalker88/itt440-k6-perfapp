# Web Application Performance Testing with k6 (PerfApp – nginx + PHP)

**Course:** ITT440 – Individual Assignment  
**Student:** Muhammad Ikhwan bin Mohammad Faisal  
**Matric No.:** 2023516519
**Tool:** k6  
**Target Application:** PerfApp (simple flight booking demo on nginx + PHP)  
**Video Walkthrough:** [YouTube link here]

---

## 1. Introduction

This project demonstrates a basic web application performance testing workflow using **k6** on a small PHP application called **PerfApp**. The objective is to design and execute different types of performance tests and interpret key metrics:

- Response time  
- Throughput (requests per second)  
- Error rate  
- Resource usage (CPU and memory, observed via Task Manager)

PerfApp is hosted locally on an nginx + PHP environment. Using k6, I executed three test types:

1. **Load test** – small, “normal” concurrency (5 virtual users)  
2. **Stress test** – aggressive concurrency up to 100 VUs  
3. **Spike test** – sudden jump in VUs, then back down

The results show clearly how a simple, single-process PHP backend behaves as load increases.

---

## 2. Objectives and Hypothesis

### 2.1 Objectives

1. Design a simple performance test plan for a web application using k6.  

2. Execute at least three performance test types: Load, Stress, and Spike tests.
  
3. Measure and interpret:
   - Response time (average and 95th percentile)
   - Throughput (requests per second)
   - Error rate (% of failed HTTP requests)
   - CPU and memory usage (observed via Task Manager)

4. Identify performance bottlenecks and propose improvements based on the metrics.

### 2.2 Initial Hypothesis

Before testing, my rough expectations were:

- Under **light load** (a few concurrent users), PerfApp should respond in **< 500–800 ms** with **0% errors**.
- As load increases, I expected:
  - Some increase in response time.
  - Errors to start appearing only at higher concurrency levels.

The actual results show that, with this particular environment (single `php-cgi.exe` process), the application is much more fragile than expected.

---

## 3. Tool Selection – k6

### 3.1 Overview of k6

**k6** is an open-source load and performance testing tool. Test scripts are written in JavaScript and executed from the command line. Features used in this project include:

- **Stages** for defining ramp-up, steady, and ramp-down load profiles.
- **Checks** to assert HTTP status codes.
- Built-in metrics such as:
  - `http_req_duration` (response time)
  - `http_reqs` (total requests)
  - `http_req_failed` (failed requests)
- JSON output for further analysis.

### 3.2 Why k6

I chose k6 because:

- It is script-based and easy to store in GitHub.
- The CLI output is straightforward and readable.
- JavaScript is simple to understand for defining user journeys.
- It is lighter than GUI tools like JMeter for a small, local demo.

For an assignment that focuses on understanding performance behaviour, k6 provides exactly what I need without heavy setup.

---

## 4. Target Web Application – PerfApp

### 4.1 Functional Description

PerfApp is a very simple PHP demo application with three main endpoints:

- `index.php` – displays a static list of sample flights.  
- `search.php` – simulates a flight search based on `from` and `to` parameters.  
- `booking.php` – simulates a “heavier” booking process (includes a random delay).

These scripts are intentionally small, but they allow me to model a realistic user journey:

> Home → search results → booking confirmation

### 4.2 Architecture

- **Web server:** nginx on Windows  
- **Backend:** PHP 8 (CGI/FastCGI)  
- **Host:** Local machine (localhost)  

Request flow:

```text
k6 (client) → nginx → php-cgi.exe → PerfApp PHP scripts