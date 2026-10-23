# Changelog

All notable changes to this package are documented in this file.

## 1.0.1

### Changed (behaviour)

- Timestamps the package generates itself are now sent in local time. `tijdstipBericht` in every StUF message that contains it and `datumStatusGezet` in `actualiseerZaakstatus` used the application timezone, so an application running on UTC sent times one or two hours behind local time. They are now converted to the timezone in the new `zaakdms.timezone` option (`ZAAKDMS_TIMEZONE`, default `Europe/Amsterdam`) before they are formatted. For example, 13:30 UTC is sent as `153000` in summer and as `143000` in winter, and 23:30 UTC on 31 December is sent as 00:30 on 1 January. An application that already runs on `Europe/Amsterdam` sends the same times as before. Set the option to an empty value to keep using the application timezone. Dates passed in by the caller, such as `creatiedatum`, are not converted.

### Changed (possibly breaking)

- `cert`, `cert_key` and `verify` no longer have default paths under `storage/cert`. They are read from `ESB__SEND_CERT`, `ESB_SEND_PK` and `ESB_VERIFY` only (the environment variable names are unchanged). If you relied on the old default paths, set these variables to the paths of your files. When a variable is not set, the option is passed to the HTTP client as empty: no client certificate is sent and the server certificate is verified against the default CA bundle.
- `voegZaakdocumentToe` validates the `disk` option against the disks configured in `filesystems.disks`, like `updateZaakDocument`, instead of a fixed list of disk names. Any configured disk can now be used; a disk that is not configured is rejected with an `InvalidZaakdmsArgumentsException`.

### Fixed

- Removed environment specific default values and comments from the configuration and the code.

## 1.0.0

First tagged release. Before this release the package was only available from the `main` branch.

### Changed (behaviour)

- Message timestamps now use a 24 hour clock. `tijdstipBericht` in every StUF message that contains it and `datumStatusGezet` in `actualiseerZaakstatus` were formatted with `Ymdhis`, which renders the hour on a 12 hour clock without an AM/PM marker: 15:30 was sent as `033000`. They are now formatted with `YmdHis`, so 15:30 is sent as `153000`. Times from 13:00 onwards and between 00:00 and 00:59 (previously sent as `12`) are now correct; times from 01:00 to 12:59 are unchanged.

### Added

- `updateZaakDocument` accepts a `disk` option, like `voegZaakdocumentToe`. The file passed in `file` is read from that disk; without the option the default disk is used, as before. The value must be one of the disks configured in `filesystems.disks`.
- A test suite based on Orchestra Testbench (`composer test`).

### Changed

- `updateZaakDocument` sends its request with the same timeout and retry as `voegZaakdocumentToe`: a 45 second timeout and one retry after 100 milliseconds. An error response that is still returned after the retry is thrown by the HTTP client as an `Illuminate\Http\Client\RequestException` (a failed connection as a `ConnectionException`), so for this call the response body is no longer written to the `zaakdms` log channel. This matches `voegZaakdocumentToe`.
