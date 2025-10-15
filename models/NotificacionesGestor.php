<?php

namespace Model;

use Model\ActiveRecord;

class NotificacionesGestor extends ActiveRecord
{
    public static function obtenerGestoresPromesasHoy()
    {
        self::useSQLSrv();
        $sql = "
            SELECT e.Code AS codigo,
                   e.NOMBRECOMPLETO AS nombre,
                   e.CORREO AS correo,
                   e.TELEFONO AS telefono,
                   p.creado_por,
                   COUNT(*) AS total_clientes
            FROM NotificacionesPromesas p
            INNER JOIN TBL_EMPLEADOS e ON e.NOMBRECOMPLETO = p.creado_por
            WHERE CONVERT(date, p.fecha_promesa) = CONVERT(date, GETDATE())
              AND p.tipo_notificacion = 'PROMESA_HOY'
            GROUP BY e.Code, e.NOMBRECOMPLETO, e.CORREO, e.TELEFONO, p.creado_por;
        ";

        return self::consultarSQL($sql);
    }

    public static function obtenerGestoresRecordatorios()
    {
        self::useSQLSrv();
        $sql = "
            SELECT e.Code AS codigo,
                   e.NOMBRECOMPLETO AS nombre,
                   e.CORREO AS correo,
                   e.TELEFONO AS telefono,
                   p.creado_por,
                   COUNT(*) AS total_clientes
            FROM NotificacionesPromesas p
            INNER JOIN TBL_EMPLEADOS e ON e.NOMBRECOMPLETO = p.creado_por
            WHERE CONVERT(date, p.fecha_notificacion) = CONVERT(date, GETDATE())
              AND p.tipo_notificacion = 'RECORDATORIO_CLIENTE'
            GROUP BY e.Code, e.NOMBRECOMPLETO, e.CORREO, e.TELEFONO, p.creado_por;
        ";

        return self::consultarSQL($sql);
    }


    public static function obtenerDetallePromesasHoyPorGestor($gestorNombre)
    {
        self::useSQLSrv();
        $sql = "
        SELECT 
            p.prenumero,
            c.PreNombre AS nombre_cliente,
            p.montoPromesa
        FROM vw_PromesasDetalle p
		left join [192.168.1.60].[SKG_BP].sifco.CrPrestamos as c on c.PreNumero = p.prenumero
        WHERE 
            CONVERT(date, p.fecha_promesa) = CONVERT(date, GETDATE())
            AND p.estado_promesa = 'PENDIENTE'
            AND p.creado_por = ?
        ORDER BY c.PreNombre
    ";
        return self::consultarSQL($sql, [$gestorNombre], false);
    }

}
