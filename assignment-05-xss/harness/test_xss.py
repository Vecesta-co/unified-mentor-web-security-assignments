"""Double-test local XSS payload corpus in Chromium and write observed matrix."""
from pathlib import Path
from playwright.sync_api import sync_playwright
ROOT=Path(__file__).parent; PAGES=ROOT/'vulnerable-pages'; PAYLOADS=ROOT.parent/'payloads'; OUT=ROOT.parent/'results'/'xss-matrix.md'
def dismiss(dialog):
    try: dialog.dismiss()
    except Exception: pass
def main():
    pages=sorted(PAGES.glob('*.html'))
    payloads=[(f.name,line.strip()) for f in sorted(PAYLOADS.glob('*.txt')) for line in f.read_text(encoding='utf8').splitlines() if line.strip() and not line.lstrip().startswith('#')]
    rows=[]
    with sync_playwright() as pw:
        browser=pw.chromium.launch(headless=True)
        for pagefile in pages:
            fixture=pagefile.read_text(encoding='utf8')
            for label,payload in payloads:
                page=browser.new_page(); fired=[]
                page.on('dialog',lambda d: (fired.append(d.type), dismiss(d)))
                page.set_content(fixture,wait_until='domcontentloaded')
                try: page.locator('#out').evaluate('(el, value) => { el.innerHTML = value }',payload)
                except Exception: pass
                page.wait_for_timeout(30)
                rows.append((label,pagefile.stem,'yes' if fired else 'no'))
                page.close()
            print(f'Completed fixture {pagefile.name}: {len(payloads)} payloads',flush=True)
        browser.close()
    OUT.parent.mkdir(parents=True,exist_ok=True)
    body=['# XSS execution matrix','',f'Observed with Playwright Chromium. Rows: {len(rows)}. A yes indicates a browser dialog was observed after injecting the payload into the fixture HTML sink.','', '| Payload file | Fixture | Dialog observed |','|---|---|---|']
    body += [f'| {a} | {b} | {c} |' for a,b,c in rows]
    OUT.write_text('\n'.join(body)+'\n',encoding='utf8')
    print(f'Wrote matrix: {OUT} ({len(rows)} rows)',flush=True)
if __name__=='__main__': main()
