#!/usr/bin/env python
# vim: set fileencoding=utf-8
import sys
import re



for line in sys.stdin:
    if re.match('.*btime.com.*', line):
        line = line.strip()
        data = line.split()
	tm = data[4][1:]
        print '%s%s%s' % (tm, '\t', data[10])
