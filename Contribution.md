# Contribution
Welcome and thank you for considering to improve this package. Please remember that all contribiutions shall be fully credited once approved. All contributions to ths package will be accepted via Pull Requests.

# Things to Note
* Follow the PSR-2 Coding Standards.

* Ensure you create a branch for every feature.

* One pull request per feature.

* Includes tests (we use **Pest** and **Orchestra Testbench**—see [README#Testing](README.md#testing)).

* Document all the changes and update README.md file accordingly.

* All tests must be running successfully before submitting a PR (`composer test` or `vendor/bin/pest`).

# CI

Every push to `main`/`develop` and every PR runs the `tests` workflow across a PHP/Laravel version matrix (see `.github/workflows/tests.yml`).

# Releases

Releases are automated via GitHub Actions, gated on the `tests` workflow passing on `develop`/`main`:

* A green `tests` run on `develop` tags and publishes a **prerelease** (`vX.Y.Z-rc.N`).
* A green `tests` run on `main` tags and publishes a **release** (`vX.Y.Z`).

Both default to bumping the patch version of the latest matching tag. To release a specific minor/major version instead — or to release without waiting on `tests` — run the workflow manually from the **Actions** tab (`Release Develop (Prerelease)` or `Release Main`) with an optional `release_version` input, e.g. `1.1.0`.

Let's Gooo!



