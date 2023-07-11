Modulo creado para "Estrenar vivienda"

PRIMEROS PASOS
--------------
Para usar este modulo se debe tener activo el modulo 'RESTUI'
Habilitar el recurso y los metodos en el formulario de configuracion '/admin/config/services/rest'
Asignar permisos para cada metodo activo '/admin/people/permissions#module-rest'


URL DE FORMULARIO
------------
'/example-module/form'


API-REST
--------

*VER TODOS LOS REGISTROS (metodo get)
/example-module/data/all

*VER REGISTRO POR ID (metodo get)
/example-module/data/{id_registro}

*ELIMINAR REGISTRO UNICO (metodo delete)
/example-module/data/{id_registro}

*ACTUALIZAR REGISTRO (metodo patch)
/example-module/data/{id_registro}

*CREAR REGISTRO (metodo post)
/example-module/data/crear/registro


EJEMPLO ESTRUCTURA JSON
-----------------------

[
    {
        "nombre": "article",
        "identificacion": "1234567",
        "fecha_nacimiento": "1999-12-11",
        "cargo": "2"    
    },
    {
        "nombre": "article 2",
        "identificacion": "1234567"    
    }
]



@autor Michael Fabian Rodriguez Rodriguez
@mail michaelfrr2@gmail.com
@phone +57 3184860230