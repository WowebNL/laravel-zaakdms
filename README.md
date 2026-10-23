# Laravel Zaakdms package

## Configuration

Publish the configuration with `php artisan vendor:publish --provider="Woweb\Zaakdms\ZaakdmsServiceProvider"`, or set the environment variables below.

### TLS

The package has no default certificate paths. Set them in the environment:

| Variable | Config key | Meaning |
| --- | --- | --- |
| `ESB__SEND_CERT` | `zaakdms.cert` | Path to the client certificate |
| `ESB_SEND_PK` | `zaakdms.cert_key` | Path to the private key of the client certificate |
| `ESB_VERIFY` | `zaakdms.verify` | Path to the CA bundle used to verify the server, or `true`/`false` |

When a variable is not set, no client certificate is sent and the server certificate is verified against the default CA bundle.

### Timezone

| Variable | Config key | Default |
| --- | --- | --- |
| `ZAAKDMS_TIMEZONE` | `zaakdms.timezone` | `Europe/Amsterdam` |

Timestamps the package generates itself (`tijdstipBericht` and `datumStatusGezet`) are converted to this timezone, because the DMS expects local time while applications often run on UTC. Set it to an empty value to use the application timezone (`app.timezone`). Dates you pass in yourself, such as `creatiedatum`, are sent as given.

## Changelog

See [CHANGELOG.md](CHANGELOG.md). Release 1.0.1 sends the message timestamps in local time and removes the default certificate paths.

## Running the tests

```bash
composer install
composer test
```
