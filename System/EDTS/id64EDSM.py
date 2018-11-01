#!/usr/bin/env python

from __future__ import print_function
import argparse
import json
import sys

sys.path.insert(1, '/var/www/library/EDTS/')
from edtslib import system, id64data, vector3

parser = argparse.ArgumentParser()
parser.add_argument("systemName")
parser.add_argument("x")
parser.add_argument("y")
parser.add_argument("z")
args = parser.parse_args()

s = system.from_name(args.systemName, allow_known=False, allow_id64data=False)

if s and s.id64:
    print(s.name)
    print(s.id64)
elif args.x != "NULL":
    s = id64data.get_id64(args.systemName, vector3.Vector3(args.x, args.y, args.z))
    if s is not None:
        print(args.systemName)
        print(s)