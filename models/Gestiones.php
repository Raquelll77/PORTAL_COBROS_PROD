<?php
namespace Model;

class Gestiones extends ActiveRecord
{
    protected static $tabla = 'Gestiones';
    protected static $columnasDB = [
        'id',
        'prenumero',
        'codigo_resultado',
        'fecha_revision',
        'fecha_promesa',
        'numero_contactado',
        'comentario',
        'creado_por',
        'fecha_creacion',
        'montoPromesa'
    ];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->prenumero = $args['prenumero'] ?? '';
        $this->codigo_resultado = $args['codigo_resultado'] ?? '';
        $this->fecha_revision = $args['fecha_revision'] ?? null;
        $this->fecha_promesa = $args['fecha_promesa'] ?? null;
        $this->numero_contactado = $args['numero_contactado'] ?? '';
        $this->comentario = $args['comentario'] ?? '';
        $this->creado_por = $args['creado_por'] ?? '';
        $this->fecha_creacion = $args['fecha_creacion'] ?? null;
        $this->montoPromesa = $args['monto_promesa'] ?? null;
    }

    public static function ObtenerGestionesPorFechas($fecha)
    {

        self::useSQLSrv();

        $sql = "EXEC ObtenerGestionesPorFechas @fecha =  ?";
        $params = [$fecha];

        return self::consultarSQL($sql, $params, true);

    }

    /**
     * Procesar datos de gestiones para cálculos
     */
    public static function procesarDatosGestiones($gestiones)
    {
        // Inicializar totales y agrupaciones
        $totales = array_map(function ($gestion) {
            return $gestion['total'];
        }, $gestiones);

        $gestores = array_map(function ($gestion) {
            return $gestion['gestor'];
        }, $gestiones);

        // Número de gestiones esperadas por gestor
        $gestionesEsperadasPorGestor = 60;

        // Calcular porcentaje realizado por cada gestor
        $porcentajesPorGestor = [];
        foreach ($totales as $index => $total) {
            $porcentajesPorGestor[] = round(($total / $gestionesEsperadasPorGestor) * 100, 2);
        }


        $listaCodigos = \Model\CodigosResultado::all();
        $codigoResultados = [];
        foreach ($listaCodigos as $codigoObj) {
            // Inicializamos cada código con un valor 0
            $codigoResultados[$codigoObj->codigo] = 0;
        }

        // Sumar los valores de cada código en las gestiones
        foreach ($gestiones as $gestion) {
            foreach ($codigoResultados as $codigo => $valor) {
                // Se asume que en cada gestión se tiene un índice con el nombre del código (por ejemplo, 'PAGO')
                if (isset($gestion[$codigo])) {
                    $codigoResultados[$codigo] += $gestion[$codigo];
                }
            }
        }

        /* // Inicializar códigos de resultado
        $codigoResultados = [
            'PAGO' => 0,
            'ABONO' => 0,
            'PROMESA DE PAGO' => 0,
            'CANCELACION' => 0,
            'DECOMISO' => 0,
            'PARA DECOMISO' => 0,
            'SE NIEGA A PAGAR' => 0,
            'PRESTO EL CREDITO' => 0,
            'SE FUE DEL PAIS' => 0,
            'CAMBIO DE DOMICILIO' => 0,
            'FRAUDE' => 0,
            'ZONA DE RIESGO' => 0,
            'ILOCALIZABLE' => 0,
            'PERFIL DE RIESGO' => 0,
            'DIFUNTO' => 0,
            'EXCEPCION' => 0,
            'ROBO' => 0,
            'TRANSITO' => 0
        ];

        // Sumar códigos de resultado
        foreach ($gestiones as $gestion) {
            $codigoResultados['PAGO'] += isset($gestion['PAGO']) ? $gestion['PAGO'] : 0;
            $codigoResultados['ABONO'] += isset($gestion['ABONO']) ? $gestion['ABONO'] : 0;
            $codigoResultados['PROMESA DE PAGO'] += isset($gestion['PROMESA DE PAGO']) ? $gestion['PROMESA DE PAGO'] : 0;
            $codigoResultados['CANCELACION'] += isset($gestion['CANCELACION']) ? $gestion['CANCELACION'] : 0;
            $codigoResultados['DECOMISO'] += isset($gestion['DECOMISO']) ? $gestion['DECOMISO'] : 0;
            $codigoResultados['PARA DECOMISO'] += isset($gestion['PARA DECOMISO']) ? $gestion['PARA DECOMISO'] : 0;
            $codigoResultados['SE NIEGA A PAGAR'] += isset($gestion['SE NIEGA A PAGAR']) ? $gestion['SE NIEGA A PAGAR'] : 0;
            $codigoResultados['PRESTO EL CREDITO'] += isset($gestion['PRESTO EL CREDITO']) ? $gestion['PRESTO EL CREDITO'] : 0;
            $codigoResultados['SE FUE DEL PAIS'] += isset($gestion['SE FUE DEL PAIS']) ? $gestion['SE FUE DEL PAIS'] : 0;
            $codigoResultados['CAMBIO DE DOMICILIO'] += isset($gestion['CAMBIO DE DOMICILIO']) ? $gestion['CAMBIO DE DOMICILIO'] : 0;
            $codigoResultados['FRAUDE'] += isset($gestion['FRAUDE']) ? $gestion['FRAUDE'] : 0;
            $codigoResultados['ZONA DE RIESGO'] += isset($gestion['ZONA DE RIESGO']) ? $gestion['ZONA DE RIESGO'] : 0;
            $codigoResultados['ILOCALIZABLE'] += isset($gestion['ILOCALIZABLE']) ? $gestion['ILOCALIZABLE'] : 0;
            $codigoResultados['PERFIL DE RIESGO'] += isset($gestion['PERFIL DE RIESGO']) ? $gestion['PERFIL DE RIESGO'] : 0;
            $codigoResultados['DIFUNTO'] += isset($gestion['DIFUNTO']) ? $gestion['DIFUNTO'] : 0;
            $codigoResultados['EXCEPCION'] += isset($gestion['EXCEPCION']) ? $gestion['EXCEPCION'] : 0;
            $codigoResultados['ROBO'] += isset($gestion['ROBO']) ? $gestion['ROBO'] : 0;
            $codigoResultados['TRANSITO'] += isset($gestion['TRANSITO']) ? $gestion['TRANSITO'] : 0;
        } */
        // Calcular totales y porcentajes
        $totalCodigos = array_sum($codigoResultados);
        $porcentajesCodigos = $totalCodigos > 0
            ? array_map(function ($valor) use ($totalCodigos) {
                return round(($valor / $totalCodigos) * 100, 2);
            }, $codigoResultados)
            : array_fill(0, count($codigoResultados), 0);

        return [
            'gestores' => $gestores,
            'totales' => $totales,
            'porcentajesPorGestor' => $porcentajesPorGestor,
            'codigoResultados' => $codigoResultados,
            'porcentajesCodigos' => $porcentajesCodigos,
            'totalGestiones' => array_sum($totales)
        ];
    }


    //cumplimiento de promesas 
    public static function obtenerTodasLasPromesas()
    {
        self::useSQLSrv();
        $sql = "EXEC sp_ConsultarPromesas @tipo_busqueda = ?";
        $params = [1]; // 1: Consultar todo
        return self::consultarSQL($sql, $params, true);
    }

    public static function obtenerPromesasPorGestor($gestor)
    {
        self::useSQLSrv();
        $sql = "EXEC sp_ConsultarPromesas @tipo_busqueda = ?, @gestor = ?";
        $params = [2, $gestor]; // 2: Filtrar por gestor
        return self::consultarSQL($sql, $params, true);
    }

    public static function obtenerPromesasPorCliente($prenumero)
    {
        self::useSQLSrv();
        $sql = "EXEC sp_ConsultarPromesas @tipo_busqueda = ?, @prenumero = ?";
        $params = [3, $prenumero]; // 3: Filtrar por cliente
        return self::consultarSQL($sql, $params, true);
    }



}
