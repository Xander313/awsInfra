<?php

return [
    'wizard' => [
        'session_key' => 'sanctions.wizard',
    ],

    'steps' => [
        1 => ['title' => 'Datos generales', 'short' => 'General'],
        2 => ['title' => 'Categoría de infracción', 'short' => 'CDI'],
        3 => ['title' => 'Peso de la infracción', 'short' => 'PDI'],
        4 => ['title' => 'Naturaleza de la vulneración', 'short' => 'NDV / IED'],
        5 => ['title' => 'Intencionalidad', 'short' => 'INT'],
        6 => ['title' => 'Reiteración y reincidencia', 'short' => 'RER'],
        7 => ['title' => 'Resultado final', 'short' => 'Resultado'],
    ],

    'cdi' => [
        'responsable' => [
            'leve' => [
                [
                    'code' => 'resp_leve_peticiones_fuera_termino',
                    'title' => 'No tramitar, tramitar fuera de término o negar injustificadamente peticiones',
                    'description' => 'Incumplimiento en la atención de solicitudes de titulares dentro de los plazos y condiciones exigidas.',
                ],
                [
                    'code' => 'resp_leve_diseno_defecto',
                    'title' => 'No implementar protección de datos desde el diseño y por defecto',
                    'description' => 'Ausencia de criterios preventivos de privacidad desde el diseño del tratamiento.',
                ],
                [
                    'code' => 'resp_leve_politicas_no_disponibles',
                    'title' => 'No mantener disponibles políticas de protección de datos personales afines al tratamiento',
                    'description' => 'Falta de políticas visibles, disponibles o actualizadas según la operación realizada.',
                ],
                [
                    'code' => 'resp_leve_encargado_sin_garantias',
                    'title' => 'Elegir un encargado del tratamiento que no ofrezca garantías suficientes',
                    'description' => 'Selección de encargado sin capacidad demostrable para proteger datos personales.',
                ],
                [
                    'code' => 'resp_leve_incumplir_medidas_autoridad',
                    'title' => 'Incumplir medidas correctivas dispuestas por la autoridad',
                    'description' => 'Desatención de requerimientos o medidas correctivas emitidas por la autoridad competente.',
                ],
            ],
            'grave' => [
                [
                    'code' => 'resp_grave_no_medidas_integrales',
                    'title' => 'No implementar medidas administrativas, técnicas, físicas, organizativas y jurídicas para garantizar el tratamiento',
                    'description' => 'Falta grave de controles integrales para garantizar un tratamiento seguro y conforme.',
                ],
                [
                    'code' => 'resp_grave_fines_distintos',
                    'title' => 'Utilizar información o datos para fines distintos a los declarados',
                    'description' => 'Uso de datos personales con finalidades incompatibles o no informadas.',
                ],
                [
                    'code' => 'resp_grave_cesion_irregular',
                    'title' => 'Ceder o comunicar datos personales sin cumplir requisitos',
                    'description' => 'Comunicación o cesión de datos sin base, formalidad o condición legal suficiente.',
                ],
                [
                    'code' => 'resp_grave_sin_metodologia_riesgos',
                    'title' => 'No utilizar metodologías de análisis y gestión de riesgos adaptadas a la naturaleza de los datos',
                    'description' => 'Ausencia de metodologías de riesgo acordes al contexto y sensibilidad de los datos tratados.',
                ],
                [
                    'code' => 'resp_grave_sin_eipd',
                    'title' => 'No realizar evaluaciones de impacto cuando era necesario',
                    'description' => 'Omisión de evaluaciones de impacto en tratamientos de riesgo elevado.',
                ],
                [
                    'code' => 'resp_grave_sin_mitigacion',
                    'title' => 'No implementar medidas para prevenir, impedir, reducir, mitigar y controlar riesgos y vulneraciones',
                    'description' => 'Ausencia de acciones preventivas y de contención frente a riesgos identificados.',
                ],
                [
                    'code' => 'resp_grave_no_notificar_vulneracion',
                    'title' => 'No notificar a la autoridad y al titular sobre vulneraciones cuando afecte derechos',
                    'description' => 'Incumplimiento del deber de notificación ante una vulneración relevante.',
                ],
                [
                    'code' => 'resp_grave_sin_clausulas_confidencialidad',
                    'title' => 'No suscribir contratos con cláusulas adecuadas de confidencialidad y tratamiento con encargados y personal',
                    'description' => 'Falta de acuerdos contractuales suficientes con encargados o personal con acceso a datos.',
                ],
            ],
        ],
        'encargado' => [
            'leve' => [
                [
                    'code' => 'enc_leve_no_colaborar_solicitudes',
                    'title' => 'No colaborar con el responsable para atender solicitudes de titulares',
                    'description' => 'Falta de cooperación operativa para responder a derechos de los titulares.',
                ],
                [
                    'code' => 'enc_leve_no_facilitar_informacion',
                    'title' => 'No facilitar acceso al responsable a la información de cumplimiento',
                    'description' => 'No entrega de información, evidencia o soporte necesario al responsable.',
                ],
                [
                    'code' => 'enc_leve_no_auditorias',
                    'title' => 'No permitir o no contribuir a auditorías o inspecciones',
                    'description' => 'Obstrucción o falta de colaboración frente a revisiones de cumplimiento.',
                ],
                [
                    'code' => 'enc_leve_incumplir_medidas_autoridad',
                    'title' => 'Incumplir medidas correctivas dispuestas por la autoridad',
                    'description' => 'Desatención de medidas correctivas emitidas por la autoridad competente.',
                ],
            ],
            'grave' => [
                [
                    'code' => 'enc_grave_sin_instrucciones',
                    'title' => 'Tratar datos personales sin instrucciones del responsable o para fines distintos',
                    'description' => 'Tratamiento ejecutado fuera del marco de instrucciones autorizadas.',
                ],
                [
                    'code' => 'enc_grave_no_devolver_eliminar',
                    'title' => 'No eliminar o devolver datos al responsable al finalizar la prestación contractual',
                    'description' => 'Retención o conservación indebida de datos al finalizar el servicio.',
                ],
                [
                    'code' => 'enc_grave_no_informar_vulneracion',
                    'title' => 'No informar al responsable sobre vulneraciones de seguridad de datos personales',
                    'description' => 'Omisión de reporte oportuno ante incidentes o vulneraciones de seguridad.',
                ],
                [
                    'code' => 'enc_grave_no_medidas_seguridad',
                    'title' => 'No aplicar las medidas de seguridad exigidas por la LOPDP o por contrato',
                    'description' => 'Falta de implementación de medidas de seguridad legales o contractuales.',
                ],
                [
                    'code' => 'enc_grave_no_colaborar_autoridad',
                    'title' => 'No colaborar con la autoridad en el cumplimiento de sus funciones',
                    'description' => 'Falta de cooperación con requerimientos o actuaciones de la autoridad.',
                ],
                [
                    'code' => 'enc_grave_medidas_tardias',
                    'title' => 'Incumplir medidas correctivas o cumplirlas tarde, parcial o defectuosamente',
                    'description' => 'Cumplimiento defectuoso o tardío de medidas correctivas obligatorias.',
                ],
            ],
        ],
    ],

    'pdi_questions' => [
        [
            'key' => 'legal_basis_per_activity',
            'label' => '¿Su empresa puede demostrar, con evidencias, que tiene una base legal válida para cada actividad de tratamiento de datos personales?',
            'help' => 'Considera registros, evaluaciones, documentos contractuales o evidencia verificable.',
        ],
        [
            'key' => 'processor_contracts_clauses',
            'label' => '¿Todos los contratos con encargados del tratamiento incluyen cláusulas específicas de protección de datos conforme con la LOPDP?',
            'help' => 'Evalúa si los contratos vigentes cubren deberes, seguridad y confidencialidad.',
        ],
        [
            'key' => 'incident_response_procedures',
            'label' => '¿Ha documentado y aplicado su empresa procedimientos para responder a incidentes de seguridad de datos personales?',
            'help' => 'Incluye detección, respuesta, contención, escalamiento y comunicación.',
        ],
        [
            'key' => 'recent_audits',
            'label' => '¿Se han realizado auditorías internas o externas sobre cumplimiento de protección de datos en los últimos 12 meses?',
            'help' => 'Se toma como evidencia de monitoreo y mejora del cumplimiento.',
        ],
        [
            'key' => 'forms_with_clauses',
            'label' => '¿Todos los formularios web o físicos donde se recogen datos personales incluyen cláusulas sobre el tratamiento de estos datos?',
            'help' => 'Aplica a formularios, landing pages, consentimientos y documentos físicos.',
        ],
        [
            'key' => 'dpia_when_required',
            'label' => '¿Ha realizado su organización una Evaluación de Impacto a la Protección de Datos cuando el tratamiento lo amerita?',
            'help' => 'Evalúa si se hicieron EIPD o DPIA cuando el riesgo del tratamiento lo requería.',
        ],
        [
            'key' => 'rights_requests_evidence',
            'label' => '¿Cuenta su empresa con evidencias de haber cumplido con solicitudes de derechos de titulares dentro de los plazos?',
            'help' => 'Se esperan registros de recepción, trámite y cierre oportuno.',
        ],
        [
            'key' => 'retention_and_deletion_policy',
            'label' => '¿Tiene documentado y vigente cómo procederá la conservación y eliminación de datos personales?',
            'help' => 'Incluye retención, supresión, destrucción y respaldo de cumplimiento.',
        ],
        [
            'key' => 'staff_training_evidence',
            'label' => '¿Existe registro de que al menos el 80% del personal ha recibido formación específica en protección de datos personales?',
            'help' => 'La respuesta debe basarse en evidencia documentada y reciente.',
        ],
        [
            'key' => 'formal_governance_structure',
            'label' => '¿Existe una estructura organizativa formal, comité, responsable o canal interno que supervise el cumplimiento de la LOPDP?',
            'help' => 'Se valora la existencia de gobierno interno y monitoreo formal del cumplimiento.',
        ],
    ],

    'pdi' => [
        'smoothing_delta' => 0.15,
    ],

    'impact_levels' => [
        [
            'value' => 'no_aplicable',
            'label' => 'No aplicable',
            'description' => 'No existe afectación apreciable o no hay evidencia de impacto sobre esta dimensión.',
            'score' => 0.00,
        ],
        [
            'value' => 'baja',
            'label' => 'Baja',
            'description' => 'Afectación reducida, acotada y de recuperación relativamente sencilla.',
            'score' => 0.25,
        ],
        [
            'value' => 'media',
            'label' => 'Media',
            'description' => 'Afectación relevante, con compromiso parcial y necesidad de medidas correctivas claras.',
            'score' => 0.50,
        ],
        [
            'value' => 'alta',
            'label' => 'Alta',
            'description' => 'Afectación severa con impacto sustancial en la operación o en los derechos de los titulares.',
            'score' => 0.75,
        ],
        [
            'value' => 'muy_alta',
            'label' => 'Muy alta',
            'description' => 'Afectación crítica, extendida o de recuperación compleja con alto potencial de daño.',
            'score' => 1.00,
        ],
    ],

    'data_types' => [
        [
            'value' => 'identificativos',
            'label' => 'Datos identificativos',
            'description' => 'Nombre, cedula, pasaporte u otros datos de identificacion.',
            'impact_score' => 0.25,
        ],
        [
            'value' => 'contacto',
            'label' => 'Datos de contacto',
            'description' => 'Correo, telefono, direccion o medios de contacto.',
            'impact_score' => 0.20,
        ],
        [
            'value' => 'financieros',
            'label' => 'Datos financieros',
            'description' => 'Ingresos, cuentas, historiales de pago u otra informacion economica.',
            'impact_score' => 0.70,
        ],
        [
            'value' => 'sensibles',
            'label' => 'Datos sensibles',
            'description' => 'Origen etnico, ideologia, religion, vida sexual u otros datos especialmente protegidos.',
            'impact_score' => 0.90,
        ],
        [
            'value' => 'salud',
            'label' => 'Datos de salud',
            'description' => 'Diagnosticos, tratamientos, incapacidades u otra informacion medica.',
            'impact_score' => 0.95,
        ],
        [
            'value' => 'biometricos',
            'label' => 'Datos biométricos',
            'description' => 'Huella, rostro, iris, voz u otro identificador biométrico.',
            'impact_score' => 1.00,
        ],
        [
            'value' => 'laborales',
            'label' => 'Datos laborales',
            'description' => 'Historial contractual, desempeno, cargo o expedientes laborales.',
            'impact_score' => 0.45,
        ],
        [
            'value' => 'ubicacion',
            'label' => 'Datos de ubicacion o trazabilidad',
            'description' => 'Rutas, geoposicion o registros de seguimiento y monitoreo.',
            'impact_score' => 0.50,
        ],
    ],

    'data_volume_options' => [
        [
            'value' => 'bajo',
            'label' => 'Bajo',
            'description' => 'Afectacion limitada, bajo volumen documental o registros muy acotados.',
            'score' => 0.25,
        ],
        [
            'value' => 'medio',
            'label' => 'Medio',
            'description' => 'Volumen intermedio, varias tablas, archivos o conjuntos de datos relacionados.',
            'score' => 0.50,
        ],
        [
            'value' => 'alto',
            'label' => 'Alto',
            'description' => 'Volumen amplio, multiples fuentes o bases afectadas con informacion relevante.',
            'score' => 0.75,
        ],
        [
            'value' => 'masivo',
            'label' => 'Masivo',
            'description' => 'Volumen muy alto, replicado o extendido en varios repositorios y sistemas.',
            'score' => 1.00,
        ],
    ],

    'data_subject_count_bands' => [
        ['min' => 1, 'max' => 100, 'label' => '1 a 100 titulares', 'score' => 0.25],
        ['min' => 101, 'max' => 1000, 'label' => '101 a 1.000 titulares', 'score' => 0.50],
        ['min' => 1001, 'max' => 10000, 'label' => '1.001 a 10.000 titulares', 'score' => 0.75],
        ['min' => 10001, 'max' => null, 'label' => 'Mas de 10.000 titulares', 'score' => 1.00],
    ],

    'data_volume_bands' => [
        ['min' => 0.01, 'max' => 100, 'label' => 'Hasta 100 unidades', 'score' => 0.25],
        ['min' => 100.01, 'max' => 1000, 'label' => '101 a 1.000 unidades', 'score' => 0.50],
        ['min' => 1000.01, 'max' => 10000, 'label' => '1.001 a 10.000 unidades', 'score' => 0.75],
        ['min' => 10000.01, 'max' => null, 'label' => 'Mas de 10.000 unidades', 'score' => 1.00],
    ],

    'intentionality_options' => [
        [
            'value' => 'error_aislado',
            'label' => 'Error aislado o afectacion accidental',
            'description' => 'No se evidencian patrones de negligencia grave ni intencionalidad relevante.',
            'score' => 0.20,
        ],
        [
            'value' => 'negligencia_simple',
            'label' => 'Negligencia simple',
            'description' => 'Existio falta de diligencia o control razonable, pero sin indicios de dolo.',
            'score' => 0.50,
        ],
        [
            'value' => 'negligencia_grave',
            'label' => 'Negligencia grave',
            'description' => 'Se observa desatencion severa, persistente o claramente evitable.',
            'score' => 0.80,
        ],
        [
            'value' => 'intencionalidad',
            'label' => 'Intencionalidad o culpa grave',
            'description' => 'La conducta revela decision consciente o uso deliberado contrario a la LOPDP.',
            'score' => 1.00,
        ],
    ],

    'rer' => [
        'applies_score' => 1.00,
        'not_applies_score' => 0.00,
    ],

    'monte_carlo' => [
        'iterations' => 1000,
        'histogram_bins' => 16,
        'pert_lambda' => 4.0,
        'uncertainty' => [
            'ndv_delta' => 0.18,
            'tdp_delta' => 0.15,
            'tav_delta' => 0.20,
            'tev_delta' => 0.25,
        ],
        'simulated_components' => [
            'ndv' => 'Naturaleza de la vulneracion derivada de confidencialidad, integridad y disponibilidad.',
            'tdp' => 'Sensibilidad relativa de los tipos de datos personales afectados.',
            'tav' => 'Alcance de la afectacion considerando titulares y volumen de datos.',
            'tev' => 'Exposicion adicional cuando intervienen grupos especialmente vulnerables.',
        ],
    ],

    'assumptions' => [
        'pdi' => 'El PDI se deriva del porcentaje de respuestas negativas del cuestionario, porque cada respuesta No representa una brecha de cumplimiento. Luego se suaviza con un ajuste PERT basado en el coeficiente configurado pert_weight_most_probable.',
        'tdp' => 'TDP se estima tomando la mayor sensibilidad entre los tipos de datos personales seleccionados.',
        'tav' => 'TAV se estima como promedio entre la banda de titulares afectados y la banda del volumen numérico de datos personales afectados.',
        'ndv' => 'NDV se estima como promedio de afectación a confidencialidad, integridad y disponibilidad.',
        'tev' => 'TEV se eleva a 1 cuando existen grupos especialmente vulnerables y a 0 cuando no aplican.',
        'sdi' => 'SDI se calcula usando el multiplicador configurado y los pesos ied_weight, int_weight y rer_weight.',
        'public_conversion' => 'Cuando la entidad es pública, el CDI se obtiene en SBU y luego se convierte a USD usando el SBU de referencia del caso.',
        'numeric_normalization' => 'Los montos económicos aceptan formatos humanos como 50000, 50,000, 50.000 o 50 000 y se normalizan a su valor numérico real antes del cálculo.',
    ],

    'documentation' => [
        'source' => [
            'title' => 'Fuente normativa principal',
            'summary' => 'El módulo se fundamenta en los documentos oficiales del modelo de cálculo de multas y en la guía oficial de gestión de riesgos aplicable al tratamiento de datos personales.',
            'items' => [
                'Los rangos sancionatorios base se parametrizan desde la normativa oficial aplicable.',
                'La estructura metodológica del asistente sigue una trazabilidad funcional: CDI, PDI, IED, INT, RER, SDI y multa estimada.',
                'La gestión de riesgos y vulneraciones se conecta a una lectura metodológica compatible con la guía oficial de riesgos.',
            ],
        ],
        'methodology' => [
            [
                'code' => 'CDI',
                'title' => 'Categoría de infracción',
                'description' => 'Parte de la severidad leve o grave y del tipo de entidad evaluada. En entidades privadas se usa un rango porcentual sobre el volumen de negocio; en entidades públicas se usa un rango en SBU convertido luego a USD.',
            ],
            [
                'code' => 'PDI',
                'title' => 'Peso de la infracción',
                'description' => 'Se deriva del cuestionario de cumplimiento. Las respuestas negativas representan brechas del modelo y elevan el peso de la infracción mediante una suavización metodológica.',
            ],
            [
                'code' => 'IED',
                'title' => 'Índice de exposición del daño',
                'description' => 'Integra sensibilidad de los datos, alcance de la afectación, naturaleza de la vulneración y presencia de grupos vulnerables en una sola medida ponderada.',
            ],
            [
                'code' => 'INT',
                'title' => 'Intencionalidad',
                'description' => 'Captura el grado de error, negligencia o intencionalidad asociado a la conducta y lo convierte en un factor metodológico del modelo.',
            ],
            [
                'code' => 'RER',
                'title' => 'Reiteracion y reincidencia',
                'description' => 'Opera como componente opcional o condicional. Si no aplica, su valor metodológico es cero.',
            ],
            [
                'code' => 'SDI',
                'title' => 'Severidad derivada integral',
                'description' => 'Combina IED, INT y RER según los pesos configurados y el multiplicador metodológico del modelo.',
            ],
            [
                'code' => 'MULTA',
                'title' => 'Multa estimada',
                'description' => 'Se obtiene multiplicando el CDI por el SDI. El resultado se presenta en USD para facilitar su lectura funcional y comercial.',
            ],
        ],
        'normative_fixed_parameters' => [
            'Rangos MPRIV para entidad privada leve y grave.',
            'Rangos MPUB en SBU para entidad publica leve y grave.',
        ],
        'configurable_parameters' => [
            'pert_weight_most_probable',
            'sdi_multiplier',
            'ied_weight',
            'int_weight',
            'tdp_weight',
            'tav_weight',
            'ndv_weight',
            'tev_weight',
            'sbu_default',
            'rer_weight cuando se encuentra habilitado',
        ],
        'orientation' => [
            'title' => 'Naturaleza orientativa del resultado',
            'items' => [
                'La estimacion depende de la precision y completitud de los datos ingresados por el usuario.',
                'El resultado no sustituye una evaluacion juridica, tecnica o pericial definitiva.',
                'El modulo busca trazabilidad metodologica y consistencia interna, no una decision sancionatoria formal.',
            ],
        ],
        'monte_carlo' => [
            'title' => 'Análisis de Monte Carlo',
            'summary' => 'El cálculo determinista entrega una estimación puntual. Monte Carlo agrega una capa probabilística sobre los factores estimativos del modelo para mostrar escenarios mínimo, medio y máximo sin reemplazar la base determinista.',
        ],
        'ui_notice' => 'Este resultado es orientativo, se basa en los modelos oficiales aplicables y en los datos ingresados en el asistente. No sustituye una evaluación jurídica o técnica definitiva.',
    ],
];
