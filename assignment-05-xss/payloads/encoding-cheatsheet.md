# XSS Context-Aware Encoding & Filter Bypass Cheatsheet
Assignment 05 — Cross-Site Scripting (XSS) Exploitation

Cross-Site Scripting occurs when untrusted input is interpreted as code. Because browsers parse documents through multiple sequential decoding passes (URL decoding → HTML decoding → JavaScript decoding), attackers use alternative character representations to bypass naive regex filters.

---

## 1. Encoding Methods by Target Context

### Context 1: HTML Body Context
- **Vulnerable Location:** Between normal HTML tags e.g. `<div><?= $input ?></div>`
- **Dangerous Characters:** `<`, `>`, `&`, `"`, `'`
- **Required Defense:** Convert to HTML entities:
  - `<` → `&lt;`
  - `>` → `&gt;`
  - `&` → `&amp;`
  - `"` → `&quot;`
  - `'` → `&#x27;`
- **PHP Implementation:** `htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8')`

---

### Context 2: HTML Attribute Context
- **Vulnerable Location:** Inside tag attributes e.g. `<input type="text" name="user" value="<?= $input ?>"/>`
- **Filter Evasion Tricks:**
  - If unquoted (`value=<?= $input ?>`): Space (`%20`), tab (`%09`), newline (`%0a`), or slash (`/`) terminates the attribute.
  - HTML decimal entities: `&#97;&#108;&#101;&#114;&#116;(1)` inside an event handler attribute (e.g. `onload=&#97;...`) will be decoded by the browser's HTML parser **before** the event handler executes!
- **Required Defense:** Always enclose attributes in double quotes and apply `ENT_QUOTES` encoding.

---

### Context 3: JavaScript String Literal Context
- **Vulnerable Location:** Inside a script tag string:
  ```html
  <script>
      let username = '<?= $input ?>';
  </script>
  ```
- **Filter Evasion Tricks:**
  - Unicode escapes: `\u0027` (single quote), `\u0022` (double quote).
  - Hexadecimal escapes: `\x27`, `\x22`.
  - Closing script tag trick: Regardless of quotes or backslashes, `</script>` will immediately close the script block in all major HTML parsers.
- **Required Defense:** Use `json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)`.

---

### Context 4: URL Attribute Context
- **Vulnerable Location:** Inside `href`, `src`, or `action` attributes:
  ```html
  <a href="<?= $user_url ?>">User Website</a>
  ```
- **Filter Evasion Tricks:**
  - Pseudo-protocols: `javascript:alert(1)`
  - URL encoding: `javascript:%61%6c%65%72%74(1)`
  - Whitespace/tab separation: `java\tscript:alert(1)`
  - Data URI scheme: `data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==`
- **Required Defense:**
  1. Validate that the URL begins strictly with an approved protocol allowlist (`http://` or `https://`).
  2. Disallow `javascript:`, `data:`, and `vbscript:`.

---

## 2. Polyglot Payloads Explained

A polyglot XSS payload is engineered to execute successfully across multiple distinct browser contexts (HTML body, attributes, script blocks, SVG/XML) simultaneously:

```html
-->'"</script><svg/onload=alert(1)>
```
- `-->`: Closes any open HTML comment.
- `'"`: Breaks out of single-quoted and double-quoted attribute strings.
- `</script>`: Terminates open script blocks.
- `<svg/onload=alert(1)>`: Valid self-closing SVG element executing on load across HTML and XML contexts.
