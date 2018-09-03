# David Corto - TEST

He usado SF4 para montar la base. Creo que es la opción más directa por lo siguiente:

- SF4 ya está pensando para micro servicios de base, ya no es como versiones anteriores que sí o sí tenias que instalar todo el framework
- Por tiempo me resulta mucho más sencillo usar SF4 que empezar a tirar de PHP "a pelo". Pero es por velocidad. Con SF4 tengo gratis: DBAL, consola e injector de servicios.

Por otro lado, no he usado la imagen docker para los test unitarios. Asumiendo que hay el php 7 instalado en local, ya 
está todo preparado para correr los test en local sin necesidad de Docker. En realidad al ejecutar el comando '/usr/local/bin/phpunit' me devuelve el error: ***Cannot open file "run.php"*** me imagino porque me faltá ajustar 
el script para lanzar el run.php (crearlo y tal). Lo usaréis así internamente imagino, no he querido perder tiempo en ese detalle.

Por último, no he usado el ORM de Doctrine, simplemente el DBAL Doctrine para mantener lo más simple posible la implementación.

## How to setup and run this code

> copy file .env.dist to .env and configure DATABASE_URL (should be already done, just check all envars)

> do a "***$ composer install***" for install all dependencies
 
> run the command "***$ php bin/console euromillions:results***" for run the test

> run "***$ ./bin/phpunit***" for run the tests 

## TODO's

> añadir cache de los resultados

> separar el acceso a DB de la clase de API

> usar Guzzle para comunicar con la API en lugar del get_file_contents

> tests funcionales


# Software developer test
Una prueba para unirse al equipo de desarrollo de Euromillions.com

## Que es la lotería Euromillions? 

***EuroMillions*** es la lotería transnacional más grande de Europa que cuenta con un bote mínimo de 17 millones de euros cada martes y viernes. 
> El bote puede acumularse hasta llegar a un tope de 190 millones de euros, en caso de no haber ganadores.  

> Para completar un juego de EuroMillions se seleccionan cinco números principales del 1 al 50 y dos estrellas del 1 al 12.


[Ejemplo de resultados](https://euromillions.com/es/euromillions/resultados)

[Ejemplo para jugar](https://euromillions.com/es/euromillions/jugar) 

## Como empezar

Necesitas tener docker, docker-compose y PHP7 instalado en tu entorno.

Haz un clone del proyecto y lanza el fichero bootstrap.sh(entendemos que trabajas con Linux, Mac, BSD). Si tienes
windows ponte en contacto con nosotros.

Que hace el fichero bootstrap.sh ?

Descarga dos imágenes docker y arranca dos contenedores, mysql y phpunit, que mediante un alias podrás llamarlo desde 
la línea de comandos y así ejecutar los tests.

> Cuando el contenedor mysql arranca, crea una base de datos llamada **euromillions** con una tabla para almacenar el draw
que recojas de la API. Puedes acceder la bbdd por el puerto 3306 de tu máquina local.

Seguramente necesites añadir algún contenedor para hacer caching de los resultados, pero si no te da tiempo, no te preocupes.

Si necesitas alguna aclaración o encuentras algún error, escribe una issue en el repositorio. 

## Que hay que hacer

Como tenemos muchas integraciones con diferentes Apis, el test estará relacionado con estas.

Queremos recuperar los resultados del último sorteo de Euromillions. Para esto necesitarás
invocar a una API.

##### Por favor, cuando inicies la prueba, te pones en contacto con nosotros y te envíamos el endpoint y la api key necesaria para tener acceso. 

Tienes que diseñar sabiendo que esta api puede fallar en algún momento y necesitamos poder invocar a otra API para recuperar los
resultados(esta segunda API que implementes puede ser un fake).

El último resultado se visualiza en la home y recibimos muchas visitas(evita consultar a la bbdd para mostrar el resultado).

Este último resultado lo mostrarás por el terminal cuando invoquemos el script de inicio.

Puedes utilizar cualquier patrón de diseño que necesites, VO y sobretodo, test unitarios y de integración.

Intenta sobretodo centrarte en recuperar los datos que devuelva la API y almacenarlos para después visualizarlos. Sino te da tiempo de implementar el caching del resultado o no tienes más
ganas de seguir, no pasa nada, no te preocupes.

## Dudas

Puedes abrir una issue en este repositorio.

## Como enviar tu solución

Haz un pull request al repositorio o nos la envías por email. 