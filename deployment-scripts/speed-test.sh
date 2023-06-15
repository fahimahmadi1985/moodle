#!/bin/bash


for i in {1..500000}; do echo -n "Run # $i :: "; 
curl -w 'Return code: %{http_code}; Bytes Received: %{size_download}; 
Response Time: %{time_total}\n' https://dev.education.digitalcareerinstitute.de/health.html -m 2 -o /dev/null -s; 
done|tee /dev/tty|awk '{ sum+= $NF; n++ } END { if (n > 0) print "Average Response time =",sum / n; }'