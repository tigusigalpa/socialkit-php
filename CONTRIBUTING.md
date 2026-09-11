# Contributing

Thank you for your interest in contributing to the SocialKit PHP SDK! Contributions
of every size are welcome — from fixing a typo to adding a whole endpoint.

## Getting Started

1. **Fork** the repository.
2. **Clone** your fork locally.
3. **Install** dependencies:
   ```bash
   composer install
   ```
4. **Run** the test suite to make sure everything is green:
   ```bash
   composer test
   ```

## Development Workflow

1. Create a feature branch (`git checkout -b feature/my-improvement`).
2. Make your changes, following the existing code style:
   - `declare(strict_types=1)` in all files.
   - `final` classes where appropriate.
   - Readonly properties for immutability.
   - PHPDoc with `@param`, `@return`, `@throws`.
   - PSR-18 HTTP client patterns.
3. Add or update tests for your change. All tests must pass without a real API key
   or network access (use Guzzle's `MockHandler`).
4. Run the test suite:
   ```bash
   composer test
   ```
5. Commit your changes with a clear, descriptive message.
6. Open a pull request describing what and why.

## Code Style

- Follow PSR-4 autoloading.
- Use strict types in every file.
- Prefer immutable DTOs with readonly properties.
- Include `fromArray()` and `toArray()` methods on DTOs.
- Add `Upstream docs:` links in service method docblocks.
- Never log, serialize, or expose the API key.

## Reporting Issues

Found a bug or have a question? [Open an issue](https://github.com/tigusigalpa/socialkit-php/issues).

## Pull Request Checklist

- [ ] Tests pass (`composer test`).
- [ ] No API key is exposed in code, tests, or output.
- [ ] New DTOs have `fromArray()` and `toArray()`.
- [ ] Service methods include upstream docs links.
- [ ] `declare(strict_types=1)` is present in all new files.
