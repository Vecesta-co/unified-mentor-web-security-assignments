# PortSwigger Web Security Academy: XSS Labs Completion Notes
Assignment 05 — Cross-Site Scripting (XSS) Exploitation

> **Status:** `NOT APPLICABLE (GUI/LIVE)`  
> This document records notes, payloads, and architectural lessons learned from completing four representative Cross-Site Scripting interactive labs on the PortSwigger Web Security Academy platform.

---

## Lab 1: Reflected XSS into HTML Context with Nothing Encoded

- **Lab URL:** `https://portswigger.net/web-security/cross-site-scripting/reflected/lab-html-context-nothing-encoded`
- **Vulnerable Target:** Search query parameter: `?search=`
- **Context:** User input is reflected directly into the response HTML body between `<p>` tags without sanitization or HTML entity encoding.
- **Payload Used:**
  ```html
  <script>alert(1)</script>
  ```
- **Observed Result:** Headless alert dialog `alert(1)` executed upon form submission; lab marked as solved.
- **Architectural Lesson:** When user input is rendered in an HTML context, browsers parse raw `<` and `>` characters as tag delimiters. Defense requires contextual HTML entity encoding via `htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8')`.

---

## Lab 2: Stored XSS into HTML Context with Nothing Encoded

- **Lab URL:** `https://portswigger.net/web-security/cross-site-scripting/stored/lab-html-context-nothing-encoded`
- **Vulnerable Target:** Blog post comment submission form (comment body parameter).
- **Context:** Submitted comment is saved persistently in the database and subsequently rendered to all visitors viewing the blog post.
- **Payload Used:**
  ```html
  <script>alert(document.domain)</script>
  ```
- **Observed Result:** Payload executed persistently every time the blog post page was loaded by any visitor.
- **Architectural Lesson:** Stored XSS carries higher impact because it does not require tricking the victim into clicking a specific link; any user viewing the page is compromised. Defense requires strict input validation and output encoding upon rendering.

---

## Lab 3: DOM XSS in `document.write` Sink Using Source `location.search`

- **Lab URL:** `https://portswigger.net/web-security/cross-site-scripting/dom-based/lab-document-write-sink`
- **Vulnerable Target:** Search input parsed client-side via JavaScript.
- **Context:** The client-side JavaScript reads the search term directly from `window.location.search` and passes it unsanitized into a `document.write()` sink:
  ```javascript
  var query = (new URLSearchParams(window.location.search)).get('search');
  document.write('<img src="/resources/images/tracker.gif?searchTerms=' + query + '">');
  ```
- **Payload Used:**
  ```html
  "> <img src=x onerror=alert(1)>
  ```
- **Observed Result:** Breaks out of the `src` attribute string literal, appends an image with an error handler, executing `alert(1)` in the DOM.
- **Architectural Lesson:** DOM XSS occurs entirely client-side without modifying server responses. Defending against DOM XSS requires avoiding execution sinks (`document.write`, `innerHTML`, `eval`) and using safe DOM APIs (`textContent`, `setAttribute`).

---

## Lab 4: Reflected XSS into a JavaScript String with Single Quote and Backslash Escaped

- **Lab URL:** `https://portswigger.net/web-security/cross-site-scripting/contexts/lab-javascript-string-single-quote-backslash-escaped`
- **Vulnerable Target:** Search input reflected inside a `<script>` block.
- **Context:**
  ```html
  <script>
      var searchTerms = 'USER_INPUT';
  </script>
  ```
  The server escapes single quotes (`\'`) and backslashes (`\\`), preventing standard quote breakout (`'; alert(1); //`).
- **Payload Used:**
  ```html
  </script><script>alert(1)</script>
  ```
- **Observed Result:** The HTML parser processes the closing `</script>` tag **before** the JavaScript engine executes, terminating the script block and starting a new tag which executes the alert.
- **Architectural Lesson:** When embedding variables in script blocks, string escaping alone is insufficient because HTML parsers take precedence over script interpreters. The correct mitigation is embedding data via `JSON.parse` with HTML-hex entity encoding (`json_encode($data, JSON_HEX_TAG)`).
