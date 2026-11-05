# Browser DevTools Console Snippets

> **Purpose:** Demonstrate JavaScript console techniques useful for web security analysis.
> All commands are run in the browser DevTools console (F12) on pages you own or have authorization to test.

---

## 1. Read All Cookies Accessible to JavaScript

```javascript
// Lists all cookies available to JS (httpOnly cookies are NOT shown here — that's a security feature)
document.cookie
// Expected: "PHPSESSID=abc123; theme=dark" (or empty string if only httpOnly cookies set)
```

## 2. List All Forms on the Page

```javascript
// Returns an HTMLCollection of all <form> elements
let forms = document.forms;
for (let i = 0; i < forms.length; i++) {
  console.log(`Form ${i}: action="${forms[i].action}", method="${forms[i].method}"`);
  for (let el of forms[i].elements) {
    console.log(`  Input: name="${el.name}", type="${el.type}", value="${el.value}"`);
  }
}
// Expected: logs each form's action/method and each input's name/type/value
```

## 3. Edit Visible Page Text

```javascript
// Find the first <h1> and change its text
document.querySelector('h1').textContent = 'Page Modified by Console';
// Expected: The H1 heading on the page changes visually (not saved to server)
```

## 4. Read and Write localStorage

```javascript
// Write a value
localStorage.setItem('lab_note', 'XSS demo value stored in localStorage');

// Read it back
console.log(localStorage.getItem('lab_note'));
// Expected: "XSS demo value stored in localStorage"

// List all localStorage keys
for (let i = 0; i < localStorage.length; i++) {
  let key = localStorage.key(i);
  console.log(`${key}: ${localStorage.getItem(key)}`);
}

// Remove a key
localStorage.removeItem('lab_note');
```

## 5. Read sessionStorage

```javascript
// SessionStorage — cleared when tab closes
sessionStorage.setItem('session_test', 'hello from console');
console.log(sessionStorage.getItem('session_test'));
// Expected: "hello from console"
```

## 6. Inspect All External Script Sources

```javascript
// Identify all external JS files loaded on the page (useful for detecting CDN hijacking risks)
let scripts = document.querySelectorAll('script[src]');
scripts.forEach(s => console.log(s.src));
// Expected: list of absolute URLs of all external scripts
```

## 7. Change Element Styling Dynamically

```javascript
// Make all paragraphs red with a yellow background
document.querySelectorAll('p').forEach(p => {
  p.style.color = 'red';
  p.style.backgroundColor = 'yellow';
});
// Expected: All <p> elements change color immediately (client-side only)
```

## 8. Inspect All Hyperlinks and External References

```javascript
// Find all links and flag external ones
document.querySelectorAll('a[href]').forEach(a => {
  let isExternal = a.host !== window.location.host;
  console.log(`${isExternal ? '[EXTERNAL]' : '[internal]'} ${a.href}`);
});
// Expected: each link labeled as internal or external
```

## 9. Submit a Form Programmatically via fetch()

```javascript
// Simulate a POST form submission — useful for replaying requests
let formData = new FormData();
formData.append('username', 'test_user');
formData.append('password', 'test_pass');

fetch('/php/auth.php', {
  method: 'POST',
  body: formData,
  credentials: 'include'   // include cookies
})
.then(r => r.text())
.then(html => console.log(html.substring(0, 500)));
// Expected: first 500 chars of auth.php response
```

## 10. Detect Hidden Form Fields

```javascript
// Hidden inputs are often used for CSRF tokens, user IDs, state — worth inspecting
document.querySelectorAll('input[type="hidden"]').forEach(inp => {
  console.log(`name="${inp.name}", value="${inp.value}"`);
});
// Expected: lists each hidden field's name and current value
// Security note: visible to client — never rely on hidden fields for authorization
```

---

> **Note:** These snippets are for educational analysis of pages you own.
> Never run console scripts against pages you do not own or have authorization to test.
