# Ejemplo de un plugin de Joomla para campos extras de contacto

Este es un ejemplo de un plugin de Joomla que permite agregar campos extras al formulario de contacto estándar de Joomla.

Note: there is an English README file [here](./README_EN.md).


Este plugin cumple dos funciones básicas:

1   Agrega al formulario de contacto los campos adicionales contenidos en el archivo **forms/extrafields.xml**.

2  Si una configuración del plugin es activada, este agrega un selector que permite elegir a cual contacto se le enviará el email.

## Agregando nuevos campos

Se puede agregar nuevos campos al formulario de contacto simplemente agregándolos en el formulario XML: **forms/extrafields.xml**.

No olvide agregar las traducciones correspondientes a cada nuevo campo en el archivo de idioma correspondiente.

## Instalación

Usted puede comprimir los archivos en un solo archivo con formato zip e instalarlo como cualquier otra extensión estándar de Joomla.