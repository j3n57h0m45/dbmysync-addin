# Contributing

## Commit convention

This project follows [Conventional Commits](https://www.conventionalcommits.org/) and
[Semantic Versioning](https://semver.org/). Pull requests are **squash-merged**, so the
**PR title** becomes the commit on `main` and is checked by CI:

    <type>(optional scope): <description>

| Type | Changelog section | Version bump |
| --- | --- | --- |
| `feat` | Added | minor |
| `fix` | Fixed | patch |
| `perf`, `refactor` | Changed | patch |
| `security` | Security | patch |
| `deprecate` | Deprecated | patch |
| `revert` | Removed | patch |
| `docs`, `style`, `test`, `build`, `ci`, `chore` | (not listed) | none |

A `!` after the type (`feat!: ...`) or a `BREAKING CHANGE:` footer triggers a **major** bump.

## Releasing (maintainers)

1. Run the **Release** workflow (Actions tab, "Run workflow") on `main`.
2. It computes the next version with [git-cliff](https://git-cliff.org), regenerates
   `CHANGELOG.md` ([Keep a Changelog](https://keepachangelog.com/) format, see `cliff.toml`)
   and opens a `chore(release): vX.Y.Z` pull request.
3. Review the changelog, then **squash-merge** it keeping the title.
4. The merge creates the `vX.Y.Z` tag and a GitHub Release with that version's notes.

Never add a `version` field to `composer.json`; Packagist reads versions from the tags.
