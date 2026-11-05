"""Timing oracle classifier with bounded requests; adapter required."""
import argparse,time
from urllib.parse import urlparse
ALLOWED={"127.0.0.1","localhost","dvwa"}
def extract(oracle,threshold, max_requests=500, delay=.05, retries=2, max_len=64, alphabet="abcdefghijklmnopqrstuvwxyz0123456789_{}-"):
 count=0;out=""
 for pos in range(1,max_len+1):
  for ch in alphabet:
   samples=[]
   for _ in range(retries+1):
    count+=1
    if count>max_requests:raise RuntimeError("request cap reached")
    start=time.monotonic();oracle(pos,ch);samples.append(time.monotonic()-start);time.sleep(delay)
   if sum(samples)/len(samples)>=threshold:out+=ch;break
  else:return out
 return out
def main():
 p=argparse.ArgumentParser();p.add_argument("url");p.add_argument("--max-requests",type=int,default=500);a=p.parse_args()
 if urlparse(a.url).hostname not in ALLOWED:p.error("refusing non-local target")
 p.error("provide a lab-specific oracle adapter; network extraction is disabled by default")
if __name__=="__main__":main()
