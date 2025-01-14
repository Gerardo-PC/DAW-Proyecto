# Requisitos Windows
Necesita habilitar módulos, entre ellos mbstring 
En Windows en php.ini modificaciones:

# Directorio para carga de extensiones Windows: 

~~~~
; Directory in which the loadable extensions (modules) reside.
; https://php.net/extension-dir
;extension_dir = "./"
; On windows:
extension_dir = "ext"
~~~~

## Habilitar la extensión en windows

~~~~
;;;;;;;;;;;;;;;;;;;;;;
; Dynamic Extensions ;
;;;;;;;;;;;;;;;;;;;;;;

; If you wish to have an extension loaded automatically, use the following
; syntax:
;
;   extension=modulename
;
; For example:
;
;   extension=mysqli
;
; When the extension library to load is not located in the default extension
; directory, You may specify an absolute path to the library file:
;
;   extension=/path/to/extension/mysqli.so
;
; Note : The syntax used in previous PHP versions ('extension=<ext>.so' and
; 'extension='php_<ext>.dll') is supported for legacy reasons and may be
; deprecated in a future PHP major version. So, when it is possible, please
; move to the new ('extension=<ext>) syntax.
;
; Notes for Windows environments :
;
; - Many DLL files are located in the ext/
;   extension folders as well as the separate PECL DLL download.
;   Be sure to appropriately set the extension_dir directive.
;
;extension=bz2

; The ldap extension must be before curl if OpenSSL 1.0.2 and OpenLDAP is used
; otherwise it results in segfault when unloading after using SASL.
; See https://github.com/php/php-src/issues/8620 for more info.
;extension=ldap

;extension=curl
;extension=ffi
;extension=ftp
;extension=fileinfo
;extension=gd
;extension=gettext
;extension=gmp
;extension=intl
;extension=imap
extension=mbstring  ; EXTENSIÓN REQUERIDA POR PHPUNIT
;extension=exif      ; Must be after mbstring as it depends on it
;extension=mysqli
;extension=oci8_12c  ; Use with Oracle Database 12c Instant Client
;extension=oci8_19  ; Use with Oracle Database 19 Instant Client
;extension=odbc
;extension=openssl
;extension=pdo_firebird
extension=pdo_mysql ; EXTENSIÓN REQUERIDA POR APLICACIÓN
;extension=pdo_oci
;extension=pdo_odbc
~~~~



