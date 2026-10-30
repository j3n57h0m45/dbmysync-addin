# Contributing

## Commit convention

This project follows [Conventional Commits](https://www.conventionalcommits.org/) and
[Semantic Versioning](https://semver.org/). Commits on `main` should look like this.
Pull request titles are checked by CI (so they can be squash-merged as is):

    <type>(optional scope): <description>

| Type | Changelog section | Version bump |
| --- | --- | --- |
| `feat` | Added | minor |
| `fix` | Fixed | patch |
| `perf`, `refactor` | Changed | patch |
| `revert` | Fixed | patch |
| `docs`, `style`, `test`, `build`, `ci`, `chore` | (not listed) | none |

A `!` after the type (`feat!: ...`) or a `BREAKING CHANGE:` footer triggers a **major** bump.

## Releasing (maintainers)

Run the **Release** workflow (Actions tab, "Run workflow") on `main`. It:

1. checks that every commit since the last tag follows the convention (fails with a list otherwise),
2. computes the next version with [git-cliff](https://git-cliff.org) and regenerates `CHANGELOG.md`
   ([Keep a Changelog](https://keepachangelog.com/) format, see `cliff.toml`),
3. commits `chore(release): vX.Y.Z`, tags it and pushes both to `main`,
4. creates the GitHub Release with that version's notes.

The PR-title check only runs on pull requests. Direct pushes to `main` are not checked
until release time, so a bad subject shows up as a failed release run. Commits that are not
Conventional Commits never appear in the changelog and never trigger a version bump.

Never add a `version` field to `composer.json`; Packagist reads versions from the tags.
