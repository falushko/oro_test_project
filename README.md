Database description:

Database consists of two tables: log_records and file_last_lines.

log_records:
- id
- datetime
- record
- full_record
Contains information about every single log row.

file_last_lines:
- id
- file_name
- line
Contains last line that was checked in each file. Needed to start database refresh
not from the very start, but from last line, where we stopped.

I chose MySQL because of my experience with it. I could try to use Redis here, but I didn't.

Request example:

{
   "datetime": [
        {
            "start" : "3/08/2005 14:00:45",
            "end" : "4/08/2005 12:56:34"
        },
        {
            "start": "3/08/2005 14:00:45",
            "end": "4/08/2005 12:56:34"
        },
        ...
   ],

   "text" : "some text",
   "regex" : "some regex"
}

limit and offset in headers.

See documentation on http://localhost:8051/apidoc/index.html