<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted'             => 'El campo :attribute debe ser aceptado.',
    'active_url'           => 'El campo :attribute no es una URL válida.',
    'after'                => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal'       => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha'                => 'El campo :attribute sólo debe contener letras.',
    'alpha_dash'           => 'El campo :attribute sólo debe contener letras, números y guiones.',
    'alpha_num'            => 'El campo :attribute sólo debe contener letras y números.',
    'array'                => 'El campo :attribute debe ser un conjunto.',
    'before'               => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal'      => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
    'between'              => [
        'numeric' => 'El campo :attribute tiene que estar entre :min - :max.',
        'file'    => 'El campo :attribute debe pesar entre :min - :max kilobytes.',
        'string'  => 'El campo :attribute tiene que tener entre :min - :max caracteres.',
        'array'   => 'El campo :attribute tiene que tener entre :min - :max ítems.',
    ],
    'boolean'              => 'El campo :attribute debe tener un valor verdadero o falso.',
    'confirmed'            => 'La confirmación de :attribute no coincide.',
    'date'                 => 'El campo :attribute no es una fecha válida.',
    'date_format'          => 'El campo :attribute no corresponde al formato :format.',
    'different'            => 'El campo :attribute y :other deben ser diferentes.',
    'digits'               => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between'       => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'dimensions'           => 'Las dimensiones de la imagen :attribute no son válidas.',
    'distinct'             => 'El campo :attribute contiene un valor duplicado.',
    'email'                => 'El campo :attribute no es un correo válido',
    'exists'               => 'El campo :attribute es inválido.',
    'file'                 => 'El campo :attribute debe ser un archivo.',
    'filled'               => 'El campo :attribute es obligatorio.',
    'image'                => 'El campo :attribute debe ser una imagen.',
    'in'                   => 'El campo :attribute es inválido.',
    'in_array'             => 'El campo :attribute no existe en :other.',
    'integer'              => 'El campo :attribute debe ser un número entero.',
    'ip'                   => 'El campo :attribute debe ser una dirección IP válida.',
    'json'                 => 'El campo :attribute debe tener una cadena JSON válida.',
    'max'                  => [
        'numeric' => 'El campo :attribute no debe ser mayor a :max.',
        'file'    => 'El campo :attribute no debe ser mayor que :max kilobytes.',
        'string'  => 'El campo :attribute no debe ser mayor que :max caracteres.',
        'array'   => 'El campo :attribute no debe tener más de :max elementos.',
    ],
    'mimes'                => 'El campo :attribute debe ser un archivo con formato: :values.',
    'mimetypes'            => 'El campo :attribute debe ser un archivo con formato: :values.',
    'min'                  => [
        'numeric' => 'El tamaño de :attribute debe ser de al menos :min.',
        'file'    => 'El tamaño de :attribute debe ser de al menos :min kilobytes.',
        'string'  => 'El campo :attribute debe contener al menos :min caracteres.',
        'array'   => 'El campo :attribute debe tener al menos :min elementos.',
    ],
    'not_in'               => 'El campo :attribute es inválido.',
    'numeric'              => 'El campo :attribute debe ser numérico.',
    'present'              => 'El campo :attribute debe estar presente.',
    'regex'                => 'El formato de :attribute es inválido.',
    'required'             => 'El campo :attribute es obligatorio.',
    'required_if'          => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_unless'      => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with'        => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_with_all'    => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_without'     => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de :values estén presentes.',
    'same'                 => 'El campo :attribute y :other deben coincidir.',
    'size'                 => [
        'numeric' => 'El tamaño de :attribute debe ser :size.',
        'file'    => 'El tamaño de :attribute debe ser :size kilobytes.',
        'string'  => 'El campo :attribute debe contener :size caracteres.',
        'array'   => 'El campo :attribute debe contener :size elementos.',
    ],
    'string'               => 'El campo :attribute debe ser una cadena de caracteres.',
    'timezone'             => 'El :attribute debe ser una zona válida.',
    'unique'               => 'El valor del campo :attribute ya ha sido registrado.',
    'uploaded'             => 'Subir :attribute ha fallado.',
    'url'                  => 'El formato :attribute es inválido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom'               => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes'           => [
        'name'                  => 'nombre',
        'username'              => 'usuario',
        'email'                 => 'correo electrónico',
        'first_name'            => 'nombre',
        'last_name'             => 'apellido',
        'password'              => 'contraseña',
        'password_confirmation' => 'confirmación de la contraseña',
        'city'                  => 'ciudad',
        'country'               => 'país',
        'address'               => 'dirección',
        'phone'                 => 'teléfono',
        'mobile'                => 'móvil',
        'age'                   => 'edad',
        'sex'                   => 'sexo',
        'gender'                => 'género',
        'year'                  => 'año',
        'month'                 => 'mes',
        'day'                   => 'día',
        'hour'                  => 'hora',
        'minute'                => 'minuto',
        'second'                => 'segundo',
        'title'                 => 'título',
        'body'                  => 'contenido',
        'description'           => 'descripción',
        'excerpt'               => 'extracto',
        'date'                  => 'fecha',
        'time'                  => 'hora',
        'subject'               => 'asunto',
        'message'               => 'mensaje',
        'public_key'            => 'llave pública',
        'private_key'            => 'llave privada',
        /*
        * modulo ActJugador
        */

        //ActJugador
        'jugador_id'            => 'identificador del jugador',
        'concepto_id'            => 'identificador del concepto',
        'insercion_dinero_real'            => 'inserción del dinero real',
        'deduccion_dinero_real'            => 'deducción del dinero real',
        'balance_dinero_real'            => 'balance del dinero real',
        'movimiento_id'            => 'identificador de movimiento',
        'insercion_dinero_real_bono'            => 'inserción del dinero real del bono',
        'deduccion_dinero_real_bono'            => 'deducción del dinero real del bono',
        'balance_dinero_real_bono'            => 'balance del dinero real del bono',
        'insercion_dinero_virtual_bono'            => 'inserción del dinero virtual del bono',
        'deduccion_dinero_virtual_bono'            => 'deducción del dinero virtual del bono',
        'balance_dinero_virtual_bono'            => 'balance del dinero virtual del bono',
        'balance_ganancia_bono'            => 'balance de ganacia del bono',
        'insercion_ganancia_bono'            => 'inserción de la ganancia del bono',
        'deduccion_ganancia_bono'            => 'deducción de la ganacia del bono',

        //Concepto
        'afecta_actividad_jugador'            => 'afecta actividad del jugador',
        'es_deposito'            => 'es depósito',
        'es_comision'            => 'es comisión',
        'es_concepto_sistema'            => 'es concepto del sistema',

        //Movimiento
        'fecha_operacion'            => 'fecha de operación ',
        'ip_transaccion'            => 'IP de la transacción',
        'fecha_registro'            => 'fecha de registro',
        'tarjeta_jugador_id'            => 'identificador de la tarjeta del jugador',
        'estado_transaccion_id'            => 'identificador del estado de la transacción',
        'concepto_id'            => 'identificador del concepto',
        'metodo_pago_id'            => 'identificador del método de pago',
        'tiko_mensaje_id'            => 'identificador del mensaje de tiko',
        'tipo_transaccion_id'            => 'identificador del tipo de transacción',
        'numero_transaccion'            => 'número de la transacción',
        'session_casino_id'         => 'identificador de la sesión del casino',
        //TarjetasJugador
        'tiene_transaccion_aprobada'            => 'transacciones aprobadas',
        'necesita_verificacion'            => 'necesita verificación',
        'numero_tarjeta'            => 'número de tarjeta',
        'fecha_registro'            => 'fecha de registro',
        /*
         * Modulo Juego
         */
        //juego
        'nombre_publico'            => 'nombre público ',
        'proveedor_juego_id'            => 'identificador del proveedor de juego',
        'nombre_juego_proveedor'            => 'nombre del proveedor de juego',
        'archivo_temporal_logo_id'            => 'logo del juego',
        'archivo_temporal_juego_id'            => 'imagen del juego ',
        'id_juego_proveedor'            => 'identificador del juego del proveedor',
        'es_movil'            => 'es móvil',
        'resolucion'            => 'resolución',
        'categoria_juego_id'            => 'identificador de la categoría',
        'mgs_module_id'         => 'identificador del mensaje del módulo',
        'mgs_client_id'         => 'identificador del mensaje del cliente',
        /*
         * modulo operador
         */
        //Accion
        'ruta_id'            => 'identificador de la ruta ',
        //Operador
        'rol_id'            => 'identificador del rol',
        'telefono'            => 'teléfono',
        //ruta
        'metodo'            => 'método',
        //Ticket
        'equipo_operador_id'                => 'identificador del equipo operador',
        //TicketNota
        'ticket_id'            => 'identificador de ticket',
        /*
         * Proveedor
         */
        //ProveedorJuego
        'pais_id'            => 'identificador de pais',
        /*
        * Tiko
        */
        //TikoMensaje
        'fecha_operacion'            => 'fecha de operación',
        'tiko_mensaje_estado_id'            => 'identificador del mensaje de estado de tiko',
        /*
        * Usuario
        */
        //ActividadEconomica
        'sector_economico_id'            => 'identificador del sector económico',
        //AudioJugador
        'archivo_temporal_id'            => 'identificador del archivo temporal',
        'operador_aprobador_id'          => 'identificador del operador aprobador',
        //ClaseUsuario
        'min_monto_depositado'            => 'monto mínimo depositado',
        'max_monto_depositado'            => 'monto máximo depositado',
        'maximo_deposito_d'            => 'máximo depósito por día',
        'maximo_deposito_s'            => 'máximo depósito por semana',
        'maximo_retiro_d'            => 'máximo retiro por día',
        'maximo_retiro_s'            => 'máximo retiro por semana',
        'maximo_deposito_m'            => 'máximo depósito por mes',
        'maximo_retiro_m'            => 'máximo retiro por mes',
        'operador_id'            => 'identificador del operador',
        //DocumentoJugador
        'tipo_documento_id'            => 'identificador del tipo de documeto',

        //HistorialSession
        'fecha_ultimo_refresh_token'            => 'fecha del último toke',
        'fecha_login'            => 'fecha de logueo',
        //Jugador
        'telefono_fijo'            => 'teléfono fijo',
        'telefono_celular'            => 'teléfono celular',
        'genero'            => 'género',
        'fecha_nacimiento'            => 'fecha de nacimiento',
        'codigo_postal'            => 'código postal',
        'estado_pais_id'            => 'identificador del estado del país',
        'direccion1'            => 'direción',
        'actividad_economica_id'            => 'identificador de la actividad económica',
        'clase_usuario_id'          =>'identificador de la clase de usuario',
        //NotaJugador
        'descripcion'            => 'descripción',
        //Titulo
        'titulo'            => 'título',
    ],

];
