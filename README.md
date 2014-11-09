Hard-Drive-Monitor
==================
Key points to this script include:
·         Uses DD to zero multiple drives

·         A pre defined list of drive letters is used during scan every 30 seconds, which makes it easy to adjust which drive letters to monitor for or exclude

·         Blinking finished drives

·         Reporting progress – every 30 seconds

o    progress bar

o    current write speed

o    estimated competition time

·         Reporting complete

o    the average write speed

o    size of the drive

o    duration

·         Clean formatting time library

o    parse_date(<offset in sec>,<format>);

·         Ability to watch progress from other sessions

·         Master session take over (so if main session dies, view only session will become new master to start new drives)

 
Features not included:
·         logging
