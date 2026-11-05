#!/usr/bin/env python3
"""
extractor.py — Automated HTML Source & Security Analyzer
Assignment 01 — Web Security Lab Portfolio

Audits saved HTML markup to discover:
  - Form actions, methods, and input fields
  - Hidden input fields (flags potential sensitive state or CSRF tokens)
  - HTML comments (detects information leaks e.g. TODOs, internal paths, secrets)
  - External resource references (scripts, stylesheets, links)
  - Input validation attributes (required, pattern, min/max length)

Compatible with pytest test harness and standalone CLI execution.
"""

import sys
import argparse
import json
import re
from pathlib import Path
from html.parser import HTMLParser

class Extract(HTMLParser):
    """
    Standard library HTML parser maintaining compatibility with unit tests
    while parsing forms, inputs, hidden fields, comments, and resource references.
    """
    def __init__(self):
        super().__init__()
        self.forms = []
        self.comments = []
        self.refs = []
        self.cur = None

    def handle_starttag(self, tag, attrs):
        attr_dict = dict(attrs)
        tag_lower = tag.lower()

        if tag_lower == 'form':
            self.cur = {
                'action': attr_dict.get('action', ''),
                'method': attr_dict.get('method', 'get').lower(),
                'enctype': attr_dict.get('enctype', 'application/x-www-form-urlencoded'),
                'inputs': []
            }
            self.forms.append(self.cur)

        elif tag_lower in ('input', 'textarea', 'select'):
            inp_type = attr_dict.get('type', 'text').lower() if tag_lower == 'input' else tag_lower
            is_hidden = (inp_type == 'hidden')
            input_info = {
                'name': attr_dict.get('name', ''),
                'type': inp_type,
                'value': attr_dict.get('value', ''),
                'hidden': is_hidden,
                'required': 'required' in attr_dict,
                'pattern': attr_dict.get('pattern', None)
            }
            if self.cur:
                self.cur['inputs'].append(input_info)

        # Track external and internal references
        if tag_lower in ('script', 'img', 'iframe') and 'src' in attr_dict:
            self.refs.append({tag_lower: attr_dict['src']})
        elif tag_lower in ('a', 'link') and 'href' in attr_dict:
            self.refs.append({tag_lower: attr_dict['href']})

    def handle_endtag(self, tag):
        if tag.lower() == 'form':
            self.cur = None

    def handle_comment(self, data):
        cleaned = data.strip()
        if cleaned:
            self.comments.append(cleaned)

def analyze_html_file(file_path: Path) -> dict:
    content = file_path.read_text(encoding='utf-8', errors='replace')
    parser = Extract()
    parser.feed(content)

    # Security heuristic analysis
    findings = []
    
    # Check forms
    for i, form in enumerate(parser.forms, 1):
        has_csrf = any(
            inp['name'].lower() in ('csrf_token', '_token', 'authenticity_token')
            for inp in form['inputs']
        )
        if form['method'] == 'post' and not has_csrf:
            findings.append(f"[MEDIUM] Form #{i} ({form['action']}): POST form lacks apparent CSRF token field.")
        
        has_password_in_get = (
            form['method'] == 'get' and any(inp['type'] == 'password' for inp in form['inputs'])
        )
        if has_password_in_get:
            findings.append(f"[HIGH] Form #{i}: Password field detected inside GET form (credential leakage via URL).")

    # Check comments for sensitive disclosure
    sensitive_keywords = ('password', 'secret', 'key', 'todo', 'fixme', 'admin', 'internal', 'api_key')
    for comment in parser.comments:
        comment_lower = comment.lower()
        if any(kw in comment_lower for kw in sensitive_keywords):
            findings.append(f"[INFO/WARN] Comment contains sensitive keyword: \"{comment[:80]}...\"")

    return {
        'source_file': str(file_path),
        'forms_count': len(parser.forms),
        'forms': parser.forms,
        'comments_count': len(parser.comments),
        'comments': parser.comments,
        'references_count': len(parser.refs),
        'references': parser.refs,
        'security_findings': findings
    }

def format_markdown_report(analysis: dict) -> str:
    lines = [
        f"# Source Code Analysis Report: {Path(analysis['source_file']).name}",
        f"\n**Target File:** `{analysis['source_file']}`",
        f"**Forms Detected:** {analysis['forms_count']} | **Comments Detected:** {analysis['comments_count']}",
        "\n## 1. Security Findings Summary",
    ]
    if analysis['security_findings']:
        for finding in analysis['security_findings']:
            lines.append(f"- {finding}")
    else:
        lines.append("- No immediate high-risk anomalies detected.")

    lines.append("\n## 2. Forms and Input Analysis")
    for idx, f in enumerate(analysis['forms'], 1):
        lines.append(f"\n### Form #{idx} — Action: `{f['action'] or '(current page)'}` | Method: `{f['method'].upper()}`")
        lines.append("| Field Name | Type | Hidden | Required | Pattern |")
        lines.append("| :--- | :--- | :---: | :---: | :--- |")
        for inp in f['inputs']:
            lines.append(f"| `{inp['name'] or '(unnamed)'}` | `{inp['type']}` | {'Yes' if inp['hidden'] else 'No'} | {'Yes' if inp['required'] else 'No'} | `{inp['pattern'] or '-'}` |")

    lines.append("\n## 3. Extracted HTML Comments")
    if analysis['comments']:
        for c in analysis['comments']:
            lines.append(f"```html\n<!-- {c} -->\n```")
    else:
        lines.append("_No HTML comments discovered in document._")

    lines.append("\n## 4. Resource References (Scripts, Stylesheets, Links)")
    for ref in analysis['references']:
        for tag, url in ref.items():
            lines.append(f"- `{tag}`: `{url}`")

    return "\n".join(lines)

def main():
    parser = argparse.ArgumentParser(description="HTML Source & Security Extractor (Assignment 01)")
    parser.add_argument("file", help="Path to target HTML file")
    parser.add_argument("--json", action="store_true", help="Output raw JSON results")
    parser.add_argument("--markdown", "-m", action="store_true", help="Output Markdown report format")
    args = parser.parse_args()

    target = Path(args.file)
    if not target.exists():
        parser.error(f"File not found: {target}")

    result = analyze_html_file(target)

    if args.markdown:
        print(format_markdown_report(result))
    else:
        print(json.dumps(result, indent=2))

if __name__ == '__main__':
    main()
