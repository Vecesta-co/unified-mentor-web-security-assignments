"""Bounded boolean oracle extraction. Use only explicitly allowlisted local endpoints."""
import argparse,time
from urllib.parse import urlparse
ALLOWED={"127.0.0.1","localhost","dvwa"}
def extract(oracle, max_requests=500, delay=0.05, retries=2, max_len=64, alphabet="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_{}-"):
 n=0; out=""
 for pos in range(1,max_len+1):
  found=False
  for ch in alphabet:
   for _ in range(retries+1):
    n+=1
    if n>max_requests: raise RuntimeError("request cap reached")
    if oracle(pos,ch): out+=ch;found=True;break
    time.sleep(delay)
   if found: break
  if not found: return out
 return out
def main():
 p=argparse.ArgumentParser();p.add_argument("url");p.add_argument("--max-requests",type=int,default=500);a=p.parse_args()
 if urlparse(a.url).hostname not in ALLOWED:p.error("refusing non-local target")
 p.error("provide a lab-specific oracle adapter; no network extraction is enabled by default")
if __name__=="__main__":main()
