## Overview
For this assignment, I added logging to my Lost & Found website and analyzed the traffic using a PHP script.

I created a custom logger (`log_bootstrap.php`) that writes every request to:
- `public_html/logs/lf_access.log`
- `public_html/logs/lf_error.log`

Then I used `analyze_logs.php` to parse the logs and generate charts showing:
- Requests over time
- Browser/User-Agent stats
- HTTP status distribution
- Most visited pages
- Top client IPs

The PDF report (`DB_Assignment8.pdf`) includes screenshots of these results.

## Files Included
- `log_bootstrap.php` — writes access + error logs  
- `lf_access.log` — generated access log  
- `lf_error.log` — generated error log  
- `analyze_logs.php` — log analysis script with charts  
- `DB_Assignment8.pdf` — final report  

## Note
The analyzer runs on the university server.  
Chrome  blocks the page because the campus server uses a self-signed certificate.  
It works fine on:
- Safari
- Firefox
- or using `http://` instead of `https://`

PDF output is included in case access to the live page is not possible.