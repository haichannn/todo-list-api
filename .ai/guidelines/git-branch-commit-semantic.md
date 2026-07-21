# Git Branch & Commit Semantic Guidelines

These guidelines **must** be applied to every commit and branch creation in this project.
Based on the [Conventional Commits v1.0.0](https://www.conventionalcommits.org/en/v1.0.0/) specification.

---

## Commit Message Format

Every commit message must follow this structure:

```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

### Rules

1. **`type` is required** — must always appear at the beginning of the commit message.
2. **`scope` is optional** — written in parentheses after the type, describing the part of the codebase affected.
3. **`description` is required** — a short summary of the change, written after `: ` (colon + space).
4. **`body` is optional** — a more detailed explanation, separated from the description by one blank line.
5. **`footer` is optional** — additional metadata (issue references, breaking changes, etc.).
6. **Use lowercase** for `type` and `scope`.
7. **Use English** for the entire commit message.
8. **Do not end the description with a period (`.`)**.
9. **Use imperative mood** in the description (e.g. "add", not "added" or "adds").

### Allowed Types

| Type         | Description                                                               |
| ------------ | ------------------------------------------------------------------------- |
| **feat**     | Introduces a new feature.                                                 |
| **fix**      | Patches a bug.                                                            |
| **docs**     | Documentation changes only.                                               |
| **style**    | Changes that do not affect logic (formatting, whitespace, etc.).          |
| **refactor** | Code change that is neither a bug fix nor a new feature.                  |
| **perf**     | Code change that improves performance.                                    |
| **test**     | Adding or correcting tests.                                               |
| **build**    | Changes that affect the build system or external dependencies.            |
| **ci**       | Changes to CI/CD configuration.                                           |
| **chore**    | Other changes that do not modify source or test files (maintenance, deps).|
| **revert**   | Reverts a previous commit.                                                |

### Scope

Scope describes the part of the codebase affected. Example scopes for this project:
- `api`, `auth`, `todo`, `user`, `config`, `migration`, `route`, `middleware`, `test`

### Breaking Changes

Breaking changes **must** be indicated in one of the following ways:

1. Append `!` after the type/scope:
   ```
   feat(api)!: change response format for todo endpoints
   ```
2. Add a `BREAKING CHANGE:` footer:
   ```
   feat(api): change response format for todo endpoints

   BREAKING CHANGE: response field `items` renamed to `data`
   ```

---

## Branch Naming Convention

Every new branch must follow this pattern:

```
<type>/<short-description>
```

### Rules

1. **Use the same type** from the commit type list above.
2. **Use lowercase** and separate words with hyphens (`-`).
3. **Keep it short** — maximum 3–5 words describing the purpose of the branch.
4. **Do not use spaces, underscores, or other special characters**.
5. For issue-related branches, you may include the issue number: `fix/42-resolve-auth-bug`

### Protected Branches

The following branches **must not** be pushed to directly:
- `main`
- `production` (if applicable)

---

## Quick Reference

```
# Commit
<type>[scope]: <description>      → feat(todo): add due date field
<type>[scope]!: <description>     → feat(api)!: change auth flow

# Branch
<type>/<short-description>        → feat/add-due-date
<type>/<issue>-<description>      → fix/42-auth-token-expired
```
