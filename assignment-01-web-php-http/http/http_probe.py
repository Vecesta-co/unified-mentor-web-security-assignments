# local-only probe helper
import argparse, urllib.request, urllib.error
from urllib.parse import urlparse
ALLOWED={'localhost','127.0.0.1','dvwa'}
def main():
 ap=argparse.ArgumentParser(); ap.add_argument('url'); ap.add_argument('--proxy'); a=ap.parse_args()
 u=urlparse(a.url)
 if u.hostname not in ALLOWED: ap.error('target host is not allowlisted')
 opener=urllib.request.build_opener(urllib.request.ProxyHandler({'http':a.proxy,'https':a.proxy}) if a.proxy else urllib.request.ProxyHandler({}))
 try:
  r=opener.open(a.url); print(r.status,dict(r.headers),r.read(512).decode(errors='replace'))
 except urllib.error.HTTPError as e: print(e.code,dict(e.headers),e.read(512).decode(errors='replace'))
if __name__=='__main__': main()
