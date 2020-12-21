# Development to do list

## Modernization for PHP > 5 and newer OS

* change from deprecated mysql to mysqli ✅
* switch to supported PDF library ([tcpdf](https://tcpdf.org/))

## Maintainability

* rename numbered files to something more readable
* implement release automation
* implement automated tests

## Best practice implementation

* switch everything to UTF-8 (better i18n, support for international names, support for modern PDF library, ...)
* use redirect after POST for all form actions
* implement CSRF protection

## Code quality

* do not store request scoped objects in session
* unify formatting
* add copyright headers in all files
* remove dead code / commented sections

## Documentation

* add documentation for all request flows
* add documentation for the signer protocol