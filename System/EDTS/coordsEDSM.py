#!/usr/bin/env python

from __future__ import print_function
import argparse
import json
import sys

sys.path.insert(1, '/var/www/library/EDTS/')
import edtslib.pgnames

parser = argparse.ArgumentParser()
parser.add_argument("systemName")
args = parser.parse_args()


s = edtslib.pgnames.get_system(args.systemName)

if s is not None:
    print(s.name)
    print(s.position)
    print(s.uncertainty)
