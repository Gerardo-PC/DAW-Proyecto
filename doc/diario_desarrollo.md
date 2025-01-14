# DIARIO DESARROLLO
Sobre este documento actualizo notas de diversos retos encontrados en la realización del proyecto.

## Definición de una arquitectura clara.
Ante la decisión de por donde comenzar a codificar, elijo un patrón clásico (MVC). Es complicado encontar información clara en internet para implementarlo con php nativo,sin frameworks. Finalmente elijo una estructura propuesta siguiendo la guía de [esta web]("https://dev.to/ulisesafcdev/desarrollar-un-proyecto-mvc-con-php-puro-4akg").

## Archivo de contraseñas.
Para archivo de contraseñas de modo unidireccional parece existir un criterio definido... almacenarlas encriptadas (con un algoritmo unidireccional) en la base de datos. El problema viene al decidir un método para archivar contraseñas de modo que debamos recuperarlas posteriormente para acceso a sistemas de terceros (los repositorios en mi caso.). Almacenar contraseñas encriptadas con un algoritmo bidireccional parece una mala práctica inevitable (necesitaremos recuperarlas). Algunos artículos [en stackexchange](https://security.stackexchange.com/questions/53031/storing-passwords-in-reversible-form-a-genuine-use-case)

## Acceso desde docker a repositorios smb
La idea inicial de realizar pruebas en repositorios smb se complica en el momento que php no soporta nativamente bibliotecas smb y la estructura planteada inicialmente para pruebas (un contenedor docker con los archivos compartidos para simular un servidor externo) se abandona inicialmente. Decido realizar las pruebas iniciales con ficheros locales (con la limitación que esto implica.). 

## Patrón Adapter para distintos accesos a repositorios
Bajo un interfaz común se accederán a distintos repositorios (implantados mediante clases distintas para cada tipo de repositorio: local, Docuware, Sharepoint, etc.). Se implementa un patrón "Adapter" [Ejemplo aquí](https://refactoring.guru/es/design-patterns/adapter) o  [aquí](https://medium.com/all-you-need-is-clean-code/una-interfaz-para-controlarlos-a-todos-patr%C3%B3n-adapter-a9073f3460b)

## Repositorio de ficheros lee todos los ficheros a Array al "conectar" para realizar búsquedas...
Es posible/merece la pena almacenar información con apc_fetch/apc_store ¿?

## Configuración servidor correo en hosting
Configuración SPIF y DKIM en hosting axarnet. Configuración certificado SSL para https...


## Errores al subir la aplicación al hosting
Configuración de la base de datos y error comprobando existencia bbdd.

$resultado = $this->consultaSqlParametros("SELECT schema_name FROM information_schema.schemata WHERE schema_name = ?",[$db]);

Cambiado por...

$stm=$this->con->prepare("SHOW DATABASES LIKE '$db'");






## Recursos empleados


| Recurso | Empleado en | Licencia | URL |
|---------|-------------|----------|-----|
| /img/document-text-svgrepo-com.svg | FavIcon | CC - Attribution | https://www.svgrepo.com/collection/solar-line-duotone-icons/ |
| Multiples | Iconos | Open Source Free | https://tablericons.com




Fuentes:
Arvo: https://fonts.google.com/specimen/Arvo
Licencia: https://fonts.google.com/specimen/Arvo/license

Roboto: https://fonts.google.com/specimen/Roboto
Licencia: https://fonts.google.com/specimen/Roboto/license


