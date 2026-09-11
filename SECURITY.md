# Security Policy

## Supported Versions

| Version | Supported          |
|---------|--------------------|
| 1.0.x   | :white_check_mark: |

## Reporting a Vulnerability

If you discover a security vulnerability within this SDK, please send an email to
**sovletig@gmail.com**. All security vulnerabilities will be promptly addressed.

Please do not disclose security-related issues publicly until a fix has been
released.

### Key Security Principles

This SDK follows these security principles:

1. **API keys are never logged, serialized, or exposed.** The access key is sent
   via the `x-access-key` HTTP header and is never placed in URLs or query strings
   by default.
2. **Key redaction.** All debug and error output replaces the access key with
   `[REDACTED]` via the `redactKey()` method.
3. **No key in exceptions.** Exception messages, response bodies, and metadata
   never contain the raw access key.
4. **Compatibility opt-in.** The `key_in_query` option is `false` by default and
   must be explicitly enabled to send the key as a query/body parameter.
5. **HTTPS by default.** The base URL uses `https://` to ensure encrypted transport.

## Disclosure Timeline

- **You report** the vulnerability via email.
- **We acknowledge** within 48 hours.
- **We investigate** and develop a fix.
- **We release** a patched version as soon as possible.
- **We disclose** the vulnerability after the fix is released, crediting the reporter
  if desired.
