#!/bin/bash
# usage: compare.sh <driver>  -> per-file: kind, prod dims, new dims, PSNR
d=$1
while read f kind code ctype url; do
  a=$(identify -format '%wx%h' prod/$f[0] 2>/dev/null); b=$(identify -format '%wx%h' out-$d/$f[0] 2>/dev/null)
  if [ "$a" = "$b" ]; then p=$(compare -metric PSNR prod/$f[0] out-$d/$f[0] null: 2>&1 | awk '{print $1}'); else p=DIM; fi
  echo "$f $kind $a $b $p"
done < map.txt
