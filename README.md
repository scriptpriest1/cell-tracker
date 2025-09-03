# cell-tracker
Web application for Church growth management

Cron / auto-generation
- To auto-generate weekly drafts (recommended Monday 00:00), run the CLI script:
  0 0 * * 1 /usr/bin/php /path/to/htdocs/cell-tracker/php/auto_generate_drafts.php >> /var/log/cell-tracker/auto_generate.log 2>&1

- The web ajax endpoint is also available: POST to php/ajax.php?action=auto_generate_all_drafts with optional `date=YYYY-MM-DD` for testing.
  - Optional security: set an environment variable `AUTOGEN_TOKEN` (on the system running the cron/web server) and include POST param `token=...` to secure web-triggered calls.
