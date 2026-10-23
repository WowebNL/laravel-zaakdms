# Changelog

All notable changes to this package are documented in this file.

## 1.0.0

First tagged release. Before this release the package was only available from the `main` branch.

### Changed (behaviour)

- Message timestamps now use a 24 hour clock. `tijdstipBericht` in every StUF message that contains it and `datumStatusGezet` in `actualiseerZaakstatus` were formatted with `Ymdhis`, which renders the hour on a 12 hour clock without an AM/PM marker: 15:30 was sent as `033000`. They are now formatted with `YmdHis`, so 15:30 is sent as `153000`. Times from 13:00 onwards and between 00:00 and 00:59 (previously sent as `12`) are now correct; times from 01:00 to 12:59 are unchanged.

### Added

- `updateZaakDocument` accepts a `disk` option, like `voegZaakdocumentToe`. The file passed in `file` is read from that disk; without the option the default disk is used, as before. The value must be one of the disks configured in `filesystems.disks`.
- A test suite based on Orchestra Testbench (`composer test`).

### Changed

- `updateZaakDocument` sends its request with the same timeout and retry as `voegZaakdocumentToe`: a 45 second timeout and one retry after 100 milliseconds. An error response that is still returned after the retry is thrown by the HTTP client as an `Illuminate\Http\Client\RequestException` (a failed connection as a `ConnectionException`), so for this call the response body is no longer written to the `zaakdms` log channel. This matches `voegZaakdocumentToe`.
